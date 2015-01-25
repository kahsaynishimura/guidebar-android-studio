<script src="/js/file-validator.js"></script>
<script>
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
        activatePanelPagSeguro();
        activatePanelMoip();
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

<h1>Editar dados do perfil</h1>
<div class="users form">
    <?php echo $this->Form->create('User', array('type' => 'file')); ?>
    <div class="col-md-5">
        <div class="panel panel-guidebar">
            <div class="panel-heading">Informações básicas</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php echo $this->Html->Image(h($this->data['User']['filename']), array('alt' => 'imagem do usuário', 'style' => "width:150px;", 'id' => "previewImage", 'class' => 'img-responsive event_profile_image thumbnail')); ?>
                    </div>
                    <div class="col-md-8"> 
                        <?php echo $this->Form->input('filename', array('type' => 'file', 'onchange' => "readURL(this);", 'label' => 'Imagem de perfil', 'accept' => 'image/*', 'data-max-size' => '2mb')); ?>
                    </div> 
                </div>

                <?php
                echo $this->Form->input('id');
                echo $this->Form->input('name', array('label' => __('Nome'), 'div' => 'form-group'));
                echo $this->Form->input('date_of_birth', array('label' => __('Data de nascimento'), 'div' => 'form-group'));
                $options = array('1' => 'Feminino', '2' => 'Masculino');
                echo $this->Form->input('gender', array('label' => __('Sexo'), 'div' => 'form-group', 'options' => $options, 'type' => 'select'));
                ?>
                <label ><?php echo _("Qual serviço deseja utilizar para vender ingressos?"); ?></label>
                <?php
                echo $this->Form->input('is_selling_ticket_pagseguro', array('label' => __('PagSeguro'), 'id' => 'UserIsSellingTicketPS', 'onchange' => 'activatePanelPagSeguro()', 'type' => 'checkbox', 'div' => 'form-group'));
                echo $this->Form->input('is_selling_ticket_moip', array('label' => __('Moip'), 'id' => 'UserIsSellingTicketMoip', 'onchange' => 'activatePanelMoip()', 'type' => 'checkbox', 'div' => 'form-group'));
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-7" >
        <div class="panel panel-guidebar" id="div_dados_pag_seguro" style="display: none;">
            <div class="panel-heading">Dados do PagSeguro</div>
            <div class="panel-body">
                <?php
                echo $this->Form->input('email_pagseguro', array('id' => 'UserEmailPagseguro', 'type' => 'email', 'label' => __('E-mail PagSeguro'), 'div' => 'required form-group'));
                echo $this->Form->input('token_pagseguro', array('id' => 'UserTokenPagseguro', 'label' => __('Token PagSeguro'), 'div' => 'required form-group'));
                ?>
            </div>    
        </div>
        <div class="panel panel-guidebar" id="div_dados_moip" style="display: none;">
            <div class="panel-heading">Dados Moip</div>
            <div class="panel-body">
                <?php
                echo $this->Form->input('email_moip', array('id' => 'UserEmailMoip', 'type' => 'email', 'label' => __('E-mail Principal Moip'), 'div' => 'required form-group'));
                ?>
            </div>    
        </div>
    </div>
    <?php echo $this->Form->end(__('Salvar')); ?>
</div>

