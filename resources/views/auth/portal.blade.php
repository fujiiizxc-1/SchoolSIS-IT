@extends('layouts.sis')
@php($isRegister = request()->routeIs('register'))
@section('title', $isRegister ? 'Register student' : 'Log in')
@section('main-class', 'auth-main')

@section('content')
    <section
        class="auth-switcher @if ($isRegister) is-register @endif"
        data-auth-switcher
        data-login-url="{{ route('login') }}"
        data-register-url="{{ route('register') }}"
        data-title-login="Log in · Student Information System"
        data-title-register="Register student · Student Information System"
        aria-label="Student Information System account access"
    >
        <div class="auth-forms">
            <section
                class="auth-form login-form"
                data-auth-form="login"
                aria-labelledby="login-heading"
                @if ($isRegister) inert aria-hidden="true" @endif
            >
                <h1 id="login-heading" tabindex="-1">Welcome back</h1>
                <p class="auth-description">Sign in to see your profile, subjects, and grades.</p>
                <form class="stack" method="post" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="form_id" value="login">
                    <x-form-field form="login" name="email" label="Email address" type="email" autocomplete="username" maxlength="254" required />
                    <x-form-field form="login" name="password" label="Password" type="password" autocomplete="current-password" maxlength="72" required />
                    <button class="auth-submit" type="submit">Log in</button>
                </form>
                <p class="auth-mobile-toggle">New student? <a href="{{ route('register') }}" data-auth-toggle="register">Create an account</a></p>
            </section>

            <section
                class="auth-form register-form"
                data-auth-form="register"
                aria-labelledby="register-heading"
                @unless ($isRegister) inert aria-hidden="true" @endunless
            >
                <h1 id="register-heading" tabindex="-1">Create student account</h1>
                <p class="auth-description">Register to enroll in subjects and view your grades. All fields are required.</p>
                <form class="stack" method="post" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="form_id" value="register">
                    <x-form-field form="register" name="name" label="Student name" autocomplete="name" maxlength="100" required />
                    <div class="form-grid">
                        <x-form-field form="register" name="student_number" label="Student ID" hint="Example: 2026-00123" maxlength="30" required />
                        <x-form-field form="register" name="course" label="Course" hint="Example: BS Information Technology" maxlength="120" required />
                    </div>
                    <x-form-field form="register" name="email" label="Email address" type="email" autocomplete="email" maxlength="254" required />
                    <div class="form-grid">
                        <x-form-field form="register" name="password" label="Password" type="password" autocomplete="new-password" hint="Use at least 8 characters." minlength="8" maxlength="72" required />
                        <x-form-field form="register" name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" maxlength="72" required />
                    </div>
                    <button class="auth-submit" type="submit">Create account</button>
                </form>
                <p class="auth-mobile-toggle">Already registered? <a href="{{ route('login') }}" data-auth-toggle="login">Log in</a></p>
            </section>
        </div>

        <div class="auth-overlay" aria-label="Switch account form">
            <section
                class="auth-prompt auth-prompt-login"
                data-auth-prompt="login"
                aria-labelledby="new-student-heading"
                @if ($isRegister) inert aria-hidden="true" @endif
            >
                <h2 id="new-student-heading">New student?</h2>
                <p>Create an account to enroll in subjects and check your grades.</p>
                <a class="auth-prompt-button" href="{{ route('register') }}" data-auth-toggle="register">Register student</a>
            </section>
            <section
                class="auth-prompt auth-prompt-register"
                data-auth-prompt="register"
                aria-labelledby="returning-student-heading"
                @unless ($isRegister) inert aria-hidden="true" @endunless
            >
                <h2 id="returning-student-heading">Welcome back</h2>
                <p>Already have an account? Sign in to continue.</p>
                <a class="auth-prompt-button" href="{{ route('login') }}" data-auth-toggle="login">Log in</a>
            </section>
        </div>
    </section>

    <script>
        (() => {
            const switcher = document.querySelector('[data-auth-switcher]');
            if (!switcher) return;

            const forms = [...switcher.querySelectorAll('[data-auth-form]')];
            const prompts = [...switcher.querySelectorAll('[data-auth-prompt]')];
            const links = document.querySelectorAll('[data-auth-toggle]');
            const loginUrl = switcher.dataset.loginUrl;
            const registerUrl = switcher.dataset.registerUrl;

            function setMode(mode, updateHistory = false, moveFocus = false) {
                const isRegister = mode === 'register';
                switcher.classList.toggle('is-register', isRegister);

                forms.forEach((form) => {
                    const active = form.dataset.authForm === mode;
                    form.inert = !active;
                    form.setAttribute('aria-hidden', String(!active));
                });

                prompts.forEach((prompt) => {
                    const active = prompt.dataset.authPrompt === mode;
                    prompt.inert = !active;
                    prompt.setAttribute('aria-hidden', String(!active));
                });

                links.forEach((link) => {
                    if (link.dataset.authToggle === mode) {
                        link.setAttribute('aria-current', 'page');
                    } else {
                        link.removeAttribute('aria-current');
                    }
                });

                document.title = switcher.dataset[isRegister ? 'titleRegister' : 'titleLogin'];
                if (updateHistory) {
                    history.pushState({ authMode: mode }, '', isRegister ? registerUrl : loginUrl);
                }
                if (moveFocus) {
                    switcher.querySelector('[data-auth-form="' + mode + '"] h1').focus({ preventScroll: true });
                }
            }

            document.addEventListener('click', (event) => {
                const link = event.target.closest('[data-auth-toggle]');
                if (!link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
                if (event.button !== 0 && event.button !== undefined) return;

                event.preventDefault();
                const mode = link.dataset.authToggle;
                if (switcher.classList.contains('is-register') === (mode === 'register')) return;
                setMode(mode, true, true);
            });

            window.addEventListener('popstate', () => {
                const path = window.location.pathname;
                setMode(path === new URL(registerUrl, window.location.href).pathname ? 'register' : 'login');
            });
        })();
    </script>
@endsection
