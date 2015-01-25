<script>
    $(document).ready(function() {
        $('#minhasCompras').parent().addClass('active');
    });
</script> 
<?php if (empty($purchase)): ?>
    <?php echo __('Você ainda não possui compras.'); ?>
<?php else: ?>
    <div class="row well"> 
        <div class="col-md-2">
            <div class="row">
                <?php echo h($purchase['EventPurchase']['name']); ?>
            </div>
            <div class="row">
                <?php echo $this->Html->image($purchase['EventPurchase']['filename'], array('class' => 'thumbnail', 'width' => 150, 'height' => 'auto')); ?>
            </div>
        </div>
        <div class="col-md-10">
            <div class="row">
                <?php foreach ($purchase['Item'] as $item): ?>
                    <div class="row">

                        <?php echo $item['quantity'] . 'x '; ?>
                        <?php echo $item['Product']['name'] . 'a R$'; ?>

                        <?php echo sprintf('%1.2f', $item['price']) . '= '; ?>
                        <?php echo 'R$' . sprintf('%1.2f', $item['subtotal']); ?>
                    </div>
                <?php endforeach; ?> 
                <?php if (h($purchase['Purchase']['total']) != 0): ?>
                    <div class="row">
                        <?php echo 'Total: ' . h($purchase['Purchase']['total']); ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php echo _('Status da compra: '); ?>
                    <?php
                    echo $this->Html->link(_($transactionStatus[$purchase['Purchase']['item_quantity']]['title']), '#', array(
                        'id' => 'example',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'right',
                        'data-original-title' => $transactionStatus[$purchase['Purchase']['item_quantity']]['description'],
                        'class' => 'tooltip_default',
                    ));
                    ?>
                </div>
                <div class="row">
                    <?php
                    if ($purchase['Purchase']['item_quantity'] == '3' || $purchase['Purchase']['item_quantity'] == '4') {
                        ?>
                        <button class="btn btn-default" data-toggle="modal" data-target="#myModal-<?php echo $purchase['Purchase']['id']; ?>">
                            Código
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="myModal-<?php echo $purchase['Purchase']['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel">Use este código para consumir os itens da compra</h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        echo $this->Html->image("https://chart.googleapis.com/chart?cht=qr&chs=150x150&choe=UTF-8&chl=http://guidebar.com.br/purchases/processCode.json?code=" . $purchase['Purchase']['qr_code']);
                                        ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
                                    </div>
                                </div>
                            </div>
                        </div><!--Modall-->

                        <?php
                    } else {
                        echo $this->Html->link(h('Pagar'), $purchase['Purchase']['payment_url'], array('class' => 'btn btn-default'));
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
<?php endif; ?>



