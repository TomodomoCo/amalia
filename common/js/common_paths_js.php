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
 * Common JavaScript Paths
 *
 * Gives static JS files access to print_internal_link, config url,
 * site url and so on.
 *
 * @package	Amalia2
 * @category	Amalia Generation
 * @author		Amalia Dev Team
 */

define('IN_AMALIA', true);
require('../../amalia-config.php');


header('Content-Type: text/javascript; charset=UTF-8');

// change caching characteristics to speed up page load

/* NOTE: this may cause occasional issues where preety URLs are not enforced on some JS pageloads
if the user changes this setting post-install. It will not break anything, unless the user switched
on pretty URLs but has a broken .htaccess. In that case, the caching may cause the breakage to persist.
However, if that happens, it will be broken anyway and need fixing manually.

In all other cases, the worst this caching can do is sometimes push the old URLs out via JS calls.

*/

if (!isset($_SESSION['mod_rewrite_haschanged']) || !isset($_SESSION['debug_haschanged']))
{
	$expires = ini_get('session.gc_maxlifetime');
	header('Pragma: public');
	header('Cache-Control: maxage='.$expires);
	header('Expires: '.gmdate('D, d M Y H:i:s', time()+$expires) .' GMT');
}

else {

	header('Pragma: no-cache');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: '.gmdate('D, d M Y H:i:s', 0) .' GMT');
	unset($_SESSION['mod_rewrite_haschanged']);
	unset($_SESSION['debug_haschanged']);
}


?>
/*
Amalia. A content management system "for the rest of us".

Copyright (C) 2007-2011 Chris Van Patten, Nick Sampsell and Peter Upfold. 

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the names of the authors or copyright holders shall not be used in advertising or otherwise to promote the sale, use or other dealings in this Software without prior written authorization from the the authors or copyright holders.
*/

// Sets various paths that may be needed by other JavaScript files

var CONFIG_URL = '<?php echo $config['config_url'];?>';
var SITE_URL = '<?php echo $config['site_url'];?>';
var DEBUG = <?php echo (DEBUG) ? 'true' : 'false'; ?>;
var THEMES_URL = '<?php echo $config['themes_url'];?>';

function js_internal_link(page, args) {

	var link;
	
	// if args is unset, use default of false
	args = typeof(args) != 'undefined' ? args : false;
	
	<?php
	
	if ($config['mod_rewrite'])
	{
	?>link = CONFIG_URL + '/' + page;
	
	if (args != false)
	{
		link = link + '?' + args;
	}
	
	return link;
	
	<?php
	}
	else {
	?>
	link = CONFIG_URL + '/?action=' + page;
	
	if (args != false)
	{
		link = link + '&' + args;
	}
	
	return link;
	
	<?php
	}
	
	?>

}


