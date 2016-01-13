function Submitdata() {

    $( "#calculateform" ).submit(function( event ) {

        // Stop form from submitting normally
        event.preventDefault();

        // Get some values from elements on the page:
        var $form = $( this ),
            date = $form.find( "input[name='sendkuup2ev']" ).val(),
            amount = $form.find( "input[name='l2htesumma']" ).val(),
            fromCur = $form.find( "select[name='l2htevaluuta']" ).val(),
            toCur = $form.find( "select[name='sihtvaluuta']" ).val(),
            url = $form.attr( "action" );
        // Send the data using post
        alert(date);
        var posting = $.post( url, { kuup2ev: date , l2htesumma: amount , l2htevaluuta: fromCur, sihtvaluuta: toCur } );

        // Put the results in a div
        posting.done(function( data ) {
            $( "#result").html( data );
        });
    });
}
window.onload = Submitdata;
