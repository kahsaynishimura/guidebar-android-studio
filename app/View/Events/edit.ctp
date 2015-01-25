<script src="/js/file-validator.js"></script>
<?php echo $this->Html->css(array('event_form')); ?>

<script type="text/javascript"
        src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBpDqjkodlidnl63noZ7VTt0KjDqY6dUAE&sensor=true">
</script>
<script type="text/javascript"
        src="/js/event_form.js">
</script>
<script type="text/javascript"
        src="/js/tickets_table.js">
</script>
<script>
    function clearAddressFields() {
        $("#place_name").val("");
        $("#street").val("");
        $("#street_number").val("");
        $("#complement").val("");
        $("#neighborhood").val("");
        $("#zip_code").val("");
    }
    $(document).ready(function() {

        loadPreviousTickets();

        initializeMap();
        updateAddressfields();

        $("#txt_ticket_price").keypress(validarDecimal);
        $("#txt_ticket_qty").keypress(holdChars);
        $('input[type=file]').fileValidator({
            onValidation: function(files) {
                $('#errorFile').remove();
                $(this).removeClass('form-error');
                $(this).parent().removeClass('error');
            },
            onInvalid: function(type, file) {
                $(this).val(null);
                $(this).addClass('form-error');
                $(this).parent().addClass('error');
                if ($('#errorFile').html() === undefined) {
                    $(this).after('<div id="errorFile" class="error-message">Aceita apenas os formatos: jpeg, jpg, png, gif, bmp.</div>');
                    $('#errorFile').html('Aceita apenas imagens de até 2Mb nos formatos: jpeg, jpg, png, gif, bmp.');
                    $('#errorFile').addClass('error-message');
                }
            },
            type: 'image'
        });
    });
</script>

