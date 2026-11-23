@extends('layouts.sis')
@section('title', $subject->name)

@section('content')
    <p class="back"><a href="{{ route('teacher.subjects') }}">Back to assigned subjects</a></p>
    <h1>{{ $subject->name }}</h1>
    <p class="intro">{{ $subject->code }} · Record whole-number grades from 0 to 100.</p>
    <div class="card-grid">
        @forelse ($enrollments as $enrollment)
            <article class="panel stack">
                <div>
                    <h2>{{ $enrollment->student->user->name }}</h2>
                    <p class="record-meta">{{ $enrollment->student->student_number }} · {{ $enrollment->school_year }} · Semester {{ $enrollment->semester }}</p>
                </div>
                @include('components.grade-form', ['action' => route('teacher.grades.update', $enrollment)])
            </article>
        @empty
            <p class="panel">No students have enrolled in this subject yet.</p>
        @endforelse
    </div>
    {{ $enrollments->links('components.pagination') }}
@endsection
