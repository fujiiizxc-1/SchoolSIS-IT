@extends('layouts.sis')
@section('title', 'My subjects')

@section('content')
    <h1>My subjects</h1>
    <p class="intro">Review the subjects you are enrolled in.</p>
    <div class="card-grid">
        @forelse ($enrollments as $enrollment)
            <article class="panel">
                <p class="muted">{{ $enrollment->subject->code }}</p>
                <h2>{{ $enrollment->subject->name }}</h2>
                <p class="record-meta">{{ $enrollment->school_year }} · Semester {{ $enrollment->semester }}</p>
            </article>
        @empty
            <p class="panel">You have no enrollments yet. <a href="{{ route('subjects') }}">Browse available subjects</a> to get started.</p>
        @endforelse
    </div>
    {{ $enrollments->links('components.pagination') }}
@endsection
