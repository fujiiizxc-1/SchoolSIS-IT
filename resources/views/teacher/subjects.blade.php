@extends('layouts.sis')
@section('title', 'Assigned subjects')

@section('content')
    <h1>Assigned subjects</h1>
    <p class="intro">Open a subject to see its students and record grades.</p>
    <div class="card-grid">
        @forelse ($subjects as $subject)
            <article class="panel stack">
                <div>
                    <p class="muted">{{ $subject->code }}</p>
                    <h2>{{ $subject->name }}</h2>
                </div>
                <div><a class="button secondary" href="{{ route('teacher.subjects.show', $subject) }}">View students<span class="visually-hidden"> in {{ $subject->name }}</span></a></div>
            </article>
        @empty
            <p class="panel">You have no assigned subjects. Contact the school administrator if you expect to teach a subject.</p>
        @endforelse
    </div>
    {{ $subjects->links('components.pagination') }}
@endsection
