<?php echo $this->Form->create('Item', array('id' => 'itemsForm-' . $product['Product']['id'])); ?>
<?php

echo $this->Form->input('quantity', array('type' => 'number', 'min' => '1', 'max' => '99', 'class' => 'itemQuantity', 'id' => 'itemQuantity-' . $product['Product']['id'], 'data-id' => $product['Product']['id'], 'label' => 'Quantidade'));
echo $this->Form->submit('Adicionar ao carrinho', array('onclick' => 'enviar(' . $product['Product']['id'] . ')', 'class' => 'btn btn-primary', 'type' => 'button', 'id' => 'btnAddToCart-' . $product['Product']['id']));
echo $this->Form->end();
?>