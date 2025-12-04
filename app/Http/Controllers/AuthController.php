<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AuthController extends Controller
{
    public function authIndex()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Email not found.'
                ], 422);
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'message' => 'Password is incorrect.'
                ], 422);
            }

            if ($user->role !== UserRole::Patient) {
                return response()->json([
                    'message' => 'This login is only for patients.'
                ], 403);
            }

            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Welcome back!',
                'redirect' => route('home')
            ]);
        } catch (Exception $e) {
            Log::error('Login Failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. please try again later.',
            ], 500);
        }
    }


    public function regisIndex()
    {
        return view('auth.register');
    }

    public function regisPatient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|min:11|max:11|unique:users,contact_number',
            'address' => 'required|string|max:255',
            'password' => 'required|min:6|confirmed',
        ]);

        // Check if patient already exists by name + phone
        $exists = User::where('name', Str::title($validated['name']))
            ->where('contact_number', $validated['phone'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A patient with the same name and phone already exists.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 🔹 Generate unique patient ID and number
            $patientId = 'PID-' . strtoupper(Str::random(6)); // Example: PID-8AF3QZ
            $patientNumber = 'PN-' . str_pad(User::count() + 1, 5, '0', STR_PAD_LEFT); // Example: PN-00023

            User::create([
                'patient_id' => $patientId,
                'patient_number' => $patientNumber,
                'name' => Str::title($validated['name']),
                'email' => $validated['email'],
                'role' => UserRole::Patient,
                'contact_number' => $validated['phone'],
                'address' => Str::title($validated['address']),
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Account created successfully!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Patient registration failed: " . $e->getMessage());

            return response()->json([
                'message' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function personnelPanel()
    {
        return view('auth.personnel-panel');
    }

    public function personnelAuth(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Email not found.'
                ], 422);
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'message' => 'Password incorrect.'
                ], 422);
            }

            if ($user->role === UserRole::Patient) {
                return response()->json([
                    'message' => 'This login is for authorized personnel only.'
                ], 403);
            }

            Auth::login($user);
            $request->session()->regenerate();

            $redirectTo = match ($user->role) {
                UserRole::Admin => route('admin.manage.account'),
                UserRole::Doctor => route('doctor.appointment'),
                UserRole::Staff => route('manage.appointment'),
                default => route('personnel.panel'),
            };

            return response()->json([
                'message' => 'Welcome back!',
                'redirect' => $redirectTo
            ]);
        } catch (Exception $e) {
            Log::error('Login Failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
    public function forgotPasswordPage()
    {
        return view('auth.forgot-password');
    }

    public function resetPasswordPage($token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'No account found with that email.',
            ], 404);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        $resetUrl = url("/reset-password/{$token}?email=" . urlencode($request->email));

        Mail::to($request->email)->send(new ResetPasswordMail($resetUrl));

        return response()->json([
            'message' => 'Password reset link has been sent to your email.'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (! $record) {
            return response()->json(['message' => 'Invalid request.'], 400);
        }

        if (! Hash::check($request->token, $record->token)) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password successfully updated.']);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::flush();

        return redirect()->route('login');
    }

    public function personnelLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::flush();

        return redirect()->route('personnel.panel');
    }
}
