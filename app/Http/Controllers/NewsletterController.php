<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicFrontend\Newsletter\SubscriptionStore;
use App\Http\Requests\PublicFrontend\Newsletter\VerifyEmail;
use App\Http\Requests\PublicFrontend\Newsletter\ResendVerificationEmail;
use App\Jobs\SendVerificationMailJob;
use App\Models\NewsletterSubscription;
use Doctrine\Inflector\Rules\Substitution;
use Illuminate\Http\Request;

use Config;

use Str;

use App\Mail\TestMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubscriptionStore $request)
    {
        $validated = $request->validated();

        //check if E-Mail already exists in table newsletter_subscriptions
        if (NewsletterSubscription::where('email', $validated['email'])->exists()) {

            //check if E-Mail is Verified
            if (NewsletterSubscription::where('email', $validated['email'])->pluck('email_verified_at')[0] === NULL) {
                return response()->json(['message' => 'Bitte bestätige erst deine E-Mail-Adresse. Neue verifizierungs E-Mail <a class="underline" href="/resend-verification">erhalten</a>.'], 409);
            }

            $update = $this->update($validated);
            return response()->json(['message' => $update['message']], $update['code']);
        }

        $options['story_id'] = $validated['option'];

        $sub = new NewsletterSubscription();
        $sub->email = $validated['email'];
        $sub->name = $validated['name'];
        $sub->options = json_encode($options);
        $sub->token =  (string) Str::uuid();
        $sub->save();

        $validated['token'] = $sub->token;

        $this->sendMail($validated);

        return response()->json(['message' => "Du wurdest für den Newsletter eingetragen."], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NewsletterSubscription  $newsletterSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(NewsletterSubscription $newsletterSubscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NewsletterSubscription  $newsletterSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(NewsletterSubscription $newsletterSubscription)
    {
        //
    }

    public function verify(VerifyEmail $request)
    {
        $validated = $request->validated();

        $sub = NewsletterSubscription::where('token', $validated['token'])->first();

        if (empty($sub->email_verified_at)) {
            $sub->email_verified_at = Carbon::now();
            $sub->save();
            return response()->json(['message' => $sub->name], 200);
        } else {
            return response()->json(['message' => "Deine E-Mail-Adresse ist bereits verifiziert."], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NewsletterSubscription  $newsletterSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(array $validated)
    {
        $sub = NewsletterSubscription::where('email', $validated['email'])->first();

        $subscriptions = json_decode($sub->options, true);
        if (in_array($validated['option'], explode('|', $subscriptions['story_id']))) {


            return ['message' => 'Du bist bereits angemeldet.', 'code' => 409];
        }

        $subscriptions['story_id'] .= "|" . $validated['option'];


        $sub->update(['options' =>  json_encode($subscriptions)]);

        return ['message' => "Du wurdest für den Newsletter eingetragen.", 'code' => 200];
    }

    public function resend(ResendVerificationEmail $request)
    {
        $validated = $request->validated();
        $sub = NewsletterSubscription::where('email', $validated['email'])->first();

        $data = [
            'name' => $sub->name,
            'email' => $sub->email,
            'token' => $sub->token
        ];

        $this->sendMail($data);
    }


    public function sendMail($data)
    {
        $details = [
            'name' => $data['name'],
            'email' => $data['email'],
            'link' => config('app.frontend_url') . '/verify-email/' . $data['token'],
        ];
        SendVerificationMailJob::dispatch($details);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NewsletterSubscription  $newsletterSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(NewsletterSubscription $newsletterSubscription)
    {
        //
    }
}
