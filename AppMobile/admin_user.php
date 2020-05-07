<?php

# Variables _________________________________________________________________

# Fonctions _________________________________________________________________

function FormGet() {
//	Récupération valeurs du formulaire dans variables globales + Vérification saisie
	global $Erreur, $ErreurMsg, $status, $role;
	//var_dump($_POST);
	foreach ($_POST as $Key => $Value)
	{
		global $$Key;
		$$Key = $Value;
		if (!is_array($$Key)) $$Key = stripslashes(trim(str_replace("\"", "'", $Value)));			
	}
	if (!isset($status))    $status = 0;
	if (!isset($role)) 		$role = 0;

# Verification des champs
	if ($Action == "Supprimer")				return (true);

	$ErreurMsg = "Enregistrement impossible : ";

	if (strlen($username) == 0) {
		$Erreur = True; $ErreurMsg .= "Identifiant Invalide";         return (false);
	}
	if ($role== "0") {
		$Erreur = True; $ErreurMsg .= "Choissisez un rôle";           return (false);
	}	

	if ($role !== "1" && $role !== "2")  {
		$Erreur = True; $ErreurMsg .= "Rôle invalide";           return (false);
	}

	if (strlen($dt_start_acces) == 0)  {
		$Erreur = True; $ErreurMsg .= "Date invalide";           return (false);
	}
	if (strlen($dt_end_acces) == 0)  {
		$Erreur = True; $ErreurMsg .= "Date invalide";           return (false);
	}

	if (strlen($time_start) == 0)  {
		$Erreur = True; $ErreurMsg .= "Heure invalide";           return (false);
	}

	if (strlen($time_end) == 0)  {
		$Erreur = True; $ErreurMsg .= "Heure invalide";           return (false);
	}

	return (true);
}

function InitFields() {
//	Init valeur par défaut du formulaire (= Champs table user)
	global $status, $id, $pdo;

	$rs = $pdo->query('SELECT * FROM users LIMIT 0');
	for ($i = 0; $i < $rs->columnCount(); $i++) {
		$col = $rs->getColumnMeta($i);
		global ${"{$col['name']}"};
	}

	$id     = 0;
	$status = 0;
}

function UserGet($id) {
	//	récupération contenu User
	global $pdo;

	if (!is_numeric($id)) return false;

	$sql = "SELECT * FROM users WHERE id = $id ";
	$rsUser = $pdo->query($sql);
	$aUser =  $rsUser->fetch(PDO::FETCH_ASSOC);

	if (!is_array($aUser)) return false;

	for ($i = 0; $i < $rsUser->columnCount(); $i++) {
		$field = $rsUser->getColumnMeta($i);
		$name = $field['name'];
		global ${"{$name }"};
		//echo 'ici $name1 : '.$name.'</br>';
		$$name = $aUser[$name];
		//echo 'ici $name2 : '.$$name.'</br>';
	}
	$rsUser = null;

	return(true);
}

function UserSet($id) {
//	Insertion ou MAJ User
	global $status, $id, $pdo;

	$rs = $pdo->query('SELECT * FROM users LIMIT 0');
	for ($i = 0; $i < $rs->columnCount(); $i++) {
		$col = $rs->getColumnMeta($i);
		global ${"{$col['name']}"};
	}
	
	$Sql    = " Set ".
			  "     username			= " . SqlIn($username,		 "Text")	.
			  " ,   email				= " . SqlIn($email,			 "Text")	.
			  " ,   num_mobile			= " . SqlIn($num_mobile,	 "Text")	.
			  " ,   role				= " . SqlIn($role,			 "Numeric")	.
			  " ,   dt_start_access		= " . SqlIn($dt_start_access,"DateUS")	.
			  " ,   dt_end_access		= " . SqlIn($dt_end_access,	 "DateUS")	.
			  " ,   time_start			= " . SqlIn($time_start,	 "Time")	.
			  " ,   time_end			= " . SqlIn($time_end,		 "Time")	.
			  " ,   status				= " . SqlIn($status,		 "Box");
	if ($id == 0) { 
	  $Sql = " INSERT INTO users " . $Sql;
	} else 	{
	  $Sql = " UPDATE users " . $Sql .  " WHERE id =" . $id;
	  //echo $Sql;
	}
	$rs = $pdo->query($Sql);
    if ($id == 0) {
		$id   = $pdo->lastInsertId();;
	}
}

function UserDel($id) {
//	Destruction User
	global $pdo, $username;

	$Sql = "DELETE FROM users WHERE id = $id";
	$result = $pdo->exec($Sql);
	m_log("Un admin a supprimé l'utilisateur $username ($id), STATUS $result");
}

function Existant($username, $id) {
//	Existance User avec ce username
	global $pdo;
	$Sql = "
	SELECT		id
	FROM		users
	WHERE		username = '$username' AND
				id <> $id		
	" ;
	$stmt = $pdo->query($Sql);
	if ($stmt->rowCount() == 0) return false;
	return true;
}

