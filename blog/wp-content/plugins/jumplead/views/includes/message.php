<?php
if (isset($errors) && !empty($errors)) {
?>
    <div class="error">
        <p>
            <strong>
    <?php
    foreach ($errors as $error) {
        echo _e($error) . ' <br />';
    }
    ?>
            </strong>
        </p>
    </div>
<?php
}


if (isset($info) && !empty($info)) {
?>
    <div class="updated">
        <p>
            <strong>
    <?php
    foreach ($info as $i) {
        echo _e($i) . ' <br />';
    }
    ?>
            </strong>
        </p>
    </div>
<?php
}
?>