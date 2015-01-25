$(document).ready(function() {
    $('.tooltip_default').tooltip();
});

//cadastro de evento - imagem

function removeImage(){
    $("#previewImage").attr('src','/img/icone_transparente.png');
    $("#uploadImage").val("");
    $("#is_remove_image").val("1");
}
function readURL(input)
{
     $('#is_remove_image').val("0");
    if (input.files && input.files[0])
    {
        var reader = new FileReader();
        reader.onload = function(e)
        {
            $('#previewImage')
                    .attr('src', e.target.result)
                    .width(150);
            //.height(200);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
//ativar/desativar evento
function setIsActiveEvent(id, e) {
    $.ajax({
        type: 'POST',
        data: "",
        url: e.href,
        dataType: "json",
        success: function(response, textStatus, xhr) {
            if (response.success) {
                e.innerHTML = "Ativar";
                e.href = e.href.replace('deactivate', 'activate');
            } else{
                alert('Erro: não foi possível alterar o estado do evento.');
            }
        },
        error: function(xhr, textStatus, error) {
            window.location = '/users/login';
        }
    });
    return false;

}