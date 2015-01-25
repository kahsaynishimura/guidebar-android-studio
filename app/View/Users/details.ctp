<script>
    $(document).ready(function() {
        $('#viewUser').parent().addClass('active');
    });
</script>
<div class="col-md-12">
    <h1><?php echo h($user['User']['name']); ?></h1> 

    <div class="col-md-2">
        <?php echo $this->Html->Image(h($user['User']['filename']), array('alt' => h($user['User']['name']), 'class' => 'thumbnail', 'width' => 150, 'height' => 150)); ?>
    </div>
    <div class="col-md-9">
        <?php
        $dataNascimento = date('d/m/Y', strtotime(h($user['User']['date_of_birth'])));
        echo __('Data de nascimento: ' . $dataNascimento);
        ?>
        <br />
        <?php echo __('Sexo: '); ?>
        <?php
        if ($user['User']['gender'] == 1) {
            echo __('feminino');
        } else {
            echo __('masculino');
        }
        ?>
        <div class="row"> 
            <div class="col-md-1">
                <?php
                if ($user_login['User']['id'] === $user['User']['id']) {
                    echo $this->Html->link(__('Alterar'), array('action' => 'edit', $user['User']['id']), array('class' => 'btn btn-default'));
                }
                ?> 
            </div>

        </div>
    </div>
</div>
<div class="actions">

</div>