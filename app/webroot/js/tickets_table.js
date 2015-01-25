

//atualizar dados de formas de pagamento
function validateDadosPagamento() {
    var errors = "";
    if ($("#UserIsSellingTicketPS").is(":checked") === false && $("#UserIsSellingTicketMoip").is(":checked") === false) {
        errors = "Por favor, selecione pelo menos um serviço de vendas.";
    }
    if ($("#UserIsSellingTicketPS").is(":checked")) {

        if ($('#userPagSeguro').find("input").get(3).checkValidity() === false) {
            errors = "Insira um e-mail PagSeguro válido";
        } else if ($('#userPagSeguro').find("input").get(4).checkValidity() === false
                || $("#UserTokenPagseguro").val().trim().length !== 32) {
            errors = "Insira um token PagSeguro válido";
        }
    }
    if ($("#UserIsSellingTicketMoip").is(":checked")) {

        if ($('#userPagSeguro').find("input").get(7).checkValidity() === false) {
            errors += "<p>Insira um e-mail Moip válido</p>";
        }
    }
    if (errors) {
        $("#user_payment_message").html(errors);
        return false;
    }
    return true;

}
function saveUserPayment() {

    if (validateDadosPagamento()) {
        var formData = $("#userPagSeguro").serialize();
        var formUrl = $("#userPagSeguro").attr('action');
        $.ajax({
            type: 'POST',
            url: formUrl,
            data: formData,
            dataType: "json",
            success: function(response, textStatus, xhr) {
                if (response.success) {
                    $('#user_payment_message').html('Seus dados foram salvos.');
                    $('#user_payment_message').show();
                    $("#email_pagseguro").val($('#UserEmailPagseguro').val());
                    $("#token_pagseguro").val($('#UserTokenPagseguro').val());
                    $("#email_moip").val($('#UserEmailMoip').val());
                    $("#is_selling_ticket_pagseguro").val($('#UserIsSellingTicketPS').val());
                    $("#is_selling_ticket_moip").val($('#UserIsSellingTicketMoip').val());
                    $('#paymentData').modal('hide');
                    addTicket();
                } else {
                    $('#user_payment_message').html('Desculpe, não foi possível salvar seus dados do PagSeguro.');
                    $('#user_payment_message').show();
                }
            },
            error: function(xhr, textStatus, error) {
                window.location = '/users/login';
            }
        });
    }
    return false;
}
var tickets = new Array();

/**********   validacao dos campos de ingressos online     ***********/
function validarDecimal(evt) {
    var indexDotOrComma = ($("#txt_ticket_price").val().indexOf(',') === -1) ? ($("#txt_ticket_price").val().indexOf('.')) : $("#txt_ticket_price").val().indexOf(',');
    if (indexDotOrComma !== -1) {
        if (evt.which < 48 || evt.which > 57) {

            evt.preventDefault();
        }
    } else {
        if ((evt.which < 48 || evt.which > 57) && (evt.which !== 44 && evt.which !== 46))
        {
            evt.preventDefault();
        }
    }
}
function holdChars(evt) {
    if (evt.which < 48 || evt.which > 57)
    {
        evt.preventDefault();
    }
}


