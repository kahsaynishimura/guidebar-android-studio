<div class="complaints view">
<h2><?php echo __('Complaint'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($complaint['Complaint']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Event'); ?></dt>
		<dd>
			<?php echo $this->Html->link($complaint['Event']['name'], array('controller' => 'events', 'action' => 'view', $complaint['Event']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($complaint['User']['name'], array('controller' => 'users', 'action' => 'view', $complaint['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($complaint['Complaint']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($complaint['Complaint']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
