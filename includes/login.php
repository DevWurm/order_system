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
	if (!isset($__SQL__)) { //check if sql.php is already included
   		include('sql.php'); //include SQL Functions library
	}
	
	
//function declarations
	function get_login_data() { //get user input from POST and return false if date is not set
		if (!isset($_POST['login_username'])) {
			die("FEHLER: Fehler beim übertragen der Daten!");
		}
		else {
			$login_data['username'] = $_POST['login_username'];
		}
		if (!isset($_POST['login_password'])) {
			die("FEHLER: Fehler beim übertragen der Daten!");
		}
		else {
			$login_data['password'] = $_POST['login_password'];
		}
		
		return $login_data;
	}
	
	
	function validate_login_data($login_data) { //check if login data are set and in the right size
		if (strlen($login_data['username']) > 50 || strlen($login_data['username']) == 0) {
			die("FEHLER: Fehlerhafte Daten!");
		}
		else if (strlen($login_data['password']) == 0) {
			die("FEHLER: Fehlerhafte Daten!");
		}
		else {
			return TRUE;
		} 
	}
	
	function validate_login($user_data, $type) { //check if login credentials are correct
		
		$user_data['password_hash']=hash('sha512', $user_data['password']); //create hash from password
		
		$config = read_config(); //read mysql config
		
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) { //handle connection error
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		// send the correct select query depending on the login type
		if ($type == 'user') {
			$db_data = mysqli_query($db_link, "SELECT user_name, pwhash FROM users WHERE user_name='".$user_data['username']."'");
		}
		else if ($type == 'admin') {
			$db_data = mysqli_query($db_link, "SELECT user_name, pwhash FROM admins WHERE user_name='".$user_data['username']."'");
		}
		else if ($type == 'provider') {
			$db_data = mysqli_query($db_link, "SELECT user_name, pwhash FROM providers WHERE user_name='".$user_data['username']."'");
		}
		else {
			die("FEHLER: Fehlerhafter Login Typ!");
		}
		
		mysqli_close($db_link);
		
		if ($db_data != FALSE) { //check if an error occured
			if (mysqli_num_rows($db_data) == 1) { //check if the account exits
				$data = parse_sql_data($db_data);
				
				if ($data[0]['user_name']==$user_data['username'] && $data[0]['pwhash']==$user_data['password_hash']) { //check if the login credentials are correct
					return TRUE;
				}
				else {
					return FALSE;
				}			
			}
			else {
				return FALSE;
			}
		}
		else {
			die("FEHLER: Fehler beim Abfragen der Login Daten");
		}
		
	}
?>