/**********   cadastro de ingressos online     ***********/
function addTicketsToTable() {
    var linhas = "";
    for (var x in tickets) {
        linhas += "<tr id='linha_" + x + "'><td>" +
                tickets[x].Product.name
                + "</td>" +
                "<td>" +
                tickets[x].Product.price
                + "</td>" +
                "<td>" +
                tickets[x].Product.quantity
                + "</td>" +
                "<td>";
        if (tickets[x].Product.is_active) {
            linhas += "<a alt='Desativar' title='Desativar' onclick='setActiveTicket(" + x + ", this)'><i class='glyphicon glyphicon-eye-close'></i> </a>";
        } else {
            linhas += "<a alt='Ativar' title='Ativar' onclick='setActiveTicket(" + x + ", this)'><i class='glyphicon glyphicon-eye-open'></i> </a>";
        }

        if (tickets[x].Product.Item.length <= 0) {
            linhas += "<a alt='Apagar' title='Apagar' onclick='removeTicket(" + x + ")'><i class='glyphicon glyphicon-trash'></i> </a>"
        }
        linhas += "</td></tr>";
    }
    $("#eventTickets").val(JSON.stringify(tickets));
    $("#ticket_table").html(linhas);
}
function deleteTicket(ticketId, position) {
    //removes specified ticket from db
    $.ajax({
        type: 'POST',
        url: "/products/delete/" + ticketId,
        dataType: 'json',
        beforeSend: function() {
            $('#loadingTickets').show();
            $("#div_ticket_table").hide();
        },
        success: function(response, textStatus, xhr) {
            if (response.success) {
                tickets.splice(position, 1);
                $("#linha_" + position).remove();
                $("#ticket_errors").hide();
            } else {
                $("#ticket_errors").show();
                $("#ticket_errors").append("<br>Não foi possível remover o ingresso.");
            }
        },
        complete: function() {
            $('#loadingTickets').hide();
            $("#div_ticket_table").show();
            document.getElementById("txt_ticket_name").focus();
        },
        error: function(xhr, textStatus, error) {
            window.location = "/";
        }
    });
}
function removeTicket(x) {
    if (tickets[x].Product.id === 0) {
        tickets.splice(x, 1);
        $("#linha_" + x).remove();
    } else {
        deleteTicket(tickets[x].Product.id, x);
    }
    addTicketsToTable();
}
function updateIsActiveTicket(id, position, element) {
    $.ajax({
        type: 'POST',
        url: "/products/setIsActive/" + id,
        dataType: 'json',
        data: {
            updatevalue: !tickets[position].Product.is_active
        },
        beforeSend: function() {
            $('#loadingTickets').show();
            $("#div_ticket_table").hide();
        },
        success: function(response, textStatus, xhr) {
            if (response.success) {
                updateTicketLocal(position, element);
                $("#ticket_errors").hide();
            } else {
                $("#ticket_errors").show();
                $("#ticket_errors").append(response.errors);
            }
        },
        complete: function() {
            $('#loadingTickets').hide();
            $("#div_ticket_table").show();
        },
        error: function(xhr, textStatus, error) {
            $("#ticket_errors").html('<span>Não foi possível alterar o ingresso.</span>');
            $("#ticket_errors").show();
        }
    });
}
function updateTicketLocal(x, element) {
    tickets[x].Product.is_active = !tickets[x].Product.is_active;
    if (!tickets[x].Product.is_active) {
        $(element).html("<i class='glyphicon glyphicon-eye-open'></i> ");
        $(element).attr('alt', 'Ativar');
        $(element).attr('title', 'Ativar');
    } else {
        $(element).html("<i class='glyphicon glyphicon-eye-close'></i> ");
        $(element).attr('alt', 'Desativar');
        $(element).attr('title', 'Desativar');
    }
}
function setActiveTicket(x, element) {
    if (tickets[x].Product.id === 0) {
        updateTicketLocal(x, element);
    } else {
        updateIsActiveTicket(tickets[x].Product.id, x, element);
    }


    $("#eventTickets").val(JSON.stringify(tickets));
}
function resetTicketForm() {
    $('#txt_ticket_name').val("");
    $('#txt_ticket_price').val("");
    $('#txt_ticket_qty').val("");
}
function validateTicket() {
    var error = "";
    if ($("#is_selling_ticket_pagseguro").val() === "0" && $("#is_selling_ticket_moip").val() === "0") {
        $('#paymentData').modal('show');
        error = "Para continuar, escolha um serviço de vendas.";
    } else if ($('#txt_ticket_name').val().trim().length === 0 ||
            $('#txt_ticket_price').val().trim().length === 0 ||
            $('#txt_ticket_qty').val().trim().length === 0) {
        error = "Campos obrigatórios: Tipo de ingresso, Preço e Quantidade."
    } else if ($('#txt_ticket_qty').val() < 1) {
        error = "A quantidade deve ser maior que zero."
    }
    $('#txt_ticket_price').val($('#txt_ticket_price').val().replace(',', '.'));
    if (error !== "") {

        $("#ticket_errors").html(error);
        $("#ticket_errors").show();
        return false;
    } else {
        $("#ticket_errors").hide();
        return true;
    }
}
function addTicket() {
    if (validateTicket()) {
        tickets.push({Product: {
                name: $('#txt_ticket_name').val(),
                price: $('#txt_ticket_price').val(),
                quantity: $('#txt_ticket_qty').val(),
                is_active: true,
                id: 0,
                Item: []
            }});
        addTicketsToTable();
        resetTicketForm();
        document.getElementById("txt_ticket_name").focus();
    }
}
function loadPreviousTickets() {
    var t = $("#eventTickets").val();
    if (t !== "") {
        tickets = JSON.parse(t);
    }
    addTicketsToTable();
}
