@props([
    'name',
    'id' => null,
    'value' => '1',
    'checked' => false,
    'label' => null,
    'required' => false,
    'disabled' => false,
    'class' => ''
])

@php
    $id = $id ?? $name;
    $isChecked = old($name, $checked);
@endphp

<div class="flex items-start">
    <div class="flex items-center h-5">
        <input 
            type="checkbox" 
            name="{{ $name }}" 
            id="{{ $id }}" 
            value="{{ $value }}" 
            @if($isChecked) checked @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 ' . $class]) }}
        >
    </div>
    
    @if($label)
    <div class="ml-3 text-sm">
        <label for="{{ $id }}" class="font-medium text-gray-700">{{ $label }}</label>
    </div>
    @endif
    
    @error($name)
        <div class="ml-3">
            <x-input-error :messages="$message" class="mt-1" />
        </div>
    @enderror
</div>