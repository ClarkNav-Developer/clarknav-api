<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class VerifyEmailCustom extends VerifyEmailBase
{
    protected function verificationUrl($notifiable)
    {
        $frontendUrl = config('app.frontend_url') . '/verify-email';

        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );

        return str_replace(url('/api'), $frontendUrl, $temporarySignedUrl);
    }
}