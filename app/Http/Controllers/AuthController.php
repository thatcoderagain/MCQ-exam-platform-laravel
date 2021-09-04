<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Method authenticates a user and returns an access_token
     *
     * @param $userId
     * @param array $abilities
     * @param string $tokenName
     * @return JsonResponse
     */
    private function authenticateAndGetToken($userId, $tokenName = 'access_token', $abilities = []) {
        Auth::loginUsingId($userId);
        $token = auth()->user()->createToken($tokenName, $abilities);
        return response()->json(['access_token' => $token->plainTextToken], 200);
    }

    /**
     * Method validates a register request
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validateRegister(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'alpha', 'min:2', 'max:250'],
            'email' => ['required', 'email:rfc', 'max:250', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'device_name' => ['nullable']
        ], [
            'name' => trans('auth.invalid_name'),
            'email' => trans('auth.invalid_email'),
            'password' => trans('auth.invalid_password'),
        ]);
    }

    /**
     * Method register a user and return an access_token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request) {
        $validator = $this->validateRegister($request);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        return $this->authenticateAndGetToken($user->id, 'access_token', []);
    }

    /**
     * Method validates a login request
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => ['required', 'string', 'exists:users', 'max:250'],
            'password' => ['required', 'string', 'min:6'],
            'device_name' => ['nullable'],
        ], [
            'email' => trans('auth.invalid_email'),
            'password' => trans('auth.invalid_password'),
        ]);
    }

    /**
     * Method tries to authenticate a user request with valid credentials and return access_token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function authenticate(Request $request) {
        $validator = $this->validateLogin($request);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('email', $request->input('email'))->first();
        if (!$user || !Hash::check($request->input('password'),$user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.invalid_credentials')],
            ]);
        }
        return $this->authenticateAndGetToken($user->id, 'access_token', []);
    }

    /**
     * Methods returns the authenticated user
     *
     * @param Request $request
     * @return User|Authenticatable|null
     */
    public function authenticatedUser(Request $request)
    {
        return auth()->user();
    }


    /**
     * Methods invalidates the access_token and logout the user
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request) {
        $user = auth()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json(['message' => trans('auth.token_revoked')], 200);
    }
}
