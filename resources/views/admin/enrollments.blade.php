@extends('layouts.sis')
@section('title', 'Manage enrollments')

@section('content')
    <h1>Manage enrollments</h1>
    <p class="intro">Review student enrollments and record whole-number grades from 0 to 100.</p>
    <div class="record-list">
        <table class="record-table">
            <caption class="visually-hidden">Student enrollments and final grades</caption>
            <thead><tr><th scope="col">Student</th><th scope="col">Subject</th><th scope="col">Term</th><th scope="col">Final grade</th><th scope="col">Actions</th></tr></thead>
            <tbody>
                @forelse ($enrollments as $enrollment)
                    <tr>
                        <th scope="row">
                            {{ $enrollment->student->user->name }}
                            <p class="secondary-text">{{ $enrollment->student->student_number }}</p>
                        </th>
                        <td data-label="Subject">
                            {{ $enrollment->subject->name }}
                            <p class="secondary-text">{{ $enrollment->subject->code }}</p>
                        </td>
                        <td data-label="Term">{{ $enrollment->school_year }}<br>Semester {{ $enrollment->semester }}</td>
                        <td class="grade-cell" data-label="Final grade">
                            @include('components.grade-form', ['action' => route('admin.grades.update', $enrollment)])
                        </td>
                        <td data-label="Actions">
                            @unless ($enrollment->grade)
                                <details class="disclosure">
                                    <summary>Remove</summary>
                                    <form class="stack" method="post" action="{{ route('admin.enrollments.destroy', $enrollment) }}">
                                        @csrf
                                        @method('DELETE')
                                        <p>Remove {{ $enrollment->student->user->name }} from {{ $enrollment->subject->name }} for this term?</p>
                                        <button type="submit" class="danger">Confirm removal</button>
                                    </form>
                                </details>
                            @else
                                <span class="muted">Grade saved</span>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-cell" colspan="5">There are no enrollments yet. They will appear here as students enroll in subjects.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $enrollments->links('components.pagination') }}
@endsection
