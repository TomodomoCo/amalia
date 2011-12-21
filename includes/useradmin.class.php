<?php
/**
 * Amalia2
 *
 * The way "real people" manage websites
 *
 * @package	Amalia2
 * @author		Amalia Dev Team
 * @copyright	Copyright (c) 2007-2011 Chris Van Patten, Nick Sampsell, Peter Upfold
 * @license		http://getamalia.com/license.html
 * @link		http://getamalia.com
 * @since		Version 2.0
 * @filesource
 */
 
/*
Amalia. A content management system "for the rest of us".

Copyright (C) 2007-2011 Chris Van Patten, Nick Sampsell and Peter Upfold. 

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies
or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the names of the authors or copyright holders
shall not be used in commercial advertising or to otherwise promote the sale,
commercial use or other commercial dealings regarding this Software without prior
written authorization from the the authors or copyright holders. Non-commercial use
of the authors and copyright holders' names is permitted, but it may be revoked on
a case-by-case basis if the authors wish to disconnect themselves from a particular use.
*/

// ------------------------------------------------------------------------

/**
 * Users Admin Backend Class *
 * @package	Amalia2
 * @category	Amalia Generation
 * @author		Amalia Dev Team
 */


if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}

class amalia_useradmin_FM extends amalia_file_manager
{
	// dummy class because the file manager is abstract
};


class amalia_useradmin
{

	private $usersFM = false; // reference to file manager class for users file
	private $permissionsFM = false; // ditto for permissions file


	public function __construct()
	{
		global $config;
	
		$this->usersFM = new amalia_useradmin_FM($config['users_path'].'/amalia-users.txt');
		$this->permissionsFM = new amalia_useradmin_FM($config['users_path'].'/amalia-users-permissions.txt');
	
	}


