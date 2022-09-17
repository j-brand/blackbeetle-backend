<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Jobs\SendVerificationMail;


class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriber = Subscriber::all();
        return response()->json($subscriber, 200);
    }

    public function sendVerification($id)
    {

        $sub = Subscriber::find($id);


        $details = [
            'name' => $sub->name,
            'email' => $sub->email,
            'verifyLink' => config('app.frontend_url') . '/subscription/verify/' . $sub->token,
            'manageLink' => config('app.frontend_url') . '/subscription/manage/' . $sub->token,
        ];
        SendVerificationMail::dispatch($details);

        return response()->json([
            'message' => 'Mail wird versand.'
        ], 200);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sub = Subscriber::find($id);
        $sub->delete();

        return response()->json([
            'message' => 'Abonnent wurde gelöscht.'
        ], 200);
    }
}
