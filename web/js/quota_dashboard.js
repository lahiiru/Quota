/**
 * Created by Lahiru on 02/01/2016.
 */
$(document).ready(function(){
    // EVENT LISTENERS BELOW
    $('.request-new-grant').click(
        function() {
            processAction(1, this)
        }
    );

    $('.request-new-reject').click(
        function() {
            processAction(0, this)
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
function processAction(accept,context){
    document.y=$(context).closest("td").siblings()[0].innerHTML;
    var requestID = $(context).closest("td").siblings()[0].innerHTML;
    document.lastRow=$(context).closest("tr");
    $url="requests/reject";
    if(accept==1){
        $url="requests/accept";
    }
    function posted(data){
        document.x=$('.modal-body');
        $('.modal-body')[0].innerHTML="<p>"+data+"</p>";
        $('#request-info').modal('toggle');
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