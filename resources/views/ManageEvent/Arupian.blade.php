@extends('Shared.Layouts.Master')

@section('title')
    @parent

    @lang("Event.event_orders")
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
            <div class="btn-group btn-group btn-group-responsive">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                    <i class="ico-users"></i> @lang("basic.export") <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    {{--<li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'xlsx'])}}">@lang("File_format.Excel_xlsx")</a></li>--}}
                    {{--<li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'xls'])}}">@lang("File_format.Excel_xls")</a></li>--}}
                    {{--<li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'csv'])}}">@lang("File_format.csv")</a></li>--}}
                </ul>
            </div>
        </div>
        <!--/ Toolbar -->
    </div>
    <div class="col-md-3 col-sm-6">
        {!! Form::open(array('url' => route('showEventOrders', ['event_id'=>$event->id,'sort_by'=>$sort_by]), 'method' => 'get')) !!}
        <div class="input-group">
            <input name='q' value="{{$q or ''}}" placeholder="@lang('Order.search_placeholder')" type="text" class="form-control">
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
                                    {!! Html::sortable_link('name', $sort_by, 'first_name', $sort_order, ['q' => $q , 'page' => $arupians->currentPage()]) !!}
                                </th>
                                <th width="20%">
                                    {!! Html::sortable_link('email', $sort_by, 'email', $sort_order, ['q' => $q , 'page' => $arupians->currentPage()]) !!}
                                </th>
                                <th width="10%">
                                    {!! Html::sortable_link('gender', $sort_by, 'gender', $sort_order, ['q' => $q , 'page' => $arupians->currentPage()]) !!}
                                </th>
                                <th width="35%">
                                    {!! Html::sortable_link('group', $sort_by, 'group', $sort_order, ['q' => $q , 'page' => $arupians->currentPage()]) !!}
                                </th>
                                <th width="15%">
                                    {!! Html::sortable_link('status', $sort_by, 'order_status_id', $sort_order, ['q' => $q , 'page' => $arupians->currentPage()]) !!}
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
                                    <td>
                                        {{$arupian->gender}}
                                    </td>
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
                {!!$arupians->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order, 'q' => $q])->render()!!}
            </div>

        @else

            @if($q)
                @include('Shared.Partials.NoSearchResults')
            @else
                @include('ManageEvent.Partials.OrdersBlankSlate')
            @endif

        @endif
    </div>    <!--/End attendees table-->
@stop
