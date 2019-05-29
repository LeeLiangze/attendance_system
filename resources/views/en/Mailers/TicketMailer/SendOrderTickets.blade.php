@extends('en.Emails.Layouts.Master')

@section('message_content')
Hello,<br><br>

Your register for the event <b>{{$order->event->title}}</b> was successful.<br><br>

Your tickets are attached to this email.

@if(!$order->is_payment_received)
<br><br>
<br><br>
@endif
<h3>Register Form</h3>
register Reference: <b>{{$order->order_reference}}</b><br>
Register Name: <b>{{$order->full_name}}</b><br>
Register Date: <b>{{$order->created_at->format(config('attendize.default_datetime_format'))}}</b><br>
Register Email: <b>{{$order->email}}</b><br>
<a href="{!! route('downloadCalendarIcs', ['event_id' => $order->event->id]) !!}">Add To Calendar</a>

<br><br>
Thank you
@stop
