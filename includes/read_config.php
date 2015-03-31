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

	$__READ_CONFIG__=TRUE;
	

    function read_config() { //read config file and return parsed data

		if (file_exists('./config/order_system.conf')) { //search config folder in current directory
			$file = fopen("./config/order_system.conf", "r"); //open file
    		$config = parse_config($file, filesize('./config/order_system.conf')); //parse config data
		}
		else if (file_exists('../config/order_system.conf')) {//search config folder in top directory
			$file = fopen("../config/order_system.conf", "r"); //open file
    		$config = parse_config($file, filesize('../config/order_system.conf')); //parse config data
		}
		else {
			die("ERROR 002: Fehler beim öffnen der Konfigurationsdatei");
		}
		
		return $config; //return config data
		
	}
	
	
	
	function parse_config($file, $file_size) { //parse config file
		$config = array();
		
		if ($file != FALSE) {				
				$buffer = fread($file, $file_size); //read file
				
				if (strpos($buffer, "MYSQL_USERNAME")) { //search for config keyword
					$start = strpos($buffer, 'MYSQL_USERNAME=') + strlen('MYSQL_USERNAME=');
					$length = strpos($buffer, ';', $start) - $start;
					$config['mysql_user']=substr($buffer, $start, $length); //read config attributes
				}
				else {
					$config['mysql_user']="root"; //set default value
				}
				if (strpos($buffer, "MYSQL_PASSWORD")) { //search for config keyword
					$start = strpos($buffer, 'MYSQL_PASSWORD=') + strlen('MYSQL_PASSWORD=');
					$length = strpos($buffer, ';', $start) - $start;
					$config['mysql_password']=substr($buffer, $start, $length); //read config attributes
				}
				else {
					$config['mysql_password']=""; //set default value
				}
				if (strpos($buffer, "MYSQL_HOST")) { //search for config keyword
					$start = strpos($buffer, 'MYSQL_HOST=') + strlen('MYSQL_HOST=');
					$length = strpos($buffer, ';', $start) - $start;
					$config['mysql_host']=substr($buffer, $start, $length); //read config attributes
				}
				else {
					$config['mysql_host']="localhost"; //set default value
				}
				if (strpos($buffer, "MYSQL_DATABASE")) { //search for config keyword
					$start = strpos($buffer, 'MYSQL_DATABASE=') + strlen('MYSQL_DATABASE=');
					$length = strpos($buffer, ';', $start) - $start;
					$config['mysql_database']=substr($buffer, $start, $length); //read config attributes
				}
				else {
					$config['mysql_database']="order_system"; //set default value
				}
				if (strpos($buffer, "MYSQL_PORT")) { //search for config keyword
					$start = strpos($buffer, 'MYSQL_PORT=') + strlen('MYSQL_PORT=');
					$length = strpos($buffer, ';', $start) - $start;
					$config['mysql_port']=substr($buffer, $start, $length); //read config attributes
				}
				else {
					$config['mysql_port']="3306"; //set default value
				}
		}
		else {
			die("FEHLER: Fehler beim lesen der Konfigurationsdatei");
		}
		return $config;
	}
?>