# Principal _________________________________________________________________

# Init ----
$Info				= true;
$Erreur				= false;
$InfoMsg			= "Les champs suffixés par * doivent être obligatoirement saisis";
$ErreurMsg			= "";
$Action				= "Init";
$Sortie				= "Normal";
$id					= "0";

// Include config file
require_once "config_db.php";
include "genlib.php";
include "my_log.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") 	$FormValid = FormGet();
if (isset($_GET['id'])) {
	$id   = $_GET['id'];
	$Action     = "Charger";
}

switch($Action) {

	case "Init":
	InitFields();
	break;

	case "Charger":
	if (!UserGet($id)) 	{
		$Erreur     = True;
		$ErreurMsg  = "User inexistant";
		$Sortie     = "Erreur";
	}

	break;

	Case "Valider":
	if ($FormValid) {
		if (!Existant($username, $id)) {
			UserSet($id);
			$Info		= True;
			$InfoMsg	= "Modification effectuée";
		} else
		{
			$Erreur		= True;
			$ErreurMsg	= "Création impossible : cet utilisateur existe déja dans l'application";			
		}		
	}
	break;

	Case "Supprimer":
		UserDel($id);
		$Info		= true;
		$InfoMsg	= "Suppression effectuée";
		$Sortie		= "Suppression";
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
    <title>HODOR - Admin - Edition utilisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <?php $base_url = "localhost/"?>
    
    <link href="/AppMobile/bootstrap-4.4.1-dist/css/bootstrap.css" rel="stylesheet" type="text/css" media="screen"/>
	<link href="/AppMobile/css/style_hodor.css" rel="stylesheet" type="text/css" media="screen"/>
	
<script language="javascript">
function Supprimer(theForm)
{
    Conf = confirm('Voulez-vous vraiment supprimer cet utilisateur ?');
    if (Conf != '1')
    {
      return (false);
    }
    theForm.Action.value = "Supprimer"
    theForm.submit();
}

function Valider(theForm)
{
    theForm.Action.value = "Valider"
    theForm.submit();
}
</script>
</head>
<body class="login_page">
    <div class="login-wrapper">
        <div id="login" class="login loginpage offset-xl-4 col-xl-4 offset-lg-3 col-lg-6 offset-md-3 col-md-6 col-offset-0 col-12">
		<a href="/AppMobile/login.php"><img class="rounded mx-auto d-block" id="logo2" src="/AppMobile/img/logo_hodor.JPG"></a>

<?php include('pagemessage.php'); ?>

<?php switch($Sortie) {
	case "Normal" :
?>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="userform" class="visible" action="" method="post">
			<input name="id" type="hidden" value="<?php echo $id?>">
			<input name="Action" type="hidden" value="<?php echo $Action?>">   
            <p class="submit text-center mt-5">
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="username">Identifiant*</span>
					<input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
					<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="email">Email*</span>
					<input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
					<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="num_mobile">Numéro de mobile*</span>
					<input type="text" name="num_mobile" class="form-control" value="<?php echo $num_mobile; ?>">
					<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="role">Rôle*</span>
				<select name="role" size="1" id="role">
					<option value="0">Aucun</option>
					<option value="1" <?php if($role == "1") {?> selected <?php }?> >Administrateur</option>
					<option value="2" <?php if($role == "2") {?> selected <?php }?> >Visiteur</option>
				</select>
					<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="dt_start_access">Date début accès*</span>
					<input type="date" name="dt_start_access" class="form-control" value="<?php echo($dt_start_access)?$dt_start_access:null; ?>">
					<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="dt_end_access">Date fin accès*</span>
					<input type="date" name="dt_end_access" class="form-control" value="<?php echo($dt_end_access)?$dt_end_access:null; ?>">
					<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="dt_start_access">Début plage horaire accès*</span>
					<input type="time" name="time_start" class="form-control" value="<?php echo($time_start)?$time_start:null; ?>">
					<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="dt_end_access">Fin plage horaire accès*</span>
					<input type="time" name="time_end" class="form-control" value="<?php echo($time_end)?$time_end:null; ?>">
					<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
				<div class="input-group-prepend form-group <?php echo (!empty($Erreur)) ? 'has-error' : ''; ?>">
				<span class="input-group-text" id="status">Actif</span>
					<input type="checkbox" name="status" class="form-control" value="1" <?php if ($status == 1) {?> checked <?php } ?>">
				<span class="help-block"><?php echo $Erreur; ?></span>
				</div>
			</p>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Valider" OnClick="Valider(document.userform)">
			<?php if($id !== 0) { ?>
					<input type="button" class="btn btn-warning" value="Supprimer" OnClick="Supprimer(document.userform)">
			<?php } ?>
				</div>
		</form>
<?php 
	break;
	case "Erreur" :
 
?>
<?php 
	break;
	case "Suppression" : ?>
		<a href="list_users.php" class="btn btn-warning">Liste des utilisateurs</a>
<?php 
	}
?>
</body>
</html>
