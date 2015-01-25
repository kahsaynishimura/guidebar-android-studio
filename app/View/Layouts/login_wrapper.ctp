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
<html  >
    <head>

        <!--        
                <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>-->
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

        echo $this->Html->css(array('bootstrap.min.css', 'normalize.css', 'generic.css'));
//        echo $this->Html->css('cake.generic');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        ?>

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>

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
    </head>
    <body>

        <div  style="background:#dc1c4f;height: 10px;"></div>

        <div  class="container">
            <?php echo $this->Session->flash(); ?>

            <?php echo $this->fetch('content'); ?>
        </div>
        <footer >
            <div class="footer_links row"> 
                <div class="container">
                    <div class="col-md-3">
                        <p>Sobre o GuideBar</p>
                        <div><?php echo $this->Html->link('Sobre nós', array('controller' => 'pages', 'action' => 'sobreNos'), array('escape' => false)); ?> </div>
                        <div><?php echo $this->Html->link('Tour', array('controller' => 'pages', 'action' => 'tour'), array('escape' => false)); ?> </div>
                        <div><?php echo $this->Html->link('GuideBar Blog', "http://blog.guidebar.com.br/", array('escape' => false)); ?> </div>
                        <div><?php echo $this->Html->link('Empregos', array('controller' => 'users', 'action' => 'empregos'), array('escape' => false)); ?> </div>
                    </div>
                    <div class="col-md-3">
                        <p>Ajuda</p>
                        <div><?php echo $this->Html->link('Precisa de ajuda? Comece aqui.', array('controller' => 'pages', 'action' => 'comeceAqui'), array('escape' => false)); ?> </div>
                        <div><?php echo $this->Html->link('Denunciar evento', array('controller' => 'pages', 'action' => 'instrucoesDenunciar'), array('escape' => false)); ?> </div>
                        <div><?php echo $this->Html->link('Forum', array('controller' => 'pages', 'action' => 'forum'), array('escape' => false)); ?></div>
                        <div><?php echo $this->Html->link('FAQs', array('controller' => 'pages', 'action' => 'faq'), array('escape' => false)); ?> </div>

                    </div>
                    <div class="col-md-3">
                        <p>Apps</p>
                        <div><?php echo $this->Html->link('App para iOS', array('controller' => 'pages', 'action' => 'iosApp'), array('escape' => false)); ?> </div>
                        <div><?php echo $this->Html->link('App para Android', "https://play.google.com/store/apps/details?id=br.com.guidebar", array('target' => '_blank', 'escape' => false)); ?> </div>
                        <div><?php echo $this->Html->link('App para Windows Phone', array('controller' => 'pages', 'action' => 'windowsPhoneApp'), array('escape' => false)); ?></div>
                        <div><?php echo $this->Html->link('App para Blackberry', array('controller' => 'pages', 'action' => 'blackberryApp'), array('escape' => false)); ?> </div>

                    </div>
                    <section class="col-md-3">
                        <p>Parceiros</p>
                        <div><?php echo $this->Html->link('ToatySoft', "http://toastysoft.com.br/", array('target' => '_blank', 'escape' => false)); ?> </div>
                        <div><?php echo $this->Html->link('SCardápios', 'http://scardapios.com.br/', array('target' => '_blank', 'escape' => false)); ?> </div>
                    </section>
                </div>
            </div>
            <div class="footer_copyright row">
                <div class="container">
                    <div class="col-md-6">
                        <p>Deutsch | English | Español | Français | Italiano | Português</p> 
                    </div>
                    <div class="col-md-6">
                        <p>Copyright (C) 2013 GuideBar Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>
        <?php echo $this->element('sql_dump'); ?>

        <!-- scripts_for_layout -->
        <!--echo $scripts_for_layout;--> 
        <!-- Js writeBuffer -->
        <?php
        if (class_exists('JsHelper') && method_exists($this->Js, 'writeBuffer')) {
            echo $this->Js->writeBuffer();
            // Writes cached scripts
        }
        ?>
    </body>
</html>
