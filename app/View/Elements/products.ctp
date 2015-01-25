 
<script >
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
        $.ajax({
            type: 'POST',
            url: '/items/add',
            data: {
                Item: {
                    quantity: Math.round($('#itemQuantity-' + key).val()),
                    product_id: $('#itemQuantity-' + key).attr('data-id')
                }
            }
            ,
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
    <?php
    $i = 0;
    foreach ($products as $product):
        $i++;
        if (($i % 4) == 0) {
            echo "\n<div class=\"row\">\n\n";
        }
        ?>
        <div class="col col-sm-3">
            <?php 
//            echo $this->Html->image('' . $product['Product']['image'], array('url' => array('controller' => 'products', 'action' => 'view', $product['Product']['id']), 'alt' => $product['Product']['name'], 'width' => '80%', 'class' => 'thumbnail')); 
            ?>
            <div class="col-md-12" style="text-align: center;overflow: hidden;">
                <?php
                $name = $product['Product']['name'];
                if ((strlen($name)) > 25) {
                    $name = (substr($name, 0, 22));
                    $name.="...";
                }
                echo $this->Html->link($name, array('controller' => 'products', 'action' => 'view', $product['Product']['id']));
                ?>
                <p>R$<?php echo $product['Product']['price']; ?></p>
            </div>


            <div id="addToCart_<?php echo $product['Product']['id']; ?>">
                <?php echo $this->Form->create('Item', array('id' => 'itemsForm-' . $product['Product']['id'])); ?>
                <?php
                echo $this->Form->input('quantity', array('type' => 'number', 'min' => '1', 'max' => '99', 'class' => 'itemQuantity', 'id' => 'itemQuantity-' . $product['Product']['id'], 'data-id' => $product['Product']['id'], 'label' => 'Quantidade'));
                echo $this->Form->submit('Comprar', array('onclick' => 'enviar(' . $product['Product']['id'] . ')', 'class' => 'btn btn-primary', 'type' => 'button', 'id' => 'btnAddToCart-' . $product['Product']['id']));
                echo $this->Form->end();
                ?>
            </div>
        </div>
        <?php
        if (($i % 4) == 0) {
            echo "\n</div>\n\n";
        }
    endforeach;
    ?>
</div>
