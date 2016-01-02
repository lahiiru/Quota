/**
 * Created by Lahiru on 02/01/2016.
 */
$('.request-new-grant').click(
    function(){
        var requestID = $(this).closest("td").siblings()[0].innerHTML;
        document.lastRow=$(this).closest("tr");
        function posted($data){
            alert($data);
            $(document.lastRow).remove();
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
