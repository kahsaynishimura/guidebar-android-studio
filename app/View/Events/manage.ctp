<script type="text/javascript" src="/js/sponsors.js"></script>
<script>
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

    function saveUserPayment(eventId) {

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
                        saveTicket(eventId);
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
                    (tickets[x].Product.quantity - tickets[x].Product.quantity_available) + "/" + tickets[x].Product.quantity
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
                    window.location = "/";
                }
            },
            complete: function() {
                $('#loadingTickets').hide();
                $("#div_ticket_table").show();
                document.getElementById("txt_ticket_name").focus();
            },
            error: function(xhr, textStatus, error) {
                $("#ticket_errors").html('<span>Não foi possível remover o ingresso.</span>');
                $("#ticket_errors").show();
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
    function saveTicket(eventId) {
        var ticket = {Product: {
                name: $('#txt_ticket_name').val(),
                price: $('#txt_ticket_price').val(),
                quantity: $('#txt_ticket_qty').val(),
                is_active: true,
                event_id: eventId,
                id: 0,
                quantity_available: $('#txt_ticket_qty').val(),
                Item: []
            }};

        if (validateTicket()) {
            $.ajax({
                type: 'POST',
                url: "/products/addTicket/" + eventId,
                dataType: 'json',
                data: ticket,
                beforeSend: function() {
                    $('#loadingTickets').show();
                    $("#div_ticket_table").hide();
                },
                success: function(response, textStatus, xhr) {
                    if (response.success) {
                        ticket.Product.id = response.id;
                        tickets.push(ticket);
                        addTicketsToTable();
                        resetTicketForm();
                        $("#ticket_errors").hide();
                    } else {
                        $("#ticket_errors").show();
                        $("#ticket_errors").append(response.errors);
                    }
                },
                complete: function() {
                    $('#loadingTickets').hide();
                    $("#div_ticket_table").show();
                    document.getElementById("txt_ticket_name").focus();
                },
                error: function(xhr, textStatus, error) {
                    $("#ticket_errors").html('<span>Não foi possível salvar o ingresso.</span>');
                    $("#ticket_errors").show();
                }
            });
        }
    }
    function loadPreviousTickets() {
        var t = $("#eventTickets").val();
        if (t !== "") {
            tickets = JSON.parse(t);
        }
        addTicketsToTable();
    }

    function activatePanelPagSeguro() {
        if ($("#UserIsSellingTicketPS").is(":checked")) {
            $("#div_dados_pag_seguro").show();
            $("#UserEmailPagseguro").attr('required', 'required');
            $("#UserTokenPagseguro").attr('required', 'required');
        } else {
            $("#div_dados_pag_seguro").hide();
            $("#UserEmailPagseguro").removeAttr('required');
            $("#UserTokenPagseguro").removeAttr('required');
        }
    }
    function activatePanelMoip() {
        if ($("#UserIsSellingTicketMoip").is(":checked")) {
            $("#div_dados_moip").show();
            $("#UserEmailMoip").attr('required', 'required');
        } else {
            $("#div_dados_moip").hide();
            $("#UserEmailMoip").removeAttr('required');
        }
    }
    $(document).ready(function() {
        $('.attendance_tooltip').tooltip();
        $('.nav-tabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        loadPreviousTickets();
        loadPreviousSponsors();

        $("#txt_ticket_price").keypress(validarDecimal);
        $("#txt_ticket_qty").keypress(holdChars);

        $("#txt_ticketoff_price").keypress(validarDecimal);
        $("#txt_ticketoff_qty").keypress(holdChars);

        var my_value =<?php echo $event['Event']['average_rating']; ?>;
        $('#example-f').prop("selectedIndex", (my_value - 1));
        $('#example-f').barrating({
            readonly: true
        });
    });
</script>

<div id="fb-root"></div><?php
$url = 'http://guidebar.com.br/events/view/' . $event['Event']['id'];

//echo $this->Html->meta(array('property' => 'fb:app_id', 'content' => '*******'),'',array('inline'=>false));
//echo $this->Html->meta(array('property' => 'og:type', 'content' => 'book'),'',array('inline'=>false));

$this->Html->meta(array('property' => 'og:url', 'content' => $url), '', array('inline' => false));

$this->Html->meta(array('property' => 'og:title', 'content' => $event['Event']['name']), '', array('inline' => false));

$this->Html->meta(array('property' => 'og:description', 'content' => $event['Event']['description']), '', array('inline' => false));

$imgurl = 'http://guidebar.com.br/img/' . $event['Event']['filename'];

$this->Html->meta(array('property' => 'og:image', 'content' => $imgurl), '', array('inline' => false));
?>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=402213736551577";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<div class="row">
    <div class="col-md-12">
        <h1> <?php echo $this->Html->link($event['Event']['name'], array('controller' => 'events', 'action' => 'view', $event['Event']['id'])); ?></h1>
    </div>
</div>
<div class="row">
    <aside class="col-md-3 view_event_col1">
        <div class="panel panel-guidebar">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
                <div class="event_links"> 
                    <ul>  
                        <li>
                            <span style="font-weight: bold"><?php echo __("Status do evento: "); ?></span>
                            <?php echo (($event['Event']['is_active'] == '1') ? __("Ativo") : __("Inativo")); ?>
                        </li>
                        <?php if ($event['Event']['is_active'] == '1'): ?>
                            <li>
                                <?php
                                echo $this->Html->link('<i class="glyphicon glyphicon-eye-close"></i> ' . __("Desativar"), array('controller' => 'events', 'action' => 'deactivate', $event['Event']['id'])
                                        , array('alt' => __("Desativar"), 'escape' => false));
                                ?>
                            </li>
                        <?php else: ?>     
                            <li><?php
                                echo $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i> ' . __("Ativar"), array('controller' => 'events', 'action' => 'activate', $event['Event']['id'])
                                        , array('alt' => __("Ativar"), 'escape' => false));
                                ?> 
                            </li>
                        <?php endif; ?>
                        <hr>
                        <li>
                            <?php echo $this->Html->link('<i class="glyphicon glyphicon-edit"></i> ' . __('Editar evento'), array('controller' => 'events', 'action' => 'edit', $event['Event']['id']), array('escape' => false)); ?> 
                        </li>
                        <li>
                            <?php echo $this->Html->link('<i class="glyphicon glyphicon-print"></i> ' . __("Imprimir lista de participantes"), array('controller' => 'events', 'action' => 'printAttendance', 'ext' => 'pdf', $event['Event']['id']), array('target' => '_blank', 'escape' => false)); ?>
                        </li>
                        <?php $podeDeletar = TRUE; ?>
                        <?php foreach ($event['Product'] as $prod) : ?>

                            <?php if (sizeof($prod['Item']) > 0): ?>   

                                <?php $podeDeletar = FALSE; ?>
                            <?php endif; ?>

                        <?php endforeach; ?>
                        <?php if ($podeDeletar): ?>

                            <li>
                                <?php echo $this->Html->link('<i class="glyphicon glyphicon-remove"></i> ' . __('Excluir evento'), array('controller' => 'events', 'action' => 'delete', $event['Event']['id']), array('escape' => false)); ?>
                            </li>

                        <?php endif; ?>

                        <hr>
                        <li style="text-align: center;">
                            <div>
                                <?php echo $this->Html->link('<i class="glyphicon glyphicon-new-window"></i> ' . __('Visualizar Evento'), array('controller' => 'events', 'action' => 'view', $event['Event']['id']), array('escape' => false, 'class' => 'btn btn-default', 'target' => '_blank')); ?>
                            </div>
                        </li>
                        <hr>
                        <li> 
                            <span class="bold_text">
                                <?php echo __('Visualizações: '); ?>
                            </span>
                            <?php echo h($event['Event']['views']); ?>
                        </li>

                        <li>
                            <span class="bold_text">
                                <?php echo __('Quantidade de avaliações: '); ?> 
                            </span> 
                            <?php echo $evaluationsCount; ?>

                        </li>
                        <li>
                            <span class="bold_text">
                                <?php echo __('Número de favoritos: '); ?>

                            </span>   
                            <?php echo $bookmarksCount; ?>
                        </li>
                        <li>
                            <span class="bold_text">
                                <?php echo __('Avaliação média: '); ?>
                            </span>
                            <div class="evaluations display_rating">
                                <div class="input select rating-f">
                                    <select id="example-f" name="data[Evaluation][rating]">
                                        <option value="1">Ruim</option>
                                        <option value="2">Regular</option>
                                        <option value="3">Bom</option>
                                        <option value="4">Muito bom</option>
                                        <option value="5">Ótimo</option>
                                    </select>
                                </div>
                            </div> 
                        </li>
                    </ul>
                </div>
            </div>
        </div> 
    </aside> 

    <div class="col-md-9 middle-column">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tabSummary">Resumo</a>
                </li>
                <li>
                    <a href="#tabTickets">Ingressos online</a>
                </li>
                <li>
                    <a href="#tabPrintTickets">Ingressos impressos</a>
                </li>
                <li>
                    <a href="#tabGallery">Galeria</a>
                </li>
                <li>
                    <a href="#tabSponsors">Patrocinadores</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tabSummary" >
                    <div class="well">
                        <div style="float: right; display: inline-block;">
                            <?php echo $this->Html->link('<i class="glyphicon glyphicon-edit"></i> ' . __('Editar evento'), array('controller' => 'events', 'action' => 'edit', $event['Event']['id']), array('escape' => false, 'class' => 'btn btn-default')); ?> 
                        </div>
                        <div >

                            <span class="bold_text">
                                <?php echo __('Local: '); ?>
                            </span><?php
                            echo $event['Address']['place_name'] . "<br />" . $event['Address']['street'] . ', ' . $event['Address']['street_number'] . '-' .
                            $event['Address']['complement'] . ' ' . $event['Address']['neighborhood'] . ' - ' .
                            $event['Address']['City']['name'] . '/' . $event['Address']['City']['State']['name'];
                            ?>
                            &nbsp;
                        </div> 
                        <div >  
                            <span class="bold_text">
                                <?php echo __('Categoria: '); ?>
                            </span>
                            <?php echo h($event['Category']['name']); ?>
                            &nbsp;
                        </div> 
                        <div >   
                            <span class="bold_text">
                                <?php echo __('Início: '); ?>
                            </span>
                            <?php
                            $dataInicial = date('d/m/Y h:i \h', strtotime(h($event['Event']['start_date'])));
                            echo h($dataInicial);
                            ?>
                            &nbsp;
                        </div>
                        <div > 
                            <span class="bold_text">
                                <?php echo __('Fim: '); ?>
                            </span>
                            <?php
                            $dataFinal = date('d/m/Y h:i \h', strtotime(h($event['Event']['end_date'])));
                            echo h($dataFinal);
                            ?>
                            &nbsp;
                        </div> 
                        <div >  
                            <span class="bold_text">
                                <?php echo __('Idade mínima: '); ?>
                            </span>
                            <?php echo h($event['Event']['minimum_age']); ?>
                            &nbsp;
                        </div> 
                        <div >  
                            <span class="bold_text">
                                <?php echo __('Open bar: '); ?>
                            </span>
                            <?php
                            if ($event['Event']['is_open_bar'] == '1') {
                                echo 'Sim';
                            } else {
                                echo 'Não';
                            }
                            ?>
                            &nbsp;
                        </div> 
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-3" >
                                <g:plus action="share" annotation="bubble" href="http://guidebar.com.br/events/view/<?php echo $event['Event']['id']; ?>"></g:plus>
                                <script type="text/javascript">
                                    (function() {
                                        var po = document.createElement('script');
                                        po.type = 'text/javascript';
                                        po.async = true;
                                        po.src = 'https://apis.google.com/js/plusone.js';
                                        var s = document.getElementsByTagName('script')[0];
                                        s.parentNode.insertBefore(po, s);
                                    })();
                                </script>
                            </div> 
                            <div class="col-md-3" >
                                <div class="fb-share-button" data-href="http://guidebar.com.br/events/view/<?php echo $event['Event']['id']; ?>" data-type="button_count"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="tab-pane" id="tabTickets" >

                    <div class="well">

                        <?php
                        echo $this->Form->input('tickets', array('type' => 'hidden', 'name' => 'data[Event][Tickets]',
                            'value' => (empty($event['Event']['Tickets']) ? "" : $event['Event']['Tickets']), 'id' => 'eventTickets'));
                        ?>       
                        <div id="div_ticket_form" class="row" style="margin-bottom:10px;">
                            <div class="col-md-3">
                                <?php echo $this->Form->input('txt_ticket_name', array('label' => false, 'name' => 'ticket', 'id' => 'txt_ticket_name', 'placeholder' => 'Tipo de ingresso', 'div' => 'form-group')); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo $this->Form->input('txt_ticket_price', array('label' => false, 'name' => 'ticket', 'id' => 'txt_ticket_price', 'placeholder' => 'Preço', 'div' => 'form-group')); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo $this->Form->input('txt_tiket_qty', array('label' => false, 'name' => 'ticket', 'type' => 'number', 'min' => '1', 'id' => 'txt_ticket_qty', 'placeholder' => 'Quantidade', 'div' => 'form-group')); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo $this->Form->input('email_pagseguro', array('type' => 'hidden', 'value' => $event['User']['email_pagseguro'], 'id' => 'email_pagseguro')); ?> 
                                <?php echo $this->Form->input('token_pagseguro', array('type' => 'hidden', 'value' => $event['User']['token_pagseguro'], 'id' => 'token_pagseguro')); ?> 
                                <?php echo $this->Form->input('is_selling_ticket_pagseguro', array('type' => 'hidden', 'value' => $event['User']['is_selling_ticket_pagseguro'], 'id' => 'is_selling_ticket_pagseguro')); ?> 
                                <?php echo $this->Form->input('email_moip', array('type' => 'hidden', 'value' => $event['User']['email_moip'], 'id' => 'email_moip')); ?> 
                                <?php echo $this->Form->input('is_selling_ticket_moip', array('type' => 'hidden', 'value' => $event['User']['is_selling_ticket_moip'], 'id' => 'is_selling_ticket_moip')); ?> 
                                <center>
                                    <?php echo $this->Form->button(__("Adicionar ingresso"), array('type' => 'button', 'class' => 'btn btn-default', 'onclick' => "saveTicket(" . $event['Event']['id'] . ")", 'div' => 'form-group')); ?>                                
                                </center>
                            </div>
                        </div>
                        <div id="loadingTickets"style="display: none;" ><center><span><img alt=""  src="/img/loading_gif_32x32.gif"></span></center></div>
                        <div id="div_ticket_table">
                            <table class="table table-striped" id="" >
                                <thead>
                                    <tr>
                                        <th>Ingresso</th>
                                        <th>Preço</th>
                                        <th>Vendidos</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="ticket_table">

                                </tbody>
                            </table>
                        </div>
                        <div class="instructions" id="ticket_errors" ><span>Atenção: </span> Só é possível remover ingressos que ainda não foram vendidos para ninguém.</div>
                    </div>
                </div>
                <div class="tab-pane" id="tabPrintTickets" >
                    <div class="well">
                        <div class="row">
                            <div class="col-md-4">

                                <div class="panel panel-guidebar">
                                    <div class="panel-heading">
                                        Gerar entradas
                                    </div>

                                    <div class="panel-body"> 
                                        <?php
                                        echo $this->Form->create('Item', array(
                                            'url' => array('controller' => 'items', 'action' => 'generateTickets', $event['Event']['id']),
                                            "target" => '_blank'));
                                        ?>

                                        <?php echo $this->Form->input('name', array('label' => 'Nome', 'div' => 'form-group required', 'required' => 'required', 'placeholder' => 'Ingresso feminino - Lote1')); ?>
                                        <?php echo $this->Form->input('quantity', array('div' => 'form-group number required', 'type' => 'number', 'max' => '50', 'required' => 'required', 'label' => 'Quantidade', 'id' => 'txt_ticketoff_qty')); ?>
                                        <?php
                                        echo $this->Form->input('price', array('div' => 'form-group required', 'type' => 'number', 'step' => "0.01",
                                            'required' => 'required', 'label' => 'Preço', 'id' => 'txt_ticketoff_price'));
                                        ?>                                    
                                        <?php echo $this->Form->end(__('Gerar entradas')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">

                                <div class="instructions">Cada ingresso impresso validado custa R$ 0,25. </div>   
                                <?php echo $this->element('previous_tickets', $event['previous_tickets']); ?> 
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tabGallery" >
                    <div class="well">
                        <?php echo $this->Upload->edit('Event', $event['Event']['id']); ?>
                    </div>
                </div>
                <div class="tab-pane" id="tabSponsors" >
                    <div class="well">
                        <div class="row"> 
                            <div class="col-md-5">
                                <div id="loadingSponsors"style="display: none;" ><center><span><img alt=""  src="/img/loading_gif_32x32.gif"></span></center></div>

                                <ul id="sponsor_list" style="text-decoration: none;">

                                </ul>
                            </div>
                            <div class="col-md-7">
                                <?php echo $this->Html->link('Adicionar patrocinador', "#", array('id' => 'addSponsorForm', 'class' => 'btn btn-default', 'onclick' => "$('#div_sponsor_form').show();return false")); ?>

                                <div class="panel panel-guidebar" id="div_sponsor_form" style="display: none;">

                                    <div class="panel-heading">Adicionar patrocinador </div>
                                    <div class="panel-body"> 
                                        <?php
                                        echo $this->Form->input('sponsors', array('type' => 'hidden', 'name' => 'data[Event][Sponsors]',
                                            'value' => (empty($event['Event']['Sponsors']) ? "" : $event['Event']['Sponsors']), 'id' => 'eventSponsors'));
                                        ?>    
                                        <div id="sponsor_errors" class="instructions" style="display: none;"></div>
                                        <?php echo $this->Form->create('Sponsor', array('id' => 'SponsorManageForm', 'type' => 'file')); ?>
                                        <?php echo $this->Form->input('event_id', array('type' => 'hidden', 'value' => $event['Event']['id'])); ?> 
                                        <?php echo $this->Form->input('name', array('div' => 'form-group', 'label' => false, 'placeholder' => __("Nome"))); ?> 
                                        <?php echo $this->Form->input('url', array('div' => 'form-group', 'type' => 'url', 'label' => false, 'placeholder' => __("http://www.guidebar.com"))); ?>
                                        <?php echo $this->Form->input('include_image', array('div' => 'form-group', 'type' => 'checkbox', 'label' => 'Incluir imagem', 'onchange' => 'isIncludeImage(this)')); ?>
                                        <div id="div_include_image" style="display:none;">
                                            <?php echo $this->Html->Image('icone_transparente.png', array('div' => 'form-group', 'alt' => 'ícone do patrocinador', 'style' => "width:150px;", 'id' => "previewImage", 'class' => 'img-responsive thumbnail')); ?>
                                            <?php echo $this->Form->file('Sponsor.filename', array('data[Sponsor][filename]', 'div' => 'form-group', 'label' => 'Escolha uma imagem para o patrocinador', 'onchange' => "readURL(this);", 'id' => 'uploadImage', 'accept' => 'image/*')); ?>
                                        </div>
                                        <div class="btn btn-group">
                                            <?php echo $this->Form->button(__('Cancelar'), array('type' => 'button', 'class' => 'btn btn-default', 'onclick' => "$('#div_sponsor_form').hide()")); ?>                                       
                                            <?php echo $this->Form->button(__('Salvar'), array('type' => 'button', 'class' => 'btn btn-default', 'onclick' => "addSponsor()")); ?>                                       
                                            <?php echo $this->Form->end(); ?>
                                        </div>
                                    </div>
                                </div>                  
                            </div>                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="modal fade"id="paymentData" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only"><?php echo __('Fechar'); ?></span></button>
                <h4 class="modal-title"><?php echo __('Escolha um serviço para vender ingressos no guideBAR'); ?></h4>
            </div>
            <div class="modal-body">

                <?php echo $this->Form->create('User', array('id' => 'userPagSeguro', 'url' => array('controller' => 'users', 'action' => 'setDadosPagamento'))); ?>
                <label><?php echo _("Qual serviço deseja utilizar para vender ingressos?"); ?></label>
                <div class="row" >
                    <div class="col-md-6" >
                        <?php echo $this->Form->input('is_selling_ticket_pagseguro', array('label' => __('PagSeguro'), 'id' => 'UserIsSellingTicketPS', 'onchange' => 'activatePanelPagSeguro()', 'type' => 'checkbox', 'div' => 'form-group')); ?>

                        <div class="panel panel-guidebar" id="div_dados_pag_seguro" style="display: none;">
                            <div class="panel-heading">Dados do PagSeguro</div>
                            <div class="panel-body">
                                <?php
                                echo $this->Form->input('email_pagseguro', array('id' => 'UserEmailPagseguro', 'label' => 'E-mail do PagSeguro', 'value' => $event['User']['email_pagseguro'], 'type' => 'email', 'required' => 'required', 'div' => 'form-group'));
                                echo $this->Form->input('token_pagseguro', array('id' => 'UserTokenPagseguro', 'label' => 'Token do PagSeguro', 'value' => $event['User']['token_pagseguro'], 'type' => 'text', 'max-length' => '50', 'required' => 'required', 'div' => 'form-group'));
                                ?>
                            </div>    
                        </div>
                    </div>

                    <div class="col-md-6" >
                        <?php echo $this->Form->input('is_selling_ticket_moip', array('label' => __('Moip'), 'id' => 'UserIsSellingTicketMoip', 'onchange' => 'activatePanelMoip()', 'type' => 'checkbox', 'div' => 'form-group')); ?>

                        <div class="panel panel-guidebar" id="div_dados_moip" style="display: none;">
                            <div class="panel-heading">Dados Moip</div>
                            <div class="panel-body">
                                <?php
                                echo $this->Form->input('email_moip', array('id' => 'UserEmailMoip', 'label' => __('E-mail Principal Moip'), 'value' => $event['User']['email_moip'], 'type' => 'email', 'div' => 'required form-group'));
                                ?>
                            </div>    
                        </div>
                    </div>

                </div>

                <div id="user_payment_message" class="instructions"><?php echo __('Para vender ingressos no guidebar é preciso informar como deseja receber pelas vendas.'); ?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Fechar'); ?></button>
                <button type="button" onclick="saveUserPayment(<?php echo $event['Event']['id']; ?>)" class="btn btn-primary pull-right"><?php echo __('Salvar'); ?></button>
                <?php echo $this->Form->end(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->