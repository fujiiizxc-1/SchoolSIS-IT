@php($form = $teacher ? 'teacher-'.$teacher->id : 'new-teacher')
<form class="stack" method="post" action="{{ $teacher ? route('admin.teachers.update', $teacher) : route('admin.teachers.store') }}">
    @csrf
    @if ($teacher)
        @method('PUT')
    @endif
    <input type="hidden" name="form_id" value="{{ $form }}">
    <x-form-field :form="$form" name="name" label="Teacher name" :value="$teacher?->user->name" maxlength="100" required />
    <x-form-field :form="$form" name="email" label="Email address" type="email" :value="$teacher?->user->email" maxlength="254" required />
    @unless ($teacher)
        <x-form-field :form="$form" name="password" label="Password" type="password" autocomplete="new-password" hint="Use at least 8 characters." minlength="8" maxlength="72" required />
        <x-form-field :form="$form" name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" maxlength="72" required />
    @endunless
    <button type="submit">{{ $teacher ? 'Save teacher' : 'Add teacher' }}</button>
</form>
