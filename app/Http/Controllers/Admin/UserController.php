<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

use App\Http\Controllers\Controller;

use App\Http\Requests\User\UserStore;
use App\Http\Requests\User\UserUpdate;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function get($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function store(UserStore $request)
    {
        $validated = $request->validated();

        $user = new User;
        $user->fill(Arr::except($validated, ['password']));
        $user->password = Hash::make($validated['password']);
        $user->save();

        event(new Registered($user));


        return response()->json($user);
    }

    public function update(UserUpdate $request, $id)
    {

        $validated = $request->validated();

        $user = User::find($id);
        $user->update($validated);
        return response()->json($user);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(true);
    }

    public function sendVerification($id)
    {
        $user = User::find($id);
        event(new Registered($user));
        return response()->json(true);
    }
}
