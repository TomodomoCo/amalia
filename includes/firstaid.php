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
 * First Aid Backend *
 * @package	Amalia2
 * @category	Amalia Generation
 * @author		Amalia Dev Team
 */

//TODO: plugin directory permissions fixing

if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}

function operatingEnvironmentChecks()
{

	$checks['os'] = PHP_OS;
	
	if (strpos('windows', strtolower(PHP_OS)) !== 0 && strpos('winnt', strtolower(PHP_OS)) !== 0)
	{
		$checks['os_moreinfo'] .= exec('cat /etc/*-release');
		$checks['os_moreinfo'] .= '; '.exec('uname -a');
	}


	$checks['php'] = (version_compare(PHP_VERSION, '5.2.3', '>='));
	$checks['curl'] = (function_exists('curl_init') && curl_init());
	
	$checks['gd'] = (
					function_exists('imagecreatetruecolor') &&
					function_exists('imagecreatefromjpeg') &&
					function_exists('imagecreatefrompng') &&			
					function_exists('imagecreatefromgif') &&
					function_exists('imagejpeg') &&
					function_exists('imagepng')
				);
				
	return $checks;

}


function checkPermissions()
{
	global $config;

	// checks permissions on all items where Amalia should have write access
	
	// returns true if everything is in order, returns an array of strings that
	// are the errors if there were problems

	$errors = array();
	
	
	// check permissions on root level of Config Path
	if (!is_writable($config['config_path'])) {
	
		$errors[] = 'The Config Path "'.safe_filename($config['config_path']).'" is not writable.';
		
	}
	else {
	
		// create a test file to check inside the folder
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			$errors[] = 'The Config Path "'.safe_filename($config['config_path']).'" is not writable.';
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			$errors[] = 'The Config Path "'.safe_filename($config['config_path']).'" is not writable.';
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}
	}
	
	clearstatcache();
	
	// check permissions on Config File amalia-config.php
	if (!is_writable($config['config_path'].'/amalia-config.php'))
	{
		$errors[] = 'The Amalia configuration file "'.safe_filename($config['config_path'].'/amalia-config.php').'" is not writable.';
	}
	
	clearstatcache();
	
	// check permissions on the Scheduler file
	if (!is_writable($config['config_path'].'/amalia-scheduler.txt'))
	{
		$errors[] = 'The scheduler file "'.safe_filename($config['config_path'].'/amalia-scheduler.txt').'" is not writable.';
	}
	
	// check directory permissions on userfiles topdir folder
	if (!is_writable($config['config_path'].'/userfiles'))
	{
		$errors[] = 'The userfiles folder "'.safe_filename($config['config_path'].'/userfiles').'" is not writable.';
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/userfiles'.'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			$errors[] = 'The userfiles folder "'.safe_filename($config['config_path'].'/userfiles').'" is not writable.';
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			$errors[] = 'The userfiles folder "'.safe_filename($config['config_path'].'/userfiles').'" is not writable.';
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	
	clearstatcache();	

	
	// check directory permissions on thumbcache
	if (!is_writable($config['config_path'].'/userfiles/thumbcache'))
	{
		$errors[] = 'The thumbnail cache folder "'.safe_filename($config['config_path'].'/userfiles/thumbcache').'" is not writable.';
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/userfiles/thumbcache'.'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			$errors[] = 'The thumbnail cache folder "'.safe_filename($config['config_path'].'/userfiles/thumbcache').'" is not writable.';
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			$errors[] = 'The thumbnail cache folder "'.safe_filename($config['config_path'].'/userfiles/thumbcache').'" is not writable.';
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	
	clearstatcache();
	
	// check directory permissions on filedata
	if (!is_writable($config['config_path'].'/userfiles/filedata'))
	{
		$errors[] = 'The file data folder "'.safe_filename($config['config_path'].'/userfiles/filedata').'" is not writable.';
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/userfiles/filedata'.'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			$errors[] = 'The file data folder "'.safe_filename($config['config_path'].'/userfiles/filedata').'" is not writable.';
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			$errors[] = 'The file data folder "'.safe_filename($config['config_path'].'/userfiles/filedata').'" is not writable.';
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	
	clearstatcache();	
	
	// check directory permissions on recycle folder
	if (!is_writable($config['config_path'].'/userfiles/recycle'))
	{
		$errors[] = 'The Recycle Bin folder "'.safe_filename($config['config_path'].'/userfiles/recycle').'" is not writable.';
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/userfiles/recycle'.'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			$errors[] = 'The Recycle Bin folder "'.safe_filename($config['config_path'].'/userfiles/recycle').'" is not writable.';
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			$errors[] = 'The Recycle Bin folder "'.safe_filename($config['config_path'].'/userfiles/recycle').'" is not writable.';
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	
	clearstatcache();	
	
	// check Recycle Bin catalogue file
	if (!is_writable($config['config_path'].'/userfiles/recycle/amalia-recycle-catalogue.txt'))
	{
		$errors[] = 'The Recycle Bin Catalogue file "'.safe_filename($config['config_path'].'/userfiles/recycle/amalia-recycle-catalogue.txt').'" is not writable.';
	}

	clearstatcache();	
	
	// check permissions on root of Users Path
	if (!is_writable($config['users_path']))
	{
		$errors[] = 'The Users Path "'.safe_filename($config['users_path']).'" is not writable.';
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['users_path'].'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			$errors[] = 'The Users Path "'.safe_filename($config['users_path']).'" is not writable.';
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			$errors[] = 'The Users Path "'.safe_filename($config['users_path']).'" is not writable.';
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	clearstatcache();
	
	
	// check individual permissions on users file and permissions file
	if (!is_writable($config['users_path'].'/amalia-users.txt'))
	{
		$errors[] = 'The Users file "'.safe_filename($config['users_path'].'/amalia-users.txt').'" is not writable.';
	}
	clearstatcache();	
	
	if (!is_writable($config['users_path'].'/amalia-users-permissions.txt'))
	{
		$errors[] = 'The User Permissions file "'.safe_filename($config['users_path'].'/amalia-users-permissions.txt').'" is not writable.';
	}
	clearstatcache();		
	
	// now check permissions on root of Site Path
	if (!is_writable($config['site_path']))
	{
		$errors[] = 'The Site Path "'.safe_filename($config['site_path']).'" is not writable.';
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['site_path'].'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			$errors[] = 'The Site Path "'.safe_filename($config['site_path']).'" is not writable.';
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			$errors[] = 'The Site Path "'.safe_filename($config['site_path']).'" is not writable.';
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	clearstatcache();
	
	// loop through Site Path to check every single file and folder inside it for writability
	$dh = opendir($config['site_path']);
	
	while (($file = readdir($dh)) !== false)	
	{
		recursivePermissionsCheck($config['site_path'], $file, $errors);			
	}
	
	closedir($dh);
	
	// loop through Recycled Files to check all its files
	$dh = opendir($config['config_path'].'/userfiles/recycle');
	
	while (($file = readdir($dh)) !== false)
	{
		recursivePermissionsCheck($config['config_path'].'/userfiles/recycle', $file, $errors, false);
	}
	
	closedir($dh);
	
	// loop through thumbcache to check it
	$dh = opendir($config['config_path'].'/userfiles/thumbcache');
	
	while (($file = readdir($dh)) !== false)
	{
		recursivePermissionsCheck($config['config_path'].'/userfiles/thumbcache', $file, $errors, false);
	}
	
	closedir($dh);
	
	// now, we are done
	
	// if errors count is >0, there were errors -- return them to the controller
	// for display in the UI
	if (is_array($errors) && count($errors) > 0)
	{
		return $errors;
	}
	else {
		// return BOOL 	true indicates no errors
		return true;
	}

}

function recursivePermissionsCheck($dir, $file, &$errors, $configPathCheck = true)
{
	global $config;
	
	// called by checkPermissions() on the Site Path.
	
	// does a permissions check. If $file is a dir, recursively checks $file for the file permissions
	// of the folder and its subfiles and subfolders.
	
	// exclusions
	// do not check ., .., or a config path underneath the site path
	if (strpos($file, '.') === 0)
	{
		return true;
	}
	if ($configPathCheck)
	{
		if (strpos($dir.'/', $config['config_path'].'/') === 0)
		{
			return true;
		}
	}	
	
	if (is_dir($dir.'/'.$file))
	{
		// directory. check dir writability, then loop through subfiles to check theirs
	
		if (!is_writable($dir.'/'.$file))
		{
			$errors[] = 'The folder "'.safe_filename($dir.'/'.$file).'" is not writable.';
		}
		clearstatcache();
		
		$dh = opendir($dir.'/'.$file);
		
		while (($subfile = readdir($dh)) !== false)
		{
			recursivePermissionsCheck($dir.'/'.$file, $subfile, $errors);
			clearstatcache();
		}
		
		return is_writable($dir.'/'.$file);
		clearstatcache();
	
	}
	else {
		 // normal file. check writability
		 if (is_writable($dir.'/'.$file))
		 {
	 		clearstatcache();
		 	return true;
		 }
		 else {
		 	clearstatcache();
		 	$errors[] = 'The file "'.safe_filename($dir.'/'.$file).'" is not writable.';
		 	return false;
		 }
	}

}


function repairPermissions()
{
	global $config;
	
	// attempts to chown and/or chmod any files that are out of whack, erm, into whack
	
	// return true if the operation was a complete, perfect success
	// or an array of errors explaining why chmod failed on each file if not

	$errors = array();
	
	// check permissions on root level of Config Path
	if (!is_writable($config['config_path'])) {
	
		// attempt to fix 
		repairFilePermission($config['config_path'], $errors);		
	}
	
	clearstatcache();
	
	// check permissions on amalia-config.php
	if (!is_writable($config['config_path'].'/amalia-config.php'))
	{
		// attempt fix
		repairFilePermission($config['config_path'].'/amalia-config.php', $errors);
	}
	
	clearstatcache();
	
	// scheduler file
	if (!is_writable($config['config_path'].'/amalia-scheduler.txt'))
	{
		repairFilePermission($config['config_path'].'/amalia-scheduler.txt', $errors);
	}
	
	// userfiles topdir folder
	if (!is_writable($config['config_path'].'/userfiles'))
	{
		repairFilePermission($config['config_path'].'/userfiles', $errors);
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/userfiles'.'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			repairFilePermission($config['config_path'].'/userfiles', $errors);
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			repairFilePermission($config['config_path'].'/userfiles', $errors);
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	
	clearstatcache();	
	
	clearstatcache();
	
	// thumbcache
	if (!is_writable($config['config_path'].'/userfiles/thumbcache'))
	{
		repairFilePermission($config['config_path'].'/userfiles/thumbcache', $errors);
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/userfiles/thumbcache'.'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			repairFilePermission($config['config_path'].'/userfiles/thumbcache', $errors);
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			repairFilePermission($config['config_path'].'/userfiles/thumbcache', $errors);
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	
	clearstatcache();
	
	// filedata
	if (!is_writable($config['config_path'].'/userfiles/filedata'))
	{
		repairFilePermission($config['config_path'].'/userfiles/filedata', $errors);
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/userfiles/filedata'.'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			repairFilePermission($config['config_path'].'/userfiles/filedata', $errors);
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			repairFilePermission($config['config_path'].'/userfiles/filedata', $errors);
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	
	clearstatcache();
	
	// recycle folder
	if (!is_writable($config['config_path'].'/userfiles/recycle'))
	{
		repairFilePermission($config['config_path'].'/userfiles/recycle', $errors);
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['config_path'].'/userfiles/recycle'.'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			repairFilePermission($config['config_path'].'/userfiles/recycle', $errors);
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			repairFilePermission($config['config_path'].'/userfiles/recycle', $errors);
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	
	clearstatcache();
	
	// recycle bin catalogue
	if (!is_writable($config['config_path'].'/userfiles/recycle/amalia-recycle-catalogue.txt'))
	{
		repairFilePermission($config['config_path'].'/userfiles/recycle/amalia-recycle-catalogue.txt', $errors);
	}
	
	clearstatcache();
	
	
	// root of Users Path
	if (!is_writable($config['users_path']))
	{
		repairFilePermission($config['users_path'], $errors);
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['users_path'].'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			repairFilePermission($config['users_path'], $errors);
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			repairFilePermission($config['users_path'], $errors);
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	clearstatcache();
	
	// users file
	if (!is_writable($config['users_path'].'/amalia-users.txt'))
	{
		repairFilePermission($config['users_path'].'/amalia-users.txt', $errors);
	}
	clearstatcache();
	
	if (!is_writable($config['users_path'].'/amalia-users-permissions.txt'))
	{
		repairFilePermission($config['users_path'].'/amalia-users-permissions.txt', $errors);
	}
	clearstatcache();
	
	// now check permissions on root of Site Path
	if (!is_writable($config['site_path']))
	{
		repairFilePermission($config['site_path'], $errors);
	}
	else {
		// since this is a directory, do a test file create
		
		$testFileSuffix = md5(uniqid().time().microtime().rand(0,getrandmax()));
		$testFileName = $config['site_path'].'/amalia_test_file_'.$testFileSuffix.'.txt';
		$testFh = @fopen($testFileName, 'w');
		if (!$testFh)
		{
			repairFilePermission($config['site_path'], $errors);
		}
		else if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
		{
			@unlink($testFileName);	
			fclose($testFh);
			repairFilePermission($config['site_path'], $errors);
		}
		else
		{
			// delete the test file and move on
			@unlink($testFileName);
			fclose($testFh);
		}	
	}
	clearstatcache();
	
	// loop through Site Path to attempt repair on every single subfile and subfolder
	$dh = opendir($config['site_path']);
	
	while (($file = readdir($dh)) !== false)	
	{
		recursivePermissionsRepair($config['site_path'], $file, $errors);			
	}
	
	closedir($dh);
	
	// loop through Recycle Bin to attempt repair on any recycled files
	$dh = opendir($config['config_path'].'/userfiles/recycle');
	
	while (($file = readdir($dh)) !== false)
	{
		recursivePermissionsRepair($config['config_path'].'/userfiles/recycle', $file, $errors, false);
	}	
	
	closedir($dh);
	
	// loop through thumbcache to attempt repair on any of that
	$dh = opendir($config['config_path'].'/userfiles/thumbcache');
	
	while (($file = readdir($dh)) !== false)
	{
		recursivePermissionsRepair($config['config_path'].'/userfiles/thumbcache', $file, $errors, false);
	}	
	
	closedir($dh);
	
	// if errors count is >0, there were errors -- return them to the controller
	// for display in the UI
	if (is_array($errors) && count($errors) > 0)
	{
		return $errors;
	}
	else {
		// return BOOL 	true indicates no errors
		return true;
	}
	

}

function repairFilePermission($fullFilePath, &$errors)
{
	// repair permissions on a single file, throwing errors back to array $errors
	
	$whoami = safe_plain(exec('whoami')); //TODO: Windows support?

	if (is_dir($fullFilePath))
	{
		$mode = 0755; // u=rwx,g=rx,o=rx
		$type = 'folder';
	}
	else {
		$mode = 0644; // u=rw,g=r,o=r
		$type = 'file';
	}
		
	// attempt to chmod the file
	if (!@chmod($fullFilePath, $mode))
	{
		$errors[] = 'Could not automatically repair permissions on "'.safe_filename($fullFilePath).'". Please give the user "'.$whoami.'" read and write permissions to this '.$type.'.';
		return false;
	}
	else {
		return true;
	}
}

function recursivePermissionsRepair($dir, $file, &$errors, $configPathCheck = true)
{
	global $config;
	
	// called by repairPermissions() on the Site Path.
	
	// does a permissions repair attempt. If $file is a dir, recursively repairs $file for its subfiles and subfolders.
	
	// exclusions
	// do not do ., .., or a config path underneath the site path
	if (strpos($file, '.') === 0)
	{
		return true;
	}
	if ($configPathCheck)
	{
		if (strpos($dir.'/', $config['config_path'].'/') === 0)
		{
			return true;
		}
	}
	
	
	if (is_dir($dir.'/'.$file))
	{
		// directory. check dir writability, then loop through subfiles to check theirs
	
		if (!is_writable($dir.'/'.$file))
		{
			$result = repairFilePermission($dir.'/'.$file, $errors);
		}
		clearstatcache();
		
		$dh = opendir($dir.'/'.$file);
		
		while (($subfile = readdir($dh)) !== false)
		{
			recursivePermissionsRepair($dir.'/'.$file, $subfile, $errors);
			clearstatcache();
		}
		
		return $result;	
	}
	else {
		 // normal file. check writability
		 if (is_writable($dir.'/'.$file))
		 {
	 		clearstatcache();
		 	return true;
		 }
		 else {
		 	clearstatcache();
			return repairFilePermission($dir.'/'.$file, $errors);
		 }
	}

}


function rewriteConfigFile()
{

	global $config;
	
	// recreate the config file based on current values, but writing a whole new file


	$amaliaConfig = <<<EOT
<?php

// Amalia Config File. Regenerated by the Amalia 'Repair Configuration File' feature on {genDate}

if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}

// Defines the hash algorithm used for security and authentication purposes.
// You MUST NOT change this after install, or all the passwords must be reset
// manually.
define('HASH_ALGO', '{hashToUse}');

// If DEBUG is true, error details will be printed to browser output. This does not affect
// whether PHP shows errors as well (must be changed in php.ini or .htaccess).
define('DEBUG', {WRITE_DEBUG_TO_CONFIG});

// The salt is a long string of randomness used in various security and authentication
// routines. You MUST NOT change this after install, or all the passwords must be reset 
// manually.
%config['salt'] = '{salt}';


// Config Path
%config['config_path'] = '{configPath}';

// Config URL
%config['config_url'] = '{configURL}';

// Site Path
%config['site_path'] = '{sitePath}';

// Site URL
%config['site_url'] = '{siteURL}';

// Users File Path
%config['users_path'] = '{usersPath}';

// Themes Path (assumed to be site_path/.themes), can be customised.
%config['themes_path'] = '{themesPath}';

// Themes URL (assumed to be site_url/.themes), can be customised.
%config['themes_url'] = '{themesURL}';

// These settings are for the client website.
%config['mod_rewrite'] = {enableRewrite};
%config['version'] = '{version}';
%config['recycle_days'] = 30;
%config['show_thumbnails'] = true; // show thumbnails in file browser
%config['browser_sort_by_filename_only'] = false; // always sort by filename rather than by file title


// This defines the allowed file types.  (White listing is much easier than blacklisting)
%config['allowed'] = array("aif" => "audio/x-aiff",
				
				"aifc" => "audio/x-aiff",
				"aiff" => "audio/x-aiff",
				"asf" => "video/x-ms-asf",
				"asr" => "video/x-ms-asf",
				"asx" => "video/x-ms-asf",
				"au" => "audio/basic",
				"avi" => "video/x-msvideo",
				"bmp" => "image/bmp",
				"doc" => "application/msword",
				"dot" => "application/msword",
				"gif" => "image/gif",
				"jfif" => "image/pipeg",
				"jpe" => "image/jpeg",
				"jpeg" => "image/jpeg",
				"jpg" => "image/jpeg",
				"mdb" => "application/x-msaccess",
				"mid" => "audio/mid",
				"mny" => "application/x-msmoney",
				"mov" => "video/quicktime",
				"movie" => "video/x-sgi-movie",
				"mp2" => "video/mpeg",
				"mp3" => "audio/mpeg",
				"mpa" => "video/mpeg",
				"mpe" => "video/mpeg",
				"mpeg" => "video/mpeg",
				"mpg" => "video/mpeg",
				"mpp" => "application/vnd.ms-project",
				"mpv2" => "video/mpeg",
				"pdf" => "application/pdf",
				"png" => "image/png",
				"pot" => "application/vnd.ms-powerpoint",
				"pps" => "application/vnd.ms-powerpoint",
				"ppt" => "application/vnd.ms-powerpoint",
				"pub" => "application/x-mspublisher",
				"qt" => "video/quicktime",
				"ra" => "audio/x-pn-realaudio",
				"ram" => "audio/x-pn-realaudio",
				"rtf" => "application/rtf",
				"rtx" => "text/richtext",
				"svg" => "image/svg+xml",
				"tif" => "image/tiff",
				"tiff" => "image/tiff",
				"txt" => "text/plain",
				"vcf" => "text/x-vcard",
				"wav" => "audio/x-wav",
				"wcm" => "application/vnd.ms-works",
				"wdb" => "application/vnd.ms-works",
				"wks" => "application/vnd.ms-works",
				"wmf" => "application/x-msmetafile",
				"wps" => "application/vnd.ms-works",
				"wri" => "application/x-mswrite",
				"xbm" => "image/x-xbitmap",
				"xla" => "application/vnd.ms-excel",
				"xlc" => "application/vnd.ms-excel",
				"xlm" => "application/vnd.ms-excel",
				"xls" => "application/vnd.ms-excel",
				"xlt" => "application/vnd.ms-excel",
				"xlw" => "application/vnd.ms-excel",
				"zip" => "application/zip",

				"docm" =>"application/vnd.ms-word.document.macroEnabled.12",
				"docx" =>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
				"dotm" =>"application/vnd.ms-word.template.macroEnabled.12",
				"dotx" =>"application/vnd.openxmlformats-officedocument.wordprocessingml.template",
				"potm" =>"application/vnd.ms-powerpoint.template.macroEnabled.12",
				"potx" =>"application/vnd.openxmlformats-officedocument.presentationml.template",
				"ppam" =>"application/vnd.ms-powerpoint.addin.macroEnabled.12",
				"ppsm" =>"application/vnd.ms-powerpoint.slideshow.macroEnabled.12",
				"ppsx" =>"application/vnd.openxmlformats-officedocument.presentationml.slideshow",
				"pptm" =>"application/vnd.ms-powerpoint.presentation.macroEnabled.12",
				"pptx" =>"application/vnd.openxmlformats-officedocument.presentationml.presentation",
				"xlam" =>"application/vnd.ms-excel.addin.macroEnabled.12",
				"xlsb" =>"application/vnd.ms-excel.sheet.binary.macroEnabled.12",
				"xlsm" =>"application/vnd.ms-excel.sheet.macroEnabled.12",
				"xlsx" =>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
				"xltm" =>"application/vnd.ms-excel.template.macroEnabled.12",
				"xltx" =>"application/vnd.openxmlformats-officedocument.spreadsheetml.template",
				
				"dat" => "application/octet-stream" // required for Flash uploader, which does not send valid mimetypes
				// this will be ignored for server-side mimetype checking.
				// this entry MUST be last in the list
				
);

%config['file_types'] = array('images' => array('png','jpg','jpeg','gif','bmp','tif','tiff','svg'),
					'dynamic_files' => array('php','xml'),
					'static_files' => array('doc','docx','ppt','pptx','xls','xlsx','pdf','txt','vcard'),
					'video' => array('mov','mpg','mpeg','flv','ogg','wma','avi'),
					'audio' => array('wav','mp3','mid','midi','au','asx','m3u'),
					);
?>
EOT;
	
	
	// force path variables to be in the correct format
	$site_path = rtrim($config['site_path'], '/');
	$config_path = rtrim($config['config_path'], '/');
	$config_url = rtrim($config['config_url'], '/');
	$site_url = rtrim($config['site_url'], '/');
	$users_path = rtrim($config['users_path'], '/');
	$themes_path = rtrim($config['themes_path'], '/');
	$themes_url = rtrim($config['themes_url'], '/');
	
	$debugMode = (WRITE_DEBUG_TO_CONFIG) ? 'true' : 'false';
	
	// substitute in variables
	$amaliaConfig = str_replace('%', '$', $amaliaConfig);
	$amaliaConfig = str_replace('{genDate}', date('Y-m-d H:i'), $amaliaConfig);
	$amaliaConfig = str_replace('{hashToUse}', HASH_ALGO, $amaliaConfig);
	$amaliaConfig = str_replace('{WRITE_DEBUG_TO_CONFIG}', $debugMode, $amaliaConfig);
	$amaliaConfig = str_replace('{salt}', safe_plain($config['salt']), $amaliaConfig);
	$amaliaConfig = str_replace('{sitePath}', safe_plain($site_path), $amaliaConfig);
	$amaliaConfig = str_replace('{configPath}', safe_plain($config_path), $amaliaConfig);
	$amaliaConfig = str_replace('{configURL}', safe_plain($config_url), $amaliaConfig);
	$amaliaConfig = str_replace('{siteURL}', safe_plain($site_url), $amaliaConfig);
	$amaliaConfig = str_replace('{usersPath}', safe_plain($users_path), $amaliaConfig);
	$amaliaConfig = str_replace('{version}', safe_plain(AMALIA_VERSION), $amaliaConfig);
	$amaliaConfig = str_replace('{themesPath}', safe_plain($themes_path), $amaliaConfig);
	$amaliaConfig = str_replace('{themesURL}', safe_plain($themes_url), $amaliaConfig);
	
	$enableRewrite = ($config['mod_rewrite'] == true) ? 'true' : 'false';
	
	$amaliaConfig = str_replace('{enableRewrite}', $enableRewrite, $amaliaConfig);
	
	
	// finish substituting in variables into config file, ready to write
	$amaliaConfigFileName = safe_filename($config_path.'/amalia-config.php');

	$fh = @fopen($amaliaConfigFileName, 'w');
	if (!$fh)
	{
		return false;
	}

	fwrite($fh, $amaliaConfig);
	fclose($fh);

	// finished writing config file
	
	return true;


}

function resetAllUsers()
{

	global $config, $auth;

	// should only run if user has ALL permissions (as it gives all permissions back to them!)
	
	//create-pages:1;edit-pages:1;delete-files:1;upload-files:1;rename-files:1;manage-plugins:1;manage-users:1;configure-amalia:1;
	if (!$auth->has_perm('create-pages') || !$auth->has_perm('edit-pages') || !$auth->has_perm('delete-files') ||
		!$auth->has_perm('upload-files') || !$auth->has_perm('rename-files') || !$auth->has_perm('manage-plugins') ||
		!$auth->has_perm('manage-users') || !$auth->has_perm('configure-amalia')
	)
	{
		return false;
	}
	
	
	$usersFile = <<<EOT
# Amalia Users File
#
#
# Please do not edit this file by hand. Instead, log
# in to Amalia and use the User Management tool.
#
#
#id;username;hashed password;email;name
{userID};{username};{hashed_password};{email};{name}
EOT;

	$usersFile = str_replace('{userID}', strtolower(safe_usersline($_SESSION['amalia_auth']['id'])), $usersFile);	
	$usersFile = str_replace('{username}', strtolower(safe_usersline($_SESSION['amalia_auth']['username'])), $usersFile);
	$usersFile = str_replace('{hashed_password}', safe_usersline($_SESSION['amalia_auth']['hashed_password']), $usersFile);
	$usersFile = str_replace('{email}', safe_usersline($_SESSION['amalia_auth']['email']), $usersFile);
	$usersFile = str_replace('{name}', safe_usersline($_SESSION['amalia_auth']['f_name']), $usersFile);
	
	$fh = @fopen($config['users_path'].'/amalia-users.txt', 'w');
	
	if (!$fh)
	{
		return false;
	}
	
	fwrite($fh, $usersFile);
	fclose($fh);
	
	$permissionsFile = <<<EOT
# Amalia User Permissions File
#
#
# Please do not edit this file by hand. Instead, log
# in to Amalia and use the User Management tool.
#
#
{userID};create-pages:1;edit-pages:1;delete-files:1;upload-files:1;rename-files:1;manage-plugins:1;manage-users:1;configure-amalia:1;
EOT;
	
	$permissionsFile = str_replace('{userID}', strtolower(safe_usersline($_SESSION['amalia_auth']['id'])), $permissionsFile);	
	
	$fh = @fopen($config['users_path'].'/amalia-users-permissions.txt', 'w');
	
	if (!$fh)
	{
		return false;
	}
	
	fwrite($fh, $permissionsFile);
	fclose($fh);
	
	return true;
	
}

?>