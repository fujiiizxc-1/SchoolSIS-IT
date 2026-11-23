@php($form = 'grade-'.$enrollment->id)
<form class="inline-form" method="post" action="{{ $action }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="form_id" value="{{ $form }}">
    <x-form-field :form="$form" name="grade" label="Final grade" type="number" min="0" max="100" step="1"
        :value="$enrollment->grade?->grade" placeholder="Pending" required />
    <button type="submit" aria-label="Save grade for {{ $enrollment->student->user->name }}">Save grade</button>
</form>
