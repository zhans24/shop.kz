<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Http\RedirectResponse;

class NewPasswordController extends Controller
{
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'token'    => ['required'],
                'email'    => ['required', 'email'],
                'password' => ['required', 'confirmed', PasswordRule::defaults()],
            ],
            [
                'email.required'      => 'Укажите e-mail.',
                'email.email'         => 'Введите корректный e-mail.',
                'password.required'   => 'Укажите пароль.',
                'password.confirmed'  => 'Пароли не совпадают.',
                'min.string'          => 'Длина поля :attribute должна быть не меньше :min символов.',
                'password.min'        => 'Пароль должен содержать минимум :min символов.',
            ],
            [
                'email'    => 'e-mail',
                'password' => 'пароль',
            ]
        );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                ])->save();
            }
        );

        $map = [
            Password::PASSWORD_RESET  => 'Пароль успешно обновлён.',
            Password::INVALID_TOKEN   => 'Токен сброса пароля недействителен.',
            Password::INVALID_USER    => 'Пользователь с таким e-mail не найден.',
            Password::RESET_THROTTLED => 'Пожалуйста, подождите перед повторной попыткой.',
        ];

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', $map[$status]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $map[$status] ?? 'Не удалось обновить пароль.']);
    }
}
