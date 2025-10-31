<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmailBase
{
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Подтверждение электронной почты')
            ->greeting('Здравствуйте!')
            ->line('Пожалуйста, подтвердите свой адрес электронной почты.')
            ->action('Подтвердить', $this->verificationUrl($notifiable))
            ->line('Если вы не регистрировались на сайте, просто проигнорируйте это сообщение.');
    }
}
