@props(['name', 'label', 'type' => 'text', 'hint' => null, 'value' => '', 'form' => null, 'options' => null])

@php
    $id = $form ? $form.'-'.$name : $name;
    $submitted = $form === null || old('form_id') === $form;
    $error = $submitted ? $errors->first($name) : '';
    $description = trim(($hint ? $id.'-hint ' : '').($error ? $id.'-error' : ''));
    $current = $submitted ? old($name, $value) : $value;
    $current = is_scalar($current) ? (string) $current : '';
@endphp
<div class="field">
    <label for="{{ $id }}">{{ $label }}</label>
    @if ($options !== null)
        <select id="{{ $id }}" name="{{ $name }}"
            @if ($description) aria-describedby="{{ $description }}" @endif
            @if ($error) aria-invalid="true" @endif
            {{ $attributes }}>
            @foreach ($options as $key => $option)
                <option value="{{ $key }}" @selected($current === (string) $key)>{{ $option }}</option>
            @endforeach
        </select>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
            @unless ($type === 'password') value="{{ $current }}" @endunless
            @if ($description) aria-describedby="{{ $description }}" @endif
            @if ($error) aria-invalid="true" @endif
            {{ $attributes }}>
    @endif
    @if ($hint)
        <p class="hint" id="{{ $id }}-hint">{{ $hint }}</p>
    @endif
    @if ($error)
        <p class="field-error" id="{{ $id }}-error">{{ $error }}</p>
    @endif
</div>
