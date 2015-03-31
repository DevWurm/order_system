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

    //get dependecies
	include('../includes/login.php');

//local function declarations
	function login_provider() {
		$provider_data = get_login_data();
		
		if ($provider_data != FALSE) {  //check if input data is correctly transfered
			if (validate_login_data($provider_data)) { //check if input data is valid
				if (validate_login($provider_data, 'provider')) { //check if login credentials are correct
					if(!isset($_SESSION['proof'])) {session_start(); $_SESSION['proof']=TRUE;} //start session if not started
					$_SESSION['provider_username'] = $provider_data['username'];
					$_SESSION['provider_password'] = $provider_data['password'];
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
		}
	}

?>
<?php
//execute login
	
	if (login_provider()) {
		header('Location: ./overview.php');
		exit();
	}
	else {
	?>
	<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8">
				
				
						<title>Login to Order-Provider</title>
						<meta name="description" content="Login eines Administrators">
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
								<h2>Falsche Login Daten!</h2>
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