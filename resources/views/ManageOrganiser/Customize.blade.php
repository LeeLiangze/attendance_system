@extends('Shared.Layouts.Master')

@section('title')
    @parent
    @lang("Organiser.organiser_events")
@stop

@section('page_title')
    @lang("Organiser.organiser_name_events", ["name"=>$organiser->name])
@stop

@section('top_nav')
    @include('ManageOrganiser.Partials.TopNav')
@stop

@section('head')
    <style>
        .page-header {
            display: none;
        }
    </style>
    <script>
        $(function () {
            $('.colorpicker').minicolors({
                changeDelay: 500,
                change: function () {
                    var replaced = replaceUrlParam('{{route('showOrganiserHome', ['organiser_id'=>$organiser->id])}}', 'preview_styles', encodeURIComponent($('#OrganiserPageDesign form').serialize()));
                    document.getElementById('previewIframe').src = replaced;
                }
            });

        });

        @include('ManageOrganiser.Partials.OrganiserCreateAndEditJS')
    </script>
@stop

@section('menu')
    @include('ManageOrganiser.Partials.Sidebar')
@stop

@section('page_header')

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#organiserSettings" data-toggle="tab">@lang("Organiser.organiser_settings")</a>
                </li>
                <li>
                    <a href="#OrganiserPageDesign" data-toggle="tab">@lang("Organiser.organiser_page_design")</a>
                </li>
            </ul>
            <div class="tab-content panel">
                <div class="tab-pane active" id="organiserSettings">
                    {!! Form::model($organiser, array('url' => route('postEditOrganiser', ['organiser_id' => $organiser->id]), 'class' => 'ajax')) !!}

                    <div class="form-group">
                        {!! Form::label('enable_organiser_page', trans("Organiser.enable_public_organiser_page"), array('class'=>'control-label required')) !!}
                        {!!  Form::select('enable_organiser_page', [
                        '1' => trans("Organiser.make_organiser_public"),
                        '0' => trans("Organiser.make_organiser_hidden")],Input::old('enable_organiser_page'),
                                                    array(
                                                    'class'=>'form-control'
                                                    ))  !!}
                        <div class="help-block">
                            @lang("Organiser.organiser_page_visibility_text")
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('name', trans("Organiser.organiser_name"), array('class'=>'required control-label ')) !!}
                        {!!  Form::text('name', Input::old('name'),
                                                array(
                                                'class'=>'form-control'
                                                ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', trans("Organiser.organiser_email"), array('class'=>'control-label required')) !!}
                        {!!  Form::text('email', Input::old('email'),
                                                array(
                                                'class'=>'form-control ',
                                                'placeholder'=>trans("Organiser.organiser_email_placeholder")
                                                ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('about', trans("Organiser.organiser_description"), array('class'=>'control-label ')) !!}
                        {!!  Form::textarea('about', Input::old('about'),
                                                array(
                                                'class'=>'form-control ',
                                                'placeholder'=>trans("Organiser.organiser_description_placeholder"),
                                                'rows' => 4
                                                ))  !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('google_analytics_code', trans("Organiser.google_analytics_code"), array('class'=>'control-label')) !!}
                        {!!  Form::text('google_analytics_code', Input::old('google_analytics_code'),
                                                array(
                                                'class'=>'form-control',
                                                'placeholder' => trans("Organiser.google_analytics_code_placeholder"),
                                                ))
                        !!}
                    </div>
                    @if(is_file($organiser->logo_path))
                        <div class="form-group">
                            {!! Form::label('current_logo', trans("Organiser.current_logo"), array('class'=>'control-label ')) !!}

                            <div class="thumbnail">
                                {!!HTML::image($organiser->logo_path)!!}
                                {!! Form::label('remove_current_image', trans("Organiser.delete_logo?"), array('class'=>'control-label ')) !!}
                                {!! Form::checkbox('remove_current_image') !!}
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        {!!  Form::labelWithHelp('organiser_logo', trans("Organiser.organiser_logo"), array('class'=>'control-label '),
                            trans("Organiser.organiser_logo_help"))  !!}
                        {!!Form::styledFile('organiser_logo')!!}
                    </div>
                    <div class="modal-footer">
                        {!! Form::submit(trans("Organiser.save_organiser"), ['class'=>"btn btn-success"]) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="tab-pane scale_iframe" id="OrganiserPageDesign">
                    {!! Form::model($organiser, array('url' => route('postEditOrganiserPageDesign', ['event_id' => $organiser->id]), 'class' => 'ajax ')) !!}

                    <div class="row">

                        <div class="col-md-6">
                            <h4>@lang("Organiser.organiser_design")</h4>

                            <div class="form-group">
                                {!! Form::label('page_header_bg_color', trans("Organiser.header_background_color"), ['class'=>'control-label required ']) !!}
                                {!!  Form::input('text', 'page_header_bg_color', Input::old('page_header_bg_color'),
                                                            [
                                                            'class'=>'form-control colorpicker',
                                                            'placeholder'=>'#000000'
                                                            ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('page_text_color', trans("Organiser.text_color"), ['class'=>'control-label required ']) !!}
                                {!!  Form::input('text', 'page_text_color', Input::old('page_text_color'),
                                                            [
                                                            'class'=>'form-control colorpicker',
                                                            'placeholder'=>'#FFFFFF'
                                                            ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('page_bg_color', trans("Organiser.background_color"), ['class'=>'control-label required ']) !!}
                                {!!  Form::input('text', 'page_bg_color', Input::old('page_bg_color'),
                                                            [
                                                            'class'=>'form-control colorpicker',
                                                            'placeholder'=>'#EEEEEE'
                                                            ])  !!}
                            </div>
                            <div class="form-group">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>@lang("Organiser.organiser_page_preview")</h4>
                            <div class="preview iframe_wrap"
                                 style="overflow:hidden; height: 500px; border: 1px solid #ccc; overflow: hidden;">
                                <iframe id="previewIframe"
                                        src="{{ route('showOrganiserHome', ['organiser_id' => $organiser->id]) }}"
                                        frameborder="0" style="overflow:hidden;height:100%;width:100%" width="100%"
                                        height="100%"></iframe>
                            </div>
                        </div>


                    </div>

                    <div class="panel-footer mt15 text-right">
                        {!! Form::submit(trans("basic.save_changes"), ['class'=>"btn btn-success"]) !!}
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
@stop
