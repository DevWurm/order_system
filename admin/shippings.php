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
	
	function get_shippings() { //get all shippings from database
			
		//get config
		$config = read_config(); //read config data
		
		//get database data
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) {
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		//send select query
		$db_data = mysqli_query($db_link, "SELECT products.product_name, shippings.static_price, shippings.quantity, shippings.shipping_date, providers.provider_name, providers.user_name FROM providers INNER JOIN (shippings INNER JOIN products ON shippings.p_id = products.p_id) ON shippings.pr_id = providers.pr_id");
		
		mysqli_close($db_link);
		
		if ($db_data != FALSE) { //check if an error occured
			if (mysqli_num_rows($db_data)>0) { //check if data are in existent
				$data = parse_sql_data($db_data);
				return $data; //return parsed data
			}
			else {
				return FALSE;
			}
		}
		else {
			die("FEHLER: Fehler beim lesen der Datenbank");
		}
	
	}
?>
<?php
//Execute old Orders overview
	
	if (!check_session('admin')) { //check if not logged in
		header('Location: ./index.php');
		exit();
	}
	else {
		
	$orders = get_shippings();
		
		
		
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
		<link rel="stylesheet" type="text/css" href="./css/shippings.css" />

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
				
				
				<?php
				if ($orders != FALSE) {  //if data are existent print shippings 'pseudo-table'
					echo "<h2>Lieferungen</h2>";
					echo "<span class='shipping_user_name'><b>Benutzername</b></span>";
					echo "<span class='shipping_provider_name'><b>Anbietername</b></span>";
					echo "<span class='shipping_product'><b>Produktname</b></span>";
					echo "<span class='shipping_price'><b>Preis</b></span>";
					echo "<span class='shipping_quantity'><b>Liefermenge</b></span>";
					echo "<span class='shipping_date'><b>Lieferdatum</b></span><br /><br />";
					for ($i = 0; $i <= count($orders)-1; $i++) {
						echo "<span class='shipping_user_name'>".$orders[$i]['user_name']."</span>";
						echo "<span class='shipping_provider_name'>".$orders[$i]['provider_name']."</span>";
						echo "<span class='shipping_product'>".$orders[$i]['product_name']."</span>";
						echo "<span class='shipping_price'>".$orders[$i]['static_price']."€</span>";
						echo "<span class='shipping_quantity'>".$orders[$i]['quantity']."</span>";
						echo "<span class='shipping_date'>".$orders[$i]['shipping_date']."</span><br /><br />";
					}
				}
				else {
					echo "<h2>Keine Lieferungen!</h2>";
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