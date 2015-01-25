
<div class="events index">
    <div class="col-md-3">
        <!--        <div class="events form">
        <?php echo $this->Form->create('Event'); ?>
                    <fieldset>
                        <legend><?php //echo __('Busca avançada');  ?></legend>
        <?php
//                echo $this->Form->input('category_id', array('label' => 'Categoria')); 
//                
//                $dt = new DateTime();
//                $options = array(
//                    'label' => 'Data/hora inicial',
//                    'timeFormat' => '24',
//                    'dateFormat' => 'DMY',
//                    'minYear' => date('Y'),
//                    'orderYear' => 'asc',
//                    'selected' => $dt->format('Y-m-d H:i:s')
//                );
//
//                echo $this->Form->input('start_date', $options);
//
//                echo $this->Form->input('state_id', array('label' => 'Estado'), $states);
//                echo $this->Form->input('city_id', array('label' => 'Cidade', 'style' => 'width:100%;'), $cities);
//                echo $this->Form->input('is_open_bar', array('label' => 'Somente open bar'));
        ?>
                    </fieldset> 
        <?php // echo $this->Form->end(__('Buscar')); ?>
        
                </div>-->

        <?php
//        $this->Js->get('#EventStateId')->event('change', $this->Js->request(array(
//                    'controller' => 'cities',
//                    'action' => 'getByStateSearch'
//                        ), array(
//                    'update' => '#EventCityId',
//                    'async' => true,
//                    'method' => 'post',
//                    'dataExpression' => true,
//                    'data' => $this->Js->serializeForm(array(
//                        'isForm' => true,
//                        'inline' => true
//                    ))
//                ))
//        );
        ?>
    </div>  
    <div class="col-md-9">
        <h1><?php echo __($title_events); ?></h1><br />
        <?php foreach ($events as $event): ?>
            <div  class="row">
                <div class="col-md-3">
                    <?php echo $this->Html->image('' . $event['Event']['thumb'], array('class' => 'thumbnail', 'alt' => $event['Event']['filename'], 'width' => 150, 'height' => 'auto', 'url' => array('action' => 'view', $event['Event']['id']))); ?>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <?php echo $this->Html->link(__($event['Event']['name']), array('action' => 'view', $event['Event']['id'])); ?>  
                    </div>
                    <div class="row">               
                        <?php echo $this->Html->link($event['User']['name'], array('controller' => 'users', 'action' => 'details', $event['User']['id'])); ?> 
                    </div> 

                    <div class="row">
                        <?php
                        $dataInicial = date('d/m/Y H:i', strtotime(h($event['Event']['start_date'])));
//                    echo $event['Event']['start_date'];
                        echo h($dataInicial);
                        ?> até <?php
                        $dataFinal = date('d/m/Y H:i', strtotime(h($event['Event']['end_date'])));
                        echo h($dataFinal);
                        ?>
                    </div> 
                    <div class="row">          
                        <?php echo h('Categoria: ' . $event['Category']['name']); ?>               
                    </div> 
                    <div class="row">    
                        <?php echo h('Idade mínima: ' . $event['Event']['minimum_age']); ?>               
                    </div> 
                    <div class="row">
                        <?php echo h($event['Event']['views']); ?>  Visualizações
                    </div>
                    <div class="row"> 
                        
                        <?php echo "Avaliação: " . h(number_format((float)$event['Event']['average_rating'], 2, '.', '')); ?>
                    </div>
                    <div class="row">
                        <span class="bold_text"> <?php
                        if ($event['Event']['is_open_bar']) { 
                            echo 'Open bar';
                        } 
                        ?>
                        </span>
                    </div>
                </div>
            </div>
        <hr>
    <?php endforeach; ?>
        </div>
</div>
</div>