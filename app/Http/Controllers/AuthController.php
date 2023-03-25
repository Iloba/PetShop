<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Auth\JwtAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __invoke(Request $request, JwtAuth $jwtAuth)
    {
        $valid = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users,email',
            'password'  => 'required|string|min:8',
        ]);

        if ($errors = $valid->errors()->all()) {
            return response()->json(['errors' => $errors], 400);
        }

        if ($token = $jwtAuth->authenticateAndReturnJwtToken($request->email, $request->password)) {
            return response()->json(['token' => $token]);
        }

        return response()->json(['errors' => 'Cannot be!'], 400);
    }
}
