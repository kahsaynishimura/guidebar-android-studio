<div class="col-md-12">
    <h4><?php echo __("<i class='glyphicon glyphicon-tags'></i> Entradas geradas anteriormente"); ?></h4>
</div>
<div class="col-md-12">
    <table>
        <thead>
        <th>Nome</th>
        <th>Preço</th>
        <th>Validados</th>
        <th>Criado em</th>
        </thead>
        <tbody>
            <?php $totalValidated = $totalQuantity = 0; ?>
            <?php foreach ($event['previous_tickets'] as $product): ?>
                <tr>
                    <td>
                        <?php echo h($product['Product']['name']); ?>
                    </td>
                    <td>
                        R$<?php echo $product['Product']['price']; ?>
                    </td>
                    <td>
                        <?php
                        $validated = 0;
                        foreach ($product['Item'] as $key => $value) {
                            $validated+=$value['validated'];
                        }
                        echo $validated . '/' . $product['Product']['quantity'];
                        $totalValidated+=$validated;
                        $totalQuantity+=$product['Product']['quantity'];
                        ?>
                    </td>
                    <td>
                        <?php echo $product['Product']['created']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total:</td>
                <td></td>
                <td><?php echo $totalValidated . '/' . $totalQuantity; ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>