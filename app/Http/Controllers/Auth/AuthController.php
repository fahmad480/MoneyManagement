<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\VerifyEmailMail;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Check if email verification is required and user hasn't verified
            if (config('auth.email_verification_required') && is_null(Auth::user()->email_verified_at)) {
                return response()->json([
                    'message' => 'Email belum diverifikasi',
                    'redirect' => route('verification.notice'),
                    'requiresVerification' => true
                ]);
            }
            
            return response()->json([
                'message' => 'Login berhasil',
                'redirect' => route('dashboard')
            ]);
        }

        return response()->json([
            'message' => 'Email atau password salah'
        ], 401);
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'email_verified_at' => config('auth.email_verification_required') ? null : now(),
        ]);

        // Assign default role
        $user->assignRole('member');

        // Send verification email if required
        if (config('auth.email_verification_required')) {
            event(new Registered($user));
            $this->sendVerificationEmail($user);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Check if email verification is required
        if (config('auth.email_verification_required')) {
            return response()->json([
                'message' => 'Registrasi berhasil. Silakan verifikasi email Anda.',
                'redirect' => route('verification.notice')
            ]);
        }

        return response()->json([
            'message' => 'Registrasi berhasil',
            'redirect' => route('dashboard')
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Password Reset Methods
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset password telah dikirim ke email Anda'
            ]);
        }

        return response()->json([
            'message' => 'Gagal mengirim link reset password'
        ], 500);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password berhasil direset. Silakan login dengan password baru Anda.'
            ]);
        }

        return response()->json([
            'message' => 'Token reset password tidak valid atau sudah kadaluarsa'
        ], 400);
    }

    // Email Verification Methods
    public function showVerificationNotice()
    {
        if (Auth::check() && !is_null(Auth::user()->email_verified_at)) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan');
        }

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Link verifikasi tidak valid');
        }

        if (!is_null($user->email_verified_at)) {
            return redirect()->route('dashboard')->with('success', 'Email sudah terverifikasi');
        }

        $user->markEmailAsVerified();

        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
    }

    public function resendVerificationEmail(Request $request)
    {
        if (!is_null(Auth::user()->email_verified_at)) {
            return response()->json([
                'message' => 'Email sudah terverifikasi'
            ]);
        }

        $this->sendVerificationEmail(Auth::user());

        return response()->json([
            'message' => 'Link verifikasi telah dikirim ulang ke email Anda'
        ]);
    }

    protected function sendVerificationEmail(User $user)
    {
        $verificationUrl = $this->generateVerificationUrl($user);
        
        try {
            Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl));
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }

    protected function generateVerificationUrl(User $user)
    {
        return route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);
    }
}
