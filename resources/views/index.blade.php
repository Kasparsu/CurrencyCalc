<!DOCTYPE html>
<html>
<head>
    <title>Laravel</title>

    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="jquery-ui.min.css" rel="stylesheet">
    <script src="js/jquery-2.2.0.min.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/jquery-ui.min.js"></script>



</head>
<body>
<div class="container">
    <div class="header">

    </div>
    <div class="form_container">
        {!! Form::open(array('url' => '/calculate', 'id' => 'calculateform')) !!}

        {!! Form::number('l2htesumma',null ,array('min'=>"0", 'step'=>'any', 'required', 'placeholder' => 'Lähtesumma')) !!}

        {!! Form::select('l2htevaluuta', $dbcurrency) !!}
        <img id="arrow" src="images/arrow-24-32.png">

        {!! Form::select('sihtvaluuta', $dbcurrency) !!}

        {!! Form::text('kuup2ev',null,array('required', 'placeholder' => 'Kuupäev')) !!}
        {!! Form::hidden('sendkuup2ev',null,array('required')) !!}
        {!! Form::submit('Kalkuleeri', array('id'=>'submitbtn')) !!}
        <div id="result">Vastus</div>
        {!! Form::close() !!}

    </div>

</div>
<script>
    $(function() {
        $( "input[name='kuup2ev']" ).datepicker({
            inline: true,
            dateFormat: "dd.mm.yy",
            altField: "input[name='sendkuup2ev']",
            altFormat: "yy-mm-dd"
        });

    });


</script>
</body>
</html>
