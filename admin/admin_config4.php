<?php
/**
 * admin_config4.php
 * Interface permettant à l'administrateur la configuration de certains paramètres généraux (sécurité, connexions)
 * Ce script fait partie de l'application GRR
 * Dernière modification : $Date: 2018-08-22 11:15$
 * @author    Laurent Delineau & JeromeB & Yan Naessens
 * @copyright Copyright 2003-2018 Team DEVOME - JeromeB
 * @link      http://www.gnu.org/licenses/licenses.html
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */
// page à internationaliser 
$grr_script_name = "admin_config4.php";

include "../include/admin.inc.php";

$back = '';
if (isset($_SERVER['HTTP_REFERER']))
	$back = htmlspecialchars($_SERVER['HTTP_REFERER']);
$_SESSION['chemin_retour'] = "admin_accueil.php";
$day   = date("d");
$month = date("m");
$year  = date("Y");
check_access(6, $back);
//vérifications
if (!Settings::load())
	die("Erreur chargement settings");
if (isset($_GET['motdepasse_backup']))
{
	if (!Settings::set("motdepasse_backup", $_GET['motdepasse_backup']))
	{
		echo "Erreur lors de l'enregistrement de motdepasse_backup !<br />";
		die();
	}
}
if (isset($_GET['disable_login']))
{
	if (!Settings::set("disable_login", $_GET['disable_login']))
	{
		echo "Erreur lors de l'enregistrement de disable_login !<br />";
		die();
	}
}
if (isset($_GET['url_disconnect']))
{
	if (!Settings::set("url_disconnect", $_GET['url_disconnect']))
		echo "Erreur lors de l'enregistrement de url_disconnect ! <br />";
}
// Restriction iP
if (isset($_GET['ip_autorise']))
{
	if (!Settings::set("ip_autorise", $_GET['ip_autorise']))
		echo "Erreur lors de l'enregistrement de ip_autorise !<br />";
}
// Max session length
if (isset($_GET['sessionMaxLength']))
{
	settype($_GET['sessionMaxLength'], "integer");
	if ($_GET['sessionMaxLength'] < 1)
		$_GET['sessionMaxLength'] = 30;
	if (!Settings::set("sessionMaxLength", $_GET['sessionMaxLength']))
		echo "Erreur lors de l'enregistrement de sessionMaxLength !<br />";
}
// pass_leng
if (isset($_GET['pass_leng']))
{
	settype($_GET['pass_leng'], "integer");
	if ($_GET['pass_leng'] < 1)
		$_GET['pass_leng'] = 1;
	if (!Settings::set("pass_leng", $_GET['pass_leng']))
		echo "Erreur lors de l'enregistrement de pass_leng !<br />";
}
// début du code html
# print the page header
start_page_w_header("", "", "", $type="with_session");
if (isset($_GET['ok']))
{
	$msg = get_vocab("message_records");
	affiche_pop_up($msg,"admin");
}
// Affichage de la colonne de gauche
include "admin_col_gauche2.php";
echo '<div class="col-md-9 col-sm-8 col-xs-12">';
echo "<h2>".get_vocab('admin_config4.php')."</h2>";
//
// dans le cas de mysql, on propose une sauvegarde et l'ouverture de la base
//
if ($dbsys == "mysql")
{
	//
	// Saving base
	//********************************
	//
	echo "<h3>".get_vocab('title_backup')."</h3>\n";
	echo "<p>".get_vocab("explain_backup")."</p>\n";
	echo "<p><i>".get_vocab("warning_message_backup")."</i></p>\n";
	?>
	<form action="admin_save_mysql.php" method="get" style="width:100%;">
		<div class="center">
			<input type="hidden" name="flag_connect" value="yes" />
			<input class="btn btn-primary" type="submit" value=" <?php echo get_vocab("submit_backup"); ?>" style="font-variant: small-caps;" />
		</div>
	</form>
		<?php
		//
		// Loading base
		//********************************
		//
	if($restaureBBD == 1){
		echo "\n<hr /><h3>".get_vocab('Restauration de la base GRR')."</h3>";
		echo "\n<p>En cas de perte de donnée ou de problème sur la base GRR, cette fonction vous permet de la retrouver dans l'état antérieur lors d'une sauvegarde. Vous devez sélectionner un fichier créé à l'aide de la fonction Lancer une sauvegarde.</p>";
		echo "\n<p><span class=\"avertissement\"><i>Attention! Restaurer la base vous fera perdre toutes les données qu'elle contient actuellement. De plus, tous les utilisateurs présentement connectés, ainsi que vous-mêmes, serez déconnectés. Alors, il est conseillé de créer d'abord une sauvegarde et de vous assurer que vous êtes le seul connecté.</i></span></p>\n";
		?>
		<form method="post" enctype="multipart/form-data" action="admin_open_mysql.php">
			<div class="center">
				<input type="file" name="sql_file" size="30" />
				<br /><br />
				<input class="btn btn-primary" type="submit" value="<?php echo get_vocab('Restaurer la sauvegarde'); ?>" style="font-variant: small-caps;" />
			</div>
		</form>
<?php
	}
}
	echo "<form action=\"./admin_config4.php\" method=\"get\">";
	# Backup automatique
	echo "\n<hr /><h3>".get_vocab("execution automatique backup")."</h3>";
	echo "<p>".get_vocab("execution automatique backup explications")."</p>";
	echo "\n<p>".get_vocab("execution automatique backup mdp").get_vocab("deux_points");
	echo "\n<input class=\"form-control\" type=\"password\" name=\"motdepasse_backup\" value=\"".Settings::get("motdepasse_backup")."\" size=\"20\" /></p>";
	//
	// Suspendre les connexions
	//*************************
	//
	echo "\n<hr /><h3>".get_vocab('title_disable_login')."</h3>";
	echo "\n<p>".get_vocab("explain_disable_login");
    echo "<br />";
	echo "<input type='radio' name='disable_login' value='yes' id='label_1' ";
    if (Settings::get("disable_login")=='yes') echo "checked=\"checked\""; 
    echo "/>";
	echo "<label for='label_1'>".get_vocab("disable_login_on")."</label>";
	echo "<br />";
	echo "<input type='radio' name='disable_login' value='no' id='label_2' ";
    if (Settings::get("disable_login")=='no') echo "checked=\"checked\"";
    echo " />";
	echo "<label for='label_2'>".get_vocab("disable_login_off")."</label>";
	echo "</p>";
	//
	// iP autorisé
	//*************************
	//
	echo "\n<hr /><h3>".get_vocab('title_ip_autorise')."</h3>";
	echo "\n<p>".get_vocab("explain_ip_autorise")."</p>";
	echo "<br />";
	echo '<input class="form-control" type="text" name="ip_autorise" value="'.(Settings::get("ip_autorise")).'" />';
    echo "\n<hr />";
	//
	// Durée d'une session
	//********************
	//
