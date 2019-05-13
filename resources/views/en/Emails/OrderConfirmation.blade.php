@extends('en.Emails.Layouts.Master')

@section('message_content')
Hello,<br><br>

Your order for the event <b>{{$order->event->title}}</b> was successful.<br><br>

Your tickets are attached to this email. You can also view you order details and download your tickets at: {{route('showOrderDetails', ['order_reference' => $order->order_reference])}}


<h3>Order Details</h3>
Order Reference: <b>{{$order->order_reference}}</b><br>
Order Name: <b>{{$order->full_name}}</b><br>
Order Date: <b>{{$order->created_at->format(config('attendize.default_datetime_format'))}}</b><br>
Order Email: <b>{{$order->email}}</b><br>

<br><br>
Thank you
@stop