	public function list_users() 
	{
	
		/*
		
			raw data is returned as:
			
				0 => user id
				1 => user login name
				2 => hashed password
				3 => email address
				4 => friendly name
				
				
			this function will return an array of users,
			each of which is an assoc array with:
			
				['id'] => user id
				['username'] => username
				['hashed_password'] => hashed_password
				['email'] => email address
				['fname'] => friendly name/real name
		
		
		*/
	
		global $auth;
	
		
		$file = $this->usersFM->read();
		$usersArray = explode("\n",$file);
		$usersArray = array_filter($usersArray, 'filter_commented_line'); 
		
		$i = 0;
	
		if (is_array($usersArray) && count($usersArray) > 0)
		{
			foreach($usersArray as $user) 
			{
				
				$u[$i] = array();
				
				if (count(explode(';',$user)) > 0)
				{
					foreach(explode(';', $user) as $index => $field)
					{
						// put the data into an assoc array so we can work 
						// with it more easily
						
						switch ($index)
						{
							case 0:
								$u[$i]['id'] = $field;
							break;					
							case 1:
								$u[$i]['username'] = $field;
							break;
							case 2:
								$u[$i]['hashed_password'] = $field;
							break;
							case 3:
								$u[$i]['email'] = $field;
							break;
							case 4:
								$u[$i]['fname'] = $field;
							break;
							default:
							break;
						}
					
					}				
				}
				
				$i++;
			}
		}
		
		return $u;		
	}
	
	
	public function get_permissions_for_user($userID) {
	
		// get permissions set for a given user ID
	
		$file = $this->permissionsFM->read();
		$usersArray = explode("\n",$file);
		$usersArray = array_filter($usersArray, 'filter_commented_line'); 
		
		$i = 0;
		
		$permissionsOfUser = array(); // eventual store for parsed out permissions
			
		if (is_array($usersArray) && count($usersArray) > 0)
		{
			foreach($usersArray as $user) 
			{
			
				$record = explode(';', $user);
				
				// we are now looping through the permissions file
				// each record should have the 'userid;key:value;key:value' format
				
				if (is_array($record) && count($record) > 0 && $record[0] == $userID) 
				// is this the record for our user of interest?
				{
					// we now are in the right place, so from here, let's parse out
					// the permissions in $user and get them into a nice data structure
					
					foreach ($record as $loop => $permission)
					{
						if ($loop == 0)
						{
							continue; // ignore first row, it is just the user ID
						}
						
						if (strpos($permission, ':') !== false)
						{
						
							$permissionKeyVal = explode(':', $permission);
							// permissionKeyVal[0] will be permission 'key',
							// and [1] will be the value
						
							$permissionName = $permissionKeyVal[0]; //TODO: check for security issues
							$hasPermission = ($permissionKeyVal[1] == '1') ? true : false;
						
							$permissionsOfUser[$permissionName] = $hasPermission;
							
						
						}	
						
						
					}				
				
					break;
				
				} // done with the user we want
				
			} // done processing this $user
			
		}
		
		if (is_array($permissionsOfUser) && count($permissionsOfUser) > 0)
		{
			return $permissionsOfUser;
		}
		else {
			return false;
		}
			
	}
	
	
	public function validate_user_form($data)
	{
		// validate data from an edit or create user form
	
		global $config;
		
		$errors = array();
		
		$data['username'] = safe_usersline($data['username']);
		$data['fname'] = safe_usersline($data['fname']);
		$data['email'] = safe_usersline($data['email']);
		
		// check presence of all the fields we are expecting
		
		if (empty($data['username']))
		{
			$errors[count($errors)] = 'You must enter a username.';		
		}
		
		if (!preg_match('/^([a-z0-9_\-])+$/', strtolower($data['username'])))
		{
			$errors[count($errors)] = 'Usernames may only contain letters and numbers, underscores (_) and hyphens (-). All usernames are lowercase.';
		}
		
		if (strlen($data['username']) > 15)
		{
			$errors[count($errors)] = 'Usernames must be less than 16 characters long.';
		}
		
		if (empty($data['password']))
		{
			$errors[count($errors)] = 'You must enter a password. If you do not want to change an existing password, please just leave the field as it is.';
		}
		
		// dummy_amalia is used to tell the form that an existing password should be left alone
		// while also displaying some visible password data in the form		
		if (empty($data['password_retype']) && $data['password'] != 'dummy_amalia')
		{
			$errors[count($errors)] = 'You must retype the password. If you do not want to change an existing password, please just leave the field as it is.';		
		}

		if (empty($data['fname']))
		{
			$errors[count($errors)] = 'You must enter a real name.';		
		}
		
		if (strlen($data['fname']) > 63)
		{
			$errors[count($errors)] = 'The real name must be less than 64 characters long.';
		}
		
		if (empty($data['email']))
		{
			$errors[count($errors)] = 'You must enter an email address.';		
		}
		
		if (strlen($data['email']) > 63)
		{
			$errors[count($errors)] = 'The email address must be less than 64 characters long.';
		}
		
		if (!preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $data['email']))
  		{
		    $errors[count($errors)] = 'You must enter a valid email address.';
		}  
  
		
		if ($data['password'] != 'dummy_amalia' && $data['password'] != $data['password_retype'])
		{
			$errors[count($errors)] = 'The two passwords did not match.';
		}
		
		// permissions will be assumed to be off if not present, so we leave them
		
