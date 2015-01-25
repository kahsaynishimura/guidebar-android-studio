<div class="logincontent">
   
    <div class="row">
        <div class="col-md-1"></div>

        <div class="col-md-3" id="loginform_wrapper" >
            <div class="loginform">
                <div id="fb-root"></div>
                <script>
                    function updateStatusCallback(response) {
                        if (response.status === 'connected') {
                            testAPITeste();
                        } else if (response.status === 'not_authorized') {
                            FB.login(function(response) {
                                if (response.authResponse) {
                                    testAPITeste();
                                }
                            }, {scope: 'email, user_birthday'});
                        } else {
                            FB.login(function(response) {

                                if (response.authResponse) {
                                    testAPITeste();
                                }
                            }, {scope: 'email, user_birthday'});
                        }
                    }
                    function testAPITeste() {
                        var picture = '';
                        FB.api(
                                "/me/picture",
                                {
                                    "redirect": false,
                                    "height": "200",
                                    "type": "normal",
                                    "width": "200"
                                },
                        function(resposta) {
                            if (resposta && !resposta.error) {
                                picture = resposta.data.url;
                            }
                        }
                        );
                        FB.api('/me', function(response) {
                            response.filename = picture;
                            console.log(response);
                            $.ajax({
                                type: 'POST',
                                url: '/users/loginfb/',
                                data: response,
                                success: function(response, textStatus, xhr) {

                                    window.location = '/events/index';

                                },
                                error: function(xhr, textStatus, error) {
                                    alert('erro' + xhr + '|' + textStatus + '|' + error);
                                    window.location = '/users/login';
                                }
                            });
                        });
                    }
                </script>
                <script>
                    $(document).ready(function() {
                        $.ajaxSetup({cache: true});
                        $(".headerContent").hide();
                        $.getScript('//connect.facebook.net/pt_BR/all.js', function() {
                            FB.init({
                                appId: '402213736551577',
                                status: true, // check login status
                                cookie: true, // enable cookies to allow the server to access the session
                                xfbml: true  // parse XFBML
                            });
                            $('#loginFacebook,#fb-login-button').removeAttr('disabled');
                        });

                    });
                </script>

                <?php
                echo $this->Html->image('login_guidebar_form.png');
                ?>

                <?php echo $this->Session->flash('auth'); ?>
                <?php echo $this->Form->create('User'); ?>
                <fieldset>
                    <?php
                    echo $this->Form->input('email', array('div' => 'form-group', 'label' => 'e-mail'));
                    echo $this->Form->input('password', array('div' => 'form-group', 'label' => 'senha'));
                    ?>
                </fieldset>
                <div class="login_fieldset_bottom" >
                    <?php echo $this->Html->link('esqueceu sua senha?', array('controller' => 'users', 'action' => 'recover_password')); ?>
                    <div class="submit">
                        <input  type="submit" value="Entrar">
                    </div>
                </div>
                <?php echo $this->Form->end(); ?>
                <div class="loginform_bottom">
                    Novo no guidebar? <?php echo $this->Html->link(__('Cadastre-se'), array('controller' => 'users', 'action' => 'add')); ?>  ou entre com o Facebook.

                    <div class="fb-login-button" data-max-rows="1" data-scope="email,user_birthday" onlogin="updateStatusCallback" data-size="small" data-show-faces="false" data-auto-logout-link="false"></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="login_download">
                    <p>Baixe o App GuideBar</p>
                    <a href="https://play.google.com/store/apps/details?id=br.com.guidebar">
                        <img alt="Android app on Google Play"
                             src="https://developer.android.com//images/brand/en_app_rgb_wo_45.png" />
                    </a>
                </div>       
                <div class="login_search">
                    <h2>Procure por festas agora mesmo</h2>
                    <form action="/events/index" role="search" >

                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Digite o nome da festa" name="srch-term" id="srch-term">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                            </div>

                        </div>
                    </form>      
                    <p>Ex.: Cervejada, Churrasco.</p>
                </div>

            </div>
        </div>

        <div class="col-md-2"></div>
    </div>
</div>
</div>