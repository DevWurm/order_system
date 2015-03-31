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

    include ('../includes/session.php');
	
	if (check_session('admin')) { //check if logged in
		header('Location: ./overview.php'); //redirect to overview
		exit();
	}
	else {
		//Login Page
?>
	<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<title>Login to Order Admin</title>
		<meta name="author" content="DevWurm">


		<!-- Stylesheets -->
		<link rel="stylesheet" type="text/css" href="./css/body.css" />
		<link rel="stylesheet" type="text/css" href="./css/nav.css" />
		<link rel="stylesheet" type="text/css" href="./css/login.css" />
		<link rel="stylesheet" type="text/css" href="./css/header.css" />
		<link rel="stylesheet" type="text/css" href="./css/dynamic_ui.css" />
		
		<!-- JavaScript -->
		<script src="./js/form_validation_login.js" type="text/javascript"></script>
	</head>

	<body>
		<div>
			<header>
				<h1>Order</h1>
			</header>
			<nav>
				<ul>
					<li>
						<a href="/">Home</a>
					</li>
				</ul>
			</nav>
			<div id="login">
				<h2>Login</h2>
				<form method="post" action="login_admin.php" onsubmit="return validate_login()">
					<input type="text" name="login_username" placeholder="Benutzername"/>
					<input type="password" name="login_password" placeholder="Passwort"/>
					<input type="submit" value="Einloggen">
				</form> 
			</div>
			
			<div>
				<h2>Registrierung kann nur von einem anderen Administratorkonto durchgeführt werden!</h2>
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
	
