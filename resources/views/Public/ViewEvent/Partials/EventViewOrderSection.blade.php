<style>
    /*@todo This is temp - move to styles*/
    h3 {
        border: none !important;
        font-size: 30px;
        text-align: center;
        margin: 0;
        margin-bottom: 30px;
        letter-spacing: .2em;
        font-weight: 200;
    }

    .order_header {
        text-align: center
    }

    .order_header .massive-icon {
        display: block;
        width: 120px;
        height: 120px;
        font-size: 100px;
        margin: 0 auto;
        color: #63C05E;
    }

    .order_header h1 {
        margin-top: 20px;
        text-transform: uppercase;
    }

    .order_header h2 {
        margin-top: 5px;
        font-size: 20px;
    }

    .order_details.well, .offline_payment_instructions {
        margin-top: 25px;
        background-color: #FCFCFC;
        line-height: 30px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, .9);
        color: #656565;
        overflow: hidden;
    }

    .ticket_download_link {
        border-bottom: 3px solid;
    }
</style>

<section id="order_form" class="container">
    <div class="row">
        <div class="col-md-12 order_header">
            <span class="massive-icon">
                <i class="ico ico-checkmark-circle"></i>
            </span>
            <h1>{{ @trans("Public_ViewEvent.thank_you_for_your_order") }}</h1>
            <h2>
                {{ @trans("Public_ViewEvent.your") }}
                <a class="ticket_download_link"
                   href="{{ route('showOrderTickets', ['order_reference' => $order->order_reference] ).'?download=1' }}">
                    {{ @trans("Public_ViewEvent.tickets") }}</a> {{ @trans("Public_ViewEvent.confirmation_email") }}
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="content event_view_order">

                @if($event->post_order_display_message)
                    <div class="alert alert-dismissable alert-info">
                        {{ nl2br(e($event->post_order_display_message)) }}
                    </div>
                @endif

                <div class="order_details well">
                    <div class="row">
                        <div class="col-sm-4 col-xs-6">
                            <b>@lang("Public_ViewEvent.first_name")</b><br> {{$order->first_name}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <b>@lang("Public_ViewEvent.last_name")</b><br> {{$order->last_name}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <b>@lang("Public_ViewEvent.reference")</b><br> {{$order->order_reference}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <b>@lang("Public_ViewEvent.date")</b><br> {{$order->created_at->format(config('attendize.default_datetime_format'))}}
                        </div>

                        <div class="col-sm-4 col-xs-6">
                            <b>@lang("Public_ViewEvent.email")</b><br> {{$order->email}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

