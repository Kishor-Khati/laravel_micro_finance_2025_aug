@props([
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'flex justify-end space-x-4 mt-6 ' . $class]) }}>
    {{ $slot }}
</div>