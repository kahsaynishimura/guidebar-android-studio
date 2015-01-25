<script>
    function sendEmail() {
        var formData = $("#formPromoter").serialize();
        var formUrl = $("#formPromoter").attr('action');
        $.ajax({
            type: 'POST',
            url: formUrl,
            data: formData,
            dataType: "json",
            success: function(response, textStatus, xhr) {
                if (response.success) {
                    $('#email_message').html('Obrigado por entrar em contato. Sua mensagem foi enviada.');
                    $('#email_message').show();
                } else {
                    $('#email_message').html('Desculpe, não foi possível enviar esta mensagem');
                    $('#email_message').show();
                }
            },
            error: function(xhr, textStatus, error) {
                window.location = '/users/login';
            }
        });
        return false;
    }
    $(document).ready(function() {
        $('.attendance_tooltip').tooltip();
        $('.nav-tabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('#btnBookmark').click(function() {
            var formData = $("#bookmarkForm").serialize();
            var formUrl = $("#bookmarkForm").attr('action');
            $.ajax({
                type: 'POST',
                url: formUrl,
                data: formData,
                dataType: "json",
                success: function(response, textStatus, xhr) {
                    if (response.success) {
                        document.getElementById('btnBookmark').innerHTML = response.buttonText + "";
                        $("#bookmarkForm").attr('action', response.action);
                    } else {
                        window.location = '/events';
                    }
                },
                error: function(xhr, textStatus, error) {
                    window.location = '/users/login';
                }
            });
            return false;
        });

        $('#btnAttendance').click(function() {
            var formData = $("#attendanceForm").serialize();
            var formUrl = $("#attendanceForm").attr('action');
            $.ajax({
                type: 'POST',
                url: formUrl,
                data: formData,
                success: function(response, textStatus, xhr) {
                    $(".list_attendances").html(response);
                },
                error: function(xhr, textStatus, error) {
                    window.location = '/users/login';
                }
            });
            return false;
        });

        $('#btnComplaint').click(function() {
            var formData = $("#complaintForm").serialize();
            var formUrl = $("#complaintForm").attr('action');
            $.ajax({
                type: 'POST',
                url: formUrl,
                data: formData,
                dataType: "json",
                success: function(response, textStatus, xhr) {
                    if (response.success) {
                        alert(response.data);
                    } else {
                        window.location = '/events';
                    }
                },
                error: function(xhr, textStatus, error) {
                    window.location = '/users/login';
                }
            });
            return false;
        });

        $('#commentForm').submit(function() {
            var formData = $("#commentForm").serialize();
            var formUrl = $("#commentForm").attr('action');
            $.ajax({
                type: 'POST',
                url: formUrl,
                data: formData,
                success: function(response, textStatus, xhr) {
                    $(".lista_comentarios").html(response);
                    $("#CommentDescription").val('');
                },
                error: function(xhr, textStatus, error) {
                    window.location = '/users/login';
                }
            });
            return false;
        });

        var directionsDisplay;
        var directionsService = new google.maps.DirectionsService();
        var map;

        function initialize() {
            directionsDisplay = new google.maps.DirectionsRenderer();

            var chicago = new google.maps.LatLng('-23.20165601956605', '-50.79480931162834');
            var mapOptions = {
                zoom: 9,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: chicago
            };

            map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    map.setCenter(new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude));

                }, function(error) {

                });
            }
            directionsDisplay.setMap(map);

            var eventLat =<?php echo $event['Address']['latitude'] ?> + '';
            if (eventLat !== 0) {
                calcRoute();
            }
        }

        function calcRoute() {
            var start = map.getCenter();
            var eventLat =<?php echo $event['Address']['latitude'] ?> + '';
            var eventLon =<?php echo $event['Address']['longitude'] ?> + '';
            var end = new google.maps.LatLng(eventLat, eventLon);

            var request = {
                origin: start,
                destination: end,
                travelMode: google.maps.TravelMode.DRIVING
            };
            directionsService.route(request, function(result, status) {

                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(result);
                }
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
        var my_value =<?php echo $evaluation_rating ?>;
        $('#userEvaluation').prop("selectedIndex", (my_value - 1));
        $('#userEvaluation').barrating({
            showSelectedRating: true,
            showValues: false,
            onSelect: function(value, text) {
                saveEvaluation(value);
            }});

        function saveEvaluation(value) {
            var formData = $("#evaluationForm").serialize();
            var formUrl = $("#evaluationForm").attr('action');
            $.ajax({
                type: 'POST',
                url: formUrl,
                data: formData,
                dataType: 'json',
                success: function(response, textStatus, xhr) {
                    $('#myModal').modal('show');
                },
                error: function(xhr, textStatus, error) {
                    window.location = '/users/login';
                }
            });
            return false;
        }
        FB.init({
            appId: '402213736551577',
            status: true,
            cookie: true,
            xfbml: true
        });

    });
    function shareOnFB() {
        FB.ui(
                {
                    method: 'feed',
                    caption: 'guideBAR',
                    name: '<?= $event['Event']['name'] ?>',
                    picture: '<?php echo "http://guidebar.com.br/img/" . $event['Event']['filename'] ?>',
                    link: '<?php echo "http://guidebar.com.br/events/" . $event['Event']['id']; ?>',
                    description: '<?= mysql_escape_string($event['Event']['description']) ?>'
                },
        function(response) {
//            if (response && !response.error_code) {
//                alert('Posting completed.');
//            } else {
//                alert('Error while posting.');
//            }
        }
        );
    }
