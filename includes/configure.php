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
 * Config File Updater
 *
 * Able to write changes to various configuration options directly
 * back to the config script.
 *
 * @package	Amalia2
 * @category	Amalia Generation
 * @author		Amalia Dev Team
 */

 
function update_config_file($data)
{
	
	global $config;

	$errors = array();


	//			preg_match('/\$amalia_title = \'(.*)\';/', $text, $match);
	
	// check post data
	
	if (empty($data['debug']))
	{
		$errors[count($errors)] = 'You must select whether debug mode is on or off.';
	}
	else {
		$newDebugMode = ($data['debug'] == 'true') ? 'true': 'false';
		$_SESSION['debug_haschanged'] = true;
	}
	
	if (empty($data['rewrite']))
	{
		$errors[count($errors)] = 'You must select whether rewriting of URLs is on or off.';
	}
	else {
		$newRewriteMode = ($data['rewrite'] == 'true') ? 'true' : 'false';
		$_SESSION['mod_rewrite_haschanged'] = true;
	}
	
	if (empty($data['thumbnails']))
	{
		$errors[count($errors)] = 'You must select whether thumbnails should be shown in the file browser.';
	}
	else {
		$newThumbnailMode = ($data['thumbnails'] == 'true') ? 'true' : 'false';
	}
	
	if (count($errors) > 0)
	{
		// just end now, sending back the errors.
		return $errors;
		// otherwise, continue processing
	}
	
	
	// open the config file and read it into an array
	try {
		
		$fh = @fopen($config['config_path'].'/amalia-config.php', 'r');
		
		if (!$fh)
		{
			$errors[0] = 'Unable to open the config file for reading.';
			return $errors;
		}
	}
	catch (Exception $e)
	{
		$errors[0] = 'Unable to open the config file for reading. '.safe_plain($e);
		return $errors;
	}
	
	$configFileArray = fread($fh, @filesize($config['config_path'].'/amalia-config.php'));
	$configFileArray = explode("\n", $configFileArray);
	
	fclose($fh);

	if (count($configFileArray) > 0)
	{
		foreach($configFileArray as $lineNo => $line)
		{
		
			// is this a line we can replace? if so, prepare to replace this line
			
			if (preg_match("/^define\('DEBUG'/", $line))	
			{
				// is the debug line. replace this line with a new debug line
				$configFileArray[$lineNo] = "define('DEBUG', $newDebugMode);";			
			}
			
			else if (preg_match('/^\$config\[\'mod_rewrite\'\]/', $line))
			{
				// is the mod_rewrite line. replace with new mod_rewrite line
				$configFileArray[$lineNo] = '$config[\'mod_rewrite\'] = '.$newRewriteMode.';';
			}		
			
			else if (preg_match('/^\$config\[\'show_thumbnails\'\]/', $line))
			{
				// is the show thumbnails line, replace with new thumbnail setting
				$configFileArray[$lineNo] = '$config[\'show_thumbnails\'] = '.$newThumbnailMode.';';
			}
			
			
		
		}
	}
	else
	{
		$errors[0] = 'Config file appears empty.';
		return $errors;
	}
	
	
	// write the new config file
	
	$newConfigFile = implode("\n", $configFileArray);
	
	try {
		
		$fh = @fopen($config['config_path'].'/amalia-config.php', 'w');
		
		if (!$fh)
		{
			$errors[0] = 'Unable to open the config file for writing.';
			return $errors;
		}
	}
	catch (Exception $e)
	{
		$errors[0] = 'Unable to open the config file for writing. '.safe_plain($e);
		return $errors;
	}
	
	
	fwrite($fh, $newConfigFile);
	fclose($fh);
	
	return true;

}





function recursiveDeleteInstallerDir($dir)
{

	global $config;
	
	try {
		$handle = @opendir($dir);
		
		if (!$handle)
		{
			return false;
		}
	}
	catch (Exception $e)
	{
		return false;
	}
	
	// loop through contents
	while ( ($file = readdir($handle)) !== false)
	{
	
		if ($file == '.' || $file == '..')
		{
			continue; // ignore self and parent directory!
		}
	
		if (!is_dir($dir.'/'.$file))
		{
			// normal file that we can go ahead and unlink
			
			@unlink($dir.'/'.$file);
		
		}
		
		else {
			
			// recursively run me to delete the stuff inside that
			recursiveDeleteInstallerDir($dir);
		
		}
	
	}
	
	// once we're done, rmdir this dir
	@rmdir($dir);
	
	return true;
		
} 

function deleteGitRevisionFooterFile()
{

	global $config;

	try {
	
		@unlink($config['config_path'].'/git_revision_footer.php');
	
	}
	
	catch (Exception $e)
	{
		return false;
	}

}



?>