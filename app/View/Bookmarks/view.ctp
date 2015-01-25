<div class="bookmarks view">
<h2><?php echo __('Bookmark'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($bookmark['Bookmark']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('User'); ?></dt>
		<dd>
			<?php echo $this->Html->link($bookmark['User']['name'], array('controller' => 'users', 'action' => 'view', $bookmark['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Event'); ?></dt>
		<dd>
			<?php echo $this->Html->link($bookmark['Event']['name'], array('controller' => 'events', 'action' => 'view', $bookmark['Event']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($bookmark['Bookmark']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($bookmark['Bookmark']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>

