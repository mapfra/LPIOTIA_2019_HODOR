<?php
// Include config file
require_once "config_db.php";
include "my_log.php";
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
    <title>HODOR - Admin - liste utilisateurs</title>
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
        <div id="login" class="login loginpage offset-xl-2 col-xl-8 offset-lg-3 col-lg-6 offset-md-3 col-md-6 col-offset-0 col-12">
        <a href="/AppMobile/logout.php"><img class="rounded mx-auto d-block" src="/AppMobile/img/logo_hodor.JPG"></a>
		<div class="col-lg-12 col-md-12 col-12 mt-5">
        <div class="table-responsive">
            <table id="tableDelaiUrba" class="table table-bordered table-striped table-hover text-center">
                <thead>
					<tr> 
						<td nowrap>ID</td>
						<td>Identifiant</td>
						<td>Email</td>
						<td>N° mobile</td>
						<td>Rôle</td>
						<td>Début accès</td>
						<td>Fin accès</td>
						<td>Début plage horaire accès</td>
						<td>Fin plage horaire accès</td>
						<td>Status</td>
						<td>Crée le</td>
					</tr>
                </thead>
                <tbody>
				<?php 
					// Prepare an insert statement
					$sql = "SELECT id, username, email, num_mobile, role, dt_start_access, dt_end_access, time_start, time_end, status, created_at FROM users";
					$stmt = $pdo->prepare($sql);
					$stmt->execute();
					while ( $results = $stmt->fetch(PDO::FETCH_ASSOC) ) { // Boucle sur les users
					?>
						<td><a id="login_ref" href="admin_user.php?id=<?php echo $results["id"];?>"><?php echo $results["id"];?></a></td>
						<td><?php echo $results["username"];?></td>
						<td><?php echo $results["email"];?> </td>	 
						<td><?php echo $results["num_mobile"];?></td>
						<td><?php echo ($results["role"] == "Admin" ||$results["role"] == "Responsable") ? "<b>".$results["role"]."</b>" : $results["role"] ?></td>
						<td><?php echo $results["dt_start_access"];?></td>
						<td><?php echo $results["dt_end_access"];?></td>
						<td><?php echo $results["time_start"];?></td>
						<td><?php echo $results["time_end"];?></td>	
						<td><?php echo $results["status"];?></td>		
						<td><?php echo $results["created_at"];?></td>
					</tr>
					<?php 
					}                                                    // Fin Boucle sur les users
				?>
                </tbody>
			</table>
			<div class="form-group">
			<a href="admin_user.php" class="btn btn-warning">Créer un utilisateur</a>
            </div>
        </div>
    </div>
</body>
</html>
<footer>
    <script type="text/javacript" src="/AppMobile/node_modules/bootstrap/dist/bootstrap.min.js"></script>
</footer>