<div class="col-md-12">
    <h1><?php echo __('Entradas geradas'); ?></h1>
</div> 

<table>
    <tbody> 
        <?php
        $i = 0;
        foreach ($tickets as $ticket):
            if (($i % 4) == 0) {
                echo "<tr>";
            }
            ?>
        <td>
            <div><img src="<?php echo 'https://chart.googleapis.com/chart?cht=qr&chs=150x150&choe=ISO-8859-1&chl=http://guidebar.com.br/purchases/processCode?code=' . $ticket['Purchase']['qr_code']; ?>"/></div> 
            <div class="eventName"><?php echo $this->Html->link($ticket['Purchase']['EventPurchase']['name'], array('controller' => 'events', 'action' => 'view', $ticket['Purchase']['EventPurchase']['id'])); ?></div>
            <div><?php echo $ticket['Product']['name']; ?></div>
            <div>
                <?php
                $dataInicial = date('d/m/Y', strtotime(h($ticket['Purchase']['EventPurchase']['start_date'])));
                echo h($dataInicial);
                ?>
            </div>
            <div>
                <?php
                $address = $ticket['Purchase']['EventPurchase']['Address'];
                $local = $address['street'] . ', ' . $address['street_number'] . '-' .
                        $address['neighborhood'] . ' ' . $address['complement'] . ' ';

                $local .= $address['City']['name'] .
                        '/' . $address['City']['State']['uf'];

                ?>
                <span style="font-weight: bold;"><?php echo __("Local: "); ?></span> 
                <?php echo $local; ?>
            </div>

            <div>
                <span style="font-weight: bold;"><?php echo __("Valor: ");?></span> 
                <?php echo __($ticket['Purchase']['total']); ?>
            </div>
            <div>
                <?php  echo $this->Html->image('login_guidebar_form.png',array('class'=>'logo')); ?>
            </div>
            
        </td>

        <?php
        $i++;
        if (($i % 4) == 0 || $i == sizeof($tickets)) {
            echo "</tr>";
        }
    endforeach;
    ?>
</tbody>
</table>

