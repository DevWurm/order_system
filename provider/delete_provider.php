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
	
//Local function declarations

	function get_provider_id() { //get user ID for deletion
		$config = read_config(); //read mysql config
		
		if(!isset($_SESSION['proof'])) {session_start(); $_SESSION['proof']=TRUE;} //start session if not started
		$user_name = $_SESSION['provider_username'];

		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		//send select query to database
		$db_data = mysqli_query($db_link, "SELECT pr_id FROM providers WHERE user_name='".$user_name."'");
		
		mysqli_close($db_link);
		
		if ($db_data != FALSE) { //check if an error occured
			if (mysqli_num_rows($db_data)==1) { //check if user name exists
				$data = parse_sql_data($db_data);
				return $data[0]['pr_id']; //return provider ID
			}
			else {
				die("FEHLER: Fehler beim übertragen der Daten!");
			}
		}
		else {
			die("FEHLER: Fehler beim Abfragen der Nutzer Daten");
		}
	}
	
	function delete_references($provider_id) { //delete referencing table entries
		$config = read_config(); //read mysql config
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		
		//send delete query to the database and return true at successfull and false at unsuccessfull handeling	
		if (!mysqli_query($db_link, "DELETE FROM shippings WHERE pr_id='".$provider_id."'")) {
			return FALSE;
		}
		else {
			return TRUE;
		}
		mysqli_close($db_link);
	}
	
	function delete_provider($provider_id) { //delete provider from database
		$config = read_config(); //read mysql config
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		
		//send delete query to the database and return true at successfull and false at unsuccessfull handeling	
		if (!mysqli_query($db_link, "DELETE FROM providers WHERE pr_id='".$provider_id."'")) {
			return FALSE;
		}
		else {
			return TRUE;
		}
		mysqli_close($db_link);
	}
?>
<?php
// Execute deletion
if (!check_session('provider')) { //check if not logged in
		header('Location: ./index.php');
		exit();
	}
	else {
		$provider_id = get_provider_id();
		
		if(!isset($_SESSION['proof'])) {session_start(); $_SESSION['proof']=TRUE;} //start session if not started
		session_destroy();
		
		if (delete_references($provider_id)) {
			if (delete_provider($provider_id)) {
					?>
					<!DOCTYPE html>
					<html lang="en">
						<head>
							<meta charset="utf-8">
					
					
							<title>Order</title>
							<meta name="description" content="Löschen des Accounts">
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
									<h2>Account gelöscht!</h2>
									<a href="./index.php">Zur Registrierung!</a>
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
				die("FEHLER: Fehler beim Löschen des Accounts!");
			}
		}
		else {
			die("FEHLER: Fehler beim Löschen der Abhänigkeiten!");
		}		
		
	}
?>