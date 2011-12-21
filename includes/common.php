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
 * Common Functions
 *
 * @package	Amalia2
  * @category	Common
 * @author		Amalia Dev Team
 */
 

if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}
	if(!isset($_COOKIE['AmaliaCookie'])) 
	{
		setcookie("AmaliaCookie", base64_encode(time()), time()+86400);
	} 


$rev = '4720197'; // git-rev-auto-update-line
define('AMALIA_VERSION', 'git-'.$rev);

function safe_plain($text, $doHTMLEntities = true)
{
	
	// you should doHTMLEntities unless there is a specific, compelling reason not to.
	
	if ($doHTMLEntities)
	{
		return trim(htmlentities(strip_tags($text), ENT_QUOTES, 'UTF-8', false));
	}	
	else
	{
		return trim(strip_tags($text));
	}
	
}

function safe_plain_xmldoc($text)
{

	$text = html_entity_decode(safe_plain($text, false), ENT_QUOTES, 'UTF-8');
	// output as raw, native UTF-8. PHP might get confused if we need to
	// do any processing, but we don't care. just push it to the client raw
	
	$text = str_replace(']]>', '', $text); // prevent breaking out of CDATA
	
	// you MUST MUST MUST put anything you get out of this function inside a <![CDATA[   ]]> block
	return $text;

}

function safe_filename($text)

{
	
	// make a filename safe for saving or looking for
	
	// Any dynamic hunting for filenames, e.g. router.php:view() must pass them through this.
	// there should be no / to the right of a .

	$text = str_replace("\0", '', $text);
	$text = str_replace('../', '', $text);	
	$text = str_replace('..', '', $text);
	$text = str_replace('./', '', $text);	
	
	$text = trim($text); // should get rid of nullchars etc.
	
	return safe_plain($text);

}

function safe_usersline($text, $doHTMLEntities = true)
{

	// simply strips out semicolons and newlines which would otherwise break parsing
	
	$tmp = str_replace(';', '', safe_plain($text, $doHTMLEntities));
	$tmp = str_replace("\n", '', $tmp);
	$tmp = str_replace("\r", '', $tmp);
	$tmp = str_replace("\0", '', $tmp);
	return $tmp;


}

function generate_filename_from_title($title)
{
	// to be used if we need to server-side generate a underscored, FS-safe filename from a page/image title
	
	$filename = strtolower($title);

	$filename = preg_replace('/\s+/', '_', $filename); // spaces to underscores	
	$filename = preg_replace('/[^\w\-]+/', '', $filename); // non-word character filter

	return $filename; // the filename generated now MUST be run through valid_new_filename

}

function valid_new_filename($name, $desiredExtension = 'php')
{
	$desiredExtension = strtolower($desiredExtension);
	$name = safe_filename($name);
	
	// first, filter any entities (may have been introduced by safe_filename)
	$name = preg_replace('/\&(\S*)\;/', '', $name);

	// don't allow bad filenames -- filter any crap
	$name = preg_replace('/^\W+|\W+$/', '', $name);
	// remove all non-alphanumeric chars at begin & end of string
	$name = preg_replace('/\s+/', '_', $name);
	// compress internal whitespace and replace with _
	
	//$name = strtolower(preg_replace('/\W-\./', '', $name));
	$name = strtolower(preg_replace('/[^0-9a-zA-Z\-_\.]/', '', $name));
	// remove all non-alphanumeric chars except _, - and .
	
	
	// if file is too long, truncate it
	if (strlen($name) > 64)
	{
		$name = substr($name, 0, 64);
	}
	
	// check extension is desired, add if not
	if (pathinfo($name, PATHINFO_EXTENSION) != $desiredExtension)
	{
		if (!empty($desiredExtension))
		{
			$name .= '.'.$desiredExtension;
		}
	}
	
	ltrim($name, '/');
	// make sure dot is not first char, or the file will disappear as hidden
	if (strpos($name, '.') === 0)
	{
		$name = preg_replace('/^\./', '', $name);
	}

	// if after that, the filename is just 'php', then it was blank, so give it a random one
	if ($name == $desiredExtension)
	{
		if (!empty($desiredExtension))
		{
			$name = uniqid().'.'.$desiredExtension;
		}
		else {
			$name = uniqid();
		}
	}
	
	return $name;

}

function string_to_filename($word) {
    $tmp = preg_replace('/^\W+|\W+$/', '', $word); // remove all non-alphanumeric chars at begin & end of string
    $tmp = preg_replace('/\s+/', '_', $tmp); // compress internal whitespace and replace with _
    return strtolower(preg_replace('/[^0-9a-zA-Z\-_\.]/', '', $tmp)); // remove all non-alphanumeric chars except _ and -
}

