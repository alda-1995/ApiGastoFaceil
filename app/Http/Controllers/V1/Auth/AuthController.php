<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function createUser(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()->toArray()], 400);
        }
        try {
            $hashedPassword = Hash::make($request->password);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
            ]);

            $token = $user->createToken('Access User')->plainTextToken;
            return response()->json(['token' => $token], 201);
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al crear el usuario"], 400);
        }
    }

    public function authenticate(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'password' => 'required|string|max:50',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()->toArray()], 400);
        }
        try {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = User::where('email', $request->email)->first();
                $user->tokens()->delete();
                $token = $user->createToken('Access User')->plainTextToken;
                return response()->json(['token' => $token]);
            }
            return response()->json(['message' => 'Accesos incorretos'], 401);
        } catch (Exception $ex) {
            return response()->json(['error' => "Ocurrio un error inesperado al intentar autenticar."], 400);
        }
    }

    public function logout(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()->toArray()], 400);
        }

        try {
            $user = User::where("email", $request->email)->first();
            $user->tokens()->delete();
            return response()->json(['message' => "Vuelve pronto."], 201);
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al finalizar la sesión"], 400);
        }
    }
}
