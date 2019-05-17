<div role="dialog"  class="modal fade " style="display: none;">
    {!! Form::model($arupian, array('url' => route('postCancelArupian', array('event_id' => $event, 'arupian_id' => $arupian->id)), 'class' => 'ajax')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-cancel"></i>
                    {{ @trans("ManageEvent.cancel_attendee_title", ["cancel" => $arupian->full_name]) }}</h3>
            </div>
            <div class="modal-body">
                <p>
                    {{ @trans("ManageEvent.cancel_description") }}
                </p>

                <p>
{{--                    {!! @trans("ManageEvent.cancel_refund", ["url"=>route('showEventOrders', ['event_id' => $attendee->event->id, 'q' => $attendee->order->order_reference])]) !!}--}}
                </p>
                <br>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
                {!! Form::hidden('arupian_id', $arupian->id) !!}
                {!! Form::button(trans("basic.cancel"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit(trans("ManageEvent.confirm_cancel"), ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
        {!! Form::close() !!}
    </div>
</div>

