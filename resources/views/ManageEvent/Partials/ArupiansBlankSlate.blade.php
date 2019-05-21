@extends('Shared.Layouts.BlankSlate')


@section('blankslate-icon-class')
    ico-users
@stop

@section('blankslate-title')
    No Arupian yet
@stop

@section('blankslate-text')
    Arupians will appear here once they successfully registered for your event, or, you can manually invite arupian yourself.
@stop

@section('blankslate-body')
<button data-invoke="modal" data-modal-id='InviteAttendee' data-href="{{route('showCreateArupian', array('event_id'=>$event->id))}}" href='javascript:void(0);'  class=' btn btn-success mt5 btn-lg' type="button" >
    <i class="ico-user-plus"></i>
    Invite Arupian
</button>
@stop


