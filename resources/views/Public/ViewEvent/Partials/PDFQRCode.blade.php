<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>QRCode</title>
</head>
<body>
@foreach($attendees as $attendee)
    @if(!$attendee->is_cancelled)
    <div style="position:absolute;top:0.10in;left:0.08in;width:0.86in;height:0.86in">
        {!! DNS2D::getBarcodeSVG($attendee->private_reference_number, "QRCODE", 6, 6) !!}
    </div>
    {{--<img style="position:absolute;top:0.10in;left:0.08in;width:0.86in;height:0.86in" src="ci_1.png"/>--}}
    <div style="position:absolute;top:1.04in;left:0.12in;width:0.82in;line-height:0.12in;"><span
                style="font-style:normal;font-weight:normal;font-size:7pt;font-family:HelveticaNeue;color:#000000">Nicolette </span></SPAN>
        <br/></div>
    <div style="position:absolute;top:1.04in;left:0.12in;width:0.82in;line-height:0.12in;">
        <DIV style="position:relative; left:0.45in;"><span
                    style="font-style:normal;font-weight:normal;font-size:7pt;font-family:HelveticaNeue;color:#000000">Lee</span><span
                    style="font-style:normal;font-weight:normal;font-size:7pt;font-family:HelveticaNeue;color:#000000"> </span><br/></SPAN>
        </DIV>
    </div>
    <div style="position:absolute;top:1.15in;left:0.12in;width:0.82in;line-height:0.12in;"><span
                style="font-style:normal;font-weight:normal;font-size:7pt;font-family:HelveticaNeue;color:#000000">Leichten </span></SPAN>
        <br/></div>
    <div style="position:absolute;top:1.15in;left:0.12in;width:0.82in;line-height:0.12in;">
        <DIV style="position:relative; left:0.43in;"><span
                    style="font-style:normal;font-weight:normal;font-size:7pt;font-family:HelveticaNeue;color:#000000">Schlag </span></SPAN>
        </DIV>
        <br/></div>
    <div style="position:absolute;top:1.15in;left:0.12in;width:0.82in;line-height:0.12in;"><span
                style="font-style:normal;font-weight:normal;font-size:7pt;font-family:HelveticaNeue;color:#000000"> </span><br/></SPAN>
    </div>
    @endif
@endforeach
<img style="position:absolute;top:0.00in;left:0.00in;width:1.03in;height:1.42in" src="{{ asset('assets/images/ci_2.png') }}"/>
</body>
</html>