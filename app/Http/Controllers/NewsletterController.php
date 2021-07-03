<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicFrontend\Newsletter\SubscriptionStore;
use App\Mail\SubscribeToNewsletter;
use App\Models\NewsletterSubscription;
use Doctrine\Inflector\Rules\Substitution;
use Illuminate\Http\Request;

use Str;

use App\Mail\TestMail;
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

        //check if E-Mail exists in table newsletter_subscriptions
        if (NewsletterSubscription::where('email', $validated['email'])->exists()) {

            //check if E-Mail is Verified
            if (NewsletterSubscription::where('email', $validated['email'])->pluck('token')[0] !== NULL) {
                return response()->json(['message' => 'Bitte bestätige erst deine E-Mail-Adresse.'], 409);
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

    public function verify($token){

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


    public function sendMail($validated)
    {


        $sub_title = \App\Models\Story::where('id', $validated['option'])->pluck('title');

        $details = [
            'name' => $validated['name'],
            'subscription_title' => $sub_title,
            'newsletter' => 'This is for testing email using smtp'
        ];
        Mail::to($validated['email'])->send(new SubscribeToNewsletter($details));
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