		if (count($errors) > 0)
		{
		
			// validation did not succeed
			
			return $errors;
		
		
		}
		else {
			return true; // all good
		}
	
	}
	
	public function update_user_record($userID, $data)
	{
	
		global $config;
	
		// pass pre-validated POSTDATA to $data
		// and the user ID to update to $userID
		
		
		// some quick sanitisation
		$userID = preg_replace('/[^0-9]/', '', $userID);
		$username = safe_usersline(strtolower($data['username']));
		$newPassword = hash(HASH_ALGO, $config['salt'].$data['password']);
		$email = safe_usersline($data['email']);
		$fname = safe_usersline($data['fname']);
		
	
		$file = $this->usersFM->read();
		$usersArray = explode("\n",$file);
		//$usersArray = array_filter($usersArray, 'filter_commented_line'); 
		
		if (is_array($usersArray) && count($usersArray) > 0)
		{
		
			foreach($usersArray as $lineNo => $user)
			{
				if (preg_match("/^\#/", $user))
				{
					// ignore commented lines (but we still do want them present in the array)
					continue;
				}			
				
				// find this user record
				$record = explode(';', $user);
				
				if (is_array($record) && count($record) > 0)
				{			
						
					if ($record[0] == $userID)
					{
						
						$oldPassword = $record[2];		
						$replaceLine = $lineNo;
	
						break;			
					}
				}	
			}
		}
		
		if ($replaceLine === false)
		{
			return false;
		}

		// create the new user line which we will write to the file shortly
		
		if ($data['password'] != 'dummy_amalia')
		{
			$password = $newPassword;
		}
		else
		{
			$password = $oldPassword;
		}
				
		$newUserLine = $userID.';'.$username.';'.$password.';'.$email.';'.$fname;
		
		
		// replace the old user line with the new line in our data structure
		$usersArray[$replaceLine] = $newUserLine;
		
		// recombine into a load of text
		$newUsersFile = implode("\n", $usersArray);

		// write our new users data back to the users file
		$this->usersFM->write($newUsersFile);
		
		// HANDLE PERMISSIONS FOR THIS USER
		
		// only edit permissions if not editing same user as is logged in
		if ($userID != $_SESSION['amalia_auth']['id'] && !isset($data['should_not_edit_permissions']))
		{
					
			/* first, we assume that all the permissions are switched off,
			   then any checkboxes we do get we will switch on
			*/
			
			$permission['create-pages'] = ($data['permission_create-pages'] == 'permission_create-pages') ? '1' : '0';
			$permission['edit-pages'] = ($data['permission_edit-pages'] == 'permission_edit-pages') ? '1' : '0';
			$permission['delete-files'] = ($data['permission_delete-files'] == 'permission_delete-files') ? '1' : '0';
			$permission['upload-files'] = ($data['permission_upload-files'] == 'permission_upload-files') ? '1' : '0';
			$permission['rename-files'] = ($data['permission_rename-files'] == 'permission_rename-files') ? '1' : '0';
			$permission['manage-plugins'] = ($data['permission_manage-plugins'] == 'permission_manage-plugins') ? '1' : '0';
			$permission['manage-users'] = ($data['permission_manage-users'] == 'permission_manage-users') ? '1' : '0';
			$permission['configure-amalia'] = ($data['permission_configure-amalia'] == 'permission_configure-amalia') ? '1' : '0';
			
			// craft our new permissions line
			$newPermissionsLine = $userID.';';
			
			foreach($permission as $key => $per)
			{
				$newPermissionsLine .= $key.':'.$per.';';
			}
			
			rtrim($newPermissionsLine, ';');
			
			// read in the permissions file, and work out which line to replace
			
			$permFile = $this->permissionsFM->read();
			$permissionsArray = explode("\n", $permFile);
			
			
			if (is_array($permissionsArray) && count($permissionsArray) > 0)
			{
			
				foreach($permissionsArray as $lineNo => $user)
				{
					if (preg_match("/^\#/", $user))
					{
						// ignore commented lines (but we still do want them present in the array)
						continue;
					}			
					
					// find this user record
					$record = explode(';', $user);
					
					if (is_array($record) && count($record) > 0)
					{			
							
						if ($record[0] == $userID)
						{	
							$replaceLine = $lineNo;
							break;			
						}
					}	
				}
			}
			
			if ($replaceLine === false)
			{
				return false;
			}
			
			// splice in our new permissions line
			$permissionsArray[$replaceLine] = $newPermissionsLine;
			
			// recombine the array into our permissions file again, ready to write
			$newPermissionsFile = implode("\n", $permissionsArray);
			
			// write the new file
			$this->permissionsFM->write($newPermissionsFile);	

		} // end if not same user
		
		//a semi-hacky way to prevent the user from being logged out when changing their information.
		$_SESSION['amalia_admin'] = hash(HASH_ALGO, $config['salt'].date('dmY'));
										
		return true; // all done!
	
	}
	
	
	public function new_user_record($data)
	{
		
		global $config;
	
		// pass pre-validated POSTDATA to $data
		
		// some quick sanitisation
		$username = safe_usersline(strtolower($data['username']));
		$newPassword = hash(HASH_ALGO, $config['salt'].$data['password']);
		$email = safe_usersline($data['email']);
		$fname = safe_usersline($data['fname']);
		
		// work out what the next user ID should be
		$userids = array();
		
		$file = $this->usersFM->read();
		$usersArray = explode("\n",$file);		
		if (is_array($usersArray) && count($usersArray) > 0)
		{
		
			foreach($usersArray as $lineNo => $user)
			{
				if (preg_match("/^\#/", $user))
				{
					// ignore commented lines (but we still do want them present in the array)
					continue;
				}
				
				// find this user record
				$record = explode(';', $user);
				
				if (is_array($record) && count($record) > 0)
				{			
					$userids[count($userids)] = $record[0];
				}							
				
			}
		}
		
		
		$newUserID = max($userids) + 1; // new user ID is highest user ID plus one
		$newUserLine = $newUserID.';'.$username.';'.$newPassword.';'.$email.';'.$fname; // construct new line
		
		$usersArray[count($usersArray)] = $newUserLine; // append to file
		
		// recombine the file
		$newUsersFile = implode("\n", $usersArray);
		
		// write the file to disk
		$this->usersFM->write($newUsersFile);
		
		// HANDLE PERMISSIONS FOR THIS USER
				
		/* first, we assume that all the permissions are switched off,
		   then any checkboxes we do get we will switch on
		*/
		
		$permission['create-pages'] = ($data['permission_create-pages'] == 'permission_create-pages') ? '1' : '0';
		$permission['edit-pages'] = ($data['permission_edit-pages'] == 'permission_edit-pages') ? '1' : '0';
		$permission['delete-files'] = ($data['permission_delete-files'] == 'permission_delete-files') ? '1' : '0';
		$permission['upload-files'] = ($data['permission_upload-files'] == 'permission_upload-files') ? '1' : '0';
		$permission['rename-files'] = ($data['permission_rename-files'] == 'permission_rename-files') ? '1' : '0';
		$permission['manage-plugins'] = ($data['permission_manage-plugins'] == 'permission_manage-plugins') ? '1' : '0';
		$permission['manage-users'] = ($data['permission_manage-users'] == 'permission_manage-users') ? '1' : '0';
		$permission['configure-amalia'] = ($data['permission_configure-amalia'] == 'permission_configure-amalia') ? '1' : '0';
		
		// craft our new permissions line
		$newPermissionsLine = $newUserID.';';
		
		foreach($permission as $key => $per)
		{
			$newPermissionsLine .= $key.':'.$per.';';
		}
		
		rtrim($newPermissionsLine, ';');
		
		// add new line to permissions file
		$permFile = $this->permissionsFM->read();
		$permissionsArray = explode("\n", $permFile);
		
		$permissionsArray[count($permissionsArray)] = $newPermissionsLine;
		
		// recombine
		$newPermissionsFile = implode("\n", $permissionsArray);
		
		// write updated file
		$this->permissionsFM->write($newPermissionsFile);
		
		
		return true;
		
			
	}
	
	public function delete_user_record($userID)
	
	{
		
		/* Deletes the specified user (by user ID). This function
		does not ask, so auth checks and checks at the genuine desire
		to delete a user should be done first! */
	
		global $config;
				
		$userID = preg_replace('/[^0-9]/', '', $userID);
		
		if (empty($userID) && $userID !== 0)
		{
			friendly_fatal('No user ID specified for the delete.');
			die();
		}
		
		if ($userID == $_SESSION['amalia_auth']['id'])
		{
			friendly_fatal('You cannot delete the user who you are currently logged in as.');
			die();
		}
		
		// look up to make sure the user ID exists and to find its row number
		
		$file = $this->usersFM->read();
		$usersArray = explode("\n",$file);
		//$usersArray = array_filter($usersArray, 'filter_commented_line'); 
		
		$deleteLine = false;
		
		if (is_array($usersArray) && count($usersArray) > 0)
		{
		
			foreach($usersArray as $lineNo => $user)
			{
				if (preg_match("/^\#/", $user))
				{
					// ignore commented lines (but we still do want them present in the array)
					continue;
				}			
				
				// find this user record
				$record = explode(';', $user);
				
				if (is_array($record) && count($record) > 0)
				{			
						
					if ($record[0] == $userID)
					{
								
						$deleteLine = $lineNo;
	
						break;			
					}
				}	
			}
		}
		
		if ($deleteLine === false) // no record of user
		{
			friendly_fatal('A user with that user ID could not be found to delete.');
			die();
		}
		
		// ok, so we now have the row to delete in the users file, let's delete that row
		// and rewrite the file
		
		unset($usersArray[$deleteLine]);
		
		// recombine the file
		$newUsersFile = implode("\n", $usersArray);
		
		// write the file to disk
		$this->usersFM->write($newUsersFile);
		
		
		// go and find the permissions line for this user and delete that too
		
		$permFile = $this->permissionsFM->read();
		$permissionsArray = explode("\n", $permFile);
		
		$deleteLine = false;
		
		if (is_array($permissionsArray) && count($permissionsArray) > 0)
		{
		
			foreach($permissionsArray as $lineNo => $user)
			{
				if (preg_match("/^\#/", $user))
				{
					// ignore commented lines (but we still do want them present in the array)
					continue;
				}			
				
				// find this user record
				$record = explode(';', $user);
				
				if (is_array($record) && count($record) > 0)
				{			
						
					if ($record[0] == $userID)
					{	
						$deleteLine = $lineNo;
						break;			
					}
				}	
			}
		}
		
		if ($deleteLine === false)
		{
			return false;
		}
		
		// remove this line
		unset($permissionsArray[$deleteLine]);
		
		// recombine the array into our permissions file again, ready to write
		$newPermissionsFile = implode("\n", $permissionsArray);
		
		// write the new file
		$this->permissionsFM->write($newPermissionsFile);
		
		
		return true;
	
	}
	
	public function set_user_fname($userID, $newFname)
	{
	
		global $config;
	
		// pass pre-validated newFname to $newFname
		// and the user ID to update to $userID
		
		$userID = preg_replace('/[^0-9]/', '', $userID);
		$newFname = safe_usersline($newFname);
		
		$file = $this->usersFM->read();
		$usersArray = explode("\n",$file);
		//$usersArray = array_filter($usersArray, 'filter_commented_line'); 
		
		if (is_array($usersArray) && count($usersArray) > 0)
		{
		
			foreach($usersArray as $lineNo => $user)
			{
				if (preg_match("/^\#/", $user))
				{
					// ignore commented lines (but we still do want them present in the array)
					continue;
				}			
				
				// find this user record
				$record = explode(';', $user);
				
				if (is_array($record) && count($record) > 0)
				{			
						
					if ($record[0] == $userID)
					{
						$existingUsername = $record[1];
						$existingPassword = $record[2];		
						$existingEmail = $record[3];
						$existingFname = $record[4];
						$replaceLine = $lineNo;
	
						break;			
					}
				}	
			}
		}
		
		if ($replaceLine === false)
		{
			return false;
		}
		
		// only allow the change if the user's fname is blank or default
		if ($existingFname !== '_AMALIA_DEFAULT_FNAME' && !empty($existingFname))
		{
			return false;
		}
	
		// create the updated user line which we will write to the file shortly
		$newUserLine = $userID.';'.$existingUsername.';'.$existingPassword.';'.$existingEmail.';'.$newFname;
		
		// replace the old user line with the new line in our data structure
		$usersArray[$replaceLine] = $newUserLine;
		
		// recombine into a load of text
		$newUsersFile = implode("\n", $usersArray);

		// write our new users data back to the users file
		$this->usersFM->write($newUsersFile);

		//a semi-hacky way to prevent the user from being logged out when changing their information.
		$_SESSION['amalia_admin'] = hash(HASH_ALGO, $config['salt'].date('dmY'));
		
		return true;		
	}


};


?>