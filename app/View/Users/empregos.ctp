
<div> <?php echo $this->Html->link('GuideBar Home', array('controller' => 'users', 'action' => 'login'), array('escape' => false)); ?> </div>
<div class="col-md-3"></div>

<div class="col-md-6">
    <?php echo $this->Form->create('User', array('id' => 'form1')); ?>
    <fieldset>
        <legend><?php echo __('Empregos'); ?></legend>
        <?php
        echo $this->Form->input('email', array('div' => 'form-group', 'label' => false, 'placeholder' => 'E-mail'));
        echo $this->Form->input('name', array('div' => 'form-group', 'label' => false, 'placeholder' => 'Nome'));
        echo $this->Form->input('description', array('div' => 'form-group', 'label' => false, 'placeholder' => 'Conte o quanto está interessado em trabalhar conosco.'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Salvar')); ?>
</div>
<div class="col-md-3"></div>