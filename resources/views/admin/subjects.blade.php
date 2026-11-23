@extends('layouts.sis')
@section('title', 'Manage subjects')

@section('content')
    @php($teacherOptions = ['' => 'Unassigned'] + $teachers->mapWithKeys(fn ($teacher) => [$teacher->id => $teacher->user->name])->all())
    <h1>Manage subjects</h1>
    <p class="intro">Add subjects, update their details, and assign teachers.</p>
    <details class="panel disclosure" @if (old('form_id') === 'new-subject') open @endif>
        <summary>Add a subject</summary>
        @include('admin.subject-form', ['subject' => null])
    </details>
    <div class="record-list">
        <table class="record-table">
            <caption class="visually-hidden">Subjects</caption>
            <thead><tr><th scope="col">Subject</th><th scope="col">Assigned teacher</th><th scope="col">Manage</th></tr></thead>
            <tbody>
                @forelse ($subjects as $subject)
                    <tr>
                        <th scope="row">
                            {{ $subject->name }}
                            <p class="secondary-text">{{ $subject->code }}</p>
                        </th>
                        <td data-label="Assigned teacher">{{ $subject->teacher?->user->name ?? 'Teacher to be assigned' }}</td>
                        <td data-label="Manage">
                            <details class="disclosure" @if (old('form_id') === 'subject-'.$subject->id) open @endif>
                                <summary>Edit</summary>
                                @include('admin.subject-form', ['subject' => $subject])
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-cell" colspan="3">There are no subjects. Add one to make it available for enrollment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $subjects->links('components.pagination') }}
@endsection
