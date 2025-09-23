<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\ResetPasswordViewResponse as ResetPasswordViewResponseContract;

class ResetPasswordViewResponse implements ResetPasswordViewResponseContract
{
    /**
     * Create an HTTP response that renders the reset password view.
     */
    public function toResponse($request)
    {
        return response()->view('auth.reset-password');
    }
}