<?php
  	/*
	 * License
	 
	 * Copyright 2015 DevWurm
	
	 * This file is part of order_system.

	 *  order_system is free software: you can redistribute it and/or modify
	    it under the terms of the GNU General Public License as published by
	    the Free Software Foundation, either version 3 of the License, or
	    (at your option) any later version.
	
	    order_system is distributed in the hope that it will be useful,
	    but WITHOUT ANY WARRANTY; without even the implied warranty of
	    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	    GNU General Public License for more details.
	
	    You should have received a copy of the GNU General Public License
	    along with order_system.  If not, see <http://www.gnu.org/licenses/>.
	
	    Diese Datei ist Teil von order_system.
	
	    order_system ist Freie Software: Sie können es unter den Bedingungen
	    der GNU General Public License, wie von der Free Software Foundation,
	    Version 3 der Lizenz oder (nach Ihrer Wahl) jeder späteren
	    veröffentlichten Version, weiterverbreiten und/oder modifizieren.
	
	    order_system wird in der Hoffnung, dass es nützlich sein wird, aber
	    OHNE JEDE GEWÄHRLEISTUNG, bereitgestellt; sogar ohne die implizite
	    Gewährleistung der MARKTFÄHIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK.
	    Siehe die GNU General Public License für weitere Details.
	
	    Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
	    Programm erhalten haben. Wenn nicht, siehe <http://www.gnu.org/licenses/>.
*/

//get dependencies
	if (DIRECTORY_SEPARATOR == '\\') { //Windows Systems --> use ; as seperator
    	set_include_path(get_include_path().";../includes".";./includes"); //set include directory, so includes folder is available from every (sub)directory
	}
	else { //unix Systems --> use : as seperator
		set_include_path(get_include_path().":../includes".":./includes"); //set include directory, so includes folder is available from every (sub)directory
	}
   	include ('../includes/session.php');
   	if (!isset($__READ_CONFIG__)) { //check if read_config.php is already included
   		include('read_config.php'); //include config parsing library
	}
	if (!isset($__SQL__)) { //check if sql.php is already included
   		include('sql.php'); //include SQL Functions library
	}
	
//local function declarations

function get_provider_data() {
	if(!isset($_SESSION['proof'])) {session_start(); $_SESSION['proof']=TRUE;} //start session if not started
	
	$user_name = $_SESSION['provider_username'];
	
	//get config
	$config = read_config(); //read config data
		
	//get database data
	$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
	if (!$db_link) {
		die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
	}
	
	//send select query to database	
	$db_data = mysqli_query($db_link, "SELECT provider_name, user_name FROM providers WHERE user_name='".$user_name."'");
	
	mysqli_close($db_link);
	
	if ($db_data != FALSE) {  //check if an error occured
		if (mysqli_num_rows($db_data)>0) {  //check if iser name is existing
			$data = parse_sql_data($db_data);
			return $data; // return parsed data
		}
		else {
			die("FEHLER: Fehler beim Abrufend der Nutzerdaten!");
		}
	}
	else {
		die("FEHLER: Fehler beim lesen der Datenbank");
	}

}
?>
<?php
//Execute Account overview
	
	if (!check_session('provider')) { //check if not logged in
		header('Location: ./index.php');
		exit();
	}
	else {
		
	$provider_data = get_provider_data();
		
		
		
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<title>Order</title>
		<meta name="author" content="DevWurm">


		<!-- Stylesheets -->
		<link rel="stylesheet" type="text/css" href="./css/body.css" />
		<link rel="stylesheet" type="text/css" href="./css/nav.css" />
		<link rel="stylesheet" type="text/css" href="./css/header.css" />
		<link rel="stylesheet" type="text/css" href="./css/dynamic_ui.css" />
		
		<!-- Scripts -->
		<script type="text/javascript" src="./js/form_validation_change_password.js"></script>

	</head>

	<body>
		<div>
			<header>
				<h1>Order</h1>
			</header>
			<nav>
				<ul>
					<li>
						<a href="./overview.php">Home</a>
					</li>
					<li>
						<a href="./old_shippings.php">Lieferungen</a>
					</li>
					<li>
						<a href="./account.php">Account</a>
					</li>
					<li>
						<a href="./logout.php">Logout</a>
					</li>
				</ul>
			</nav>
			
			<div>
				
				
				
					<h2>Account</h2>
					<?php //print provider data
					echo "<span class='provider_provider_name'>Anbietername: ".$provider_data[0]['provider_name']."</span><br />";	
					echo "<span class='provider_user_name'>Benutzername: ".$provider_data[0]['user_name']."</span><br />";	
					?>
					<h3>Passwort ändern</h3>
					<form action='./change_password.php' method='post' onsubmit='return validate_data()'>
					<input type='password' name='password' placeholder='Neues Passwort'/><br />
					<input type='password' name='repeat_password' placeholder='Passwort wiederholen'/><br />
					<input type='submit' value='Ändern' />
					</form><br />
					
					<h3>Account löschen</h3>
					<form action="./delete_provider.php" method="post" >
						<input type="submit" value="Löschen" />
					</form>
					
				
				
			</div>

			<footer>
				<p>
					order_system von DevWurm ist lizenziert unter GPL3 Lizenz
				</p>
			</footer>
		</div>
	</body>
</html>

<?php		
	}
?>