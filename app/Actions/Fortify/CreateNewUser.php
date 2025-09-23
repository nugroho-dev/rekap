<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password; // added

class CreateNewUser
{
    /**
     * Validate and create a new user.
     *
     * @param  array<string, string>  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)               // at least 8 chars
                    ->mixedCase()             // uppercase & lowercase
                    ->numbers()               // at least one number
                    ->symbols()               // at least one symbol
                    ->uncompromised(),        // not found in data leaks
            ],
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
