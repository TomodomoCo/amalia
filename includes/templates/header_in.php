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

if (!defined('IN_AMALIA'))
{
	// template not to be viewed outside Amalia
	header('HTTP/1.0 403 Forbidden');
	die('Forbidden');
}

define('USE_MINIATURE_JS', false); // switch to false to go back to the development JS files
// THIS FEATURE IS NOT AUTOMATICALLY SUPPORTED AT THE MOMENT.
// IT MIGHT NOT WORK AT ALL. You were warned. Leave it 'false'.

// sets body CSS class based on current action/page type
switch ($_GET['action'])
{

	case 'dir':
		$bodyClass = 'browser';
	break;
	
	case 'edit':
		$bodyClass = 'editor';
	break;
	
	case 'recyclebin':
		$bodyClass = 'recyclebin';
	break;
		
	case 'settings':
		$bodyClass = 'settings';
	break;
		
	default:
		$bodyClass = 'browser';		
	break;

}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" type="text/css" href="<?php echo $config['config_url'];?>/common/css/style.css" />
		
		<meta name="robots" content="noindex,noarchive" />
		
		<!-- global paths for static JS -->
		<script src="<?php echo $config['config_url'];?>/common/js/common_paths_js.php<?php
			// prevent stale caching of the old JS configuration if the user has changed debug mode
			
			if (isset($_SESSION['mod_rewrite_haschanged']) || isset($_SESSION['debug_haschanged']) || isset($_SESSION['cpj_time']))
			{
				
				if (!isset($_SESSION['cpj_time']))
				{
					$_SESSION['cpj_time'] = time();
				}
			
				?>?reload=<?php echo safe_plain($_SESSION['cpj_time']);
				
				$_SESSION['mod_rewrite_haschanged'] = false;
				$_SESSION['debug_haschanged'] = false;
				
			}
			
			?>" type="text/javascript"></script>
		
		<!-- jQuery -->
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/jquery-1.4.2.min.js"></script>
		
		<!-- scripts for file uploader -->
			<script src="<?php echo $config['config_url'];?>/common/js/swfobject/swfobject.js" type="text/javascript"></script>		
			<script src="<?php echo $config['config_url'];?>/common/js/uploadify/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
			<script src="<?php echo $config['config_url'];?>/common/js/upload.js" type="text/javascript"></script>
			<script type="text/javascript">
				<?php
				
				if (DEBUG) {
				?>
	
				//DEBUG ONLY
				function dump(arr,level) {
					var dumped_text = "";
					if(!level) level = 0;
					
					//The padding given at the beginning of the line.
					var level_padding = "";
					for(var j=0;j<level+1;j++) level_padding += "    ";
					
					if(typeof(arr) == 'object') { //Array/Hashes/Objects 
						for(var item in arr) {
							var value = arr[item];
							
							if(typeof(value) == 'object') { //If it is an array,
								dumped_text += level_padding + "'" + item + "' ...\n";
								dumped_text += dump(value,level+1);
							} else {
								dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
							}
						}
					} else { //Stings/Chars/Numbers etc.
						dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
					}
					return dumped_text;
				}
				// END DEBUG ONLY
				<?php }
				?>
			var session_id_override = '<?php echo urlencode(session_id());?>';
			var dir = '<?php echo rtrim(safe_filename($dir), '/');?>';
			</script>
			<!-- end file uploader -->
		
		<?php
		
		if (USE_MINIATURE_JS) {
		
		?><!-- All other JS files (minified) -->
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/minieverything.js"></script><?php
		
					if ($bodyClass == 'editor')
					{
						// any editor-specific JS includes
						
						?>
						
		<!-- TinyMCE only -->
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/tiny_mce/tiny_mce.js"></script><?php
					
					}
		
		}
		
		else { // not using mini JS
		
		?><script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/jq-color.js"></script>
		
		<!-- universal JS -->
		<script src="<?php echo $config['config_url'];?>/common/js/js.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/jScrollPane-1.2.3.min.js"></script>
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/jquery.mousewheel.js"></script>
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/jquery.DOMWindow.js"></script>
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/jquery.editinplace.js"></script>
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/templatedSelect.js"></script>
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/edit.js"></script>

		<?php
		if ($bodyClass == 'browser')
		{
			// any file browser-specific JS includes

			?>
			<!-- file rename in thickbox-->
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/rename.js"></script>
		
		<?php
			
		}
		
		if ($bodyClass == 'editor')
		{
			// any editor-specific JS includes
			
			?><!-- TinyMCE -->
		<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/tiny_mce/tiny_mce.js"></script>
			
		<?php
		
		}
		
		if ($bodyClass == 'settings' && $_GET['do'] == 'manage-plugins')
		{
			?><!-- plugin management -->
			<script type="text/javascript" src="<?php echo $config['config_url'];?>/common/js/manage_plugins.js"></script><?php
		}
		
		} // end else not using miniature JS
		
		?>
		
		<title><?php echo defined('AMALIA_PAGETITLE') ? safe_plain(AMALIA_PAGETITLE) . ' &#8212; ' : ''; ?>Amalia</title>
		
	</head>
	<body class="<?php echo $bodyClass; ?>">
	<div id="white"></div>
	<div id="wrap">
		<div id="bookshelf">
			<div id="l-end"></div>
			<div id="books">
				<h1 id="h1"><a href="#" id="menu-button">Amalia</a></h1>
				<div id="menu">
					<div id="menu-inner">
						<div id="scroll-outer">
							<div id="scroll-inner" class="scroll">
								<ul id="menu-list">

									<li id="file-browser"><a href="<?php echo print_internal_link('dir'); ?>">File Browser <em>View and edit your webpages.</em></a></li>
									<?php $hk_header_is_showing_navbar = new Hook('header_is_showing_navbar', false);?>

								</ul>
							</div>
						</div>
						<!--<div id="menu-scroll">
							<div id="menu-scroll-slider"></div>
						</div>-->
						<ul id="global-options">
							<li id="setting"><a href="<?php echo print_internal_link('settings'); ?>">Settings</a></li>

							<li id="signout"><a href="<?php echo print_internal_link('logout'); ?>">Sign out</a></li>
						</ul>
					</div>
				</div>
				
				<?php
				if ($_SESSION['amalia_auth']['f_name'] == '_AMALIA_DEFAULT_FNAME' || empty($_SESSION['amalia_auth']['f_name']))
				{
				?>
				<form id="hello-edit">
					<span id="hello">Hello! <input type="text" name="fname" value="What should we call you?" /> <input type="submit" value="Save" /> (<a href="<?php echo print_internal_link('logout');?>">Sign out</a>)</span>
				</form>
				<?php
				}
				else {
				// I need the <form> (or a class tacked on to the <span>) if it's showing editor. Stupid CSS form inconsistency.
				?>
				
				<span id="hello">Hello, <strong><?php echo safe_plain($_SESSION['amalia_auth']['f_name']); ?></strong>! (<a href="<?php echo print_internal_link('logout');?>">Sign out</a>)</span>
				<?php
				} // end else
				?>
			</div>
			<div id="r-end"></div>
		</div>
		<div id="fill">
			<h1 id="loc"><?php echo defined('AMALIA_PAGETITLE') ? AMALIA_PAGETITLE : 'Amalia'; ?></h1>
		</div>
		<div id="content">
			<div id="container">
				<div id="inner">

					<noscript><p><strong style="color:#ff0000;">Hold on!</strong> You need JavaScript enabled in your browser
					in order to use Amalia properly. Please enable JavaScript in your browser or set exceptions for
					<em>http://<?php echo safe_plain($_SERVER['SERVER_NAME']);?>/</em>
					and for <em>http://ajax.googleapis.com/</em>.</p></noscript>

					<div id="error"<?php echo (count($friendly_errors) > 0) ? '': ' style="display:none"'; ?>>
					<?php
					if (is_array($friendly_errors) && count($friendly_errors) > 0)
					{
						foreach($friendly_errors as $error)
						{
							echo '<strong>'.safe_plain($error[0]).' Error:</strong> '.safe_plain($error[1]).'<br /><br />';						
						}
					}
					?>
					<input type="button" value="Ignore" onclick="$('#error').hide();" style="font-size:16pt;" />
					</div>