<div role="dialog"  class="modal fade " style="display: none;">
   {!! Form::open(array('url' => route('postCreateArupian', array('event_id' => $event->id)), 'class' => 'ajax')) !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3 class="modal-title">
                    <i class="ico-user"></i>
                    Create Arupian</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                {!! Form::label('first_name', trans("Attendee.first_name"), array('class'=>'control-label required')) !!}

                                {!!  Form::text('first_name', Input::old('first_name'),
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                {!! Form::label('last_name', trans("Attendee.last_name"), array('class'=>'control-label')) !!}

                                {!!  Form::text('last_name', Input::old('last_name'),
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('email', trans("Attendee.email_address"), array('class'=>'control-label required')) !!}

                            {!!  Form::text('email', Input::old('email'),
                                                array(
                                                'class'=>'form-control'
                                                ))  !!}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('gender', trans("Attendee.gender"), array('class'=>'control-label required')) !!}
                                    {!!  Form::select('gender', array('female'=>'Female', 'male'=>'Male'), 'female',
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('group_id', trans("Attendee.group"), array('class'=>'control-label required')) !!}
                                    {!!  Form::select('group_id', $groups, 1,
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div> <!-- /end modal body-->
            <div class="modal-footer">
               {!! Form::button(trans("basic.cancel"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
               {!! Form::submit('Create Arupian', ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
       {!! Form::close() !!}
    </div>
</div>
