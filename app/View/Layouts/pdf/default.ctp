<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            Entradas
        </title>
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>

        <?php echo $this->Html->css(array('bootstrap.min.css', 'normalize.css', 'generic'), 'stylesheet', array('media' => 'mpdf')); ?>
    </head>
    <body>
        <div  class="container">
            <?php echo $this->fetch('content'); ?>
        </div>
    </body>
</html>