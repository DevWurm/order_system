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
   	include ('../includes/session.php');
?>
<?php
//Execute Account overview
	
	if (!check_session('admin')) { //check if not logged in
		header('Location: ./index.php');
		exit();
	}
	else {
		
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
		<script type="text/javascript" src="./js/form_validation.js"></script>

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
				
				<h2>Account</h2>
				<?php //show current user name
					echo "<span class='user_user_name'>Benutzername: ".$_SESSION['admin_username']."</span><br />";	
				?>
					<h3>Passwort ändern</h3>
					<form action='./change_password.php' method='post' onsubmit='return validate_password_change()'>
					<input type='password' name='change_password' placeholder='Neues Passwort'/><br />
					<input type='password' name='change_repeat_password' placeholder='Passwort wiederholen'/><br />
					<input type='submit' value='Ändern' />
					</form><br />
					
					<h3>Account löschen</h3>
					<form action="./delete_admin.php" method="post" >
						<input type="submit" value="Löschen" />
					</form>	
					
					<h2>Neuer Administrator Account</h2>
					<form action='./register_admin.php' method='post' onsubmit='return validate_register()'>
					<input type='text' name='user_name' placeholder='Benutzername'/><br />
					<input type='password' name='password' placeholder='Neues Passwort'/><br />
					<input type='password' name='repeat_password' placeholder='Passwort wiederholen'/><br />
					<input type='submit' value='Registrieren' />
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