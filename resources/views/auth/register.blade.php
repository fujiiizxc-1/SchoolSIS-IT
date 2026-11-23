@extends('layouts.sis')
@section('title', 'Register student')
@section('main-class', 'narrow')

@section('content')
    <section class="panel">
        <h1>Register student</h1>
        <p class="intro">Create an account to enroll in subjects and view your grades. All fields are required.</p>
        <form class="stack" method="post" action="{{ route('register') }}">
            @csrf
            <x-form-field name="name" label="Student name" autocomplete="name" maxlength="100" required />
            <div class="form-grid">
                <x-form-field name="student_number" label="Student ID" hint="Example: 2026-00123" maxlength="30" required />
                <x-form-field name="course" label="Course" hint="Example: BS Information Technology" maxlength="120" required />
            </div>
            <x-form-field name="email" label="Email address" type="email" autocomplete="email" maxlength="254" required />
            <div class="form-grid">
                <x-form-field name="password" label="Password" type="password" autocomplete="new-password" hint="Use at least 8 characters." minlength="8" maxlength="72" required />
                <x-form-field name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" maxlength="72" required />
            </div>
            <button type="submit">Register student</button>
        </form>
        <p class="form-footer">Already registered? <a href="{{ route('login') }}">Log in</a></p>
    </section>
@endsection
