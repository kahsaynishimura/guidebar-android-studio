<div class="row well sales">
    <?php if (empty($purchases)): ?>
        <?php echo __('Nenhum resultado encontrado'); ?>
    <?php else: ?>
        <?php
        $totalPaid = 0;
        $totalUnpaid = 0;
        ?>
        <div class="col-md-6">
            <?php foreach ($purchases as $purchase): ?>
                <div class="row">
                    <div class="col-md-2">
                        <?php
                        echo $this->Html->image('' . $purchase['UserBuy']['filename'], array(
                            'url' => array('controller' => 'users', 'action' => 'view', $purchase['UserBuy']['id']),
                            'alt' => $purchase['UserBuy']['name'], 'width' => 50, 'height' => 50,
                            'class' => 'thumbnail'));
                        ?>
                    </div>
                    <div class="col-md-10"> 
                        <?php echo __($purchase['UserBuy']['name']); ?>
                    </div>
                </div>
                <?php foreach ($purchase['Item'] as $item): ?>

                    <div class="row">
                        <?php echo $item['quantity'] . 'x '; ?>
                        <?php echo $item['Product']['name'] . 'a R$'; ?>
                        <?php echo sprintf('%1.2f', $item['price']) . '= '; ?>
                        <?php echo 'R$' . sprintf('%1.2f', $item['subtotal']); ?>
                    </div>
                <?php endforeach; ?> 
                <div class="row">
                    <?php echo 'Total: ' . h($purchase['Purchase']['total']); ?>
                </div> 
                <div class="row">
                    <?php echo _('Status da compra: '); ?>
                    <?php echo $transactionStatus[$purchase['Purchase']['item_quantity']]['title']; ?>
                    <?php
                    if ($purchase['Purchase']['item_quantity'] == 3 || $purchase['Purchase']['item_quantity'] == 4) {
                        $totalPaid+=$purchase['Purchase']['total'];
                    } else {
                        $totalUnpaid+=$purchase['Purchase']['total'];
                    }
                    ?>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>
        <div class="col-md-6">
            <section class="well">
                <h2>Total</h2>
                <div class="col-md-6"><?php echo 'Pago: R$ ' . sprintf('%1.2f', $totalPaid); ?></div>
                <div class="col-md-6"><?php echo 'Não pago: R$ ' . sprintf('%1.2f', $totalUnpaid); ?></div>
            </section>
        </div>
    <?php endif; ?>
</div>