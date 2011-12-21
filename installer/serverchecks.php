<?php
/**
 * Amalia2
 *
 * The way "real people" manage websites
 *
 * @package	Amalia2
 * @author		Amalia Dev Team
 * @copyright	Copyright (c) 2007-2011, Chris Van Patten, Nick Sampsell, Peter Upfold
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
 * Installer Server Checks
 * 
 * Checks that various requirements for the installation of Amalia have
 * been met.
 *
 * @package	Amalia2
 * @category	Setup
 * @author		Amalia Dev Team
 */

if (!defined('IN_AMALIA_INSTALLER'))
{

	header('HTTP/1.0 403 Forbidden');
	die('<h1>Please run the <a href="index.php">installer</a>.</h1>');

}

$rev = '54c07d7'; // git-rev-auto-update-line
define('AMALIA_VERSION', 'git-'.$rev);

$checks['php'] = (version_compare(PHP_VERSION, '5.2.3', '>='));

// fail the PHP check if magic_quotes is on
if ($checks['php'])
{

	if (get_magic_quotes_gpc() || get_magic_quotes_runtime())
	{
		$checks['php'] = false;
	}
	
}

$checks['curl'] = (function_exists('curl_init') && curl_init()); // dummy URL, just to check init


// permissions checks -- create a test file
$directory = dirname(__FILE__);
// strip installer from directory, we should get top-level
$directory = str_replace('installer', '', $directory); //TODO this isn't good enough
$testFileSuffix = md5(time().microtime().rand(0,getrandmax()));
$testFileName = $directory.'amalia_test_file_'.$testFileSuffix.'.txt';
$testFh = @fopen($testFileName, 'w');
if (!$testFh)
{
	$checks['permissions'] = false;
}
else
{
	if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
	{
		$checks['permissions'] = false;
		@unlink($testFileName);
		fclose($testFh);
	}
	else
	{
		$checks['permissions'] = true;
		@unlink($testFileName);
		fclose($testFh);
	}
}
// finished permission checks


$checks['fileman'] = (function_exists('fopen') &&
						function_exists('fread') &&
						function_exists('fwrite') &&
						function_exists('file_exists') &&
						function_exists('is_readable') &&
						function_exists('is_dir') &&
						function_exists('file_get_contents') &&
						function_exists('unlink') &&
						function_exists('rmdir') &&
						function_exists('chmod'));
// addendum to $checks['fileman']
if ($checks['fileman'])
{
	// test a real file open to make sure stuff actually works
	$testfh = fopen($_SERVER['SCRIPT_FILENAME'], 'r');
			
	if (!$testfh || !fread($testfh, filesize($_SERVER['SCRIPT_FILENAME'])))
	{
		$checks['fileman'] = false;	
	}
	
}
if ($checks['fileman'])
{

	// check uploads will work
	if (!function_exists('mime_content_type') || !is_callable('mime_content_type'))
	{
		$checks['fileman'] = false;
	}

}

// check for GD with JPEG + PNG + GIF support
$checks['gd'] = (
					function_exists('imagecreatetruecolor') &&
					function_exists('imagecreatefromjpeg') &&
					function_exists('imagecreatefrompng') &&			
					function_exists('imagecreatefromgif') &&
					function_exists('imagejpeg') &&
					function_exists('imagepng')
				);
				
				
// mod_rewrite check
if (!function_exists('apache_get_modules'))
{
	$checks['mod_rewrite'] = false;
}
else
{
	$checks['mod_rewrite'] = in_array('mod_rewrite', apache_get_modules());
}

if (!empty($_SESSION['disable_mod_rewrite']))
{
	$checks['mod_rewrite'] = false;
}

/* NOTE: whether or not mod_rewrite works is annoyingly complex. The correct options must be set
   in Apache, *and* each directory must have the correct .htaccess file inside it. This check will
   not be complete until and unless it also checks the validity of .htaccess files in the root
   Amalia directory.
*/




?>