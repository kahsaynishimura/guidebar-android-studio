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
</script><div class="products form">
    <?php echo $this->Form->create('Product', array('type' => 'file')); ?>
    <fieldset>
        <legend><?php echo __('Adicionar Produto'); ?></legend>
        <div class="row">
            <div class="col-md-5">
                <?php echo $this->Form->input('name', array('div' => 'form-group', 'label' => false, 'placeholder' => 'Nome')); ?>
                <?php echo $this->Form->input('description', array('div' => 'form-group', 'label' => false, 'placeholder' => 'Descrição')); ?>
                <div class='row'>
                    <div class="col-md-6">
                        <?php echo $this->Form->input('price', array('div' => 'form-group', 'label' => false, 'placeholder' => 'Preço')); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $this->Form->input('quantity', array('div' => 'form-group', 'label' => false, 'placeholder' => 'Quantidade')); ?>
                    </div>
                </div> 
                <div class='row'>
                    <div class="col-md-5">
                        <?php echo $this->Form->input('is_active', array('label' => 'Ativo')); ?>
                    </div> 
                    <div class="col-md-7">
                        <div>
                            <?php echo $this->Html->link('Visualizar evento', array('controller' => 'events', 'action' => 'view', $this->request->params['pass'][0])); ?>
                        </div>
                        <p class="instructions">* Você pode cadastrar produtos mais tarde.</p>
                    </div>  
                </div>
            </div>
            <div class="col-md-7">
                <div class="col-md-4">
                    <?php echo $this->Html->Image('Eventos/default.jpg', array('alt' => 'ícone do evento', 'style' => "width:150px;", 'id' => "previewImage", 'class' => 'img-responsive event_profile_image thumbnail')); ?>
                </div>
                <div class="col-md-8">
                    <?php echo $this->Form->input('image', array('type' => 'file', 'onchange' => "readURL(this);", 'accept' => 'image/*', 'data-max-size' => '2mb')); ?>                    
                </div>
            </div>
        </div>

    </fieldset>
    <?php echo $this->Form->end(__('Salvar')); ?>
</div>
