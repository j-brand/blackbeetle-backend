<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AuthLogin;

use Auth;

class AuthController extends Controller
{
    public function  authenticate(AuthLogin $request)
    {
        $validated = $request->validated();

        if (!auth()->attempt($validated)) {
            return response()->json(['message' => 'So nicht du Trottel!'], 403);
        }

        if (!Auth::user()->active) {
            return response()->json(['message' => 'Dein Account ist noch nicht freigeschaltet.'], 403);
        }

        return Auth::user();
    }

    public function logout()
    {
        Auth::logout();
    }

    public function user()
    {
        return $user = Auth::user();
    }
}
