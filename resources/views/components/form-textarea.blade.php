@props([
    'name',
    'id' => null,
    'value' => null,
    'label' => null,
    'required' => false,
    'placeholder' => null,
    'disabled' => false,
    'readonly' => false,
    'rows' => 3,
    'class' => ''
])

@php
    $id = $id ?? $name;
    $value = $value ?? old($name);
@endphp

<div>
    @if($label)
        @if($required)
            <x-required-label for="{{ $id }}" value="{{ $label }}" />
        @else
            <x-label for="{{ $id }}" value="{{ $label }}" />
        @endif
    @endif
    
    <textarea 
        name="{{ $name }}" 
        id="{{ $id }}" 
        rows="{{ $rows }}"
        @if($required) required @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ' . $class]) }}
    >{{ $value }}</textarea>
    
    @error($name)
        <x-input-error :messages="$message" class="mt-1" />
    @enderror
</div>