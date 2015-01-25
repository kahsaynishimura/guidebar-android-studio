<div class="users index">
    <h2><?php echo __('Users'); ?></h2>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('name', 'Nome'); ?></th>
            <th><?php echo $this->Paginator->sort('email', 'E-mail'); ?></th>
            <th><?php echo $this->Paginator->sort('date_of_birth', 'Data de nascimento'); ?></th>
            <th><?php echo $this->Paginator->sort('token_pagseguro'); ?></th>
            <th><?php echo $this->Paginator->sort('email_pagseguro'); ?></th>
            <th class="actions"><?php echo __('Actions'); ?></th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $this->Html->Image(h($user['User']['filename']), array('alt' => h($user['User']['name']),'class'=>'thumbnail', 'width' => 150, 'height' => 150)); ?></td>
                <td><?php echo h($user['User']['name']); ?>&nbsp;</td>
                <td><?php echo h($user['User']['email']); ?>&nbsp;</td>
                <td><?php echo h($user['User']['date_of_birth']); ?>&nbsp;</td>
                <td><?php echo h($user['User']['token_pagseguro']); ?>&nbsp;</td>
                <td><?php echo h($user['User']['email_pagseguro']); ?>&nbsp;</td>
                <td class="actions">
                    <?php echo $this->Html->link(__('View'), array('action' => 'details', $user['User']['id'])); ?>
                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])); ?>
                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['User']['id']), array(), __('Are you sure you want to delete # %s?', $user['User']['id'])); ?>
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
