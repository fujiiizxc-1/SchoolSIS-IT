@extends('layouts.sis')
@section('title', 'Manage teachers')

@section('content')
    <h1>Manage teachers</h1>
    <p class="intro">Create teacher accounts or update their details. Assign teachers on the Manage subjects page.</p>
    <details class="panel disclosure" @if (old('form_id') === 'new-teacher') open @endif>
        <summary>Add a teacher</summary>
        @include('admin.teacher-form', ['teacher' => null])
    </details>
    <div class="record-list">
        <table class="record-table">
            <caption class="visually-hidden">Teacher accounts</caption>
            <thead><tr><th scope="col">Teacher</th><th scope="col">Manage</th></tr></thead>
            <tbody>
                @forelse ($teachers as $teacher)
                    <tr>
                        <th scope="row">
                            {{ $teacher->user->name }}
                            <p class="secondary-text">{{ $teacher->user->email }}</p>
                        </th>
                        <td data-label="Manage">
                            <details class="disclosure" @if (old('form_id') === 'teacher-'.$teacher->id) open @endif>
                                <summary>Edit</summary>
                                @include('admin.teacher-form', ['teacher' => $teacher])
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty-cell" colspan="2">There are no teacher accounts. Add a teacher to assign them to subjects.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $teachers->links('components.pagination') }}
@endsection
