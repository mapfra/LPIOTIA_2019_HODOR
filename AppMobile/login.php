<!DOCTYPE html>
<html class=" ">
<head>
    <!--
     * @Package: Ultra Admin - Responsive Theme
     * @Subpackage: Bootstrap
     * @Version: B4-1.1
     * This file is part of Ultra Admin Theme.
    -->
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>HODOR - Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <?php $base_url = "localhost/"?>
    
    <link href="/AppMobile/bootstrap-4.4.1-dist/css/bootstrap.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/AppMobile/css/style_hodor.css" rel="stylesheet" type="text/css" media="screen"/>

</head>
<!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="login_page">
    <div class="login-wrapper">
        <div id="login" class="login loginpage offset-xl-4 col-xl-4 offset-lg-3 col-lg-6 offset-md-3 col-md-6 col-offset-0 col-12">
            <img class="rounded mx-auto d-block" src="/AppMobile/img/logo_hodor.JPG">
            <img class="rounded mx-auto d-block img-fluid" alt="Responsive image" src="/AppMobile/img/hodor_happy2.jpeg">

                <h1 class="text-center text-bleu-enedis">Projet IoTiA 2020 </h1>
                <h3 class="text-center">Wilfrid Mezard / Raphaël GUIOT</h3>

                <form name="loginform" id="loginform" action="" method="post">
                <p>
                    <h1 class="text-center text-bleu-enedis">H.O.D.O.R.</h1>
                <!-- <br> -->
                    <h3 class="text-center"><span class="text-bleu-enedis">H</span><span class="text-bleu-enedis">O</span class="text-bleu-enedis">ld The <span class="text-bleu-enedis">D</span><span class="text-bleu-enedis">O</span>o<span class="text-bleu-enedis">R</span></h3>
                </p>
                <p>
                </p>

                <p class="submit text-center mt-5">
                    <input type="hidden" name="login_id" id="wp-submit" class="btn btn-block" placeholder="Votre identifiant" />
                    <input type="hidden" name="login_pwd" id="wp-submit" class="btn btn-block" placeholder="Votre mot de passe" />
                    <br>
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-block" value="Se connecter" />
                     <br />
                    <br />
                    <small>
                        <a href="#request_authorization" data-toggle="modal" class="text-success">Vous n'avez pas encore d'habilitation ? Demander une habilitation</a>
                    </small>
                </p>
            </form>

</body>

    <!-- END CONTAINER -->

</body>
<footer>
    <script type="text/javacript" src="/AppMobile/node_modules/bootstrap/dist/bootstrap.min.js">
    </script>
</footer>
</html>