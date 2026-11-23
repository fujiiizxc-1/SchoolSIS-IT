@php($form = $student ? 'student-'.$student->id : 'new-student')
<form class="stack" method="post" action="{{ $student ? route('admin.students.update', $student) : route('admin.students.store') }}">
    @csrf
    @if ($student)
        @method('PUT')
    @endif
    <input type="hidden" name="form_id" value="{{ $form }}">
    <x-form-field :form="$form" name="name" label="Student name" :value="$student?->user->name" maxlength="100" required />
    <x-form-field :form="$form" name="student_number" label="Student ID" :value="$student?->student_number" maxlength="30" required />
    <x-form-field :form="$form" name="course" label="Course" :value="$student?->course" maxlength="120" required />
    <x-form-field :form="$form" name="email" label="Email address" type="email" :value="$student?->user->email" maxlength="254" required />
    @unless ($student)
        <x-form-field :form="$form" name="password" label="Password" type="password" autocomplete="new-password" hint="Use at least 8 characters." minlength="8" maxlength="72" required />
        <x-form-field :form="$form" name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" maxlength="72" required />
    @endunless
    <button type="submit">{{ $student ? 'Save student' : 'Register student' }}</button>
</form>
