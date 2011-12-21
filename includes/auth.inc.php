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
 * Auth Class
 *
 * @package	Amalia2
  * @category	Authentication
 * @author		Amalia Dev Team
 */
 
if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}
 
class amalia_auth 
{


	public $users = array(); // will contain all user info parsed from file
	public $current_user = false; // $users array key of currently logged in user
	
	/**
	 * Constructor
	 *
	 * Sets up the new auth object, parsing list of users and dumping to this->users
	 *
	 * @return <object>
	 **/
	public function __construct()
	{
		if (!$this->parse_users_file())
		{
			friendly_fatal('No users defined in the users file. Please re-run the installer to create a user.');			
			die();
		}
		
		if (count($this->users) < 1)
		{
			friendly_fatal('No users defined in the users file. Please re-run the installer to create a user.');	
			die();
		}
		
	}

	/**
	 * Login Function
	 *
	 * Checks the username and password compared to the users.db.php file
	 * If combo exists, create amalia_auth session
	 *
	 * @param	string (username)
	  * @param	string (password)
	 * @return	boolean
	 */
	public function login($username, $password) 
	{
		global $config;
		
		$userExists = false;
		
		$username = strtolower(safe_usersline($username));
		
		// does the username given exist?
		foreach($this->users as $key => $user)
		{
			
			if ($user['username'] == $username)
			{
				$userExists = true;
				$userRecord = $key;
				break;				
			}
		}
		
		if (!$userExists)
		{
			return false;
		}
			
		// is the password correct?
		if (hash(HASH_ALGO, $config['salt'].$password) == $this->users[$userRecord]['hashed_password'])
		{
			$_SESSION['amalia_auth'] = $this->users[$userRecord];
			return true;
		}
		else {
			return false;	
		}
		
	}
	
	
	/**
	 * Check Authentication Session
	 *
	 * Checks the session compared to the users.db.php file
	 * If key matches, do nothing, otherwise unset session and redirect to login
	 *
	  * @return	boolean
	 */
	public function check_auth() 
	{
		global $config;
		
				
		if(isset($_SESSION['amalia_admin']) && !empty($_SESSION['amalia_admin']) && ($_SESSION['amalia_admin'] == hash(HASH_ALGO, $config['salt'].date('dmY')))) 
		{
		
			foreach($this->users as $key => $user) 
			{
			
				if($_SESSION['amalia_auth']['id'] == $user['id']) 
				{
					$_SESSION['amalia_auth'] = $user;
					return true;
				} 
				else 
				{
					return false;
				}
			
			}
		
		} 
		
		// hack override for the Flash uploader, which can't send the session cookie
		// allow the session if $_POST['amalia_session_override'] contains a valid session
		else if ($_GET['action'] == 'upload' && $_SERVER['REQUEST_METHOD'] == 'POST'
		&& isset($_POST['amalia_session_override']) && !empty($_POST['amalia_session_override']))

		// only allow for upload page

		{
		
			// attempt to pull up the old session (session_start() has not yet been called
			// as index.php also checks for this)
			
			// session_name sets a cookie name other than 'PHPSESSID', linked to the current server.
			// just to avoid using defaults
			ini_set("session.cookie_httponly", 1);
			session_name('Amalia-'.md5($_SERVER['SERVER_NAME']));
			session_id(safe_plain($_POST['amalia_session_override']));
			session_start();
			
			// OK, session is loaded as normal now, continue to do the auth check
			
			foreach($this->users as $key => $user) 
			{
			
				if($_SESSION['amalia_auth']['id'] == $user['id']) 
				{
					$_SESSION['amalia_auth'] = $user;
					return true;
				} 
				else 
				{
					return false;
				}
			
			}
						
		
		}
		
		
		else 
		{
	
			if (!isset($_SESSION['amalia_auth']) || empty($_SESSION['amalia_auth']))
			{
				$this->unset_session();
				return false; // invalid session
			}
			
			// compare current auth session to what we're expecting from the users file
			foreach($this->users as $key => $user)
			{
				if ($_SESSION['amalia_auth'] == $user)
				{
					$this->current_user = $key;
					return true;	
				}			
			}
			
			return false;
		}
	}
	
		
	/**
	 *Unset Session Function
	 *
	 * Deletes the amalia_auth session
	 *
	 * @return	boolean
	 */
	public function unset_session() 
	{

		unset($_SESSION['amalia_auth']);
		return true;

	}
	
