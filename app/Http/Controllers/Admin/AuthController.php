<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function  authenticate(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
            
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!  auth()->attempt($validator->validated())) {
            return response()->json(['message' => 'So nicht du Trottel!'], 403);
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
