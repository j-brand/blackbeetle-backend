<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\SendNewsletter;
use App\Jobs\SendNotificationMail;
use App\Models\Subscription;

class NotificationController extends Controller
{
    public function notify(SendNewsletter $request)
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
                SendNotificationMail::dispatch($details)->onQueue('emails');
            }
        }

        return response()->json(['success' => true]);
    }
}