@extends('layouts.sis')
@section('title', 'My profile')
@section('main-class', 'narrow')

@section('content')
    <section class="panel">
        <h1>My profile</h1>
        <dl class="details">
            <div><dt>Student ID</dt><dd>{{ $student->student_number }}</dd></div>
            <div><dt>Course</dt><dd>{{ $student->course }}</dd></div>
            <div><dt>Email address</dt><dd>{{ $student->user->email }}</dd></div>
        </dl>
        <form class="stack profile-form" method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <x-form-field name="name" label="Student name" :value="$student->user->name" autocomplete="name" maxlength="100" required />
            <button type="submit">Save profile</button>
        </form>
    </section>
@endsection
