<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            ['email' => ['required', 'email']],
            [
                'email.required' => 'Укажите e-mail.',
                'email.email'    => 'Введите корректный e-mail.',
            ],
            ['email' => 'e-mail']
        );

        $status = Password::sendResetLink($request->only('email'));

        $map = [
            Password::RESET_LINK_SENT => 'Мы отправили ссылку для сброса пароля на ваш e-mail.',
            Password::RESET_THROTTLED => 'Пожалуйста, подождите перед повторной попыткой.',
            Password::INVALID_USER    => 'Пользователь с таким e-mail не найден.',
        ];

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', $map[$status] ?? 'Ссылка отправлена.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $map[$status] ?? 'Не удалось отправить ссылку для сброса пароля.']);
    }
}
