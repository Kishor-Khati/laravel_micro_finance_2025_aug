@props([
    'name',
    'id' => null,
    'label' => null,
    'required' => false,
    'disabled' => false,
    'class' => ''
])

@php
    $id = $id ?? $name;
@endphp

<div>
    @if($label)
        @if($required)
            <x-required-label for="{{ $id }}" value="{{ $label }}" />
        @else
            <x-label for="{{ $id }}" value="{{ $label }}" />
        @endif
    @endif
    
    <select 
        name="{{ $name }}" 
        id="{{ $id }}" 
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ' . $class]) }}
    >
        {{ $slot }}
    </select>
    
    @error($name)
        <x-input-error :messages="$message" class="mt-1" />
    @enderror
</div>