<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Показ формы логина.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Обработка авторизации.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(
            [
                'email'    => ['required','string','email'],
                'password' => ['required','string'],
            ],
            [
                'email.required'    => 'Укажите e-mail.',
                'email.email'       => 'Введите корректный e-mail.',
                'password.required' => 'Укажите пароль.',
            ]
        );

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // auth.failed -> свой текст без lang-файлов
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Неверный e-mail или пароль.']);
        }

        $request->session()->regenerate();

        $user = $request->user();
        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')
                ->with('status', 'Мы отправили письмо подтверждения на '.$user->email);
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Выход из аккаунта.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('front.home');
    }
}
