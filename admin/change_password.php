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
	
//local function declarations

function get_password_data() { //get user input from POST and return false if date is not set
	if (!isset($_POST['change_password'])) {
		return FALSE;
	}
	else {
		$password_data['password'] = $_POST['change_password'];
		$password_data['pwhash']=hash('sha512', $password_data['password']); //create hash from password
	}
	if (!isset($_POST['change_repeat_password'])) {
		return FALSE;
	}
	else {
		$password_data['repeat_password'] = $_POST['change_repeat_password'];
	}
	return $password_data;
}

function validate_data($password_data) { //check if password is not empty and is equal to repetition
	if ($password_data['password'] == $password_data['repeat_password'] && strlen($password_data['password']) > 0) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function update_password($password_data) { //update password in database
	if(!isset($_SESSION['proof'])) {session_start(); $_SESSION['proof']=TRUE;} //start session if not started
	
	$config = read_config(); //read mysql config
		
	$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
	if (!$db_link) { //handle connection error
		die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
	}
	
	
	//send update query to the database and return true at successfull and false at unsuccessfull handeling	
	if (!mysqli_query($db_link, "UPDATE admins SET pwhash='".$password_data['pwhash']."' WHERE user_name='".$_SESSION['admin_username']."'")) {
		return FALSE;
	}
	else {
		$_SESSION['admin_password'] = $password_data['password']; //update current session
		return TRUE;
	}
	mysqli_close($db_link);
}
?>
<?php
//execute change

	if (!check_session('admin')) { //check if not logged in
		header('Location: ./index.php');
		exit();
	}
	else {
		
		$password_data = get_password_data();
		
		if ($password_data != FALSE) {
			if (validate_data($password_data)) {
				if (update_password($password_data)) {
					?>
					<!DOCTYPE html>
					<html lang="en">
						<head>
							<meta charset="utf-8">
					
					
							<title>Order</title>
							<meta name="description" content="Ändern des Passworts">
							<meta name="author" content="DevWurm">
					
					
							<!-- Stylesheets -->
							<link rel="stylesheet" type="text/css" href="./css/body.css" />
							<link rel="stylesheet" type="text/css" href="./css/header.css" />
							
						</head>
					
						<body>
							<div>
								<header>
									<h1>Order</h1>
								</header>
					
								<div>							
									<h2>Passwortänderung erfolgreich!</h2>
									<a href="./account.php">Zurück zur Accountübersicht</a>
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
				else {
					die("FEHLER: Fehler beim aktualisiern des Passworts");
				}
			}
			else {
				die("FEHLER: Fehlerhafte Daten");
			}
		}
		else {
			die("FEHLER: Fehler beim Übertragen der Daten");
		}
		
		
	}


?>