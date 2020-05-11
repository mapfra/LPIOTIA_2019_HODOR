<?php
// Include config file
require_once "config_db.php";
include "my_log.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $mobile = $mail = "";
$username_err = $password_err = $confirm_password_err = $mobile_err = $mail_err =  "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty($_POST["username"]) === true || strlen($_POST["username"]) > 50){
        $username_err = "Entrer un identifiant valide";
    } else{
        // Set parameters
        $param_username = htmlspecialchars(trim($_POST["username"]));

        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Cet identifiant est déjà pris";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            unset($stmt);
        }
    }
    
    // Validate inputs
    if(empty($_POST["password"]) === true || strlen(trim($_POST["password"])) < 6){
        $password_err = "Entrer un mot de passe valide, au moins 6 caractères";
    } else {
        $password = htmlspecialchars(trim($_POST["password"]));
        // Validate confirm password
        if(empty($_POST["confirm_password"]) === true){
            $confirm_password_err = "Confirmer votre mot de passe";     
        } else{
            $confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));
            if($password != $confirm_password) {
                $confirm_password_err = "Vos mots de passes ne correspondent pas";
            }
        } 
        if(preg_match("^((\+|00)33\s?|0)[67](\s?\d{2}){4}$^", $_POST["num_mobile"]) == 1) {
            $param_mobile = htmlspecialchars(trim($_POST["num_mobile"]));
        } else {
            $mobile_err = "Entrer un numéro de mobile valide";
        } 
        if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) !== false) {
            $param_mail = htmlspecialchars(trim($_POST["email"]));
        } else{
            $mail_err = 'Entrer un email valide';
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($mobile_err)){

        // Set parameters
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, num_mobile, email) VALUES (:username, :password, :num_mobile, :email)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":num_mobile", $param_mobile, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_mail, PDO::PARAM_STR);
            
            // Attempt to execute the prepared statement
            try { 
                $id = $stmt->execute();
                // Redirect to login page
                m_log("$username vient de créer un compte sur l'application, STATUS OK");
                header("location: login.php");
            } catch (PDOException $e) {
                echo 'Erreur : [', $e->getCode(), '] ', $e->getMessage();
            } catch (Exception $e) {
                die('Erreur SQL : '.$e->getMessage());
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
    <title>HODOR - Enregistrement</title>
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
        <a href="/AppMobile/login.php"><img class="rounded mx-auto d-block" id="logo2" src="/AppMobile/img/logo_hodor.JPG"></a>
            <img class="rounded mx-auto d-block" id="logo2" alt="" src="/AppMobile/img/door_fanart.jpg">
            <h1 class="text-center text-bleu-enedis">Enregistrement</h1>
            <p>Renseigner ce formulaire pour créer un compte.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="registerform" class="visible" action="" method="post">
            <p class="submit text-center">
                        <div class="input-group-prepend form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                        <span class="input-group-text" id="basic-addon1">Identifiant:</span>
                            <input type="text" maxlength="50" name="username" class="form-control" value="<?php echo $username; ?>">
                            <span class="help-block"><?php echo $username_err; ?></span>
                        </div>
                        <div class="input-group-prepend form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                        <span class="input-group-text" id="basic-addon1">Mot de passe:</span>
                            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                            <span class="help-block"><?php echo $password_err; ?></span>
                        </div>
                        <div class="input-group-prepend form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                        <span class="input-group-text" id="basic-addon1">Confirmer Mot de passe:</span>
                            <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                            <span class="help-block"><?php echo $confirm_password_err; ?></span>
                        </div>
                        <div class="input-group-prepend form-group <?php echo (!empty($mobile_err)) ? 'has-error' : ''; ?>">
                        <span class="input-group-text" id="basic-addon1">Numéro de mobile:</span>
                            <input type="text" name="num_mobile" class="form-control" value="<?php echo $mobile; ?>">
                            <span class="help-block"><?php echo $mobile_err; ?></span>
                        </div>
                        <div class="input-group-prepend form-group <?php echo (!empty($mail_err)) ? 'has-error' : ''; ?>">
                        <span class="input-group-text" id="basic-addon1">Email:</span>
                            <input type="email" name="email" class="form-control" value="<?php echo $mail; ?>">
                            <span class="help-block"><?php echo $mail_err; ?></span>
                        </div>
            </p>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Valider">
            </div>
            <p>Déjà un compte ?<a id="login_ref" href="login.php"> Se connecter ici</a></p>
            </form>
</body>
<footer>
    <script type="text/javacript" src="/AppMobile/node_modules/bootstrap/dist/bootstrap.min.js"></script>
</footer>
</html>