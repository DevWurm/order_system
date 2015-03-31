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
	function get_products() {
		
	//get config
	$config = read_config(); //read config data
	
	//get database data
	$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
	if (!$db_link) {
		die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
	}
	
	$db_data = mysqli_query($db_link, "SELECT p_id, product_name, quantity, max_quantity, deprecated, price FROM products");
	
	mysqli_close($db_link);
	
	if ($db_data != FALSE) {
		if (mysqli_num_rows($db_data)>0) {
			$data = parse_sql_data($db_data);
		}
		else {
			return FALSE;
		}
	}
	else {
		die("FEHLER: Fehler beim lesen der Datenbank");
	}
	
	return $data;
	}
?>
<?php
//Execute products overview	
	if (!check_session('admin')) { //check if not logged in
		header('Location: ./index.php');
		exit();
	}
	else {
		$products = get_products();
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
		<link rel="stylesheet" type="text/css" href="./css/overview.css" />
		<link rel="stylesheet" type="text/css" href="./css/dynamic_ui.css" />
		
		<!-- Scripts -->
		<script src="./js/form_validation_product.js" type="text/javascript"></script>
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
						<a href="./orders.php">Bestellungen</a>
					</li>
					<li>
						<a href="./shippings.php">Lieferungen</a>
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
				<h2>Neues Produkt</h2>
				<form action="./add_product.php" method="post" id="new_product" onsubmit="return validate_product_input('new_product');">
					<input type="text" name="product_name" class="product_name" placeholder="Produktname" />
					<input type="number" name="product_price" min='0.00' max='999999999.99' step='0.01' class="product_price" placeholder="Preis" />
					<input type="number" name="product_quantity" class="product_quantity" step='1' min="0" max="99999999999" placeholder="Lagermenge" />
					<input type="number" name="product_max_quantity" class="product_max_quantity" step='1' min="0" max="99999999999" placeholder="max. Lagermenge" />
					<input type="submit" class="add_button" value="Hinzufügen" /><br /><br />
				</form>
				
				<?php
				if ($products != FALSE) {
					echo "<h2>Produkte</h2>";
					echo "<span class='product_name'><b>Produktname</b></span>";
					echo "<span class='product_price'><b>Preis (€)</b></span>";
					echo "<span class='product_quantity'><b>Lagermenge</b></span>";
					echo "<span class='product_max_quantity'><b>max. Lagermenge</b></span>";
					echo "<span class='product_deprecated'><b>Ausgemustert</b></span><br /> <br />";
					for ($i = 0; $i <= count($products)-1; $i++) {
						echo "<form action='./change_product.php' method='post' onsubmit='return validate_product_input('product_".$products[$i]['p_id']."');' id='product_".$products[$i]['p_id']."'>";
						echo "<input type='text' hidden name='product_id' value='".$products[$i]['p_id']."'>";
						echo "<input type='text' name='product_name' class='product_name' placeholder='Produktname' value='".$products[$i]['product_name']."' />";
						echo "<input type='number' name='product_price' class='product_price' step='0.01' placeholder='Preis' value='".$products[$i]['price']."' />";
						echo "<input type='number' name='product_quantity' class='product_quantity' step='1' placeholder='Lagermenge' value='".$products[$i]['quantity']."' />";
						echo "<input type='number' name='product_max_quantity' class='product_max_quantity' step='1' placeholder='max. Menge' value='".$products[$i]['max_quantity']."' />";
						if ($products[$i]['deprecated'] == 0) {
							echo "<input type='checkbox' name='product_deprecated' class='product_deprecated' />";
						}
						else {
							echo "<input type='checkbox' name='product_deprecated' class='product_deprecated' checked/>";
						}
						echo "<input type='submit' class='change_button' value='Ändern' />";
						echo "</form><br /><br />";
					}
				}
				else {
					echo "<h2>Keine Produkte!</h2>";
				}			
				?>
				
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