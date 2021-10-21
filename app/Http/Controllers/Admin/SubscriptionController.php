<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Newsletter\SendNewsletter;
use App\Jobs\SendNothificationMail;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function nothify(SendNewsletter $request)
    {
        $validated = $request->validated();

        $subscriptions = Subscription::where('option', $validated['option'])->with('verified_subscriber')->get();

        foreach ($subscriptions as $subscription) {

            if ($subscription->verified_subscriber != NULL && $subscription->verified_subscriber->deleted_at == NULL) {
                $subscriber = $subscription->verified_subscriber;

                $details = [
                    'email' => $subscriber->email,
                    'name' => $subscriber->name,
                    'subject' => $validated['subject'],
                    'content' => $validated['content'],
                    'image' => $validated['image'],
                    'link'  => config('app.frontend_url') . '/blog/' . $validated['slug'],
                    'manageLink' => config('app.frontend_url') . '/manage-subscriptions/' . $subscriber->token,
                ];

                SendNothificationMail::dispatch($details);
            }
        }

        return response()->json(['success' => true]);
    }
}
