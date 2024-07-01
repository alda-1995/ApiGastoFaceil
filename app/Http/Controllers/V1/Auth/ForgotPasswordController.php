<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()->toArray()], 400);
        }
        $token = Password::createToken(User::where('email', $request->email)->first());
        return response()->json(['token' => $token], 200);
    }
}
