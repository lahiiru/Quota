/**
 * Created by Lahiru on 02/01/2016.
 */
$(document).ready(function(){
    // EVENT LISTENERS BELOW
    $('#reveal').change(
        function(){
            if ($(this).is(':checked')) {
                $('#inputPassword3').attr('type','text')
            }else{
                $('#inputPassword3').attr('type','password')
            }
        });

    $('.request-new-grant').click(
        function() {
            processNewUserAction(1, this)
        }
    );

    $('.request-new-reject').click(
        function() {
            processNewUserAction(0, this)
        }
    );

    $('.request-msg-remove').click(
        function() {
            processNewUserAction(0, this)
        }
    );

    $('#form_kbytes').wrap('<div id="kbytes" class="input-group"></div>');
    $('#form_kbytes').parent().prepend('<span class="input-group-addon" id="basic-addon2">0.00 GB</span>');
    $('#form_kbytes').change(
        function() {
            $('#basic-addon2').text($('#form_kbytes').val()/1000000.0+' GB');
        }
    );


});


// DEFINITIONS BELOW
function processNewUserAction(accept,context){

    var requestID = $(context).closest("td").siblings()[0].innerHTML;
    document.lastRow=$(context).closest("tr");
    $url="requests/reject";
    if(accept==1){
        $url="requests/accept";
    }
    function posted(data){
        $('.modal-body')[0].innerHTML="<p>"+data+"</p>";
        if(data.indexOf('Unknown') == -1){
            $('#request-info').modal('toggle');
        }
        if(!/error/i.test(data)) {
            $(document.lastRow).remove();
        }
    }
    $.ajax({
        type: "POST",
        url: $url,
        data: {"id":requestID},
        success: posted,
        dataType: "html"
    });
}