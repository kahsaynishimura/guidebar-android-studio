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

<div class="actions col-md-2">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>

        <li><?php echo $this->Form->postLink(__('Apagar'), array('action' => 'delete', $this->Form->value('Product.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Product.id'))); ?></li>
    </ul>
</div>
<div class="actions col-md-10">
    <div class="products form">
        <?php echo $this->Form->create('Product', array('type' => 'file')); ?>
        <fieldset>
            <legend><?php echo __('Editar Produto'); ?></legend>
            <?php echo $this->Form->input('id'); ?>
            <div class="row">
                <div class="col-md-5">
                    <?php echo $this->Form->input('name', array('div' => 'form-group', 'label' => 'Nome')); ?>
                    <?php echo $this->Form->input('description', array('div' => 'form-group', 'label' => 'Descrição')); ?>
                    <div class='row'>
                        <div class="col-md-6">
                            <?php echo $this->Form->input('price', array('div' => 'form-group', 'label' => 'Preço')); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->Form->input('quantity', array('div' => 'form-group', 'label' => 'Quantidade')); ?>
                        </div>
                    </div> 
                    <?php echo $this->Form->input('is_active', array('label' => 'Ativo')); ?>
                </div>
                <div class="col-md-7">
                    <div class="col-md-4">
                        <?php echo $this->Html->Image(h($this->data['Product']['image']), array('alt' => 'imagem do produto', 'style' => "width:150px;", 'id' => "previewImage", 'class' => 'img-responsive event_profile_image thumbnail')); ?>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('image', array('type' => 'file', 'onchange' => "readURL(this);", 'accept' => 'image/*', 'data-max-size' => '2mb')); ?>                    
                    </div>
                </div>
            </div>
        </fieldset>
        <?php echo $this->Form->end(__('Salvar')); ?>
    </div>
</div>