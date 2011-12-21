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
 * Index Loader
 * Sets up Amalia Core, then launches the Template Loader
 * Do not actually do anything other than setting stuff up
 * in this file.
 *
 * @package	Amalia2
  * @category	Setup
 * @author		Amalia Dev Team
 */

// hack for Flash uploader authentication issue
if (!isset($_POST['amalia_session_override']) || empty($_POST['amalia_session_override']))
{
	// session_name sets a cookie name other than 'PHPSESSID', linked to the current server.
	// just to avoid using defaults
	ini_set("session.cookie_httponly", 1);
	session_name('Amalia-'.md5($_SERVER['SERVER_NAME']));
	session_start();
}


define('IN_AMALIA','true');
error_reporting(E_ALL ^ E_NOTICE); //set to "0" before putting into production


if(!file_exists('amalia-config.php')) {
	header('Location: installer/');
	die();
} else {
	
	// do the includes
	
	require('amalia-config.php');
	
	$friendly_errors = array();
	
	require('includes/common.php');	
	execStartTime(); // performance reporting
	
	require('includes/auth.inc.php');
	$auth = new amalia_auth();
 
	require("plugins/plugins.inc.php");
	instantiate_enabled_plugins();
	
	require('includes/router.php');
	$route = new amalia_router();
	$route->load_controller();
	$load = $route;


}


?>