echo "<h3>".get_vocab("title_session_max_length")."</h3>";
?>
<table>
	<tr>
		<td>
			<?php echo get_vocab("session_max_length"); ?>
		</td>
		<td>
			<input type="number" name="sessionMaxLength" size="5" value="<?php echo(Settings::get("sessionMaxLength")); ?>" />
		</td>
	</tr>
</table>
<?php echo "<p>".get_vocab("explain_session_max_length")."</p>";
//Longueur minimale du mot de passe exigé
echo "<hr /><h3>".get_vocab("pwd")."</h3>";
echo "\n<p>".get_vocab("pass_leng_explain").get_vocab("deux_points")."
<input type=\"number\" name=\"pass_leng\" value=\"".htmlentities(Settings::get("pass_leng"))."\" size=\"5\" /></p>";
//
// Url de déconnexion
//*******************
//
echo "<hr /><h3>".get_vocab("Url_de_deconnexion")."</h3>\n";
echo "<p>".get_vocab("Url_de_deconnexion_explain")."</p>\n";
echo "<p><i>".get_vocab("Url_de_deconnexion_explain2")."</i>";
echo "<br />".get_vocab("Url_de_deconnexion").get_vocab("deux_points")."\n";
$value_url = Settings::get("url_disconnect");
echo "<input class=\"form-control\" type=\"text\" name=\"url_disconnect\" size=\"40\" value =\"$value_url\"/>\n<br /><br /></p>";
// echo "\n<hr />";
echo "<div id=\"fixe\" ><input class=\"btn btn-primary\" type=\"submit\" name=\"ok\" value=\"".get_vocab("save")."\" style=\"font-variant: small-caps;\"/></div>";
echo "\n</form>";
// fin de l'affichage de la colonne de droite et de la page
echo "\n</div></section></body></html>";
?>