<div class="complaints index">
	<h2><?php echo __('Complaints'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('event_id'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($complaints as $complaint): ?>
	<tr>
		<td><?php echo h($complaint['Complaint']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($complaint['Event']['name'], array('controller' => 'events', 'action' => 'view', $complaint['Event']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($complaint['User']['name'], array('controller' => 'users', 'action' => 'view', $complaint['User']['id'])); ?>
		</td>
		<td><?php echo h($complaint['Complaint']['created']); ?>&nbsp;</td>
		<td><?php echo h($complaint['Complaint']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $complaint['Complaint']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $complaint['Complaint']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>

