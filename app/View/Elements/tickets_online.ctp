<script>
    $(document).ready(function() {
        $("#purchaseForm").submit(function(event) {
            var itemsToBuy = [];
            var noItems = $("#purchaseForm").find("select").length;
            for (var x = 0; x < noItems; x++) {
                if ($("#purchaseForm").find("select").get(x).selectedIndex !== 0) {
                    itemsToBuy.push({'Item': {
                            'quantity': $("#purchaseForm").find("select").get(x).selectedIndex,
                            'product_id': $("#purchaseForm").find("select").get(x).getAttribute("data-product-id")
                        }});
                }
            }
            if (itemsToBuy.length > 0) {
                $("#selectedItems").val(JSON.stringify(itemsToBuy));
                $("#purchaseForm").prop("target", "_blank");
            } else {
                event.preventDefault();
                $("#div_purchase_errors").html("Escolha a quantidade dos ingressos que deseja comprar");
                $("#div_purchase_errors").show();
            }

        });
        $("#formMoip").submit(function(event) {
            var itemsToBuy = [];
            var noItems = $("#purchaseForm").find("select").length;
            for (var x = 0; x < noItems; x++) {
                if ($("#purchaseForm").find("select").get(x).selectedIndex !== 0) {
                    itemsToBuy.push({'Item': {
                            'quantity': $("#purchaseForm").find("select").get(x).selectedIndex,
                            'product_id': $("#purchaseForm").find("select").get(x).getAttribute("data-product-id")
                        }});
                }
            }
            if (itemsToBuy.length > 0) {

                //salvar os dados da compra no banco antes de encaminhar para o site do moip
                $("#selectedItems").val(JSON.stringify(itemsToBuy));
                var formData = $("#purchaseForm").serialize();
                $.ajax({
                    type: 'POST',
                    url: '/purchases/addMoip',
                    data: formData,
                    async: false,
                    dataType: "json",
                    success: function(response, textStatus, xhr) {
                        if (response.success && response.valor !== "0") {
                            $("#id_carteira_moip").val(response.id_carteira);
                            $("#pagador_email_moip").val(response.email_comprador);
                            $("#id_transacao_moip").val(response.id_transacao);
                            $("#nome_moip").val(response.nome);
                            $("#valor_moip").val(response.valor);
                            $("#descricao_moip").val(response.descricao);
                        } else {

                            event.preventDefault();
                            $('#email_message').html('Desculpe, não foi possível enviar esta mensagem');
                            $('#email_message').show();
                        }
                    },
                    error: function(xhr, textStatus, error) {
                        alert("Ocorreu um erro");
                    }
                });

            } else {
                event.preventDefault();
                $("#div_purchase_errors").html("Escolha a quantidade dos ingressos que deseja comprar");
                $("#div_purchase_errors").show();
            }

        });
    });

</script>
<div class="row">

    <div class="col-md-12">
        <div id="div_purchase_errors" class="instructions" style="display: none;">

        </div>

        <?php echo $this->Form->create("Purchase", array('url' => array('controller' => 'purchases', 'action' => 'add'), 'id' => 'purchaseForm', 'target' => '_blank')); ?>
        <table>
            <thead>
            <th>Nome</th>
            <th>Preço</th>
            <th>Quantidade</th>
            </thead>
            <tbody> 
                <?php echo $this->Form->input('event_id', array('name' => 'data[Purchase][event_id]', 'type' => 'hidden', 'value' => $products[0]['Product']['event_id'])); ?>
                <?php echo $this->Form->input('selectedItems', array('name' => 'data[Purchase][selectedItems]', 'type' => 'hidden', 'id' => 'selectedItems')); ?>
                <?php foreach ($products as $key => $product): ?>
                    <tr>
                        <td>
                            <?php echo h($product['Product']['name']); ?>
                        </td>
                        <td>
                            R$<?php echo $product['Product']['price']; ?>
                        </td>
                        <td>
                            <?php if ($product['Product']['quantity_available'] > 0 && $product['Product']['is_active']): ?>
                                <select style="width:60px;" data-product-id="<?php echo $products[$key]['Product']['id'] ?>">
                                    <?php for ($i = 0; $i <= $product['Product']['quantity_available']; $i++): ?> 
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?> 
                                </select>
                            <?php else: ?>
                                <?php echo __("Encerrado"); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        echo $this->Form->input('event_id', array('type' => 'hidden', 'id' => 'PurchaseEventId', 'value' => $event['Event']['id']));
        ?>
        <?php if ($is_selling_pagseguro): ?>
            <?php echo $this->Form->end("Comprar com PagSeguro"); ?>
        <?php else: ?>
            <?php echo $this->Form->end(); ?>
        <?php endif; ?>
        <?php if ($is_selling_moip): ?>
            <?php echo $this->Form->create("Purchase", array('url' => "https://www.moip.com.br/PagamentoMoIP.do", 'id' => 'formMoip', 'target' => '_blank')); ?>
            <?php echo $this->Form->input('id_carteira_moip', array('id' => 'id_carteira_moip', 'name' => 'id_carteira', 'type' => 'hidden', 'value' => $event['User']['email_moip'])); ?>
            <?php echo $this->Form->input('valor_moip', array('id' => 'valor_moip', 'name' => 'valor', 'type' => 'hidden', 'value' => "")); ?>
            <?php echo $this->Form->input('nome_moip', array('id' => 'nome_moip', 'name' => 'nome', 'type' => 'hidden', 'value' => "Teste de compra moip")); ?>
            <?php echo $this->Form->input('id_transacao_moip', array('id' => 'id_transacao_moip', 'name' => 'nome', 'type' => 'hidden', 'value' => "Teste de compra moip")); ?>
            <?php echo $this->Form->input('descricao_moip', array('id' => 'descricao_moip', 'name' => 'nome', 'type' => 'hidden', 'value' => "Teste de compra moip")); ?>
            <?php echo $this->Form->input('pagador_email_moip', array('id' => 'pagador_email_moip', 'name' => 'nome', 'type' => 'hidden', 'value' => "Teste de compra moip")); ?>
            <?php echo $this->Form->end("Comprar com Moip"); ?>
        <?php endif; ?>
    </div>
</div>