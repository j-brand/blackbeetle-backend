<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Newsletter\SendNewsletter;
use App\Jobs\SendVerificationMailJob;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use App\Models\Subscriber;
use App\Models\Subscription;
use DB;

class NewsletterController extends Controller
{
    public function nothify(SendNewsletter $request)
    {
        $validated = $request->validated();

        $subscriptions = Subscription::where('option', $validated['option'])->get();

        foreach ($subscriptions as $subscription) {

            if ($subscription->subscriber['verified_at'] != NULL) {
                $this->sendMail([
                    'name' => $subscription->subscriber['verified_at'],
                    'email' => $subscription->subscriber['email'],
                    'token' => $subscription->subscriber['token'],
                ]);
            }
        }

        /* 
        $s = DB::table('newsletter_subscriptions_options')->where('option', $validated['option'])->get();
        $s = DB::table('newsletter_subscriptions_options')->where('option', $validated['option'])->join('newsletter_subscriptions', 'newsletter_subscriptions_options.subscription_id', '=', 'newsletter_subscriptions.id')->get();
 */
        //  $subscribtions = Subscription::where('option', $validated['option'])->with('verified_subscriber')->get();
        // $subscribtions = Subscription::where('option', $validated['option'])->verified_subscriber()->get();
        //   $subscribtions = Subscription::where('option', $validated['option'])->verified_subscriber()->get();
        //   $subscribtions = Subscription::where('option', $validated['option'])->verified_subscriber()->get();

      //  $subscribtions = Subscriber::find(1)->subscriptions()->where('option', $validated['option'])->get();

        //$sub = Subscriber::whereNotNull('verified_at')->join('subscriptions','id','=','subscriptions.subscriber_id')->get();


        return response()->json(true);
    }


    public function sendMail($data)
    {
        $details = [
            'name' => $data['name'],
            'email' => $data['email'],
            'verifyLink' => config('app.frontend_url') . '/verify-email/' . $data['token'],
            'manageLink' => config('app.frontend_url') . '/manage-subscriptions/' . $data['token'],
        ];
        SendVerificationMailJob::dispatch($details);
    }
}
