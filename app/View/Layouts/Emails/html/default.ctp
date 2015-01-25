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
 * @package       app.View.Layouts.Email.html
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
    <head>
        <title><?php echo $title_for_layout; ?></title> 
    </head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="width:815px; background-color: #F2F2F3">
    <center>
        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="background-color:#f2f2f3">
            <tbody>
                <tr>
                    <td align="center" valign="top" style="padding:40px 20px">
                        <table border="0" cellpadding="0" cellspacing="0" style="width:600px">
                            <tbody>
                                <tr>
                                    <td align="center" valign="top">
                                        <a href="http://www.guidebar.com.br/" title="Guidebar" style="text-decoration:none" target="_blank">
                                            <?php
                                            echo $this->Html->image("icon.png", array(
                                                'fullBase' => true,
                                                'style' => 'border:0;color:#6dc6dd!important;font-family:Helvetica,Arial,sans-serif;font-size:60px;font-weight:bold;min-height:auto!important;letter-spacing:-4px;line-height:100%;outline:none;text-align:center;text-decoration:none',
                                                'height' => "", 'width' => "75",
                                                'alt' => "GuideBar"));
                                            ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        <?php echo $this->fetch('content'); ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td align="center" valign="top">
                                        <!-- BEGIN COLUMNS // -->
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                                            <tbody><tr>
                                                    <td align="center" valign="top">
                                                        <table border="0" cellpadding="0" cellspacing="0" width="600" >
                                                            <tbody><tr>
                                                                    <td align="left" valign="top"  width="33%">
                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                                                                            <tbody><tr>
                                                                                    <td valign="top" >
                                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                                                                                            <tbody >
                                                                                                <tr>
                                                                                                    <td  valign="top" style="padding:9px;">
                                                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"  width="false">
                                                                                                            <tbody><tr>
                                                                                                                    <td  align="left" valign="top" style="padding:0 9px 9px 9px;">
                                                                                                                        <table style="width: 164px;" >
                                                                                                                            <tr   ><td>
                                                                                                                                    <?php
                                                                                                                                    echo $this->Html->image("share.png", array(
                                                                                                                                        'fullBase' => true,
                                                                                                                                        'style' => 'border:0;color:#6dc6dd!important;font-family:Helvetica,Arial,sans-serif;font-size:60px;font-weight:bold;min-height:auto!important;letter-spacing:-4px;line-height:100%;outline:none;text-align:center;text-decoration:none',
                                                                                                                                        'height' => "", 'width' => "164",
                                                                                                                                        'alt' => "GuideBar"));
                                                                                                                                    ?>
                                                                                                                                </td></tr></table>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td  valign="top" style="padding:0 9px 0 9px;" width="164">
                                                                                                                        <span style="color:#777777; font-weight: bold;">Crie eventos</span><br>
                                                                                                                        Promova eventos e compartilhe nas redes sociais para que todos fiquem sabendo, o guideBAR 
                                                                                                                        impulsiona a divulgação da sua festa.   
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </tbody></table>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody></table>
                                                                    </td>
                                                                    <td align="left" valign="top"  width="33%">
                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                                                                            <tbody><tr>
                                                                                    <td valign="top">

                                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                                                                                            <tbody >
                                                                                                <tr>
                                                                                                    <td  valign="top" style="padding:9px;">


                                                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"  width="false">
                                                                                                            <tbody><tr>
                                                                                                                    <td  align="left" valign="top" style="padding:0 9px 9px 9px;">

                                                                                                                        <table style="width: 164px;" ><tr ><td>
                                                                                                                                    <?php
                                                                                                                                    echo $this->Html->image("email_star.png", array(
                                                                                                                                        'fullBase' => true,
                                                                                                                                        'style' => 'border:0;color:#6dc6dd!important;font-family:Helvetica,Arial,sans-serif;font-size:60px;font-weight:bold;min-height:auto!important;letter-spacing:-4px;line-height:100%;outline:none;text-align:center;text-decoration:none',
                                                                                                                                        'height' => "", 'width' => "164",
                                                                                                                                        'alt' => "GuideBar"));
                                                                                                                                    ?>

                                                                                                                                </td></tr></table>

                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td  valign="top" style="padding:0 9px 0 9px;" width="164">
                                                                                                                        <span style="color:#777777; font-weight: bold;">Venda entradas online</span><br>
                                                                                                                        É fácil: é só informar seus dados do PagSeguro no seu perfil do guideBAR e você já pode gerar entradas para serem vendidas na internet.   </td>
                                                                                                                </tr>
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>

                                                                                    </td>
                                                                                </tr>
                                                                            </tbody></table>
                                                                    </td>
                                                                    <td align="left" valign="top"  width="33%">
                                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" >
                                                                            <tbody><tr>
                                                                                    <td valign="top" >
                                                                            <tbody >
                                                                                <tr>
                                                                                    <td  valign="top" style="padding:9px;">
                                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0"  width="false">
                                                                                            <tbody><tr>
                                                                                                    <td  align="left" valign="top" style="padding:0 9px 9px 9px;">
                                                                                                        <table style="width: 164px;"  >
                                                                                                            <tr  ><td>
                                                                                                                    <?php
                                                                                                                    echo $this->Html->image("marker.png", array(
                                                                                                                        'fullBase' => true,
                                                                                                                        'style' => 'border:0;color:#6dc6dd!important;font-family:Helvetica,Arial,sans-serif;font-size:60px;font-weight:bold;min-height:auto!important;letter-spacing:-4px;line-height:100%;outline:none;text-align:center;text-decoration:none',
                                                                                                                        'height' => "", 'width' => "164",
                                                                                                                        'alt' => "GuideBar"));
                                                                                                                    ?>

                                                                                                                </td></tr></table>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td  valign="top" style="padding:0 9px 0 9px;" width="164">
                                                                                                        <span style="color:#777777; font-weight: bold;">Entradas offline</span><br>
                                                                                                        No guideBAR você pode gerar entradas para imprimir e vender da forma convencional, você pode gerar varios lotes de entradas.  </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody></table>
                                                    </td>
                                                </tr>
                                            </tbody></table>
                                        <!-- // END COLUMNS -->
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">

                                        <!--REDES SOCIAIS-->
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tbody><tr>
                                                    <td align="left" valign="top">
                                                        <table align="left" border="0" cellpadding="0" cellspacing="0">
                                                            <tbody><tr>
                                                                    <td valign="top" style="padding-right:0; padding-bottom:9px;" >

                                                                        <table border="0" cellpadding="0" cellspacing="0" width=""  style="border-collapse: separate; border: 1px solid rgb(204, 204, 204); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-color: rgb(250, 250, 250);">
                                                                            <tbody><tr>
                                                                                    <td align="left" valign="middle" style="padding-top:5px; padding-right:9px; padding-bottom:5px; padding-left:9px;">

                                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="">
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    <td align="center" valign="middle" width="24" >
                                                                                                        <a href="http://www.facebook.com/sharer/sharer.php?u=http://www.guidebar.com.br/" target="_blank">
                                                                                                            <?php
                                                                                                            echo $this->Html->image("color-facebook-48.png", array(
                                                                                                                'fullBase' => true,
                                                                                                                'style' => 'display:block;',
                                                                                                                'height' => "24", 'width' => "24",
                                                                                                                'alt' => "GuideBar"));
                                                                                                            ?>
                                                                                                    </td>
                                                                                                    <td align="left" valign="middle"  style="padding-left:5px;">
                                                                                                        <a href="http://www.facebook.com/sharer/sharer.php?u=http://www.guidebar.com.br/" target="" style="color: rgb(80, 80, 80); font-family: Arial; font-size: 12px; font-weight: normal; line-height: 100%; text-align: center; text-decoration: none;">Share</a>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody></table>

                                                                    </td>
                                                                </tr>
                                                            </tbody></table>
                                                    </td>
                                                    <td align="left" valign="top">
                                                        <table align="left" border="0" cellpadding="0" cellspacing="0">
                                                            <tbody><tr>
                                                                    <td valign="top" style="padding-right:0; padding-bottom:9px;" >
                                                                        <table border="0" cellpadding="0" cellspacing="0" width=""  style="border-collapse: separate; border: 1px solid rgb(204, 204, 204); border-top-left-radius: 5px; border-top-right-radius: 5px; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; background-color: rgb(250, 250, 250);">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td align="left" valign="middle" style="padding-top:5px; padding-right:9px; padding-bottom:5px; padding-left:9px;">

                                                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="">
                                                                                            <tbody><tr>
                                                                                                    <td align="center" valign="middle" width="24" >
                                                                                                        <a href="https://plus.google.com/share?url=http://www.guidebar.com.br/" target="_blank">
                                                                                                            <?php
                                                                                                            echo $this->Html->image("color-googleplus-48.png", array(
                                                                                                                'fullBase' => true,
                                                                                                                'style' => 'display:block;',
                                                                                                                'height' => "24", 'width' => "24",
                                                                                                                'alt' => "GuideBar"));
                                                                                                            ?>
                                                                                                    </td>
                                                                                                    <td align="left" valign="middle"  style="padding-left:5px;">
                                                                                                        <a href="https://plus.google.com/share?url=http://www.guidebar.com.br/" target="" style="color: rgb(80, 80, 80); font-family: Arial; font-size: 12px; font-weight: normal; line-height: 100%; text-align: center; text-decoration: none;">+1</a>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </tbody></table>
                                                    </td>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!--END REDES SOCIAIS-->
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tbody><tr>
                                                    <td align="center" valign="top" style="color:#606060;font-family:Helvetica,Arial,sans-serif;font-size:13px;line-height:125%">
                                                        <em>Copyright</em> © 2014 Guidebar<span style="font-size:10px!important;vertical-align:super">®</span>, <em>Todos os direitos reservados.</em>
                                                        <br>
                                                        <a href="#147faa99b7e8a978_" style="color:#606060!important;text-decoration:none!important"><span style="color:#606060!important">Rua José dos Reis, 269 • centro • Uraí/PR BRA</span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="center" valign="top" style="padding-top:30px">
                                                        <a href="http://www.guidebar.com.br" title="GuideBar" style="text-decoration:none" target="_blank">
                                                            <?php
                                                            echo $this->Html->image("login_guidebar_form.png", array(
                                                                'fullBase' => true,
                                                                'style' => 'border:0;outline:none;text-decoration:none',
                                                                'height' => "25", 'width' => "100",
                                                                'alt' => "GuideBar"));
                                                            ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody></table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </center>


</body>
</html>