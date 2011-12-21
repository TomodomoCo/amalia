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

/* Security Notes 

$dir has been safe_filename()'d before displaying this, so can be assumed to be safe.

*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="robots" content="noindex,noarchive,nofollow" />
	<script type="text/javascript" src="<?php echo $config['config_url']; ?>/common/js/tiny_mce/tiny_mce_popup.js"></script>

	<title>Amalia Mini Uploader</title>
</head>

<body>

	<h1>Mini Uploader</h1>
	
	<?php
	
	if (!isset($uploadCompleted))
	
	{
	?>	
	
	<p>Upload an image from your computer to the <strong><?php echo !empty($dir) ? '/'.ltrim(safe_filename($dir), '/') : '/'; ?></strong> directory.</p>
	
	<form name="miniuploaderform" method="post" action="<?php print_internal_link('miniuploader', 'dir='.safe_filename($dir));?>" enctype="multipart/form-data">
		<p>File: <input type="file" name="upload" /></p>
		<p>Image title: <input type="text" name="title" value="" /></p>
		<p><input type="submit" name="submit" value="Upload and insert" /></p>
	</form>
	
	<?php
	}
	
	else if ($uploadCompleted == true) {
	
		?>
		
		<!-- horrible hack for TinyMCE double-encoding issue -->
		<span style="display:none" id="imageTitleString"><?php echo safe_plain($newTitle);?></span>
		<!-- end horrible hack -->
		
		<script type="text/javascript">
		window.onload = function() {
			tinyMCEPopup.execCommand('mceInsertContent', false, tinyMCEPopup.dom.createHTML('img', {
					src : '<?php echo safe_filename($newFileURL);?>',
					alt : document.getElementById('imageTitleString').innerHTML,
					title : document.getElementById('imageTitleString').innerHTML,
					border : 0
			}));

			window.setTimeout(function () {tinyMCEPopup.close();}, 100); // to fix Safari crash if you do it right away
			
		};
		</script>
		
		<h2>Upload complete.</h2>
		
		<?php
	
	
	}
	
	?>

</body>

</html>