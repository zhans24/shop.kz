<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPasswordBase
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Сброс пароля')
            ->greeting('Здравствуйте!')
            ->line('Вы получили это письмо, потому что запросили сброс пароля.')
            ->action('Сбросить пароль', $url)
            ->line('Ссылка действительна в течение 60 минут.')
            ->line('Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.');
    }
}
