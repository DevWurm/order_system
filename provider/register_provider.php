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
   	if (!isset($__READ_CONFIG__)) { //check if read_config.php is already included
   		include('read_config.php'); //include config parsing library
	}


//local function declaration

 	function get_provider_data() { //get user input from POST and return false if date is not set
 		
		if (!isset($_POST['provider_name'])) {
 			return FALSE;
 		}
		else {
			$provider_data['provider_name'] = $_POST['provider_name'];
		}
		
		if (!isset($_POST['user_name'])) {
 			return FALSE;
 		}
		else {
			$provider_data['user_name'] = $_POST['user_name'];
		}
		
		if (!isset($_POST['password'])) {
 			return FALSE;
 		}
		else {
			$provider_data['password'] = $_POST['password'];
		}
		
		if (!isset($_POST['repeat_password'])) {
 			return FALSE;
 		}
		else {
			$provider_data['repeat_password'] = $_POST['repeat_password'];
		}
		return $provider_data;
 	}
	
	
	function validate_unique_user_name($user_name) { //check if user name is not used in the database
		
		$config = read_config(); //read mysql config
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		
		$db_data = mysqli_query($db_link, "SELECT pr_id FROM providers WHERE user_name='".$user_name."'"); //search for accounts with the same user name
		mysqli_close($db_link);

		if ($db_data != FALSE) {
		if(mysqli_num_rows($db_data)==0) { //if no accounts were found return true
			return TRUE;
		}
		else { //otherwise display error message
				?>
				<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8">
				
				
						<title>Registrierung-Provider</title>
						<meta name="description" content="Registrierung eines neuen Nutzers">
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
								<h2>Benutzername bereits vergeben!</h2>
								<a href="./index.php">Zurück zum login</a>
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
			exit(); //exit script after message
			}
		}
		else {
			die("FEHLER: Fehler beim Abrufen der Datenbank!");
		}	
	}

	
	
	function validate_data($provider_data) { //validate all user data (correct length, not null, password and repetition equal), otherwise return false
		
		if(validate_unique_user_name($provider_data['user_name'])) { //check if user name is unique, then validate other data
			
			if (strlen($provider_data['provider_name'])>50 || strlen($provider_data['provider_name'])==0) {
				return FALSE;
			}
			else if (strlen($provider_data['user_name'])>50 || strlen($provider_data['user_name'])==0) {
				return FALSE;
			}
			else if (strlen($provider_data['password'])==0 || $provider_data['password']!=$provider_data['repeat_password']) {
				return FALSE;
			}
			else {
				return TRUE;
			}
		
		}
		else {
			return FALSE; //return false if user name is allready uses (shouldn't be called, because exit is calles in validate_unique_user_name if user name is used)
		}	
	}
	
	
	function register_provider_to_database($provider_data) { //register user to database
			
		$provider_data['password']=hash('sha512', $provider_data['password']); //create hash from password
		
		$config = read_config(); //read mysql config
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		
		//send register query to the database and return true at successfull and false at unsuccessfull handeling	
		if (!mysqli_query($db_link, "INSERT INTO providers (provider_name, user_name, pwhash) VALUES('".$provider_data['provider_name']."', '".$provider_data['user_name']."', '".$provider_data['password']."')")) {
			return FALSE;
		}
		else {
			return TRUE;
		}
		mysqli_close($db_link);
	}
	
	function register_provider() {	
		$provider_data = get_provider_data(); //get user data
		
		if ($provider_data == FALSE) { //check handle missing data
				die("FEHLER: Fehler beim übermitteln der Daten!");
		}
		else {
			if (!validate_data($provider_data)) { //check and handle incorrect data
				die("FEHLER: Fehlerhafte Daten!");
			}
			else {
				if(!register_provider_to_database($provider_data)) { //register user and handle possible errors
					die("FEHLER: Fehler beim abschließen der Registrierung!");
				}
				else {
					return TRUE;
				}
			}
		}
	}
?>
<?php

//execute registration
	
	if (register_provider()) { //register user and print success message on success
		?>
			<!DOCTYPE html>
			<html lang="en">
				<head>
					<meta charset="utf-8">
			
			
					<title>Registrierung - Provider</title>
					<meta name="description" content="Registrierung eines neuen Nutzers">
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
							<h2>Registrierung erfolgreich abgeschlossen!</h2>
							<a href="./index.php">Zurück zum login</a>
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