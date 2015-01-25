<div class="col-md-3"></div>

<div class="col-md-6">
    <div class="row">

        <?php echo $this->Form->create('User', array('controller' => 'users', 'action' => 'change_password')); ?>
        <fieldset>

            <legend><?php echo __('Alterar senha'); ?></legend>
            <?php
            echo $this->Form->input('password', array('label' => 'Senha'));
            echo $this->Form->input('password_confirm', array('label' => 'Digite a senha novamente', 'type' => 'password', 'required' => 'true'));
            ?>
        </fieldset>
     <div class="row">
        <?php echo $this->Html->link('<< Login', array('controller' => 'users', 'action' => 'login')) ?>
    </div>
        <?php echo $this->Form->end(__('Enviar')); ?>
    </div>
</div>
<div class="col-md-3"></div>