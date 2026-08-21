{{--
    One labelled form control with its validation message.

    Renders the right input for the field types declared in
    config/healthtrack.php, so the shared clinical-record form does not need
    a branch for every type.

        <x-field name="first_name" label="First name" wire="form.first_name" />
        <x-field name="severity" label="Severity" type="select"
                 :options="['mild' => 'Mild']" wire="form.severity" />
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'options' => [],
    'wire' => null,
    'placeholder' => null,
    'required' => false,
])

@php
    // Livewire binding target; falls back to the field name.
    $model = $wire ?? $name;
@endphp

<label class="ht-field">
    <span>
        {{ $label }}
        @if ($required)<span style="color: var(--color-danger);">*</span>@endif
    </span>

    @if ($type === 'textarea')
        <textarea
            wire:model="{{ $model }}"
            rows="3"
            placeholder="{{ $placeholder }}"
            class="ht-input"
        ></textarea>

    @elseif ($type === 'select')
        <select wire:model="{{ $model }}" class="ht-input">
            <option value="">{{ $placeholder ?: '-- Select --' }}</option>
            @foreach ($options as $value => $text)
                <option value="{{ $value }}">{{ $text }}</option>
            @endforeach
        </select>

    @else
        <input
            type="{{ $type }}"
            wire:model="{{ $model }}"
            placeholder="{{ $placeholder }}"
            class="ht-input"
        >
    @endif

    @error($model)
        <span class="ht-error">{{ $message }}</span>
    @enderror
</label>
