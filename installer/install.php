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
 * Installer Process Script
 * 
 * Processes the installation of Amalia. Called via Ajax from the 
 * Installer interface. Responds in JSON for jQuery parsing in client-sid
 * of index.php.
 *
 * @package	Amalia2
 * @category	Setup
 * @author		Amalia Dev Team
 */
 
define('IN_AMALIA_INSTALLER', true);
define('WRITE_DEBUG_TO_CONFIG', 'true'); // please make it a string literal

ini_set("session.cookie_httponly", 1);
session_name('AmaliaInstall-'.md5($_SERVER['SERVER_NAME'].__FILE__));
session_start();

require('serverchecks.php'); // run checks again for server-side validation

/*********************** FUNCTIONS ***********************/

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

function safe_usersline($text)
{

	// simply strips out semicolons which would otherwise break parsing
	
	return str_replace(';', '', safe_plain($text));


}


// SALT GENERATOR

function assign_rand_value($num)
{
// accepts 1 - 62
  switch($num)
  {
    case 1:
     $rand_value = "a";
    break;
    case 2:
     $rand_value = "b";
    break;
    case 3:
     $rand_value = "c";
    break;
    case 4:
     $rand_value = "d";
    break;
    case 5:
     $rand_value = "e";
    break;
    case 6:
     $rand_value = "f";
    break;
    case 7:
     $rand_value = "g";
    break;
    case 8:
     $rand_value = "h";
    break;
    case 9:
     $rand_value = "i";
    break;
    case 10:
     $rand_value = "j";
    break;
    case 11:
     $rand_value = "k";
    break;
    case 12:
     $rand_value = "l";
    break;
    case 13:
     $rand_value = "m";
    break;
    case 14:
     $rand_value = "n";
    break;
    case 15:
     $rand_value = "o";
    break;
    case 16:
     $rand_value = "p";
    break;
    case 17:
     $rand_value = "q";
    break;
    case 18:
     $rand_value = "r";
    break;
    case 19:
     $rand_value = "s";
    break;
    case 20:
     $rand_value = "t";
    break;
    case 21:
     $rand_value = "u";
    break;
    case 22:
     $rand_value = "v";
    break;
    case 23:
     $rand_value = "w";
    break;
    case 24:
     $rand_value = "x";
    break;
    case 25:
     $rand_value = "y";
    break;
    case 26:
     $rand_value = "z";
    break;
    case 27:
     $rand_value = "0";
    break;
    case 28:
     $rand_value = "1";
    break;
    case 29:
     $rand_value = "2";
    break;
    case 30:
     $rand_value = "3";
    break;
    case 31:
     $rand_value = "4";
    break;
    case 32:
     $rand_value = "5";
    break;
    case 33:
     $rand_value = "6";
    break;
    case 34:
     $rand_value = "7";
    break;
    case 35:
     $rand_value = "8";
    break;
    case 36:
     $rand_value = "9";
    break;
    case 37:
     $rand_value = "A";
	break;
    case 38:
     $rand_value = "B";
    break;
    case 39:
     $rand_value = "C";
    break; 
    case 40:
     $rand_value = "D";
    break;
    case 41:
     $rand_value = "E";
    break;
    case 42:
     $rand_value = "F";
    break;
    case 43:
     $rand_value = "G";
    break;
    case 44:
     $rand_value = "H";
    break;
    case 45:
     $rand_value = "I";
    break;
    case 46:
     $rand_value = "J";
    break;
    case 47:
     $rand_value = "K";
    break;
    case 48:
     $rand_value = "L";
    break;
    case 49:
     $rand_value = "M";
    break;
    case 50:
     $rand_value = "N";
    break;
    case 51:
     $rand_value = "O";
    break;
    case 52:
     $rand_value = "P";
    break;
    case 53:
     $rand_value = "Q";
    break;
    case 54:
     $rand_value = "R";
    break;
    case 55:
     $rand_value = "S";
    break;
    case 56:
     $rand_value = "T";
    break;
    case 57:
     $rand_value = "U";
    break;
    case 58:
     $rand_value = "V";
    break;
    case 59:
     $rand_value = "W";
    break;
    case 60:
     $rand_value = "X";
    break;
    case 61:
     $rand_value = "Y";
    break;
    case 62:
     $rand_value = "Z";
    break;
  }
return $rand_value;
}

function get_rand_id($length)
{
  if($length>0) 
  { 
  $rand_id="";

  mt_srand((double)microtime() * 1000000);  
   
   for($i=1; $i<=$length; $i++)
   {

   $num = mt_rand(1,62);
   $rand_id .= assign_rand_value($num);
   }
  }
return $rand_id;
}

