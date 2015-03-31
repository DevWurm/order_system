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
	}   	include ('../includes/session.php');
   	if (!isset($__READ_CONFIG__)) { //check if read_config.php is already included
   		include('read_config.php'); //include config parsing library
	}
	if (!isset($__SQL__)) { //check if sql.php is already included
   		include('sql.php'); //include SQL Functions library
	}
	
//local function declarations
	function get_shipping_data() { //get user input from POST and return false if date is not set
		if(!isset($_POST['product_id'])) {
			return FALSE;
		}
		else {
			$shipping_data['product_id'] = $_POST['product_id'];
		}
		if(!isset($_POST['product_quantity'])) {
			return FALSE;
		}
		else {
			$shipping_data['product_quantity'] = $_POST['product_quantity'];
		}
		return $shipping_data;
	}
	
	function validate_data($shipping_data) { //check if shipping quantity is not bigger than remaining 'capacities'
		$config = read_config(); //read mysql config
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		

		$db_data = mysqli_query($db_link, "SELECT quantity, max_quantity FROM products WHERE deprecated=0 AND quantity<=0.2*max_quantity AND p_id='".$shipping_data['product_id']."'");
		
		mysqli_close($db_link);
		
		if ($db_data != FALSE) { //check if an error occured
			if (mysqli_num_rows($db_data)==1) { //check if product ID exists
				$data = parse_sql_data($db_data);
			}
			else {
				die("FEHLER: Fehler beim übertragen der Daten!");
			}
		}
		else {
			die("FEHLER: Fehler beim Abfragen der Produkt Daten");
		}
		
		if (!(($data[0]['quantity'] + $shipping_data['product_quantity']) <= $data[0]['max_quantity'])) { //compare quantitys
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	function get_product_price($shipping_data) {  //get price for static_price
				$config = read_config(); //read mysql config
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		

		$db_data = mysqli_query($db_link, "SELECT price FROM products WHERE p_id='".$shipping_data['product_id']."'");
		
		mysqli_close($db_link);
		
		if ($db_data != FALSE) { //check if an error occured
			if (mysqli_num_rows($db_data)==1) { //check if product ID exists
				$data = parse_sql_data($db_data);
				return $data[0]['price']; // return price
			}
			else {
				die("FEHLER: Fehler beim übertragen der Daten!");
			}
		}
		else {
			die("FEHLER: Fehler beim Abfragen der Produkt Daten");
		}
	}
	
	function get_provider_id() { //get provider ID for pr_id in shippings table
		$config = read_config(); //read mysql config
		
		if(!isset($_SESSION['proof'])) {session_start(); $_SESSION['proof']=TRUE;} //start session if not started
		$user_name = $_SESSION['provider_username'];

		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		
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
	
	function create_shipping($shipping_data) { //register shipping to database
		$shipping_data['provider_id']= get_provider_id(); //get provider id
		
		$config = read_config(); //read mysql config
		
		$product_price = get_product_price($shipping_data);
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		//send update query to the database and return true at successfull and false at unsuccessfull handeling	
		if (!mysqli_query($db_link, "UPDATE products SET quantity=quantity+'".$shipping_data['product_quantity']."' WHERE p_id='".$shipping_data['product_id']."'")) {
			return FALSE;
		}
		else {
			//send register query to the database and return true at successfull and false at unsuccessfull handeling	
				if (!mysqli_query($db_link, "INSERT INTO shippings (pr_id, p_id, quantity, shipping_date, static_price) VALUES ('".$shipping_data['provider_id']."','".$shipping_data['product_id']."','".$shipping_data['product_quantity']."',CURDATE(), '".$product_price."')")) {
					return FALSE;
				}
				else {
					return TRUE;
				}
		}
		mysqli_close($db_link);
	}
?>
<?php
//execute order

if (!check_session('provider')) { //check if not logged in
	header('Location: ./index.php');
	exit();
}
else {
	$shipping_data = get_shipping_data();
	
	if ($shipping_data != FALSE) {
		if (validate_data($shipping_data)) {
			if (create_shipping($shipping_data)) {
					?>
					<!DOCTYPE html>
					<html lang="en">
						<head>
							<meta charset="utf-8">
					
					
							<title>Order</title>
							<meta name="description" content="Login eines Nutzers">
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
									<h2>Lieferung erfolgreich!</h2>
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
				die("FEHLER: Fehler beim abschließen der Bestellung!");
			}
		}
		else {
			die("FEHLER: Fehlerhafte Daten!");
		}
	}
	else {
		die("FEHLER: fehler beim Übertragen der Daten!");
	}
}
?>