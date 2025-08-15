@props([
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'space-y-6 ' . $class]) }}>
    {{ $slot }}
</div>