<?php
// Initialize the session
session_start();
//var_dump($_SESSION);
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config_db.php";
include "genlib.php";
include "my_log.php";
include "mqtt-test.php";

function FormGet() {
    //	Récupération valeurs du formulaire dans variables globales + Vérification saisie
        global $Erreur, $ErreurMsg;
        //var_dump($_POST);
        foreach ($_POST as $Key => $Value)
        {
            global $$Key;
            $$Key = $Value;
            if (!is_array($$Key)) $$Key = stripslashes(trim(str_replace("\"", "'", $Value)));			
        }
    
    # Verification des champs
        if ($Action !== "openDoor" && $Action !== "closeDoor") {	
            $ErreurMsg = 'Erreur aucune action transmise';
            return (false);
        }
    
        return (true);
}

/* function getStatusDoor() {
    read_topic(TOPIC, BROKER, PORT, KEEPALIVE, TIMEOUT);
    publish_message('getStatusDoor', TOPIC, BROKER, PORT, KEEPALIVE);
} */


# Principal _________________________________________________________________

# Init ----
$Info				= true;
$Erreur				= false;
$InfoMsg			= "";
$ErreurMsg			= "";
$Action				= "Init";
$statusDoor         = 0; // porte fermée

if(isset($GLOBALS['rcv_message']) === true) {
    if ($GLOBALS['rcv_message'] == 'openDoor') {
        $statusDoor = 1;
    } 
} else {
    $statusDoor = 0; // porte fermée
}

if ($_SERVER['REQUEST_METHOD'] == "POST") 	$FormValid = FormGet();

switch($Action) {

/* 	case "Init":
	getStatusDoor();
	break; */

	case "openDoor":
        if (publish_message('openDoor', TOPIC, BROKER, PORT, KEEPALIVE) === true) {
            $Info		= true;
            $InfoMsg	= "Ouverture de la porte effectuée";
            $statusDoor = 1;
	    } else {
            $Erreur     = True;
            $ErreurMsg  = "Erreur lors de l'ouverture de la porte";
            $Sortie     = "Erreur";
        }
	    break;

	case "closeDoor":
        if (publish_message('closeDoor', TOPIC, BROKER, PORT, KEEPALIVE) === true) {
            $Info		= true;
            $InfoMsg	= "Fermeture de la porte effectuée";
            $statusDoor = 0;
	   } else {
            $Erreur     = True;
            $ErreurMsg  = "Erreur lors de la fermeture de la porte";
            $Sortie     = "Erreur";
        }
        break;

}

// Fin _______________________________________________________________________

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
    <?php include('pagemessage.php'); ?>
    <div class="login-wrapper">
        <div id="login" class="login loginpage offset-xl-4 col-xl-4 offset-lg-3 col-lg-6 offset-md-3 col-md-6 col-offset-0 col-12">
        <a href="/AppMobile/logout.php"><img class="rounded mx-auto d-block img-fluid" src="/AppMobile/img/logo_hodor.JPG"></a>
            <img class="rounded mx-auto d-block img-fluid" id="logo2" alt="Responsive image" src="/AppMobile/img/hodor_happy2.jpeg">
            <p>     
                <h1>Bonjour, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Bienvenue sur notre application.</h1>
            </p>
            <p>
            <form name="openDoor" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="visible" method="post">
                <input name="Action" type="hidden" value="<?php echo $Action?>"> 
                <h3 class="text-bleu-enedis">Ouverture de la porte <label class="switch">
                    <input type="checkbox" name="statusDoor" OnClick="Open(document.openDoor)" value="<?php echo $statusDoor?>">
                    <span class="slider round"></span></label>
                </h3>
                <?php
                    if($statusDoor == 1) { ?>
                        <label class="switch"><span class="door"><?php echo file_get_contents("img/door-open-solid.svg"); ?></span></label>
                <?php  } else { ?>
                        <label class="switch"><span class="door"><?php echo file_get_contents("img/door-closed-solid.svg"); ?></span></label>
                <?php }
                ?>
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
    if(theForm.statusDoor.value == 0) {
        alert('La porte est fermée : ouverture en cours');
        //passer value a 1 : publish message : openDoor
        theForm.Action.value = "openDoor";
        //lancer l'action ouverture en ajax?
        //Si ok afficher porte ouverte
        //Sinon ko technique
    } else {
        alert('La porte est ouverte : fermeture en cours');
        //passer la value a 0 : publish message : closeDoor
        theForm.Action.value = "closeDoor";
        //lancer l'action fermeture en ajax?
        //Si ok afficher porte est fermée
        //Sinon ko technique
    }
    theForm.submit();
}
</script>
</footer>