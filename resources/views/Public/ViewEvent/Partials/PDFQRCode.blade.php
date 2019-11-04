<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>QRCode</title>
</head>
<body>
    <div style="position:absolute;top:0.10in;left:0.08in;width:0.86in;height:0.86in">
        {!! DNS2D::getBarcodeSVG($arupian->private_reference, "QRCODE", 3, 3) !!}
    </div>
    {{--<img style="position:absolute;top:0.10in;left:0.08in;width:0.86in;height:0.86in" src="ci_1.png"/>--}}
    <div style="position:absolute;top:1.04in;left:0.12in;width:0.82in;line-height:0.12in;"><span
                style="font-style:normal;font-weight:normal;font-size:7pt;font-family:HelveticaNeue;color:#000000">{{ $arupian->first_name }} </span></SPAN>
        <br/></div>
    <div style="position:absolute;top:1.15in;left:0.12in;width:0.82in;line-height:0.12in;"><span
                style="font-style:normal;font-weight:normal;font-size:7pt;font-family:HelveticaNeue;color:#000000">{{ $arupian->last_name }} </span></SPAN>
        <br/></div>
<img style="position:absolute;top:0.00in;left:0.00in;width:1.03in;height:1.42in" src="{{ asset('assets/images/ci_2.png') }}"/>
</body>
</html>