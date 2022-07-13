<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Http\Requests\Notification\SubscriptionStore;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function store(SubscriptionStore $request)
    {

        $validated = $request->validated();
        $exists = Notification::where('email', $validated['email'])->where('story_id', $validated['story_id'])->exists();

        if ($exists) {
            return response()->json(["message" => "Du hast dich bereits eingetragen."], 409);
        }

        $not =  new Notification();
        $not->email = $validated['email'];
        $not->name = $validated['name'];
        $not->story_id = $validated['story_id'];
        $not->save();

        return response()->json(true, 200);
    }
}
