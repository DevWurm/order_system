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

// Validate password change
function validate_password_change() { //check if password is existing and repetition is equal; color input field red if an error occured
	var password = document.getElementsByName("change_password");
	var repeat_password = document.getElementsByName("change_repeat_password");
	
	if (password.value == repeat_password.value && password.value.length != 0) {
		return true;
	}
	else {
		password.setAttribute('class', 'red_placeholder');
		repeat_password.setAttribute('class', 'red_placeholder');
		password.value="";
		repeat_password.value="";
		repeat_password.placeholder = "Passwort wiederholen (muss übereinstimmen!)";
		return false;
	}
}


// Validate register
function validate_user_name() { //check if user name is existing and not too long; color input field red if an error occured
		var user_name = document.getElementsByName("user_name")[0];
		
		if (user_name.value.length > 50 ) {
			user_name.setAttribute('class', 'red_placeholder');
			user_name.placeholder = "Benutzername (maximal 50 Zeichen)";
			user_name.value='';
			return false;
		} 
		else if (user_name.value.length == 0) {
			user_name.setAttribute('class', 'red_placeholder');
			return false;
		}
		else {
			user_name.setAttribute('class', '');
			return true;
		}
	}
	
	
	function validate_password() { //check if password is existing and repetition is equal; color input field red if an error occured
		password = document.getElementsByName("password")[0];
		repeat_password = document.getElementsByName("repeat_password")[0];
		
		if (password.value != repeat_password.value) {
			password.setAttribute('class', 'red_placeholder');
			repeat_password.setAttribute('class', 'red_placeholder');
			repeat_password.placeholder = "Passwort wiederholen (muss übereinstimmen)";
			password.value='';
			repeat_password.value='';
			return false;
		}
		else if (password.value.length == 0) {
			password.setAttribute('class', 'red_placeholder');
			repeat_password.setAttribute('class', 'red_placeholder');
			return false;
		}
		else {
			password.setAttribute('class', '');
			repeat_password.setAttribute('class', '');
			return true;
		}
	}
	
	function validate_register() { //connect validation modules and confirm or abort submit
		if (validate_user_name() && validate_password()) {
			return true;
		}
		else { // repeat validation checks to ensure that the UI changes get executed (the functions in the if-statement only get interpreted until an 'false' occures)
			validate_user_name();
			validate_password();
			return false;
		}
	}