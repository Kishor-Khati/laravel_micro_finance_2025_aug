@props([
    'name',
    'label' => null,
    'required' => false,
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'space-y-2 ' . $class]) }}>
    @if($label)
        @if($required)
            <x-required-label value="{{ $label }}" />
        @else
            <x-label value="{{ $label }}" />
        @endif
    @endif
    
    <div class="space-y-2">
        {{ $slot }}
    </div>
    
    @error($name)
        <x-input-error :messages="$message" class="mt-1" />
    @enderror
</div>