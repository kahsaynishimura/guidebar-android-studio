<div class="col-md-3"></div>

<div class="col-md-6">
    <div class="row">
        <?php echo $this->Form->create('User', array('controller' => 'users', 'action' => 'recover_password')); ?>
        <fieldset>
            <legend>Recuperar acesso</legend>
            <?php echo $this->Form->input('email', array('div' => 'form-group', 'label' => false, 'placeholder' => 'E-mail')); ?>
        </fieldset>
        <?php echo $this->Form->end(__('Enviar')); ?>
    </div>
    <div class="row">
        <?php echo $this->Html->link('<< Login', array('controller' => 'users', 'action' => 'login')) ?>
    </div>
</div>
<div class="col-md-3"></div>