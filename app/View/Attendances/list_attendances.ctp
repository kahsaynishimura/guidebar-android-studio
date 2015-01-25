<?php
$i = 0;
?>
<script>
    document.getElementById('btnAttendance').innerHTML = "Participar";
//    $('#btnAttendance').val('Participar');
    $("#attendanceForm").attr('action', '/attendances/add');
</script>
<?php
foreach ($attendances as $attendance):
    if ($attendance['User']['id'] == $user_login['User']['id']):
        ?>
        <script>
            document.getElementById('btnAttendance').innerHTML = "Cancelar participação";
        //            $('#btnAttendance').val('Cancelar participação');

            $("#attendanceForm").attr('action', '/attendances/delete/' +<?php echo $attendance['Attendance']['id'] ?>);
        </script>
        <?php
    endif;
    $i++;
    if (($i % 12) == 0) {
        echo "\n<div class=\"row\">\n\n";
    }
    ?>
    <div class="col col-sm-1">
        <?php
        echo $this->Html->image('' . $attendance['User']['filename'], array(
            'url' => array('controller' => 'users', 'action' => 'details', $attendance['User']['id']),
            'alt' => $attendance['User']['name'], 'width' => 50, 'height' => 50,
            'class' => 'thumbnail attendance_tooltip',
            'data-toggle' => 'tooltip',
            'data-original-title' => $attendance['User']['name'],
            'data-placement' => 'bottom'));
        ?>
        <br /> 
    </div>
    <?php
    if (($i % 12) == 0) {
        echo "\n</div>\n\n";
    }
endforeach;
