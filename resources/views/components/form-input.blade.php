@props([
    'type' => 'text',
    'name',
    'id' => null,
    'value' => null,
    'label' => null,
    'required' => false,
    'placeholder' => null,
    'autocomplete' => null,
    'disabled' => false,
    'readonly' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'pattern' => null,
    'autofocus' => false,
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
    
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $id }}" 
        value="{{ $value }}" 
        @if($required) required @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($min) min="{{ $min }}" @endif
        @if($max) max="{{ $max }}" @endif
        @if($step) step="{{ $step }}" @endif
        @if($pattern) pattern="{{ $pattern }}" @endif
        @if($autofocus) autofocus @endif
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ' . $class]) }}
    >
    
    @error($name)
        <x-input-error :messages="$message" class="mt-1" />
    @enderror
</div>