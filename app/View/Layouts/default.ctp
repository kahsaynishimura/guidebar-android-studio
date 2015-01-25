<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$guidebarDescription = __d('guidebar', 'Guidebar');
?>
<!DOCTYPE html>
<html>
    <head>
           <!-- Google Analytics -->
        <script>
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-54739395-1', 'auto');
            ga('send', 'pageview');

        </script>
        <!-- End Google Analytics -->

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <script type="text/javascript" src="/js/jquery-2.0.3.js"></script>
        <script type="text/javascript" src="/js/jquery.barrating.min.js"></script>
        <script type="text/javascript" src="/js/bootstrap.js"></script>
        <script type="text/javascript" src="/js/default.js"></script>

        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $guidebarDescription ?>:
            <?php echo $title_for_layout; ?>
        </title>
        <?php
        echo $this->Html->meta('icon');

        echo $this->Html->css(array('bootstrap.min', 'normalize', 'generic'));
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        ?>
     
    </head>
    <body>

        <!-- Fixed navbar -->
        <div class="navbar navbar-default navbar-static-top" role="navigation">
            <style>
                .body{padding-top:70px}
            </style>
            <div class="container">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php
                    echo $this->Html->image('login_guidebar_form.png', array(
                        'class' => 'navbar-brand',
                        'alt' => 'Guidebar',
                        'url' => array('controller' => 'events', 'action' => 'index')));
                    ?>
                </div>
                <div class="collapse navbar-collapse">
                    <ul  class="nav navbar-nav pull-right">
                        <li><?php
                    echo $this->Html->link(__('Novo Evento'), array('controller' => 'events', 'action' => 'add'), array(
                        'id' => 'novoEvento', 'class' => 'tooltip_default',
                        'data-toggle' => 'tooltip',
                        'data-original-title' => 'Cadastre um novo evento',
                        'data-placement' => 'bottom'));
                    ?>
                        </li>
                        <li>
                            <?php
                            echo $this->Html->link(__('Meus eventos'), array('controller' => 'events', 'action' => 'myEvents'), array(
                                'id' => 'meusEventos', 'class' => 'tooltip_default',
                                'data-toggle' => 'tooltip',
                                'data-original-title' => 'Visualize os eventos que você criou',
                                'data-placement' => 'bottom'));
                            ?>
                        </li>
                        <li>
                            <?php
                            echo $this->Html->link(__('Minhas compras'), array('controller' => 'purchases', 'action' => 'myPurchases'), array(
                                'id' => 'minhasCompras', 'class' => 'tooltip_default',
                                'data-toggle' => 'tooltip',
                                'data-original-title' => 'Acesse suas compras',
                                'data-placement' => 'bottom'));
                            ?>
                        </li>
                        <li>
                            <form id="searchFormNav" action="/events/index" class="navbar-form" role="search">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Buscar eventos por nome" name="srch-term" id="srch-term" style="font-size: 70%;">
                                    <div class="input-group-btn">
                                        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </li>
                        <?php if (empty($user_login['User'])): ?>
                            <li class="headerContent"><?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login')); ?></li>  
                        <?php else: ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo h(substr($user_login['User']['name'], 0, strpos($user_login['User']['name'], " "))); ?> <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><?php echo $this->Html->link(__('Editar meu perfil'), array('controller' => 'users', 'action' => 'edit', $user_login['User']['id']), array('id' => 'viewUser')); ?></li> 
                                    <li><?php echo $this->Html->link(__('Favoritos'), array('controller' => 'bookmarks', 'action' => 'myFavorites'), array('id' => 'meusFavoritos')); ?></li>
                                    <li class="divider"></li> 
                                    <li><?php echo $this->Html->link(__('Sair'), array('controller' => 'users', 'action' => 'logout')); ?></li> 
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>  
                </div><!--/.nav-collapse -->
            </div>
        </div>

        <div  class="container">

            <?php echo $this->Session->flash(); ?>

            <?php echo $this->fetch('content'); ?>
        </div>

        <?php

        function curPageURL() {
            $pageURL = 'http';
            if ($_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }

        if (class_exists('JsHelper') && method_exists($this->Js, 'writeBuffer')) {
            echo $this->Js->writeBuffer();
            // Writes cached scripts
        }
        ?>
    </body>
</html>