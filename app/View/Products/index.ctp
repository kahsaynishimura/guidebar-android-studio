<div class="col-md-2">
    <h3>Opções</h3>
    <ul>
        <li><?php echo $this->Form->postLink(__('Excluir evento'), array('controller' => 'events', 'action' => 'delete', $eventId), array(), __('Tem certeza que deseja apagar este evento?')); ?></li>
        <li><?php echo $this->Html->link(__('Editar endereço'), array('controller' => 'events', 'action' => 'buscaEndereco', $eventId)); ?></li>
        <li><?php echo $this->Html->link(__('Editar Produtos'), array('controller' => 'products', 'action' => 'index', $eventId)); ?> </li>
        <li><?php echo $this->Html->link(__('Gerar entradas para impressão'), array('controller' => 'items', 'action' => 'generateTickets', $eventId)); ?> </li>
    </ul>
</div>
<div class="col-md-10">
    <div class="products index row">
        <h1><?php echo __('Produtos'); ?></h1>
        <div class="col-md-5">
            <?php echo $this->Html->link(__('Adicionar produto'), array('action' => 'add', $eventId), array('class' => 'btn btn-default')); ?>
            <?php echo $this->Html->link(__('Visualizar evento'), array('controller' => 'events', 'action' => 'view', $eventId), array('class' => 'btn btn-default')); ?>

        </div>
        <div class="col-md-7">
            <h1 style="font-size: 80%;">Insira os produtos que deseja vender online.<br>
                Ex.: Bebidas.</h1>
        </div>
    </div>
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php echo $this->element('products', $products); ?>
        <?php endif; ?>
    </div>
</div>