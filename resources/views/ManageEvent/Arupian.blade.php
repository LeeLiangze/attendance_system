@extends('Shared.Layouts.Master')

@section('title')
    @parent

    Arupians
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
    <i class='ico-users mr5'></i>
    Arupians
    <span class="page_title_sub_title hide">
</span>
@stop

@section('head')

@stop

@section('page_header')
    <div class="col-md-9 col-sm-6">
        <!-- Toolbar -->
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group btn-group-responsive">
                <button data-modal-id="InviteArupian" href="javascript:void(0);"
                        data-href="{{route('showCreateArupian', ['event_id'=>$event->id])}}"
                        class="loadModal btn btn-success" type="button"><i class="ico-user"></i>Add arupian
                </button>
            </div>

            <div class="btn-group btn-group-responsive">
                <button data-modal-id="SendArupians" href="javascript:void(0);"
                        data-href="{{route('showSendArupian', ['event_id'=>$event->id])}}"
                        class="loadModal btn btn-success" type="button"><i class="ico-users"></i>Send tickets to all
                    arupians
                </button>
            </div>

            <div class="btn-group btn-group-responsive">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                    <i class="ico-users"></i> QR Code <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                        <a href="{{route('downloadQRCode')}}">Download QR Code</a>
                    </li>
                </ul>
            </div>
        </div>
        <!--/ Toolbar -->
    </div>
    <div class="col-md-3 col-sm-6">
        {!! Form::open(array('url' => route('showArupians', ['event_id'=>$event->id,'sort_by'=>$sort_by]), 'method' => 'get')) !!}
        <div class="input-group">
            <input name='t' @if($t===null) value=""@else value="{{$t}}"@endif placeholder="Search Arupian" type="text" class="form-control">
            <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
        </div>
        {!! Form::close() !!}
    </div>
@stop


@section('content')
    <!--Start Attendees table-->
    <div class="row">
        @if($arupians->count())
        <div class="col-md-12">
            <!-- START panel -->
            <div class="panel">
                <div class="table-responsive ">
                    <table class="table">
                        <thead>
                        <tr>
                            <th width="20%">
                                {!! Html::sortable_link('name', $sort_by, 'first_name', $sort_order, ['t' => $t , 'page' => $arupians->currentPage()]) !!}
                            </th>
                            <th width="20%">
                                {!! Html::sortable_link('email', $sort_by, 'email', $sort_order, ['t' => $t , 'page' => $arupians->currentPage()]) !!}
                            </th>
                            {{--<th width="10%">--}}
                                {{--{!! Html::sortable_link('gender', $sort_by, 'gender', $sort_order, ['t' => $t , 'page' => $arupians->currentPage()]) !!}--}}
                            {{--</th>--}}
                            <th width="35%">
                                {!! Html::sortable_link('group', $sort_by, 'group', $sort_order, ['t' => $t , 'page' => $arupians->currentPage()]) !!}
                            </th>
                            <th width="15%">
                            </th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($arupians as $arupian)
                            <tr>
                                <td>
                                    {{$arupian->first_name.' '.$arupian->last_name}}
                                </td>
                                <td>
                                    {{$arupian->email}}
                                </td>
                                {{--<td>--}}
                                    {{--{{$arupian->gender}}--}}
                                {{--</td>--}}
                                <td>
                                    {{$arupian->group->name}}
                                </td>
                                <td class="text-center">
                                    <a
                                            data-modal-id="EditAttendee"
                                            href="javascript:void(0);"
                                            data-href="{{route('showEditArupian', ['event_id'=>$event->id, 'arupian_id'=>$arupian->id])}}"
                                            class="loadModal btn btn-xs btn-primary"
                                    > @lang("basic.edit")</a>

                                    <a
                                            data-modal-id="CancelAttendee"
                                            href="javascript:void(0);"
                                            data-href="{{route('showCancelArupian', ['event_id'=>$event->id, 'arupian_id'=>$arupian->id])}}"
                                            class="loadModal btn btn-xs btn-danger"
                                    > @lang("basic.cancel")</a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            {!!$arupians->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order, 't' => $t])->render()!!}
        </div>
        @else
            @if($t)
                @include('Shared.Partials.NoSearchResults')
            @else
                @include('ManageEvent.Partials.ArupiansBlankSlate')
            @endif
        @endif
    </div>
@stop
