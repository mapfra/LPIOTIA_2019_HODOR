<?php
// Include config file
require_once "config_db.php";
include "genlib.php";
include "my_log.php";

// Initialize the session
session_start();
//var_dump($_SESSION);
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!--
     * @Package: Ultra Admin - Responsive Theme
     * @Subpackage: Bootstrap
     * @Version: B4-1.1
     * This file is part of Ultra Admin Theme.
    -->
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>HODOR - Panneau de controle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <?php $base_url = "localhost/"?>
    <link href="/AppMobile/bootstrap-4.4.1-dist/css/bootstrap.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="/AppMobile/css/style_hodor.css" rel="stylesheet" type="text/css" media="screen"/>
</head>
<!-- BEGIN BODY -->
<body class="login_page">
    <div class="login-wrapper">
        <div id="login" class="login loginpage offset-xl-4 col-xl-4 offset-lg-3 col-lg-6 offset-md-3 col-md-6 col-offset-0 col-12">
        <a href="/AppMobile/logout.php"><img class="rounded mx-auto d-block" src="/AppMobile/img/logo_hodor.JPG"></a>
            <img class="rounded mx-auto d-block img-fluid" id="logo2" alt="Responsive image" src="/AppMobile/img/hodor_happy2.jpeg">
            <p>     
                <h1>Bonjour, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Bienvenue sur notre application.</h1>
            </p>
            <p>
            <form name="openDoor">
            <input type="hidden" name="valueDoor" value="0">
            <h3 class="text-bleu-enedis">Ouverture de la porte <label class="switch"><input type="checkbox" OnClick="Open(document.openDoor)"><span class="slider round"></span></label></h3>
            </form>
            </p>
            <p class="submit text-center mt-5"> 
                <a href="logout.php" class="btn btn-primary">Déconnexion</a>
                <?php 
                if(isset($_SESSION["role"]) === true && $_SESSION["role"] == 1) { ?>
                    <a href="list_users.php" class="btn btn-warning">Administration</a>
                <?php 
                }
                ?>
            </p>
</body>
</html>
<footer>
<script type="text/javacript" src="/AppMobile/node_modules/bootstrap/dist/bootstrap.min.js"></script>
<script language="javascript">
function Open(theForm)
{
    if(theForm.valueDoor = "0") {
        //alert('ça marche');
        //passer value a 1
        //afficher message action en cours
        //lancer le script python
        //traiter retour python
        //Si ok afficher porte ouverte
        //Sinon ko technique
    } else {
        //la porte est ouverte
        //passer la value a 0
        //afficher message action en cours
        //lancer le script python 2
        //traiter retour python
        //Si ok afficher porte est fermée
        //Sinon ko technique
    }
/*     theForm.Action.value = "Valider"
    theForm.submit(); */
}
</script>
</footer>