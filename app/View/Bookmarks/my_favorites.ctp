<script>
    $(document).ready(function() {
        $('#meusFavoritos').parent().addClass('active');
    });
</script>
<div class="events index">

    <div class="col-md-12">
        <h1><?php echo __('Favoritos'); ?></h1><br />
        <?php if (empty($bookmarks)): ?>
            <?php echo 'Nenhum resultado encontrado.'; ?>
        <?php else: ?>

            <?php foreach ($bookmarks as $bookmark): ?>
                <div  class="row">
                    <div class="col-md-2">
                        <?php echo $this->Html->image('' . $bookmark['Event']['thumb'], array('class' => 'thumbnail', 'alt' => $bookmark['Event']['thumb'], 'width' => 150, 'height' => 'auto', 'url' => array('controller' => 'events', 'action' => 'view', $bookmark['Event']['id']))); ?>
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <?php echo $this->Html->link(h($bookmark['Event']['name']), array('controller' => 'events', 'action' => 'view', $bookmark['Event']['id'])); ?>  
                        </div>

                        <div class="row">          
                            <?php echo h('Categoria: ' . $bookmark['Event']['Category']['name']); ?>               
                        </div> 
                        <div class="row">    
                            <?php echo h('Idade mínima: ' . $bookmark['Event']['minimum_age']); ?>               
                        </div> 
                        <div class="row">
                            <?php
                            $dataInicial = date('d/m/Y H:i', strtotime(h($bookmark['Event']['start_date'])));
//                    echo $event['Event']['start_date'];
                            echo h($dataInicial);
                            ?> até <?php
                            $dataFinal = date('d/m/Y H:i', strtotime(h($bookmark['Event']['end_date'])));
                            echo h($dataFinal);
                            ?>
                        </div> 
                        <div class="row">
                            <?php echo h($bookmark['Event']['views']); ?>  Visualizações
                        </div>
                        <div class="row"> 
                            <?php echo "Avaliação: " . h(number_format((float) $bookmark['Event']['average_rating'], 2, '.', '')); ?>
                        </div>
                        <div class="row"> 
                            <?php echo "Open bar: " . h($bookmark['Event']['is_open_bar'] == 1 ? "Sim" : "Não"); ?>
                        </div>
                    </div> 
                </div>
                <hr>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>