</script>
<script type="text/javascript"
        src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBpDqjkodlidnl63noZ7VTt0KjDqY6dUAE&sensor=true">
</script>
<script type="text/javascript"
        src="http://connect.facebook.net/en_US/all.js">
</script>
<div id="fb-root"></div> 
<div class="row">
    <aside class="col-md-2 view_event_col1">
        <?php echo $this->Html->Image('' . $event['Event']['thumb'], array('alt' => $event['Event']['thumb'], 'width' => 150, 'height' => 150, 'class' => 'img-responsive event_profile_image')); ?>
        <div class="row">  
            <div class="col-md-12">
                <div>
                    <span class="bold_text">  <?php echo __('Local: '); ?></span>
                    <?php
                    $local = $event['Address']['street'] . ', ' . $event['Address']['street_number'] . '-' .
                            $event['Address']['neighborhood'] . ' ' . $event['Address']['complement'] . ' ';
                    if ($event['Address']['street'] != '') {
                        echo $local . "<br />";
                    }
                    $local = $event['Address']['City']['name'] .
                            ' - ' . $event['Address']['City']['State']['name'];
                    if ($event['Address']['street'] != '') {
                        echo $local;
                    }
                    ?>
                    &nbsp;
                </div>
                <div>
                    <span class="bold_text"><?php echo __('Data: '); ?></span>
                    <?php
                    $dataInicial = date('d/m/Y', strtotime(h($event['Event']['start_date'])));
                    echo h($dataInicial);
                    ?>
                </div>  
                <div><span class="bold_text">
                        <?php echo __('Horário: '); ?></span>

                    <?php
                    $horaInicial = date('h:i \h', strtotime(h($event['Event']['start_date'])));
                    echo h($horaInicial);
                    ?>
                    &nbsp;
                </div>
                <div>
                    <span class="bold_text">
                        <?php echo __('Open bar: '); ?></span>

                    <?php
                    if ($event['Event']['is_open_bar']) {
                        echo 'Sim';
                    } else {
                        echo 'Não';
                    }
                    ?>
                    &nbsp;
                </div>
            </div>
        </div>
        <div class="event_links"> 
            <?php if ($event['User']['id'] == $user_login['User']['id']): ?>
                <div>
                    <?php echo $this->Html->link('<i class="glyphicon glyphicon-cog"></i> Administrar evento', array('controller' => 'events', 'action' => 'manage', $event['Event']['id']), array('escape' => false)); ?> 
                </div>
            <?php endif; ?> 
            <div>
                <?php
                echo $this->Html->link('<i class="glyphicon glyphicon-map-marker"></i> ' . __('Como chegar'), '#comoChegar', array('escape' => false));
                ?>
            </div>
            <div>
                <?php
                echo $this->Form->create('Complaint', array('controller' => 'complaints', 'action' => 'add', 'id' => 'complaintForm', 'role' => 'form', 'style' => 'display:none;'));
                echo $this->Form->input('event_id', array('type' => 'hidden', 'value' => $event['Event']['id']));
                echo $this->Form->submit(__('Denunciar este evento'));
                echo $this->Form->end();
                ?>
                <?php
                echo $this->Html->link('<i class="glyphicon glyphicon-ban-circle"></i>' . __(' Denunciar'), '#', array('id' => 'btnComplaint', 'escape' => false));
                ?>

            </div>
        </div>
    </aside> 

    <div class="col-md-8 middle-column">
        <?php echo $this->Form->create('Bookmark', array('controller' => 'bookmarks', 'action' => $bookmark_action, 'id' => 'bookmarkForm', 'role' => 'form', 'style' => 'display:none;')); ?>
        <?php
        echo $this->Form->input('event_id', array('type' => 'hidden', 'value' => $event['Event']['id']));
        echo $this->Form->submit(__($bookmark_action_text));
        echo $this->Form->end();
        ?> 
        <?php
        echo $this->Form->create('Attendance', array('controller' => 'attendances', 'action' => $attendance_action, 'id' => 'attendanceForm', 'role' => 'form', 'style' => 'display:none;'));
        echo $this->Form->input('event_id', array('type' => 'hidden', 'value' => $event['Event']['id']));
        echo $this->Form->submit(__($attendance_action_text));
        echo $this->Form->end();
        ?>
        <div class="btn-toolbar pull-right" role="toolbar">     
            <button type="button" id="btnAttendance" class="toolbar_button"><?php echo __($attendance_action_text); ?></button>
            <button type="button" id="btnBookmark" class="toolbar_button"><?php echo __($bookmark_action_text); ?></button>
        </div>
        <div class="row">
            <div class="col-md-12"><h1><?php echo h($event['Event']['name']); ?></h1></div>
        </div>

        <?php if (count(glob(WWW_ROOT . "files/Event/" . $event['Event']['id'] . "/*.*")) > 0): ?>
            <div class="c-wrapper">
                <div id="myCarousel" class="carousel slide" data-ride="carousel">
                    <!-- Carousel indicators -->
                    <ol class="carousel-indicators">
                        <?php
                        $thumbs = glob(WWW_ROOT . "files/Event/" . $event['Event']['id'] . "/*.*");
                        $i = 0;
                        while (count($thumbs) > $i) {
                            ?>
                            <li data-target="#myCarousel" data-slide-to="<?= $i ?>" <?php
                            if ($i == 0) {
                                echo 'class="active"';
                            }
                            ?>></li>
                                <?php
                                $i++;
                            }
                            $i = 0;
                            ?>
                    </ol>   
                    <div class="carousel-inner">
                        <?php
                        $thumbs = glob(WWW_ROOT . "files/Event/" . $event['Event']['id'] . "/*.*");
                        if (count($thumbs)) {
                            natcasesort($thumbs);
                            foreach ($thumbs as $thumb) {
                                if ($i == 0) {
                                    $i+=1;
                                    ?>
                                    <div class="item active">
                                        <img src="<?php echo "/" . substr($thumb, strrpos($thumb, 'files/Event')); ?>" alt="" />
                                    </div>  
                                    <?php
                                } else {
                                    ?>
                                    <div class="item">
                                        <img src="<?php echo "/" . substr($thumb, strrpos($thumb, 'files/Event')); ?>" alt="" />
                                    </div>  
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>

                    <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#myCarousel" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tabDescription">Descrição</a>
                </li>
                <li>
                    <a href="#tabComments">Comentários</a>
                </li>
                <li>
                    <a href="#tabAttendances">Participantes</a>
                </li>
                <li class="pull-right">
                    <div class="evaluations">
                        <?php echo $this->Form->create('Evaluation', array('controller' => 'evaluations', 'action' => 'add', 'id' => 'evaluationForm')); ?>
                        <div class="input select rating-f">
                            <select id="userEvaluation" name="data[Evaluation][rating]">
                                <option value="1">Ruim</option>
                                <option value="2">Regular</option>
                                <option value="3">Bom</option>
                                <option value="4">Muito bom</option>
                                <option value="5">Ótimo</option>
                            </select>
                        </div>
                        <?php
                        echo $this->Form->input('event_id', array('type' => 'hidden', 'value' => $event['Event']['id']));
                        echo $this->Form->end();
                        ?>
                    </div> 
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tabDescription" >
                    <div class="well">
                        <p>
                            <?php echo nl2br($event['Event']['description']); ?>
                        </p>
                        <p>
                            <span class="bold_text">
                                <?php echo __('Categoria: '); ?>
                            </span>
                            <?php echo h($event['Category']['name']); ?>
                            &nbsp;
                        </p> 
                        <p>
                            <span class="bold_text">
                                <?php echo __('Promotor: '); ?>
                            </span>
                            <?php echo $this->Html->link($event['User']['name'], array('controller' => 'users', 'action' => 'details', $event['User']['id'])); ?>
                            &nbsp;
                        </p> 
                        <p>
                            <span class="bold_text">
                                <?php echo __('Idade mínima: '); ?>
                            </span>
                            <?php echo h($event['Event']['minimum_age']); ?>
                            &nbsp;
                        </p> 
                        <p>
                            <span class="bold_text">
                                <?php echo __('Início: '); ?>
                            </span>
                            <?php
                            $dataInicial = date('d/m/Y h:i \h', strtotime(h($event['Event']['start_date'])));
                            echo h($dataInicial);
                            ?>
                            &nbsp;
                        </p> 
                        <p>
                            <span class="bold_text">
                                <?php echo __('Fim: '); ?>
                            </span>
                            <?php
                            $dataFinal = date('d/m/Y h:i \h', strtotime(h($event['Event']['end_date'])));
                            echo h($dataFinal);
                            ?>
                            &nbsp;
                        </p> 
                        <p>
                            <span class="bold_text">
                                <?php echo __('Visualizações: '); ?>
                            </span>
                            <?php echo h($event['Event']['views']); ?>
                            &nbsp;
                        </p> 

                        <p>
                            <span class="bold_text">
                                <?php echo __('Open bar: '); ?>
                            </span>
                            <?php
                            if ($event['Event']['is_open_bar'] == '1') {
                                echo 'Sim';
                            } else {
                                echo 'Não';
                            }
                            ?>
                            &nbsp;
                        </p>   
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-3" >
                                <?php echo $this->Html->image('facebook.png', array('style' => 'cursor: pointer;', 'class' => 'thumbnail', 'alt' => 'Share on facebook', 'onclick' => 'shareOnFB()')); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tabComments" >
                    <div class="well">
                        <div class="fb-comments" data-href="http://guidebar.com.br/events/view/<?php echo $event['Event']['id']; ?>" data-width="650" data-numposts="5" data-colorscheme="light"></div>
                    </div>
                </div>

                <div class="tab-pane" id="tabAttendances" >
                    <div class="well">
                        <div class="row list_attendances">
                            <?php
                            $i = 0;
                            foreach ($event['Attendees'] as $attendance):
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
                                        'data-original-title' => h($attendance['User']['name']),
                                        'data-placement' => 'bottom'));
                                    ?>
                                    <br />
                                </div>
                                <?php
                                if (($i % 12) == 0) {
                                    echo "\n</div>\n\n";
                                }
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <?php
            if (!empty($products) && ($event['User']['is_selling_ticket_pagseguro'] ||
                    $event['User']['is_selling_ticket_moip'])):
                ?>
                <h1><?php echo $this->Html->link(__('Ingressos'), array('controller' => 'events', 'action' => 'view', $event['Event']['id'])); ?></h1>

                <?php
                echo $this->element('tickets_online', array('products' => $products,
                    'is_selling_pagseguro' => $event['User']['is_selling_ticket_pagseguro'],
                    'is_selling_moip' => $event['User']['is_selling_ticket_moip']));
                ?>
            <?php endif; ?>
        </div>
        <h1 id="comoChegar">Como chegar?</h1>

        <div id="divMap" style="height: 300px;width: 400px;margin-top:15px;">
            <div id="map_canvas" style=" height:100%;"></div>
        </div>
        <div id="lat" style="display: none;"><?php echo $event['Address']['latitude']; ?></div>
        <div id="lon" style="display: none;"><?php echo $event['Address']['longitude']; ?></div>
    </div>
    <div class="col-md-2 view_event_col3">

        <p>Baixar o app guideBAR</p>
        <a href="https://play.google.com/store/apps/details?id=br.com.guidebar">
            <img class="img-responsive thumbnail" alt="Baixe do Google Play"
                 src="https://developer.android.com/images/brand/pt-br_generic_rgb_wo_45.png" />
        </a>
        <div class="panel panel-guidebar">
            <div class="panel-heading">
                Promovido por: 
            </div>
            <div class="panel-body">
                <div><?php echo $event['User']['name']; ?></div>
                <?php echo $this->Html->link(__("Fale com o promotor"), "#", array('onclick' => "$('#emailFormModal').modal('show');return false;")); ?> 
            </div>
        </div>

        <?php if (sizeof($event['Sponsor']) > 0): ?>

            <div class="panel panel-guidebar">
                <div class="panel-heading">
                    Patrocinadores
                </div>
                <div class="panel-body">
                    <?php foreach ($event['Sponsor'] as $key => $sponsor): ?>
                        <div class="ads">
                            <?php if ($sponsor['url']): ?>
                                <?php echo $this->Html->link($sponsor['name'], $sponsor['url'], array('target' => '_blank')); ?> 
                            <?php else: ?>
                                <?php echo $sponsor['name']; ?>
                            <?php endif; ?> 
                            <?php if (($sponsor['filename'])): ?>
                                <?php if ($sponsor['url']): ?>
                                    <?php
                                    echo $this->Html->link($this->Html->image($sponsor['filename'], array(
                                                'alt' => $sponsor['name'], 'width' => '100%',
                                                'class' => 'thumbnail',
                                            )), ($sponsor['url']), array('target' => '_blank', 'escape' => false));
                                    ?>
                                <?php else: ?>

                                    <?php
                                    echo $this->Html->image($sponsor['filename'], array(
                                        'alt' => $sponsor['name'], 'width' => '100%',
                                        'class' => 'thumbnail',
                                    ))
                                    ?>
                                <?php endif; ?> 
                            <?php endif ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>    
        <?php endif ?>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Avaliação</h4>
            </div>
            <div class="modal-body">
                <?php
                echo "Obrigado por avaliar este evento";
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
                <!--<button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div>
    </div>
</div><!--Modall-->
<div class="modal fade"id="emailFormModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only"><?php echo __('Fechar'); ?></span></button>
                <h4 class="modal-title"><?php echo __('Fale com o promotor'); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('EmailData', array('id' => 'formPromoter', 'url' => array('controller' => 'events', 'action' => 'messagePromoter'))); ?>
                <?php echo $this->Form->input('event_name', array('type' => 'hidden', 'value' => $event['Event']['name'])); ?>
                <?php echo $this->Form->input('promoter_id', array('type' => 'hidden', 'value' => $event['User']['email'])); ?>
                <?php echo $this->Form->input('nome', array('label' => 'Nome', 'value' => $user_login['User']['name'], 'type' => 'text', 'max-length' => '50', 'required' => 'required', 'div' => 'form-group')); ?>
                <?php echo $this->Form->input('email', array('label' => 'E-mail', 'value' => isset($user_login['User']['email']) ? $user_login['User']['email'] : "", 'type' => 'email', 'required' => 'required', 'div' => 'form-group')); ?>
                <?php echo $this->Form->textarea('mensagem', array('label' => 'Mensagem', 'type' => 'text', 'max-length' => '450', 'required' => 'required', 'div' => 'form-group')); ?>
                <div id="email_message" style="display: none;" class="instructions"></div>
                <?php echo $this->Form->end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Fechar'); ?></button>
                <button type="button" onclick="sendEmail()" class="btn btn-primary pull-right"><?php echo __('Enviar'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->