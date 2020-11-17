<?php

namespace App\Http\Controllers\API\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\ApiController;
use Illuminate\Validation\ValidationException;

class SanctumController extends ApiController {

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login( Request $request )
    {
        $request->validate( [
            'email'       => 'required|string|email',
            'password'    => 'required|string|min:6',
            'device_name' => 'required|string',
        ] );
        $user = User::where( 'email', $request->email )->first();
        if( !$user || !Hash::check( $request->password, $user->password ) ) {
            throw ValidationException::withMessages( [
                'email' => ['The provided credentials are incorrect.'],
            ] );
        }
        $token = $user->createToken( $request->device_name )->plainTextToken;

        return $this->successResponse( [
            'user'  => UserResource::make( $user ),
            'token' => $token,
        ] );
    }

    /**
     * Validate and update the user's password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword( Request $request )
    {
        $input = $request->all();
        $user  = $request->user();
        Validator::make( $input, [
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ] )->after( function ( $validator ) use ( $user, $input ) {
            if( !Hash::check( $input['current_password'], $user->password ) ) {
                $validator->errors()->add( 'current_password', 'The provided password does not match your current password.' );
            }
        } );
        $user->forceFill( [
            'password' => Hash::make( $input['password'] ),
        ] )->save();

        return $this->successResponse( [], Response::HTTP_NO_CONTENT );
    }

    /**
     * Store a new user in db
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register( Request $request )
    {
        $attributes = $request->validate( [
            "name"     => ['required', 'string', 'max:255'],
            "email"    => ['required', 'string', 'max:255', 'email', 'unique:users,email'],
            "password" => ['required', 'string', 'min:6', 'confirmed'],
        ] );
        $newUser    = User::create( [
            "name"     => $attributes['name'],
            "email"    => $attributes['email'],
            'password' => Hash::make( $attributes['password'] ),
        ] );

        return $this->successResponse( UserResource::make( $newUser ), Response::HTTP_CREATED );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout( Request $request )
    {
        $request->user()->tokens()->delete();

        return $this->successResponse( '', Response::HTTP_NO_CONTENT );
    }
}
