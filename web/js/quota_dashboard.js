/**
 * Created by Lahiru on 02/01/2016.
 */
$('.request-new-grant').click(
    function(){
        var requestID = $(this).closest("td").siblings()[0].innerHTML;
        function posted(){
            alert("");
        }
        $.ajax({
            type: "POST",
            url: "requests/accept",
            data: {"id":requestID},
            success: posted,
            dataType: "html"
        });
    }
)
