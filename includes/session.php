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
    function check_session($type) {

		//get config
		$config = read_config(); //read config data
		
		//get session data
		if(!isset($_SESSION['proof'])) {session_start(); $_SESSION['proof']=TRUE;} //start session if not started //start session
		if ($type=='user' && isset($_SESSION['user_username']) && isset($_SESSION['user_password'])) { //read user login data from session
    		$username=$_SESSION['user_username'];
			$password=$_SESSION['user_password'];
		}
		else if ($type=='admin' && isset($_SESSION['admin_username']) && isset($_SESSION['admin_password'])) { //or read admin login data from session
    		$username=$_SESSION['admin_username'];
			$password=$_SESSION['admin_password'];
		}
		else if ($type=='provider' && isset($_SESSION['provider_username']) && isset($_SESSION['provider_password'])) { //or read provider login data from session
    		$username=$_SESSION['provider_username'];
			$password=$_SESSION['provider_password'];
		}
		else { //if no login data are stored set login status to false
			return FALSE;
		}
		
		//get database data
		$db_link = mysqli_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password'], $config['mysql_database'], $config['mysql_port']); //connect to database
		if (!$db_link) {
			die("FEHLER: Verbindung zur Datenbank fehlgeschlagen!");
		}
		
		//send the correct select query depending on the login type
		if ($type == "user") {
			$db_result = mysqli_query($db_link, "SELECT user_name, pwhash FROM users WHERE user_name='".$username."'");
		}
		else if ($type == "admin") {
			$db_result = mysqli_query($db_link, "SELECT user_name, pwhash FROM admins WHERE user_name='".$username."'");
		}
		else if ($type == "provider") {
			$db_result = mysqli_query($db_link, "SELECT user_name, pwhash FROM providers WHERE user_name='".$username."'");
		}
		else {
			return FALSE;
		}
		
		mysqli_close($db_link); //close database connection
		
		if ($db_result != FALSE) {
			//parse data
			$result = parse_sql_data($db_result);
			
			//compare data
			if (count($result)<1) {
				return FALSE;
			}
			else {
				if ($result[0]["user_name"]==$username && $result[0]["pwhash"]==hash('sha512', $password)) {
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			
		}
		else {
			die("FEHLER: Fehler beim lesen der Datenbank");
		}
	}
?>