@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'form-control is-invalid']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
