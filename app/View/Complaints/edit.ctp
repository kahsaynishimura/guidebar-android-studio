<div class="complaints form">
<?php echo $this->Form->create('Complaint'); ?>
	<fieldset>
		<legend><?php echo __('Edit Complaint'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('event_id');
		echo $this->Form->input('user_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Salvar')); ?>
</div>

