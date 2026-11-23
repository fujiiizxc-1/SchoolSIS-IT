@extends('layouts.sis')
@section('title', 'My grades')

@section('content')
    <h1>My grades</h1>
    <p class="intro">Grades are shown on a 0–100 scale.</p>
    <div class="card-grid">
        @forelse ($enrollments as $enrollment)
            <article class="panel">
                <p class="muted">{{ $enrollment->subject->code }}</p>
                <h2>{{ $enrollment->subject->name }}</h2>
                <p class="record-meta">{{ $enrollment->school_year }} · Semester {{ $enrollment->semester }}</p>
                <p class="record-meta"><strong>Final grade</strong> · {{ $enrollment->grade?->grade ?? 'Pending' }}</p>
            </article>
        @empty
            <p class="panel">Grades appear here after you <a href="{{ route('subjects') }}">enroll in a subject</a> and your teacher records a grade.</p>
        @endforelse
    </div>
    {{ $enrollments->links('components.pagination') }}
@endsection
