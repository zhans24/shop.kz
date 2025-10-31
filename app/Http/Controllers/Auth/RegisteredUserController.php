<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name'                  => ['required','string','max:255'],
                'email'                 => ['required','string','lowercase','email','max:255','unique:users,email'],
                'phone'                 => ['nullable','string','min:7','max:32'],
                // важно: явный min, чтобы сработало сообщение 'password.min'
                'password'              => ['required','confirmed', Password::min(8)],
                'password_confirmation' => ['required'], // чтобы было понятное required
                'agree'                 => ['required', 'accepted'],
            ],
            [
                // required
                'name.required'                  => 'Укажите имя.',
                'email.required'                 => 'Укажите e-mail.',
                'password.required'              => 'Укажите пароль.',
                'password_confirmation.required' => 'Подтвердите пароль.',
                'agree.required'                 => 'Подтвердите согласие с политикой и условиями.',
                'agree.accepted'                 => 'Подтвердите согласие с политикой и условиями.',

                // email
                'email.email'   => 'Введите корректный e-mail.',
                'email.unique'  => 'Такая почта уже зарегистрирована. Войдите или восстановите пароль.',

                // phone
                'phone.min'     => 'Телефон слишком короткий.',

                // password
                'password.confirmed' => 'Пароли не совпадают.',
                'password.min'       => 'Пароль должен содержать минимум :min символов.',
            ],
            [
                'name'  => 'имя',
                'email' => 'e-mail',
                'phone' => 'телефон',
                'password' => 'пароль',
            ]
        );
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);


        event(new Registered($user));
        Auth::login($user);

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route('dashboard'));
    }
}
