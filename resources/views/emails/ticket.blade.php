<x-mail::message>

Your order has been verified. Please show this ticket at the venue

{{-- @if ($order->event->images &&  $order->event->images[0] != null)
    <img src="https://www.sentireascoltare.com/wp-content/uploads/2012/12/David-Bowie-Low-300x300.jpg" />
@endif --}}

@component('mail::table')
|               |               |
| ------------- |:-------------:|
| Event         | **{{$order->event->title}}**      |
| Date          | {{$order->event->date}} |
| Location          | {{$order->event->location}} |
| Quantity          | {{$order->quantity}} |
| Ticket Number          | **{{$order->order_number}}** |

@endcomponent
{{-- <table style="max-width: 50%;table-layout: auto;">
    <tr>
        <td>Ticket Number</td>
        <td>:</td>
        <th>{{$order->order_number}}</th>
    </tr>
    <tr>
        <td>Event</td>
        <td>:</td>
        <th>{{$order->event->title}}</th>
    </tr>
    <tr>
        <td>Quantity</td>
        <td>:</td>
        <th>{{$order->quantity}}</th>
    </tr>
    <tr>
        <td>Date</td>
        <td>:</td>
        <th>{{$order->event->date}}</th>
    </tr>
    <tr>
        <td>Location</td>
        <td>:</td>
        <th>{{$order->event->location}}</th>
    </tr>

<table> --}}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
