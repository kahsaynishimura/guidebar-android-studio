
<script type="text/javascript">

    $(document).ready(function() {
        $('.itemQuantity').on('keypress', function(event) {
            if (event.keyCode == 13) {
                return true;
            }
            return (/\d/.test(String.fromCharCode(event.keyCode)));
        });
        $('.itemQuantity').on('keyup change', function(event) {
            var quantity = Math.round($(this).val());
            if ((event.keyCode == 46 || event.keyCode == 8) && quantity > 0) {
            } else {
                if (/\d/.test(String.fromCharCode(event.keyCode)) === false) {
                    return false;
                }
            }
        });
    });

    function enviar(key) {
        var formData = $("#itemsForm-" + key).serialize();

        $.ajax({
            type: 'POST',
            url: '/items/add',
            data: {
                Item: {
                    quantity: Math.round($('#itemQuantity-' + key).val()),
                    product_id: $('#itemQuantity-' + key).attr('data-id')
                }
            },
            success: function(response, textStatus, xhr) {
                $('#addToCart_' + key).html(response);
                $('#itemQuantity-' + key).val('');
                if (response.indexOf('error-message') == -1) {
                    alert('Produto adicionado ao carrinho');
                }
            },
            error: function(xhr, textStatus, error) {
                $('#itemQuantity-' + key).val('');
                window.location = '/users/login';
            }
        });
        return false;
    }

</script>
<div class="row">
    <h1><?php echo __($product['Product']['name']); ?></h1>
    <div class="col-md-4">
        <div class="products view">
            <dl>
                <?php echo $this->Html->Image('' . $product['Product']['image'], array('alt' => $product['Product']['name'], 'width' => 300, 'height' => 'auto', 'class' => 'thumbnail')); ?>
                <dt></dt>
                <dd>
                    <?php echo $this->Html->link(__('< Voltar para o evento'), array('controller' => 'events', 'action' => 'view', $product['Event']['id'])); ?>
                    &nbsp;
                </dd>

            </dl>
        </div>   
    </div>
    <div class="col-md-7">  
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($product['Event']['user_id'] == $user_login['User']['id']) {
                    echo $this->Html->link(__('Editar produto'), array('controller' => 'products', 'action' => 'edit', $product['Product']['id']));
                    echo '<br />';
                }
                ?>
                <?php echo __('Descrição: '); ?>
                <?php echo h($product['Product']['description']); ?>
                <br />
                <?php echo __('Preço: '); ?>
                <?php echo h($product['Product']['price']); ?>
                <br />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 form" id="addToCart_<?php echo $product['Product']['id']; ?>">
                <?php echo $this->Form->create('Item', array('id' => 'itemsForm-' . $product['Product']['id'])); ?>
                <?php
                echo $this->Form->input('quantity', array('type' => 'number', 'min' => '1', 'max' => '99', 'class' => 'itemQuantity', 'id' => 'itemQuantity-' . $product['Product']['id'], 'data-id' => $product['Product']['id'], 'label' => 'Quantidade'));
                echo $this->Form->submit('Adicionar ao carrinho', array('onclick' => 'enviar(' . $product['Product']['id'] . ')', 'class' => 'btn btn-primary', 'type' => 'button', 'id' => 'btnAddToCart-' . $product['Product']['id']));
                echo $this->Form->end();
                ?>
            </div>
        </div>

    </div>

</div>
