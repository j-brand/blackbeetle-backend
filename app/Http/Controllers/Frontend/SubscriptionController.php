<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\Newsletter\SubscriptionStore;
use App\Http\Requests\Newsletter\VerifyEmail;
use App\Http\Requests\Newsletter\ResendVerificationEmail;
use App\Http\Requests\Newsletter\SubscriptionUpdate;
use App\Jobs\SendVerificationMail;
use App\Http\Controllers\Controller;

use Illuminate\Support\Str;

use App\Models\Story;
use App\Models\Subscriber;
use App\Models\Subscription;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * Options 
     * Story: s[Story ID]
     * 
     */
    public function create(SubscriptionStore $request)
    {
        $validated = $request->validated();

        //check if E-Mail already exists in table newsletter_subscriptions
        if (!Subscriber::where('email', $validated['email'])->exists()) {
            $subscriber = new Subscriber();
            $subscriber->email = $validated['email'];
            $subscriber->name = $validated['name'];
            $subscriber->token =  (string) Str::uuid();
            $subscriber->save();

            $this->store($subscriber->id, $validated['option']);
            $subscriber = Subscriber::where('email', $validated['email'])->first();

            $validated['token'] = $subscriber->token;

            $this->sendVerificationMail($validated);
            return response()->json(['message' => trans('messages.subscription_created')], 200);
        }


        //check if E-Mail is Verified
        if (Subscriber::where('email', $validated['email'])->pluck('verified_at')[0] === NULL) {
            return response()->json(['message' => trans('messages.subscription_verify_first', ['href' => '/resend-verification'])], 409);
        }


        $subscriber = Subscriber::where('email', $validated['email'])->first();
        $this->store($subscriber->id, $validated['option']);


        return response()->json(['message' => trans('messages.subscription_stored')], 200);
    }

    public function store($subscriber_id, $option)
    {
        $subscription = new Subscription();
        $subscription->subscriber_id = $subscriber_id;
        $subscription->option = $option;
        $subscription->save();
    }


    public function verify(VerifyEmail $request)
    {
        $validated = $request->validated();

        $sub = Subscriber::where('token', $validated['token'])->first();

        if (empty($sub->verified_at)) {
            $sub->verified_at = Carbon::now();
            $sub->save();
            return response()->json(['message' => $sub->name], 200);
        } else {
            return response()->json(['message' => trans('messages.subscription_already_stored')], 422);
        }
    }

    public function resend(ResendVerificationEmail $request)
    {
        $validated = $request->validated();
        if (Subscriber::where('email', $validated['email'])->exists()) {
            $sub = Subscriber::where('email', $validated['email'])->first();
            $data = [
                'name' => $sub->name,
                'email' => $sub->email,
                'token' => $sub->token
            ];

            $this->sendVerificationMail($data);
        }

        return response()->json(['message' => trans('messages.subscription_verification_send')], 200);
    }


    public function getSubscriptions($token)
    {

        $subscriptions = Subscriber::where('token', $token)->first()->subscriptions()->pluck('option')->toArray();
        $stories = Story::all();

        $subsFormatted = [];

        foreach ($stories as $story) {
            $subsFormatted[] = [
                "id" => $story->id,
                "title" => $story->title,
                "is_sub" => in_array('S' . $story->id, $subscriptions)
            ];
        }
        return response()->json($subsFormatted);
    }

    public function updateSubscription(SubscriptionUpdate $request)
    {

        $validated = $request->validated();

        $subscriber = Subscriber::where('token', $validated['token'])->first();

        if ($validated['value'] == false) {
            $subscription = Subscription::where('option', $validated['option'])->where('subscriber_id', $subscriber->id)->delete();
        } else if ($validated['value'] == true) {
            $subscription = new Subscription();
            $subscription->subscriber_id = $subscriber->id;
            $subscription->option = $validated['option'];
            $subscription->save();
        }

        return response()->json(['message' => trans('messages.subscription_config_stored')], 200);
    }

    public function sendVerificationMail($data)
    {
        $details = [
            'name' => $data['name'],
            'email' => $data['email'],
            'verifyLink' => config('app.frontend_url') . '/subscription/verify/' . $data['token'],
            'manageLink' => config('app.frontend_url') . '/subscription/manage/' . $data['token'],
        ];
        SendVerificationMail::dispatch($details);
    }
}