function checkEmail($email) // http://ocaoimh.ie/validate-email-address-in-php/
{
  // checks proper syntax
  if( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
  {
    return false;
  }  
  
  else
  	return true;   
}

function jsonFailWithError($error)
{

	?>{
	
	"type": "failure",
	"error_message": "<?php echo $error; ?>"
	
}<?php
	// must die manually after, so @unlinking can be done

}

/*********************** PROCESS INSTALLATION ***********************/

if ($_SERVER['REQUEST_METHOD'] != 'POST')
{
	header("Location: index.php");
	die();
}

header("Content-Type: application/json; charset=UTF-8");

// SERVER-SIDE VALIDATION
if (!isset($_POST['configpath']) || empty($_POST['configpath']))
{
	jsonFailWithError("You must supply a Config Path.");
	die();
}

if (!isset($_POST['configurl']) || empty($_POST['configurl']))
{
	jsonFailWithError("You must supply a Config URL.");
	die();
}

if (!isset($_POST['sitepath']) || empty($_POST['sitepath']))
{
	jsonFailWithError("You must supply a Site Path.");
	die();
}

if (!isset($_POST['siteurl']) || empty($_POST['siteurl']))
{
	jsonFailWithError("You must supply a Site URL.");
	die();
}

if (!isset($_POST['username']) || empty($_POST['username']))
{
	jsonFailWithError("You must supply a username.");
	die();
}

if (!preg_match('/^([a-z0-9_\-])+$/', strtolower($_POST['username'])))
{
	jsonFailWithError('Usernames may only contain letters and numbers, underscores (_) and hyphens (-). All usernames are lowercase.');
	die();
}

if (strlen($_POST['username']) > 15)
{
	jsonFailWithError('Usernames must be less than 16 characters long.');
	die();
}

if (!isset($_POST['email']) || empty($_POST['email']))
{
	jsonFailWithError("You must supply an email address.");
	die();
}

if (!isset($_POST['userspath']) || empty($_POST['userspath']))
{	
	jsonFailWithError("You must supply a Users Path.");
	die();
}

if (!checkEmail($_POST['email']))
{
	jsonFailWithError("The email address you supplied isn't valid, or the email server can't be contacted.");
	die();
}

if (strlen($_POST['email']) > 63)
{
	jsonFailWithError('Usernames must be less than 64 characters long.');
	die();
}

if (!isset($_POST['password']) || empty($_POST['password']))
{
	jsonFailWithError("You must supply a password.");
	die();
}

if (!isset($_POST['password_retype']) || empty($_POST['password_retype']))
{
	jsonFailWithError("You must supply the password twice.");
	die();
}

if ($_POST['password'] != $_POST['password_retype'])
{
	jsonFailWithError("The two passwords that were entered were not the same. Please retype your password.");
	die();
}

$passedAllChecks = true;
foreach($checks as $key => $req)
{
	if ($key != 'mod_rewrite')
	{
	// loop through all checks, if any are false, we didn't pass
		if ($req != true)
		{
			$passedAllChecks = false;
		}
	}
}

if (!$passedAllChecks)
{
	jsonFailWithError("At the moment, your server does not meet the requirements for Amalia. Please see the documentation for technical assistance.");
	die();

}


// strip off trailers if they are present
rtrim($_POST['configpath'], '/');
rtrim($_POST['sitepath'], '/');

// check existences of local directories
if (!file_exists($_POST['configpath']))
{
	jsonFailWithError("The Config Path you entered does not exist on this server.");
	die();
}

if (!file_exists($_POST['sitepath']))
{
	jsonFailWithError("The Site Path you entered does not exist on this server.");
	die();
}

if (!file_exists($_POST['userspath']))
{
	jsonFailWithError("The Users Path you entered does not exist on this server.");
	die();
}


// check permissions on Config Path with a test file
$testFileSuffix = md5(time().microtime().rand(0,getrandmax()));
$testFileName = $_POST['configpath'].'/amalia_test_file_'.$testFileSuffix.'.txt';
$testFh = @fopen($testFileName, 'w');
if (!$testFh)
{
	jsonFailWithError("The installer is not able to write to the Config Path. Please check that the permissions on that folder give write access to the web server user '".exec('whoami')."'.");
	die();
}
if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
{
	@unlink($testFileName);	
	fclose($testFh);
	jsonFailWithError("The installer is not able to write to the Config Path. Please check that the permissions on that folder give write access to the web server user '".exec('whoami')."'. (Could open file, but not write data to it)");
}
else
{
	// 'tis all good, just delete the test file and move on
	@unlink($testFileName);
	fclose($testFh);
}

// check permissions on Site Path with a test file
$testFileSuffix = md5(time().microtime().rand(0,getrandmax()));
$testFileName = $_POST['sitepath'].'/amalia_test_file_'.$testFileSuffix.'.txt';
$testFh = @fopen($testFileName, 'w');
if (!$testFh)
{
	jsonFailWithError("The installer is not able to write to the Site Path. Please check that the permissions on that folder give write access to the web server user '".exec('whoami')."'.");
	die();
}
if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
{
	@unlink($testFileName);
	fclose($testFh);
	jsonFailWithError("The installer is not able to write to the Site Path. Please check that the permissions on that folder give write access to the web server user '".exec('whoami')."'. (Could open file, but not write data to it)");
}
else
{
	// 'tis all good, just delete the test file and move on
	@unlink($testFileName);		
	fclose($testFh);
}

// check permissions on Users Path with a test file
$testFileSuffix = md5(time().microtime().rand(0,getrandmax()));
$testFileName = $_POST['userspath'].'/amalia_test_file_'.$testFileSuffix.'.txt';
$testFh = @fopen($testFileName, 'w');
if (!$testFh)
{
	jsonFailWithError("The installer is not able to write to the Users Path. Please check that the permissions on that folder give write access to the web server user '".exec('whoami')."'.");
	die();
}
if (!@fwrite($testFh, base64_encode(rand(0,getrandmax() ) ) ) )
{
	@unlink($testFileName);
	fclose($testFh);
	jsonFailWithError("The installer is not able to write to the Users Path. Please check that the permissions on that folder give write access to the web server user '".exec('whoami')."'. (Could open file, but not write data to it)");
}
else
{
	// 'tis all good, just delete the test file and move on
	@unlink($testFileName);		
	fclose($testFh);
}


// set permissions on userfiles/thumbcache folder to ensure writability
@chmod($_POST['configpath'].'/userfiles/thumbcache', 0755);



// VALIDATION DONE

$salt = get_rand_id(64);


// decide upon hash algo
$availableHashes = hash_algos();
if (in_array('sha512', $availableHashes))
{
	$hashToUse = 'sha512';
}
else if (in_array('sha256', $availableHashes))
{
	$hashToUse = 'sha256';
}
else if (in_array('sha1', $availableHashes))
{
	$hashToUse = 'sha1';
}
else
{
	$hashToUse = 'md5';
}

// should we enable mod_rewrite in this install by default?
$enableRewrite = ($_POST['enablerewrite'] == 'true') ? 'true' : 'false';

if (isset($_SESSION['disable_mod_rewrite']) && ($_SESSION['disable_mod_rewrite'] == true))
{
	$enableRewrite = 'false';
}

// determine whether our htaccess file already has been written with the
// desired rules in place
$fh = @fopen($_POST['configpath'].'/.htaccess', 'r');

if (!$fh)
{
	$shouldWriteHtaccess = true;
}
else {

	$existingHtaccess = file_get_contents($_POST['configpath'].'/.htaccess');
	if (strpos($existingHtaccess, '#@alreadyHasAmaliaRules') !== false)
	{
		$shouldWriteHtaccess = false;
	}
	else {
		$shouldWriteHtaccess = true;
	}

	fclose($fh);
			
}


// Write an .htaccess file, which will facilitate mod_rewrite if that is enabled
$htaccessFile = <<<EOT
# htaccess rules generated by Amalia Installer on {genDate}
# START auto-generated by Amalia Installer

<Files git_revision_footer.php>
Order allow,deny
Deny from All
</Files>

<IfModule mod_rewrite.c>
RewriteEngine On

# Please leave the line below intact. If you ever re-install Amalia,
# the installer uses this to avoid duplicating these rewrite rules
# and causing problems.
#@alreadyHasAmaliaRules
#@amaliaVersion: {amaliaVersion}

RewriteBase {rewriteBase}
# should be the relative path to your Config URL

RewriteRule ^login$ index.php?action=login [QSA]
RewriteRule ^logout$ index.php?action=logout [QSA]
RewriteRule ^create$ index.php?action=create [QSA]
RewriteRule ^create-folder$ index.php?action=create-folder [QSA]
RewriteRule ^rename$ index.php?action=rename [QSA]
RewriteRule ^edit-title$ index.php?action=edit-title [QSA]
RewriteRule ^edit$ index.php?action=edit [QSA]
RewriteRule ^delete$ index.php?action=delete [QSA]
RewriteRule ^upload$ index.php?action=upload [QSA]
RewriteRule ^minibrowser$ index.php?action=minibrowser [QSA]
RewriteRule ^miniuploader$ index.php?action=miniuploader [QSA]
RewriteRule ^recyclebin$ index.php?action=recyclebin [QSA]
RewriteRule ^settings$ index.php?action=settings [QSA]
RewriteRule ^dir$ index.php?action=dir [QSA]

</IfModule>

# END auto-generated by Amalia Installer
EOT;

// determine proper rewriteBase command
$rewriteBase = rtrim(parse_url($_POST['configurl'], PHP_URL_PATH), '/');
$htaccessFile = str_replace('{rewriteBase}', $rewriteBase, $htaccessFile);

$htaccessFile = str_replace('{amaliaVersion}', AMALIA_VERSION, $htaccessFile);
$htaccessFile = str_replace('{genDate}', date('Y-m-d H:i'), $htaccessFile);

if ($enableRewrite == 'true' && $shouldWriteHtaccess)
{
	// go and write the file
	$fh = @fopen($_POST['configpath'].'/.htaccess', 'a'); // append to file in case of custom rules in .htaccess already there
	
	if (!$fh)
	{
		// if the file save failed, then disable mod_rewrite support, as it now wouldn't work properly
		// post-install
		$enableRewrite = 'false';
	}
	else {
		fwrite($fh, $htaccessFile);
		fclose($fh);
	}
}



// set up the amalia-config file template, we will substitute in the relevant data after

$amaliaConfig = <<<EOT
<?php

// Amalia Config File. Generated by the Amalia Installer on {genDate}

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


// This defines the allowed file types.
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
$_POST['sitepath'] = rtrim($_POST['sitepath'], '/');
$_POST['configpath'] = rtrim($_POST['configpath'], '/');
$_POST['configurl'] = rtrim($_POST['configurl'], '/');
$_POST['siteurl'] = rtrim($_POST['siteurl'], '/');
$_POST['userspath'] = rtrim($_POST['userspath'], '/');

// substitute in variables
$amaliaConfig = str_replace('%', '$', $amaliaConfig);
$amaliaConfig = str_replace('{genDate}', date('Y-m-d H:i'), $amaliaConfig);
$amaliaConfig = str_replace('{hashToUse}', $hashToUse, $amaliaConfig);
$amaliaConfig = str_replace('{WRITE_DEBUG_TO_CONFIG}', WRITE_DEBUG_TO_CONFIG, $amaliaConfig);
$amaliaConfig = str_replace('{salt}', $salt, $amaliaConfig);
$amaliaConfig = str_replace('{sitePath}', safe_plain($_POST['sitepath']), $amaliaConfig);
$amaliaConfig = str_replace('{configPath}', safe_plain($_POST['configpath']), $amaliaConfig);
$amaliaConfig = str_replace('{configURL}', safe_plain($_POST['configurl']), $amaliaConfig);
$amaliaConfig = str_replace('{siteURL}', safe_plain($_POST['siteurl']), $amaliaConfig);
$amaliaConfig = str_replace('{usersPath}', safe_plain($_POST['userspath']), $amaliaConfig);
$amaliaConfig = str_replace('{version}', safe_plain(AMALIA_VERSION), $amaliaConfig);

// default themes path is assumed to be sitepath/.themes.
// Not changed by installer, must use config tool later
$themespath = safe_plain(rtrim($_POST['sitepath'], '/').'/.themes');
$amaliaConfig = str_replace('{themesPath}', $themespath, $amaliaConfig);

$themesurl = safe_plain(rtrim($_POST['siteurl'], '/').'/.themes');
$amaliaConfig = str_replace('{themesURL}', $themesurl, $amaliaConfig);

// $enableRewrite is up above the htaccess writing stuff
// needed there for clever htaccess file writing stuff

$amaliaConfig = str_replace('{enableRewrite}', $enableRewrite, $amaliaConfig);

// finish substituting in variables into config file, ready to write


// write amalia config file
$amaliaConfigFileName = safe_plain($_POST['configpath']).'/amalia-config.php';

if (file_exists($amaliaConfigFileName))
{
	jsonFailWithError("The amalia-config.php file already exists in your Config Path. To prevent accidentally losing an existing configuration, you will need to delete this file manually in order to proceed with the install.");
	die();
}

$fh = @fopen($amaliaConfigFileName, 'w');
if (!$fh)
{
	jsonFailWithError("Unable to create the amalia-config.php file, but the earlier permissions checks succeeded.");
	die();
}

fwrite($fh, $amaliaConfig);
fclose($fh);

// finished writing config file


// set up a users file with our first admin user

$usersFile = <<<EOT
# Amalia Users File
#
#
# Please do not edit this file by hand. Instead, log
# in to Amalia and use the User Management tool.
#
#
#id;username;hashed password;email;name
0;{username};{hashed_password};{email};_AMALIA_DEFAULT_FNAME
EOT;

$usersFile = str_replace('{username}', strtolower(safe_usersline($_POST['username'])), $usersFile);
$usersFile = str_replace('{hashed_password}', hash($hashToUse, $salt.$_POST['password']), $usersFile);
$usersFile = str_replace('{email}', safe_usersline($_POST['email']), $usersFile);


// already existing check
if (file_exists($_POST['userspath'].'/amalia-users.txt') || file_exists($_POST['userspath'].'/amalia-users-permissions.txt'))
{
	jsonFailWithError("The amalia-users.txt and/or amalia-users-permissions.txt file already exists in your Users Path. To prevent accidentally losing an existing configuration, you will need to delete these files manually in order to proceed with the install.");
	@unlink($amaliaConfigFileName); // delete the config file we just wrote so next install attempt will succeed.
	die();
}


$fh = @fopen($_POST['userspath'].'/amalia-users.txt', 'w');

if (!$fh)
{
	jsonFailWithError("Unable to create the amalia-users.txt file, but the earlier permissions checks succeeded.");
	@unlink($amaliaConfigFileName); // delete the config file we just wrote so next install attempt will succeed.
	die();
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
0;create-pages:1;edit-pages:1;delete-files:1;upload-files:1;rename-files:1;manage-plugins:1;manage-users:1;configure-amalia:1;
EOT;

$fh = @fopen($_POST['userspath'].'/amalia-users-permissions.txt', 'w');

if (!$fh)
{
	jsonFailWithError("Unable to create the amalia-users-permissions.txt file, but the earlier permissions checks succeeded.");
	@unlink($amaliaConfigFileName); // delete the config file we just wrote so next install attempt will succeed.
	@unlink($_POST['userspath'].'/amalia-users.txt'); // and the users file
	die();
}

fwrite($fh, $permissionsFile);
fclose($fh);


// Write a default theme file, so that the editor will have something basic to play with

$defaultThemeFile = <<<EOT
<html>
<head>
<title><?php echo %amalia_title; ?></title>
<meta name="keywords" content="<?php echo %amalia_keywords; ?>" />
<meta name="description" content="<?php echo %amalia_description; ?>" />
</head>
<body>
<?php echo %amalia_content; ?>
</body>
</html>
EOT;

$defaultThemeFile = str_replace('%', '$', $defaultThemeFile);

if (!file_exists($_POST['sitepath'].'/.themes'))
{
	// make .themes directory
	if (!@mkdir($_POST['sitepath'].'/.themes', 0755))  //TODO: check default permissions here?
	{
		jsonFailWithError("Unable to create the default themes directory, but the earlier permissions check on Site Path succeeded.");
		@unlink($amaliaConfigFileName); // delete the config file we just wrote so next install attempt will succeed.
		@unlink($_POST['userspath'].'/amalia-users.txt'); // and the users file
		@unlink($_POST['userspath'].'/amalia-users-permissions.txt'); // and the users permissions file
		die();
	}	

}

// already existing check
/* I don't think we need this -- if we can assume .themes/default.php will never include anything important
   that can't be replaced by this installer
if (file_exists($_POST['sitepath'].'/.themes/default.php'))
{
	jsonFailWithError("The .themes/default.php file already exists in your Site Path. To prevent accidentally losing an existing configuration, you will need to delete this file manually in order to proceed with the install.");
	@unlink($amaliaConfigFileName); // delete the config file we just wrote so next install attempt will succeed.
	@unlink($_POST['userspath'].'/amalia-users.txt'); // and the users file
	@unlink($_POST['userspath'].'/amalia-users-permissions.txt'); // and the users permissions file
	die();
}
*/

$fh = @fopen($_POST['sitepath'].'/.themes/default.php', 'w');

if (!$fh)
{
	jsonFailWithError("Unable to create the default themes file, but the earlier permissions check on Site Path succeeded.");
	@unlink($amaliaConfigFileName); // delete the config file we just wrote so next install attempt will succeed.
	@unlink($_POST['userspath'].'/amalia-users.txt'); // and the users file
	@unlink($_POST['userspath'].'/amalia-users-permissions.txt'); // and the users permissions file
	// don't need to trash themes 
	die();

}

fwrite($fh, $defaultThemeFile);
fclose($fh);




?>{
	
	"type": "success",
	"message": "All files written successfully. Amalia should be ready to run."
	
}<?php


?>