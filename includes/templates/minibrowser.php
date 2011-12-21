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

Any data that you pull out of the array $files must be assumed to be bad and re-safed
before display to the user.

$desiredFileType contains either 'image' or 'page' (sent via GET['filetype']), and is used
to determine which type of files we want the minibrowser to show to the user.

$desiredFileType is NOT SAFE and shouldn't even be printed out directly to the output.

*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="robots" content="noindex,noarchive,nofollow" />
	<script type="text/javascript" src="<?php echo $config['config_url']; ?>/common/js/tiny_mce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="<?php echo $config['config_url']; ?>/common/js/minibrowser.js"></script>
	<title>Amalia Mini Browser</title>
</head>

<body>

	<h1>Mini Browser</h1>
	
	<p>Browsing for <?php echo ($desiredFiletype == 'page') ? 'pages to link to' : 'images';?>.</p>

	<p>Currently in <strong><?php echo !empty($dir) ? '/'.ltrim(safe_filename($dir), '/') : '/'; ?></strong></p>
	
	<ul id="filebrowser">
	<?php
	
		//if possible, create the "move up a directory link

		// get link to parent directory
		if (!empty($dir)) {
			// explode dir into array
			$dirPaths = explode('/', '/'.$dir.'/');
			
			// slice off last one
			$dirPaths = array_slice($dirPaths, 0, count($arrayPaths)-2);
			
			// recombine
			$dirlink = implode('/', $dirPaths);
		
		}
		
		// if this is not the top directory, offer a 'go up a folder' link
		if (!empty($dir) && count($dirPaths) > 0) {
		?>
			<li>
				<a href="<?php echo print_internal_link('minibrowser', 'dir='.urlencode($dirlink).'&filetype='.safe_filename($desiredFiletype),'','true'); ?>">
					Up a folder
				</a>		
				</div>
			</li>
		<?php
		}
	
		// loop through directories first
		if (is_array($files) && count($files) > 0)
		{
			foreach($files as $loop => $file)
			{
				if ($file['type'] == 'dir')
				{
					?><li>
						<a href="<?php print_internal_link('minibrowser', 'dir='.urlencode($dir.'/'.safe_filename($file['name'])).'&filetype='.safe_filename($desiredFiletype));?>">
							<?php 
							// show file name / file title
							
							if (empty($file['title']))
							{
								echo safe_plain($file['name']);	
							}
							else {
								echo safe_plain($file['title']);
							}
							?>
						</a>
					</li>
					<?php
				}
			}
		}
		
		// loop through images
		if (is_array($files) && count($files) > 0)
		{
		
			foreach($files as $loop => $file)
			{
			
				$dir = rtrim($dir, '/');
				if (!empty($dir))
				{
					$dir .= '/'; // standardise slashes for avoiding double-slash in link
				}
			
				$file['name'] = rtrim(ltrim($file['name'], '/'), '/'); // trim off extra slashes
			
				if ($desiredFiletype == 'image' && $file['type'] == 'image')
				{
					
					// get file URL
					
					$fileURL = safe_filename($config['site_url'].'/'.$dir.$file['name']);
					
					$thumbnail = safe_filename($file['thumbnail']);
				
					?><li>
						<a href="javascript:;" onclick="insertSelectedImage('<?php echo $fileURL;?>', '<?php echo safe_plain($file['title']);?>');">
							<img src="<?php echo $thumbnail;?>" width="24" height="17" alt="Thumbnail for <?php echo safe_plain($file['name']);?>" />
							<?php 
								// show file name / file title
								
								if (empty($file['title']))
								{
									echo safe_plain($file['name']);	
								}
								else {
									echo safe_plain($file['title']);
								}
							
							?>
						</a>
					</li>
					<?php		
				
				}
				
				else if ($desiredFiletype == 'page' && $file['type'] == 'dynamic_file')
				{
					
					// get file URL
					
					$fileURL = safe_filename($config['site_url'].'/'.$dir.$file['name']);
				
					?><li>
						<a href="javascript:;" onclick="insertSelectedLink('<?php echo $fileURL;?>', '<?php echo safe_plain($file['title']);?>');">
							<?php 
								// show file name / file title
								
								if (empty($file['title']))
								{
									echo safe_plain($file['name']);	
								}
								else {
									echo safe_plain($file['title']);
								}
							
							?>
						</a>
					</li>
					<?php
				
				}
			
			}
	
		}	
	?>
	
	</ul>
	
	<!--<button name="done-button" onclick="javascript:window.close();">Done</button>-->

</body>
</html>