<h1>Editar evento</h1>
<div class="form">

    <?php echo $this->Form->create('Event', array('type' => 'file')); ?>
    <?php echo $this->Form->input('id'); ?>
    <div class="panel panel-guidebar">
        <div class="panel-heading">1. Nome e imagem do evento</div>
        <div class="panel-body">
            <div class="col-md-5">
                <div style="width: 100%; margin: 0 auto;" >
                    <?php echo $this->Form->input('name', array('autofocus' => 'autofocus', 'div' => 'form-group', 'label' => 'Nome do evento', 'placeholder' => 'Nome do evento')); ?>  
                </div>
            </div> 
            <div class="col-md-7"> 
                <div class="col-md-4">
                    <?php echo $this->Html->Image($this->request->data['Event']['filename'], array('alt' => 'ícone do evento', 'style' => "width:150px;", 'id' => "previewImage", 'class' => 'img-responsive event_profile_image thumbnail')); ?>
                    <?php echo $this->Html->link("Remover imagem", "#", array('onclick' => "removeImage();return false;")); ?> 
                </div>
                <div class="col-md-8">
                    <?php echo $this->Form->input('filename', array('label' => 'Escolha um ícone para o evento', 'onchange' => "readURL(this);", 'type' => 'file', 'id' => 'uploadImage', 'accept' => 'image/*', 'data-max-size' => '2mb')); ?>  
                    <p class="instructions"><?php echo __("Insira aqui a imagem que aparecerá na lista de eventos e nas redes sociais quando seu evento for compartilhado."); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-guidebar">
                <div class="panel-heading">
                    2. Quando?
                </div>
                <div class="panel-body">
                    <?php
                    $options = array(
                        'label' => 'Data/hora inicial',
                        'timeFormat' => '24',
                        'dateFormat' => 'DMY',
                        'minYear' => date('Y'),
                        'orderYear' => 'asc'
                    );
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            echo $this->Form->input('start_date', $options);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            $options['label'] = 'Data/hora final';
                            echo $this->Form->input('end_date', $options);
                            ?> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-guidebar">
                <div class="panel-heading">
                    3. Ingressos
                </div>
                <div class="panel-body">
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
                            <?php echo $this->Form->input('email_pagseguro', array('type' => 'hidden', 'value' => $promoter['User']['email_pagseguro'], 'id' => 'email_pagseguro')); ?> 
                            <?php echo $this->Form->input('token_pagseguro', array('type' => 'hidden', 'value' => $promoter['User']['token_pagseguro'], 'id' => 'token_pagseguro')); ?> 
                            <?php echo $this->Form->input('is_selling_ticket_pagseguro', array('type' => 'hidden', 'value' => $promoter['User']['is_selling_ticket_pagseguro'], 'id' => 'is_selling_ticket_pagseguro')); ?> 
                            <?php echo $this->Form->input('email_moip', array('type' => 'hidden', 'value' => $promoter['User']['email_moip'], 'id' => 'email_moip')); ?> 
                            <?php echo $this->Form->input('is_selling_ticket_moip', array('type' => 'hidden', 'value' => $promoter['User']['is_selling_ticket_moip'], 'id' => 'is_selling_ticket_moip')); ?> 
                            <center>
                                <?php echo $this->Form->button(__("Adicionar"), array('type' => 'button', 'style' => "font-size: 90%;", 'class' => 'btn btn-default', 'onclick' => "addTicket()", 'div' => 'form-group')); ?>                                
                            </center>
                        </div>
                    </div>
                    <div class="instructions" id="ticket_errors" style="display:none;">Campos obrigatórios: tipo de ingresso, preço e quantidade.</div>
                    <div id="loadingTickets"style="display: none;" ><center><span><img alt=""  src="/img/loading_gif_32x32.gif"></span></center></div>

                    <div id="div_ticket_table">
                        <table class="table table-striped" >
                            <thead>
                                <tr>
                                    <th>Ingresso</th>
                                    <th>Preço</th>
                                    <th>Quantidade</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="ticket_table">

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="panel panel-guidebar">
                <div class="panel-heading">
                    4. Descrição
                </div>
                <div class="panel-body">
                    <?php echo $this->Form->input('description', array('div' => 'form-group', 'label' => 'Descrição')); ?>

                </div>
            </div>
            <div class="panel panel-guidebar">
                <div class="panel-heading">
                    5. Informações adicionais
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php echo $this->Form->input('category_id', array('label' => 'Categoria')); ?>  
                        </div>
                        <div class="col-md-4">
                            <?php echo $this->Form->input('is_open_bar', array('label' => 'Open Bar')); ?>
                        </div> 
                        <div class="col-md-4">
                            <?php echo $this->Form->input('minimum_age', array('value' => '18', 'label' => 'Idade mínima')); ?> 
                        </div> 

                    </div>
                </div>
            </div> 
        </div>
        <div class="col-md-4">
            <div class="panel panel-guidebar">
                <div class="panel-heading">
                    6. Onde?
                </div>
                <div class="panel-body">

                    <div id="divEventAddressLoading" >  </div>
                    <div id="divEventAddress" style="margin-bottom:5px;">  
                        <center>
                            <?php echo $this->Form->input('address_id', array('id' => 'userAddresses', 'name' => 'data[Event][address_id]', 'label' => false, 'onchange' => 'clearAddressFields();updateAddressfields()')); ?>                            
                        </center>
                    </div>
                    <hr>
                    <div id="divAddressForm">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                echo $this->Form->input('place_name', array('id' => 'place_name', 'label' => __('Local'), 'name' => 'data[Event][Address][place_name]',
                                    'value' => (empty($this->request->data['Event']['Address']['place_name']) ? "" : $this->request->data['Event']['Address']['place_name']),
                                    'div' => 'form-group required', 'required' => 'required'));
                                ?> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                echo $this->Form->input('street', array('id' => 'street', 'label' => __('Rua'), 'name' => 'data[Event][Address][street]',
                                    'value' => (empty($this->request->data['Event']['Address']['street']) ? "" : $this->request->data['Event']['Address']['street']), 'div' => 'form-group required', 'required' => 'required'));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <?php
                                echo $this->Form->input('street_number', array('id' => 'street_number', 'label' => __('Número'),
                                    'name' => 'data[Event][Address][street_number]',
                                    'value' => (empty($this->request->data['Event']['Address']['street_number']) ? "" : $this->request->data['Event']['Address']['street_number']), 'div' => 'form-group required address_field', 'required' => 'required'));
                                ?>
                            </div>

                            <div class="col-md-7">
                                <?php
                                echo $this->Form->input('complement', array('id' => 'complement', 'label' => __('Complemento'), 'name' => 'data[Event][Address][complement]',
                                    'value' => (empty($this->request->data['Event']['Address']['complement']) ? "" : $this->request->data['Event']['Address']['complement']), 'div' => 'form-group'));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <?php
                                echo $this->Form->input('neighborhood', array('id' => 'neighborhood', 'label' => __('Bairro'), 'name' => 'data[Event][Address][neighborhood]',
                                    'value' => (empty($this->request->data['Event']['Address']['neighborhood']) ? "" : $this->request->data['Event']['Address']['neighborhood']), 'div' => 'form-group required', 'required' => 'required'));
                                ?>
                            </div>
                            <div class="col-md-7">
                                <?php
                                echo $this->Form->input('zip_code', array('id' => 'zip_code', 'label' => __('CEP'), 'name' => 'data[Event][Address][zip_code]',
                                    'value' => (empty($this->request->data['Event']['Address']['zip_code']) ? "" : $this->request->data['Event']['Address']['zip_code']), 'div' => 'form-group required', 'required' => 'required'));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">                       
                                <?php
                                echo $this->Form->input('state_id', array('id' => 'AddressStateId', 'label' => 'Estado', 'name' => 'data[Address][state_id]',
                                    'value' => (empty($this->request->data['Address']['state_id']) ? "" : $this->request->data['Address']['state_id'])));
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">       
                                <?php
                                echo $this->Form->input('city_id', array('id' => 'AddressCityId', 'onblur' => 'codeAddress()', 'label' => 'Cidade',
                                    'name' => 'data[Event][Address][city_id]', 'value' => (empty($this->request->data['Event']['Address']['city_id']) ? "" : $this->request->data['Event']['Address']['city_id'])));
                                ?>
                            </div>
                        </div>
                        <div style="text-align: center;margin-top: 10px;">
                            <?php echo $this->Form->button('Localizar endereço no mapa', array('class' => 'btn btn-default', 'type' => 'button', 'onclick' => 'codeAddress()')); ?>
                        </div>
                    </div> 

                    <div id="divAddressPresentation"></div>

                    <div id="divMap" style="height: 300px;width: 300px;margin-top:15px;">
                        <div id="map_canvas" style=" height:100%;"></div>
                    </div>
                    <div id="divMapInstructions" style="display:none" class="instructions">Verifique no mapa se a posição está correta</div>

                </div>
            </div> 
        </div>
    </div>

    <?php
    echo $this->Form->input('is_remove_image', array('type' => 'hidden', 'name' => 'data[Event][is_remove_image]', 'id' => 'is_remove_image'));
    echo $this->Form->input('tickets', array('type' => 'hidden', 'name' => 'data[Event][Tickets]', 'value' => (empty($this->request->data['Event']['Tickets']) ? "" : $this->request->data['Event']['Tickets']), 'id' => 'eventTickets'));
    echo $this->Form->input('latitude', array('type' => 'hidden', 'name' => 'data[Event][Address][latitude]', 'value' => (empty($this->request->data['Event']['Address']['latitude']) ? "" : $this->request->data['Event']['Address']['latitude']), 'id' => 'latitude'));
    echo $this->Form->input('longitude', array('type' => 'hidden', 'name' => 'data[Event][Address][longitude]', 'value' => (empty($this->request->data['Event']['Address']['longitude']) ? "" : $this->request->data['Event']['Address']['longitude']), 'id' => 'longitude'));
    echo $this->Form->input('is_active', array('type' => 'hidden', 'name' => 'data[Event][is_active]', 'value' => (empty($this->request->data['Event']['is_active']) ? "" : $this->request->data['Event']['is_active']), 'id' => 'is_active'));
    ?> 
    <?php
    echo $this->Form->button(__("Cancelar"), array('type' => 'button', 'onclick' => 'window.history.back()', 'class' => 'btn btn-default'));
    echo $this->Form->input(__('Salvar'), array('label' => false, 'div' => false, 'alt' => 'Salvar as alterações', 'type' => 'button', 'class' => 'btn btn-default'));
    ?>
    <?php echo $this->Form->end(); ?>

