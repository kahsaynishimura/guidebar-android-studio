var sponsors = new Array();
function addSponsor() {
    var hasErrors = false;
    if ($('#SponsorName').val().trim().length === 0) {
        $("#sponsor_errors").html("O campo Nome é obrigatório.");
        $("#sponsor_errors").show();
        hasErrors = true;
    } else if ($("#SponsorUrl").val().search(/http:/i) !== 0 && $("#SponsorUrl").val().search(/https:/i) !== 0) {
        $("#sponsor_errors").html("<br>Informe uma url válida. Ex: http://www.guidebar.com.br");
        $("#sponsor_errors").show();

        hasErrors = true;
    }
    if (!hasErrors) {

        var formdata = new FormData();
//        formData.append("Sponsor",  $("#SponsorManageForm").serialize());
        var file = document.getElementById("uploadImage").files[0];
        if ($("#SponsorIncludeImage").is(":checked")) {
            formdata.append("image", file);
        }
        formdata.append("data[Sponsor][event_id]", $("#SponsorEventId").val());
        formdata.append("data[Sponsor][name]", $("#SponsorName").val());
        formdata.append("data[Sponsor][url]", $("#SponsorUrl").val());
        formdata.append("data[Sponsor][include_image]", ($("#SponsorIncludeImage").is(":checked")) ? "1" : "0");
        $.ajax({
            type: 'POST',
            url: "/sponsors/add",
            processData: false,
            contentType: false,
            data: formdata,
            dataType: 'json',
            success: function(response, textStatus, xhr) {
                if (response.success) {
                    var sponsor = {Sponsor: {
                            name: $('#SponsorName').val(),
                            url: $('#SponsorUrl').val(),
                            id: response.id
                        }};

                    sponsors.push(sponsor);
                    addSponsorsToList();
                    resetSponsorForm();
                } else {
                    $("#sponsor_errors").html("Não foi possível salvar o patrocinador. Por favor, tente mais tarde.");
                    $("#sponsor_errors").show();
                }
            }
        });
    }

}

function resetSponsorForm() {
    $('#SponsorName').val("");
    $('#SponsorUrl').val("");
    $('#SponsorFilename').val("");
    $('#SponsorIncludeImage').attr('checked', false);
    isIncludeImage($('#SponsorIncludeImage'));
}
function isIncludeImage(element) {
    if (element.checked) {
        $("#div_include_image").show();
    } else {
        $("#div_include_image").hide();
    }
}
function loadPreviousSponsors() {
    var t = $("#eventSponsors").val();
    if (t !== "") {
        sponsors = JSON.parse(t);
    }
    addSponsorsToList();
}

function removeSponsor(x) {
    $.ajax({
        type: 'POST',
        url: "/sponsors/delete/" + sponsors[x].Sponsor.id,
        dataType: 'json',
        beforeSend: function() {
            $('#loadingSponsors').show();
            $("#sponsor_list").hide();
        },
        success: function(response, textStatus, xhr) {
            if (response.success) {
                sponsors.splice(x, 1);
                $("#item_" + x).remove();
                $("#sponsor_errors").hide();
                addSponsorsToList();
            } else {
                $("#sponsor_errors").show();
                $("#sponsor_errors").append("<br>Não foi possível remover o patrocinador.");
            }
        },
        complete: function() {
            $('#loadingSponsors').hide();
            $("#sponsor_list").show();
        },
        error: function(xhr, textStatus, error) {
            $("#sponsor_errors").html('<span>Não foi possível remover o ingresso.</span>');
            $("#sponsor_errors").show();
        }
    });

    return false;
}
function addSponsorsToList() {
    var linhas = "";
    for (var x in sponsors) {
        linhas += "<li id='item_" + x + "'>" +
                "<a href='" + sponsors[x].Sponsor.url + "' target='_blank'>" +
                sponsors[x].Sponsor.name
                + "</a>" +
                "<span><a alt='Apagar' title='Apagar' href='#' onclick='removeSponsor(" + x + ")'><i class='glyphicon glyphicon-trash'></i></a></span>"
                + "</li>";
    }
    $("#eventSponsors").val(JSON.stringify(sponsors));
    $("#sponsor_list").html(linhas);
}        