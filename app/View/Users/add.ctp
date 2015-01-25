<script src="/js/file-validator.js"></script>
<script>
    $(document).ready(function() {
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
<h1>Criar conta</h1>
<div class="users form">
    <?php echo $this->Form->create('User', array('type' => 'file')); ?>
    <div class="row">
        <div  style="margin: 0 auto; width: 700px;" >
            <div class="panel panel-guidebar">
                <div class="panel-heading">Informações básicas</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php echo $this->Html->Image('icone_transparente.png', array('alt' => 'imagem do usuário', 'style' => "width:100px;", 'id' => "previewImage", 'class' => 'img-responsive event_profile_image thumbnail')); ?>
                        </div>
                        <div class="col-md-8"> 
                            <?php echo $this->Form->input('filename', array('type' => 'file', 'onchange' => "readURL(this);", 'label' => 'Imagem de perfil', 'accept' => 'image/*', 'data-max-size' => '2mb')); ?>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->Form->input('name', array('label' => __('Nome'), 'div' => 'form-group')); ?>
                        </div>                
                        <div class="col-md-6">
                            <?php echo $this->Form->input('email', array('div' => 'form-group', 'label' => __('E-mail'), 'placeholder' => 'E-mail')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->Form->input('password', array('label' => 'Senha', 'div' => 'form-group')); ?>
                        </div>                
                        <div class="col-md-6">
                            <?php echo $this->Form->input('password_confirm', array('label' => 'Digite a senha novamente', 'type' => 'password', 'required' => 'true', 'div' => 'form-group')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            echo $this->Form->input('date_of_birth', array('label' => __('Data de nascimento'),
                                'dateFormat' => 'DMY',
                                'minYear' => date('Y') - 100,
                                'maxYear' => date('Y') ,
                                'div' => 'form-group'));
                            ?> 
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->Form->input('gender', array('label' => __('Sexo'), 'div' => 'form-group', 'options' => array('1' => 'Feminino', '2' => 'Masculino'), 'type' => 'select')); ?>
                        </div>
                    </div>

                    <?php echo $this->Form->end(__('Salvar')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

