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

	function get_product_data() { //get user input from POST and return false if date is not set
		if (!isset($_POST['product_name'])) {
			return FALSE;
		}
		else {
			$product_data['product_name'] = $_POST['product_name'];
		}
		if (!isset($_POST['product_price'])) {
			return FALSE;
		}
		else {
			$product_data['product_price'] = $_POST['product_price'];
		}
		if (!isset($_POST['product_quantity'])) {
			return FALSE;
		}
		else {
			$product_data['product_quantity'] = $_POST['product_quantity'];
		}
		if (!isset($_POST['product_max_quantity'])) {
			return FALSE;
		}
		else {
			$product_data['product_max_quantity'] = $_POST['product_max_quantity'];
		}

		return $product_data;
	}
	
	function validate_unique_product_name($product_data) {
		//get config
		$config = read_config(); //read config data
		
		//get database data
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) {
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		//Send select query
		$db_data = mysqli_query($db_link, "SELECT p_id FROM products WHERE product_name='".$product_data['product_name']."'");
		
		mysqli_close($db_link);
		
		if ($db_data != FALSE) { //check if an error occured
			if (mysqli_num_rows($db_data)>0) { //check if the product name already exists
				return FALSE; // --> not unique
			}
			else {
				return TRUE; // --> unique
			}
		}
		else {
			die("FEHLER: Fehler beim lesen der Datenbank");
		}
	}
	
	function validate_product_data ($product_data) {
		if (!validate_unique_product_name($product_data)){ //display message if name is already used
			?>
			<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8">
				
				
						<title>Produkt hinzufügen</title>
						<meta name="description" content="Produktdaten aktualisieren">
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
								<h2>Produktname bereits vergeben!</h2>
								<a href="./overview.php">Zurück zur Übersicht</a>
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
		//validate other data (no failure is expected, because errors should be handled in front end)
		else if (strlen($product_data['product_name'])>255 || strlen($product_data['product_quantity'])>11 || strlen($product_data['product_max_quantity'])>11 || strlen($product_data['product_price'])>11) {
			return FALSE;
		}
		else {
			return TRUE;
		} 
	}
	
	function add_product($product_data) { //insert product into database
		$config = read_config(); //read mysql config
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		//send insert query to the database and return true at successfull and false at unsuccessfull handeling	
		if (!mysqli_query($db_link, "INSERT INTO products (product_name, quantity, max_quantity, price, deprecated) values('".$product_data['product_name']."','".$product_data['product_quantity']."', '".$product_data['product_max_quantity']."', '".$product_data['product_price']."', false)")) {
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
?>
<?php
//execute insertion
	if (!check_session('admin')) { //check if not logged in
		header('Location: ./index.php');
		exit();
	}
	else {
	$product_data = get_product_data();
	
	if ($product_data != FALSE) {
		if (validate_product_data($product_data)) {
			if (add_product($product_data)) {
							?>
			<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8">
				
				
						<title>Produkt Hinzufügen</title>
						<meta name="description" content="Produktdaten aktualisieren">
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
								<h2>Produkt hinzugefügt!</h2>
								<a href="./overview.php">Zurück zur Übersicht</a>
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
				die("FEHLER: Fehler beim Eintragen in die Datenbank!");
			}
		}
		else {
			die("FEHLER: Fehlerhafte Daten!");
		}
	}
	else {
		die("FEHLER: Fehler beim Übertragen der Daten!");
	}

	}
?>