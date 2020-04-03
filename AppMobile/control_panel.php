<?php
// Initialize the session
session_start();
 
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
<body>
    <div class="page-header">
        <h1>Bonjour, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Bienvenue sur notre application.</h1>
    </div>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Réinitialiser votre mot de passe</a>
        <a href="logout.php" class="btn btn-danger">Déconnexion</a>
    </p>
</body>
</html>