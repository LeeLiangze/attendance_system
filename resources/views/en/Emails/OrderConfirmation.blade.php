@extends('en.Emails.Layouts.Master')

@section('message_content')
Hello,<br><br>

Your register for the event <b>{{$order->event->title}}</b> was successful.<br><br>

Your tickets are attached to this email. You can also view you register form and download your tickets at: {{route('showOrderDetails', ['order_reference' => $order->order_reference])}}


<h3>Register Form</h3>
Register Reference: <b>{{$order->order_reference}}</b><br>
Register Name: <b>{{$order->full_name}}</b><br>
Register Date: <b>{{$order->created_at->format(config('attendize.default_datetime_format'))}}</b><br>
Register Email: <b>{{$order->email}}</b><br>

<br><br>
Thank you
@stop