function redirect($action = null, $data = null, $die = true, $insideFrame = false)
{
	
	global $config;
	
	if (!isset($action) || empty($action))
	{
		// redirect back to the index page
		if (!$insideFrame)
			header('Location: '.$config['config_url']);
		else
			echo '<script type="text/javascript">window.parent.location.replace(\''.print_internal_link('', '', 'js', true	).'\');</script>';
		if ($die)
			die();
	}
	
	else {
		if (!$insideFrame)
			header('Location: '.print_internal_link(safe_plain($action), str_replace('&amp;', '&', safe_plain($data)), 'js', true	));
		else
			echo '<script type="text/javascript">window.parent.location.replace(\''.print_internal_link(safe_plain($action), str_replace('&amp;', '&', safe_plain($data)), 'js', true	).'\');</script>';
		if ($die)
			die();
	
	}
	
}

function redirect_to_endpoint($plugin, $endpoint, $data = null)
{

	// to be called by data_processor endpoints? 
	
	global $config;
	
	//TODO: add pretty URL structure to this
	
	$link .= $config['config_url'].'/?plugin='.safe_plugin_identifier($plugin).'&_e='.safe_plugin_identifier($endpoint);
	
	if (!empty($data))
	{
		$data = urlencode($data);
		$data = str_replace("\n", '', $data);
		$data = str_replace("\r", '', $data);
		$data = str_replace("\0", '', $data);
		$link .= '&data='.$data;
	}

	header('Location: '.$link);
	die();
	

}

function filter_commented_line($var)
{
	return !preg_match("/^\#/", $var);
}

function filter_non_commented_lines($var)
{
	
	return preg_match("/^\#/", $var); // discard all non-commented lines
		
}


function generate_small_unique_id($length) {
  $random = '';
  for ($i = 0; $i < $length; $i++) {
  	if (rand(0,1) == 0)
  	{
	    $random .= chr(rand(48, 57));
	}
	else
	{
		$random .= chr(rand(97, 122));
	}
  }
  return $random;
}


function print_internal_link($page, $args = false, $type = false, $return = false)
{	
	// set return to true if you want it to return, not print
	
	global $config;
	$page = urldecode($page);
	$args = str_replace('//','/',$args);
	$link = '';
	
	if ($config['mod_rewrite'])
	{
		$link .= $config['config_url'].'/'.$page;
		
		if ($args != false)
		{
			$link .= '?'.$args;	
		}
				
	}
	else
	{
		$link .= $config['config_url'].'/?action='.$page;
		
		if ($args != false)
		{
			if($type == false) {
			$link .= '&amp;'.$args;	
			} else {
			$link .= '&'.$args;
			}
			
		}
				
	}
	
	if ($return)
		return $link;
	else
		echo $link;
		
}

function print_endpoint_link($plugin, $endpoint, $data = null, $return = false)
{

	global $config;
		
	//TODO: add pretty URL structure to this
	
	$link .= $config['config_url'].'/?plugin='.safe_plugin_identifier($plugin).'&_e='.safe_plugin_identifier($endpoint);
	
	if (!empty($data))
	{
		$data = urlencode($data);
		$data = str_replace("\n", '', $data);
		$data = str_replace("\r", '', $data);
		$data = str_replace("\0", '', $data);
		$link .= '&data='.$data;
	}

	if ($return)
		return $link;
	else
		echo $link;

}

function friendly_fatal($msg, $internalError = false)
{

	// if internalError is false, then we display the error regardless
	// of debug status (i.e. we assume the user caused the error and it 
	// is not an Amalia breakage per se)

	if ($internalError)
	{
		
		echo '<div id="fatal" style="width: 800px; padding: 30px;margin: 25px;z-index: 999 !important;color:#ffffff;background-color:  #ff0000;font-family:\'Helvetica Neue\',Arial,sans-serif;font-size:16pt;font-weight:normal;text-align:center;">'.safe_plain($msg).'</div>';
		die();	
	
	}
	else if (DEBUG)
	{
	
		echo '<div id="fatal" style="width: 800px; padding: 30px;margin: 25px;z-index: 999 !important;color:#ffffff;background-color:  #ff0000;font-family:\'Helvetica Neue\',Arial,sans-serif;font-size:16pt;font-weight:normal;text-align:center;"><strong>Fatal Error:</strong> '.safe_plain($msg).'</div>';
		die();
	}
	else
	{
		echo '<div id="fatal" style="width: 800px;padding: 30px;margin: 25px;z-index: 999 !important;color:#ffffff;background-color:  #ff0000;font-family:\'Helvetica Neue\',Arial,sans-serif;font-size:16pt;font-weight:normal;text-align:center;">Amalia has run into a problem and cannot load. For technical details, please switch on Debug mode.</div>';
		die();
	}
		
}