</div>

<?php
$this->Js->get('#AddressStateId')->event('change', $this->Js->request(array(
            'controller' => 'cities',
            'action' => 'getByState'
                ), array(
            'update' => '#AddressCityId',
            'async' => true,
            'method' => 'post',
            'dataExpression' => true,
            'data' => $this->Js->serializeForm(array(
                'isForm' => true,
                'inline' => true
            ))
        ))
);
?>

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
                                echo $this->Form->input('email_pagseguro', array('id' => 'UserEmailPagseguro', 'label' => 'E-mail do PagSeguro', 'value' => $promoter['User']['email_pagseguro'], 'type' => 'email', 'required' => 'required', 'div' => 'form-group'));
                                echo $this->Form->input('token_pagseguro', array('id' => 'UserTokenPagseguro', 'label' => 'Token do PagSeguro', 'value' => $promoter['User']['token_pagseguro'], 'type' => 'text', 'max-length' => '50', 'required' => 'required', 'div' => 'form-group'));
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
                                echo $this->Form->input('email_moip', array('id' => 'UserEmailMoip', 'label' => __('E-mail Principal Moip'), 'value' => $promoter['User']['email_moip'], 'type' => 'email', 'div' => 'required form-group'));
                                ?>
                            </div>    
                        </div>
                    </div>

                </div>

                <div id="user_payment_message" class="instructions"><?php echo __('Para vender ingressos no guidebar é preciso informar como deseja receber pelas vendas.'); ?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Fechar'); ?></button>
                <button type="button" onclick="saveUserPayment()" class="btn btn-primary pull-right"><?php echo __('Salvar'); ?></button>
                <?php echo $this->Form->end(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->