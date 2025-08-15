@props([
    'type' => 'submit',
    'color' => 'blue',
    'class' => ''
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'red' => 'bg-red-600 hover:bg-red-700 text-white',
        'green' => 'bg-green-600 hover:bg-green-700 text-white',
        'gray' => 'bg-gray-300 hover:bg-gray-400 text-gray-800',
    ][$color] ?? 'bg-blue-600 hover:bg-blue-700 text-white';
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => $colorClasses . ' px-4 py-2 rounded-lg ' . $class]) }}
>
    {{ $slot }}
</button>