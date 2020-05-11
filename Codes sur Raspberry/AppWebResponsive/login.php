<?php
// Initialize the session
session_start();

// Include config file
require_once "config_db.php";
include "my_log.php";
include "genlib.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER['REQUEST_METHOD'] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST['username']))){
        $username_err = "Entrer votre identifiant.";
    } else{
        $username = trim($_POST['username']);
    }
    
    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = "Entrer votre mot de passe.";
    } else{
        $password = trim($_POST['password']);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password, email, num_mobile, status, role, dt_start_access, dt_end_access, time_start, time_end FROM users WHERE username = ?";

        if($stmt = $pdo->prepare($sql)){

            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute(array($param_username))){

                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        // Password is correct, so start a new session
                        $hashed_password = $row['password'];
                        $id = $row['id'];
                        $dt_start_acces = $row['dt_start_access'];
                        $dt_end_access = $row['dt_end_access'];
                        $time_start = $row['time_start'];
                        $time_end = $row['time_end'];

                        if(password_verify($password, $hashed_password)){
                            // Check if status is activated
                            if($row['status']==1){
                                // Check if date is ok timestamp between interval
                                $current_date = time();
                                $ts_dt_start_access = strtotime($row['dt_start_access']);
                                $ts__dt_end_access = strtotime($row['dt_end_access']);
                                $ts_time_start = MkTimeStamp($row['time_start']);
                                $ts_time_end = MkTimeStamp($row['time_end']);

                                //echo 'ts date courante : '.$current_date . ' ts date de début : '. $ts_dt_start_access. ' ts date de fin : '.$ts__dt_end_access .'</br>';
                                //echo 'date courante : '.date("d/m/Y",$current_date) . ' date de début : '. date("d/m/Y",$ts_dt_start_access). ' date de fin : '.date("d/m/Y",$ts__dt_end_access) .'</br>';
                                
                                // Check if time is ok
                                if($current_date > $ts_dt_start_access && $current_date < $ts__dt_end_access) {
                                    
                                    //echo 'OK </br>';
                                    //echo 'heure de début : '. $row['time_start']. ' heure de fin : '.$row['time_end'] .'</br>';
                                    //echo 'date courante : '.$current_date . ' ts heure de début : '.  $ts_time_start. ' ts heure de fin : '. $ts_time_end .'</br>';
                                    //echo 'date courante : '.date("d/m/Y H:i:s",$current_date) . ' date de début : '. date("d/m/Y H:i:s",$ts_time_start). ' date de fin : '.date("d/m/Y H:i:s",$ts_time_end) .'</br>';
                                    if($current_date > $ts_time_start && $current_date < $ts_time_end) {
                                        //echo 'OK';
                                        // Store data in session variables
                                        $_SESSION['loggedin'] = true;
                                        $_SESSION['id'] = $id;
                                        $_SESSION['username'] = $row['username'];
                                        $_SESSION['role'] = $row['role'];
                                        $_SESSION['email'] = $row['email'];
                                        $_SESSION['num_mobile'] = $row['num_mobile'];
                                        $_SESSION['dt_start_access'] = $row['dt_start_access'];
                                        $_SESSION['dt_end_access'] = $row['dt_end_access'];

                                        m_log("$username (Id : $id) vient de s'authentifier dans l'application, STATUS OK");
                                        // Redirect user to welcome page
                                        header("location: control_panel.php");
                                    } else {
                                        //echo 'KO';
                                        m_log("$username (Id : $id) a tenté de s'authentifier mais sa date de validité est expirée , STATUS KO");
                                        $username_err = "Vous n'êtes pas autorisé à vous connecter, veuillez contacter un admin";
                                    }
                                } else {
                                    //echo 'KO';
                                    m_log("$username (Id : $id) a tenté de s'authentifier hors de sa plage horaire , STATUS KO");
                                    $username_err = "Vous n'êtes pas autorisé à vous connecter, veuillez contacter un admin";
                                }
                            }
                            else {
                                $password_err = "Le compte n'est pas validé";
                                m_log("$username (Id : $id) a tenté de s'authentifier mais son compte n'est pas validé par l'administrateur , STATUS KO");
                                $username_err = "Votre compte n'a pas été validé, veuillez contacter un admin";
                            }
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "Mot de passe invalide";
                            m_log("$username (Id : $id) a tenté de s'authentifier avec un mot de passe faux dans l'application, STATUS KO");
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "Vous n'êtes pas autorisé à vous connecter, veuillez contacter un admin";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
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
            <a href="/AppMobile/login.php"><img class="rounded mx-auto d-block img-fluid" src="/AppMobile/img/logo_hodor.JPG"></a>
            <img class="rounded mx-auto d-block img-fluid" id="logo1" alt="Responsive image" src="/AppMobile/img/door_fanart.jpg">
                <h1 class="text-center text-bleu-enedis">Projet IoTiA 2020 </h1>
                <h3 class="text-center">Wilfrid Mezard / Raphaël GUIOT</h3>
                <p>
                    <h1 class="text-center text-bleu-enedis">H.O.D.O.R.</h1>
                </p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="loginform" id="loginform" class="visible" action="" method="post">
            <p class="submit text-center">
            <span class="help-block"><?php echo $username_err; ?></span>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">ID :</span>
                        </div>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Identifiant" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo htmlspecialchars($username); ?>">
                        </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        </div>
                        <input type="password" id="username" name="password" class="form-control" placeholder="Mot de passe" aria-label="password" aria-describedby="basic-addon1">
                        <span class="help-block"><?php echo $password_err; ?></span>
                    </div>
            </p>
            <p class="submit text-center"> 
            <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-block" value="Se connecter" />
                <small>
                    <a href="register.php" data-toggle="modal" class="text-success">Vous n'avez pas encore d'autorisation ? Demander une autorisation</a>
                </small>
                    <a href="reset-password.php" class="btn btn-primary mt-3">Réinitialiser votre mot de passe</a>
            </p>
            </form>
</body>

    <!-- END CONTAINER -->

</body>
<footer>
    <script type="text/javacript" src="/AppMobile/node_modules/bootstrap/dist/bootstrap.min.js"></script>
</footer>
</html>