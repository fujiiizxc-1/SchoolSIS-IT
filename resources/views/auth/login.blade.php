@extends('layouts.sis')
@section('title', 'Log in')
@section('main-class', 'narrow')

@section('content')
    <section class="panel">
        <h1>Log in</h1>
        <p class="intro">Enter the email address and password for your account.</p>
        <form class="stack" method="post" action="{{ route('login') }}">
            @csrf
            <x-form-field name="email" label="Email address" type="email" autocomplete="username" maxlength="254" required />
            <x-form-field name="password" label="Password" type="password" autocomplete="current-password" maxlength="72" required />
            <button type="submit">Log in</button>
        </form>
        <p class="form-footer">New student? <a href="{{ route('register') }}">Register a student account</a></p>
    </section>
@endsection
