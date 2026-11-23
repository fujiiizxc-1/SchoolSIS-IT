@extends('layouts.sis')
@section('title', 'Available subjects')

@section('content')
    <h1>Available subjects</h1>
    <p class="intro">School year {{ $schoolYear }} · Semester {{ $semester }}</p>
    <div class="card-grid">
        @forelse ($subjects as $subject)
            <article class="panel stack">
                <div>
                    <p class="muted">{{ $subject->code }}</p>
                    <h2>{{ $subject->name }}</h2>
                </div>
                <p>{{ $subject->teacher?->user->name ?? 'Teacher to be assigned' }}</p>
                <form method="post" action="{{ route('enrollments.store') }}">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    <button type="submit" aria-label="Enroll in {{ $subject->name }}">Enroll</button>
                </form>
            </article>
        @empty
            <p class="panel">There are no subjects available to enroll in this term. <a href="{{ route('enrollments') }}">View my subjects</a></p>
        @endforelse
    </div>
    {{ $subjects->links('components.pagination') }}
@endsection
