<x-mail::message>
{{-- Greeting --}}
# @lang('Hello!')

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach


<x-mail::button >
adawd
</x-mail::button>

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),<br>
{{ config('app.name') }}
@endif

</x-mail::message>
