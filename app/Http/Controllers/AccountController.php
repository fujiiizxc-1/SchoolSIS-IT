<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function register(Request $request): RedirectResponse
    {
        return $this->createStudent($request, 'login', 'Registration complete. Sign in to view your student profile.');
    }

    public function registerForAdmin(Request $request): RedirectResponse
    {
        return $this->createStudent($request, 'admin.students', 'Student account created.');
    }

    private function createStudent(Request $request, string $route, string $message): RedirectResponse
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => $request->string('email')->lower()->toString()]);
        }
        if (is_string($request->input('student_number'))) {
            $request->merge(['student_number' => $request->string('student_number')->upper()->toString()]);
        }

        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:100'],
                'student_number' => ['required', 'string', 'max:30', 'unique:students'],
                'course' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:254', 'unique:users'],
                'password' => ['bail', 'required', 'string', 'min:8', 'confirmed', self::validatePassword(...)],
            ], [
                'student_number.unique' => 'This student ID is already registered.',
                'email.unique' => 'This email address is already registered.',
            ], ['student_number' => 'student ID']);

            DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                ]);

                $user->student()->create([
                    'student_number' => $data['student_number'],
                    'course' => $data['course'],
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            $field = User::where('email', $data['email'])->exists() ? 'email' : 'student_number';

            throw ValidationException::withMessages([
                $field => $field === 'email' ? 'This email address is already registered.' : 'This student ID is already registered.',
            ]);
        } catch (QueryException $exception) {
            Log::warning('Registration encountered a database error.', ['sqlstate' => $exception->getCode()]);

            return back()->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['registration' => 'We could not save your registration. Please try again later.']);
        }

        return to_route($route)->with('status', $message);
    }

    public function login(Request $request): RedirectResponse
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => $request->string('email')->lower()->toString()]);
        }
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'password' => ['bail', 'required', 'string', self::validatePassword(...)],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'The email or password is incorrect.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }

    public static function validatePassword(string $attribute, string $value, \Closure $fail): void
    {
        if (strlen($value) > 72 || str_contains($value, "\0")) {
            $fail('Use a shorter password without null characters.');
        }
    }
}