	public function logout()
	{
		session_name('Amalia-'.md5($_SERVER['SERVER_NAME']));
		session_start();
	
		// delete session cookie at user's end
		
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 420000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
		
		session_destroy();

		return true;
	
	}
	
	private function parse_users_file()
	{
		
		global $config;
		
		/**
		 *Parse users file
		 *
		 * Read and parse amalia-users.txt and load the users in that file
		 * into this auth object for later reference. Should be called
		 * automatically by the constructor
		 *
		 * @return	boolean
		 */
		 
		 // we do a lot of try/catching so that we get errors if anything goes wrong
		 // rather than silence.
		 
		// open the file for reading
		try
		{
			$fh = fopen($config['users_path'].'/amalia-users.txt', 'r');
			$fh2 = fopen($config['users_path'].'/amalia-users-permissions.txt','r');
		}
		catch (Exception $e)
		{
			friendly_fatal("Unable to open the users file for reading. Please check the permissions on <em>".$config['users_path'].'/amalia-users.txt</em>. The web server user ('.safe_plain(exec('whoami')).') should have read/write access. '.$e->getMessage());
			die();
		}
		
		// in case there is a warning, but not a fatal
		if (!$fh || !$fh2) // check both!
		{
			friendly_fatal("Unable to open the users file for reading. Please check the permissions on <em>".$config['users_path'].'/amalia-users.txt</em>. The web server user ('.safe_plain(exec('whoami')).') should have read/write access.');
			die();
		}

		// now read the users file into $usersRaw
		try 
		{
			$usersRaw = fread($fh, filesize($config['users_path'].'/amalia-users.txt'));
			$permissionsRaw = fread($fh2, filesize($config['users_path'].'/amalia-users-permissions.txt'));
		}
		catch (Exception $e)
		{
			friendly_fatal("Unable to read the users file that has been opened. ".$e->getMessage());
			die();			
		}
		
		fclose($fh);
		fclose($fh2);
		
		// split out into lines		
		$usersArray = explode("\n", $usersRaw);
		
		$usersArray = array_filter($usersArray, 'filter_commented_line'); // filter comment lines
	
		if (count($usersArray) < 1)
		{
			return false;
		}
		
		$permissionsArray = explode("\n",$permissionsRaw);
		$permissionsArray = array_filter($permissionsArray, 'filter_commented_line');
		
	
		if(count($permissionsArray) < 1)
		{
			return false;
		}
			
		//ok, now lets explode the permissions
		
		$d = 0;
		
		foreach($permissionsArray as $par) 
		{
		
			$par2[$d] = explode(';',$par);
			
		
		
		$e = 0;
		
		foreach($par2 as $key2 => $val2) 
		{
				
			foreach($val2 as $key => $val) 
				{
				
					if(strpos($val,':') !== False) 
					{
					
						$temp = explode(':',$val);
						$par4[$d][$temp[0]] = $temp[1];
					
					} 
					else 
					{
						$par4[$d]['id'] = $val2[0];
					}
				
				}
			
				$e++;
			}
			$d++;
		}

		$c = 0; // to count each user into $this->users
		
		
		foreach($usersArray as $key => $user)
		{
			
			$userProperties = explode(';', $user);
						
			
			$userToAdd = array();
			$userToAdd['id'] = $userProperties[0];
			$userToAdd['username'] = $userProperties[1];
			$userToAdd['hashed_password'] = $userProperties[2];
			$userToAdd['email'] = $userProperties[3];
			$userToAdd['f_name'] = $userProperties[4];
			
			foreach($par4 as $p) {
			
				if($p['id'] == $userProperties[0]) {
					unset($p['username']);
					$userToAdd['permissions'] = $p;
				}
			
			}
			
			
			
		
			
			// add to internal representation of users
			$this->users[$c] = $userToAdd;
			
			$c++;
						
		}
		
		// now $this->users is an array containing a bunch of these
		// friendlier user objects which we can look up at any time
		// in Amalia Core code. Win!
		
		return true;
		
	}
	
	/**
	* Check Permissions
	*
	* Checks to see if the user can access a function based on permissions
	*
	* @return	boolean
	*/
	public function has_perm($permission) 
	{
	
		if($_SESSION['amalia_auth']['permissions'][safe_plain($permission)] === '1') 
		{
			return true;
		} 
		else 
		{
			return false;
		}
	
	}
	


}