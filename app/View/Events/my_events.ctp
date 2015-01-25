<script>
    $(document).ready(function () {
        $('#meusEventos').parent().addClass('active');
    }); 
</script>
<div class="events index">
    <div class="events ">
        <!--  <div class="col-md-3"> 
        <?php // echo $this->Form->create('Event'); ?>
                <fieldset>
                     <legend><?php // echo __('Busca avançada');                                                 ?></legend>-->
        <?php 
//                echo $this->Form->input('category_id', array('label' => 'Categoria')); 
//                $dt = new DateTime();
//                $options = array(
//                    'label' => 'Data/hora inicial',
//                    'timeFormat' => '24',
//                    'dateFormat' => 'DMY',
//                    'minYear' => date('Y'),
//                    'orderYear' => 'asc',
//                    'selected' => $dt->format('Y-m-d H:i:s')
//                );
//                echo $this->Form->input('start_date', $options);
//
//                echo $this->Form->input('state_id', array('label' => 'Estado'), $states);
//                echo $this->Form->input('city_id', array('label' => 'Cidade', 'style' => 'width:100%;'), $cities);
//                echo $this->Form->input('is_open_bar', array('label' => 'Somente open bar'));
        ?>
        <!--            </fieldset> 
        <?php // echo $this->Form->end(__('Buscar')); ?>
        
                </div>-->

        <?php
        $this->Js->get('#EventStateId')->event('change', $this->Js->request(array(
                    'controller' => 'cities',
                    'action' => 'getByStateSearch'
                        ), array(
                    'update' => '#EventCityId',
                    'async' => true,
                    'method' => 'post',
                    'dataExpression' => true,
                    'data' => $this->Js->serializeForm(array(
                        'isForm' => true,
                        'inline' => true
                    ))
                ))
        );
        ?>
        <!--</div>-->
        <div class="col-md-12">
            <h1><?php echo h($title); ?></h1><br />
            <?php if (empty($events)): ?>
                <?php echo 'Nenhum resultado encontrado.'; ?>
            <?php else: ?>
                <div id="event_list">
                    <?php $i = 0; ?>
                    <?php foreach ($events as $event): ?>
                        <?php if ($i % 4 == 0): ?>

                            <div  class="row">

                        <?php endif; ?>
                        <?php $i++; ?>

                        <div class="col-md-3 event_item">
                            <div class="panel panel-guidebar">
                                 <div class="panel-heading" style="heigth:150px">
                                     <?php echo $this->Html->Image('' . $event['Event']['thumb'], array('alt' => $event['Event']['thumb'], 'style'=>'margin-left: auto; margin-right:auto;','class' => 'img-responsive')); ?>
                                 </div>
                                 <div class="panel-body">
                                     <?php echo $this->Html->link($event['Event']['name'], array('action' => 'manage', $event['Event']['id'])); ?>  

                                     <div>
                                         <?php echo __("Categoria: ") . $event['Category']['name']; ?>               
                                     </div>
                                     <div>
                                         <?php echo __("Data: ") . date('d/m/Y H:i', strtotime(h($event['Event']['start_date']))); ?>               
                                     </div>
                                     <div>
                                         <?php echo $this->Html->link('<i class="glyphicon glyphicon-new-window"></i> Visualizar evento', array('controller' => 'events', 'action' => 'view', $event['Event']['id']), array('escape' => false, 'target' => '_blank')); ?> 
                                     </div>
                                     <div>
                                         <?php echo $this->Html->link('<i class="glyphicon glyphicon-usd"></i> Vendas', array('controller' => 'purchases', 'action' => 'sales', h($event['Event']['id'])), array('escape' => false)); ?>
                                     </div>
                                     <div>
                                         <?php echo $this->Html->link('<i class="glyphicon glyphicon-edit"></i> Editar evento', array('controller' => 'events', 'action' => 'edit', h($event['Event']['id'])), array('escape' => false)); ?>
                                     </div>
                                     <div>
                                         <?php echo $this->Html->link('<i class="glyphicon glyphicon-cog"></i> Administrar evento', array('controller' => 'events', 'action' => 'manage', h($event['Event']['id'])), array('escape' => false)); ?>
                                     </div>
                                 </div>

                            </div>

                        </div>

                        <?php if ($i % 4 == 0): ?>
    
                            </div>

                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>