function friendly_error($context, $msg)
{
	global $friendly_errors;
	
	$context = safe_plain($context);
	$msg = safe_plain($msg);
	if (is_array($friendly_errors) && count($friendly_errors) > -1)
	{
		$friendly_errors[count($friendly_errors)] = array($context, $msg);
	}
	else {
		friendly_fatal($msg);	
	}
}

function check_version() {
	global $config;

	if(isset($_COOKIE['AmaliaCookie']))
	{

		if(base64_decode($_COOKIE['AmaliaCookie']) >= time() - 86400) 
		{
		$url = 'http://getamalia.com/version_check';

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // wait up to 10 seconds.

		//execute post
		$result = curl_exec($ch);
		
		$result = trim($result);
		
		if($result == AMALIA_VERSION) 
		{
			// all good
			return true;
		}
		else
		{
			// out of date or mismatch
			return false;
		}


		//close connection
		curl_close($ch);
		}	
	}
}

function jsonFailWithError($error)
{

	?>{
	"type": "failure",
	"error_message": "<?php echo $error; ?>"	
}<?php
	// assumes static error message, so safe_plaining is not done

}

function jsonSuccess()
{
	
	?>{
	
	"type": "success",
	"message": "Operation completed successfully."
}<?php

}


function catch_exceptions($exception) 
{
	if (DEBUG)
	{
		echo '<div id="fatal">'.$exception->getMessage().'</div>';
		die();
	}
	else
	{
		echo '<div id="fatal">Amalia has run into a problem and cannot load. For technical details, please switch on Debug mode.</div>';
	}
}
function list_file_error($msg)
{
	//friendly_error('File Manager', $msg);
	friendly_fatal($msg, true);
}

function execStartTime() {

        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        define('START_EXEC_TIME', $mtime);

}

function execEndTime() {

        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endTime = $mtime;

        $totalTime = $endTime - START_EXEC_TIME;

        return $totalTime;

}


function sort_file_listing_by_title($compare1, $compare2)
{
	// callback function for usort() to sort a file-manager file listing assoc array by file title
	// sorts by file title, or file name if file title is unset
	
	if (!empty($compare1['title']) && !empty($compare2['title']))
	{
		// both have titles, compare those
		return strcasecmp($compare1['title'], $compare2['title']);
	}
	else if (empty($compare1['title']) && !empty($compare2['title']))
	{
		// file 1 has only filename, file 2 has title, compare name of 1 with title of 2
		return strcasecmp($compare1['name'], $compare2['title']);
	}
	else if (!empty($compare1['title']) && empty($compare2['title']))
	{
		// file 1 has title, file 2 has only filename, compare title of 1 with name of 2
		return strcasecmp($compare1['title'], $compare2['name']);
	}
	else {
		// both have only name
		return strcasecmp($compare1['name'], $compare2['name']);
	}
	
}

function createAmaliaTitleFile($relativeFilename, $newTitle)
{
	// create a title file

	global $config;
	
	$relativeFilename = safe_filename($relativeFilename);
	$newTitle = safe_plain($newTitle);

	$amaliatitleFile = $config['config_path'].'/userfiles/filedata/'.ltrim($relativeFilename, '/').'.amaliatitle';
	// get directory from relative file name
	$relativeDirs = explode('/', ltrim($relativeFilename, '/'));
	$relativeDirs = array_slice($relativeDirs, 0, -1); // slice off last element
	
	// check any required parent dirs exist for this title, and if not, create them
	foreach($relativeDirs as $idx => $dir)
	{
		
		// get the 'last level' of the dir -- anywhere up to this bit of the string (this dir)
		if (version_compare(PHP_VERSION, '5.3.0') >= 0)
		{
			$dirs = @strstr(ltrim($relativeFilename, '/'), $dir, true).$dir;				
		}
		else {
			$dirs = @strstrb(ltrim($relativeFilename, '/'), $dir).$dir;
		}
		$thisDirLevel = $config['config_path'].'/userfiles/filedata/'.$dirs;
		
		
		if (!file_exists($thisDirLevel))
		{
			@mkdir($thisDirLevel, 0755);
		}
	}
	
	// write the new title to the title file
	$titleFH = @fopen($amaliatitleFile, 'w');
	
	if (!$titleFH)
	{
		header('HTTP/1.0 500 Internal Server Error');
		die('Unable to save the new title to the page. Please check the permissions on your Config Path and the \'filedata\' in \'userfiles\' folder.');
	}
	
	fwrite($titleFH, $newTitle);
	fclose($titleFH);

}

function strstrb($h,$n) {

	// backwards strstr for pre PHP 5.3

    return array_shift(explode($n,$h,2));
}

?>