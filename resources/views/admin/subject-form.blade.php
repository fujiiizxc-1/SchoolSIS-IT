@php($form = $subject ? 'subject-'.$subject->id : 'new-subject')
<form class="stack" method="post" action="{{ $subject ? route('admin.subjects.update', $subject) : route('admin.subjects.store') }}">
    @csrf
    @if ($subject)
        @method('PUT')
    @endif
    <input type="hidden" name="form_id" value="{{ $form }}">
    <x-form-field :form="$form" name="code" label="Subject code" :value="$subject?->code" hint="Example: IT101" maxlength="24" required />
    <x-form-field :form="$form" name="name" label="Subject name" :value="$subject?->name" maxlength="120" required />
    <x-form-field :form="$form" name="teacher_id" label="Assigned teacher (optional)" :value="$subject?->teacher_id" :options="$teacherOptions" />
    <button type="submit">{{ $subject ? 'Save subject' : 'Add subject' }}</button>
</form>
