<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: control_panel.php");
    exit;
}
 
// Include config file
require_once "config_db.php";
//require_once "my_log.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Entrer votre identifiant.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Entrer votre mot de passe.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

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
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];

                        if(password_verify($password, $hashed_password)){
                        //if($hashed_password == $password){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            //m_log("$username vient de s'authentifier dans l'application");
                            // Redirect user to welcome page
                            header("location: control_panel.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "Le mot de passe entré n'est pas bon.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
                error_log("$username a tenté de s'authentifier dans l'application",3,"/logs/log.txt");

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
            <img class="rounded mx-auto d-block" src="/AppMobile/img/logo_hodor.JPG">
            <img class="rounded mx-auto d-block" id="logo1" alt="Responsive image" src="/AppMobile/img/door_fanart.jpg">
                <h1 class="text-center text-bleu-enedis">Projet IoTiA 2020 </h1>
                <h3 class="text-center">Wilfrid Mezard / Raphaël GUIOT</h3>
                <p>
                    <h1 class="text-center text-bleu-enedis">H.O.D.O.R.</h1>
<!--                     <h3 class="text-center"><span class="text-bleu-enedis">H</span><span class="text-bleu-enedis">O</span class="text-bleu-enedis">ld The <span class="text-bleu-enedis">D</span><span class="text-bleu-enedis">O</span>o<span class="text-bleu-enedis">R</span></h3>
 -->                </p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="loginform" id="loginform" class="visible" action="" method="post">
            <p class="submit text-center mt-5">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">ID :</span>
                        </div>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Identifiant" aria-label="Username" aria-describedby="basic-addon1" value="<?php echo $username; ?>">
                        <span class="help-block"><?php echo $username_err; ?></span>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        </div>
                        <input type="password" id="username" name="password" class="form-control" placeholder="Mot de passe" aria-label="password" aria-describedby="basic-addon1">
                        <span class="help-block"><?php echo $password_err; ?></span>
                    </div>
            </p>
            <p class="submit text-center mt-5"> 
            <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-block" value="Se connecter" />
                <small>
                    <a href="register.php" data-toggle="modal" class="text-success">Vous n'avez pas encore d'autorisation ? Demander une autorisation</a>
                </small>
            </p>
            </form>
</body>

    <!-- END CONTAINER -->

</body>
<footer>
    <script type="text/javacript" src="/AppMobile/node_modules/bootstrap/dist/bootstrap.min.js"></script>
</footer>
</html>