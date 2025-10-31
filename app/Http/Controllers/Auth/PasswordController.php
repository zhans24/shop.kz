<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validateWithBag(
            'updatePassword',
            [
                'current_password'       => ['required', 'current_password'],
                'password'               => ['required', Password::min(8), 'confirmed'],
                'password_confirmation'  => ['required'],
            ],
            [
                // current_password
                'current_password.required'         => 'Введите текущий пароль.',
                'current_password.current_password' => 'Текущий пароль указан неверно.',

                // password
                'password.required'     => 'Укажите новый пароль.',
                'password.confirmed'    => 'Подтверждение пароля не совпадает.',
                'password.min'          => 'Пароль должен содержать минимум :min символов.',
                // подстрахуем общий валидатор "min.string", если вдруг сработает он
                'min.string'            => 'Длина поля :attribute должна быть не меньше :min символов.',

                // confirmation
                'password_confirmation.required' => 'Подтвердите пароль.',
            ],
            [
                'current_password'      => 'текущий пароль',
                'password'              => 'пароль',
                'password_confirmation' => 'подтверждение пароля',
            ]
        );

        $request->user()->update([
            'password' => Hash::make($request->string('password')),
        ]);

        return back()->with('status', 'Пароль успешно обновлён.');
    }
}
