@component('mail::message')
    Your order status is changed to "{{$orderStatus}}".
    <br>

    Thanks,
    {{ config('app.name') }}
@endcomponent
