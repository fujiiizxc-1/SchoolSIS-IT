@extends('layouts.sis')
@section('title', 'Manage students')

@section('content')
    <h1>Manage students</h1>
    <p class="intro">Register a student or update an existing student record.</p>
    <details class="panel disclosure" @if (old('form_id') === 'new-student' || request()->boolean('new')) open @endif>
        <summary>Register a student</summary>
        @include('admin.student-form', ['student' => null])
    </details>
    <div class="record-list">
        <table class="record-table">
            <caption class="visually-hidden">Student records</caption>
            <thead><tr><th scope="col">Student</th><th scope="col">Student ID</th><th scope="col">Course</th><th scope="col">Manage</th></tr></thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <th scope="row">
                            {{ $student->user->name }}
                            <p class="secondary-text">{{ $student->user->email }}</p>
                        </th>
                        <td data-label="Student ID">{{ $student->student_number }}</td>
                        <td data-label="Course">{{ $student->course }}</td>
                        <td data-label="Manage">
                            <details class="disclosure" @if (old('form_id') === 'student-'.$student->id) open @endif>
                                <summary>Edit</summary>
                                @include('admin.student-form', ['student' => $student])
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-cell" colspan="4">There are no student records. Register a student to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $students->links('components.pagination') }}
@endsection
