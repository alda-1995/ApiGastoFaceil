<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $data = $request->only('email', 'password', 'password_confirmation', 'token');
        $validatedData = Validator::make($data, [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8|max:12',
        ]);
        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()->toArray()], 400);
        }
        $response = $this->broker()->reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
        return $response == Password::PASSWORD_RESET
            ? response()->json([
                'message' => 'Se ha restablecido la contraseña correctamente'
            ], Response::HTTP_OK)
            : response()->json('No se pudo restablecer la contraseña', 401);
    }

    public function broker()
    {
        return Password::broker();
    }
}
