<div style="text-align:center;">
    <center>
        <?php echo $this->Html->image('icon.png', array('style' => 'width:60px;')); ?>        
    </center>
</div>
<table id="table_attendees">
    <thead>
        <tr>
            <th></th>
            <th>Nome</th>
            <th>E-mail</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($attendances as $key => $attendance): ?>
            <tr>    
                <td>
                    <?php echo $key+1; ?>
                </td>
                <td>
                    <?php echo $attendance['User']['name']; ?>
                </td>
                <td>
                    <?php echo $attendance['User']['email']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

