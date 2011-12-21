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
?>
<ul id="filebrowser">
<?php

//if possible, create the "move up a directory link

$dir = ltrim($dir, '/');
$dir = '/'.$dir;
$dir2 = rtrim($dir,'/');
$dir2 = explode('/',$dir2);


$dirlink = '';

// get link to parent directory
if ($dir != '/' && count($dir2) != 1) {
	// explode dir into array
	$dirPaths = explode('/', $dir);
	
	// slice off last one
	$dirPaths = array_slice($dirPaths, 0, count($arrayPaths)-2);
	
	// recombine
	$dirlink = implode('/', $dirPaths);

}

?>
<script type="text/javascript">
var thisDir = '<?php echo safe_filename($_GET['dir']);?>';
</script>
<?php

if($dir != '/' && count($dir2) != 1) {
?>
	<li class="alt1 up-level">
		<div class="filemeta">
			<div class="filetype"><a href="<?php echo print_internal_link('dir', 'dir='.urlencode($dirlink).'','','true'); ?>" class="view">View</a></div> <span class="filetitle">Up a folder</span>

		</div>
	</li>
<?php
}

?>
	<li class="alt1 folder create" id="create-folder">
		<?php
		if (isset($msg) && !empty($msg))
		{
			?><div id="message"><?php echo safe_plain($msg);?></div><?php
		}
		?>
		<form name="create_form" id="create_form" method="post" action="<?php print_internal_link('create-folder');?>">
			<!--formsec-->
			<?php $tmt = time().'_'.microtime().rand(0,getrandmax()); ?>
			<input type="hidden" name="formsec" value="<?php echo hash(HASH_ALGO, $tmt.$config['salt']);?>" />
			<input type="hidden" name="stm" value="<?php echo base64_encode($tmt);?>" />
			<input type="hidden" name="dir" value="<?php echo safe_filename($dir);?>" />
			<div class="filemeta">
				<div class="filetype"></div>			
				<span class="filetitle"><input type="text" name="folder_name" id="folder_name" value="Folder name...<?php echo safe_plain($folder_name);?>" /></span>
			</div>
			<button type="submit" name="submit" id="submit" value="submit" style="float: right; margin: -10px 5px 0 0"><div class="button-side"></div>Create</button>
		</form>
	</li>
<?php


if (is_array($result) && count($result) > 0)
{
	foreach($result as $dir3)
	{
		if($dir3['type'] == 'dir')
		{
		?>
		<li class="alt1 folder">
			<div class="filemeta">
				<div class="filetype"><a href="<?php print_internal_link('dir', 'dir='.urlencode(safe_filename($dir.$dir3['name'])));?>" class="view">View</a></div>
				<span class="filetitle iseditable" id="<?php echo safe_plain($dir.$dir3['name']); ?>"><?php echo safe_plain($dir3['title']); ?></span> 
				
				<?php
				// display folder name only to users where rename files is enabled, or where there is no folder title
				if (empty($dir3['title']) || $_SESSION['amalia_auth']['permissions']['rename-files']) {
					?>		
					<em id="filename-<?php echo md5($dir.$dir3['name']);?>" class="filename"><?php echo safe_plain($dir3['name']); ?></em>
					<?php
				
				}
				
				?>

			</div>
			<ul class="ftools">
				<?php
				if($_SESSION['amalia_auth']['permissions']['delete-files'] === '1') {
				?>
				<form id="delete-file-form-<?php echo md5($dir.$dir3['name']);?>" style="display:inline" method="post" action="<?php echo print_internal_link('delete', 'file='.urlencode(safe_filename($dir.$dir3['name'])));?>">
					<li class="delete closed-delete">
						<div class="holder ftool">
							Recycle
							<div class="confirmation">Are you sure you want to recycle this folder? <a href="javascript:void();" onclick="if (confirm('This entire folder will be sent to the Recycle Bin. If you later want to retrieve a single file from it, you must restore the whole folder back to its original location.\n\nAre you still sure you want to delete this folder?')) { $('#delete-file-form-<?php echo md5($dir.$dir3['name']);?>').submit(); }">Yes</a> or <a href="javascript:void()">No</a></div>
						</div>
					</li>
				</form>
				<?php
				}
				?>
			</ul>
		</li>

		<?php
		}
	}
	?>
	<li class="separator"></li>
	<li class="alt1 file upload create" id="upload-file">
		<div class="filetype"></div>
		<div class="filemeta">
			<noscript>
				<h1>Please use the <a href="<?php echo print_internal_link('upload', 'type=simple&dir='.urlencode(safe_filename($dir)));?>">simple uploader</a>.</h1>
			</noscript>
			
			<div id="message" style="color:#ff0000;"><?php if (!empty($msg)) { echo $msg; } ?></div>
			<div id="file-upload-info" style="padding:0px; margin:0px; font-weight:bold"><ul></ul></div>
			<div>
				<input type="file" id="file-upload-field" name="file" value="" />
			</div>
			<div style="clear: both;"></div>
		</div>
		<button type="submit" id="file-upload-submit" name="submit" value="Upload" onclick="$('#file-upload-field').uploadifyUpload();" style="float: right; margin: -10px 5px 0 0"><div class="button-side"></div>Upload</button>
		<div style="clear: both;"></div>
	</li>
	<?php
}

// there was an error, print it:
if (!empty($message))
{
	?><div id="error"><?php echo safe_plain($message);?></div><?php	
}
// ******** CREATE FILE FORM ***********
?>
<li class="alt1 file php create" id="create-file">
	<script type="text/javascript">
		$(document).ready(function(){
			
			function filterNum(str) {
				  re = /[^\w\-]+/g;
				  return str.replace(re, "");
			}
		
			$("#file_title").keyup(function() {
			
				var theTitle = $(this).val();
				
				theTitle = theTitle.split(' ').join('_').toLowerCase();
				theTitle = filterNum(theTitle);
				
				$('#filename').val(theTitle+'.php');
			
			});
			
		});
	</script>
	<div class="filetype"></div>
	<form action="<?php echo print_internal_link('create','dir='.urlencode(safe_plain($dir)));?>" method="post">
		<div class="filemeta">
			<input type="text" name="title" id="file_title" value="Page name...<?php echo safe_plain($reset_title); ?>" />
			<?php
			// display file name only to users where rename files is enabled, or where there is no file title
			if ($_SESSION['amalia_auth']['permissions']['rename-files'])
			{
			?>
			<!-- <em class="filename"><input type="text" name="filename" id="filename" value="file_name.php<?php echo safe_plain($reset_filename); ?>" /></em> -->
			<?php
			}
			?>
		</div>
		<button type="submit" name="submit" id="submit" value="submit" style="float: right; margin: -10px 5px 0 0"><div class="button-side"></div>Create</button>
		<div id="type" style="width: 220px; float: right; margin: -10px 11px 0 0;">
			<div id="theme-drop" style="margin: 0">
				<label class="label" for="theme" style="width: 0px !important; padding: 10px 0 6px 5px !important"></label>
				<input type="hidden" id="theme" name="theme" value="" />
				<div class="input" style="margin: 0 0 0 5px !important">
					<div class="input-l"></div>
					<div class="dropdown-m">
						<ul class="select" style="display: none"> <!-- selected -->
							<li>Default page</li>
							<li>Image gallery</li>
							<li>Resum&eacute;</li>
						</ul>
						<noscript>
							<select name="template" id="template" class="select">
							</select>
						</noscript>
					</div>
					<a class="dropdown-r" href="javascript::void()" style="display: none"></a>
					<div class="input-r"></div>
				</div>
			</div>
		</div>
	</form>
</li>

<?php

$i = 0;
if (is_array($result) && count($result) > 0)
{
	foreach($result as $file) {

		if($i % 2) {
			$alt_link = 'alt2';
		} else {
			$alt_link =  'alt1';
		}
		if($file['type'] != 'dir')
		{
		if($_GET['dir'] == '/')
		{
			$dir = '/';
		}
		
		
		// set the filetype for the li class, so the correct icon comes up
		switch ($file['type'])
		{
		
			case 'dynamic_file':
				$filetypeClass = 'php';
			break;

			case 'image':
				$filetypeClass = 'image';
			break;
			
			case 'video':
				$filetypeClass = 'video';
			break;
			
			case 'audio':
				$filetypeClass = 'audio';
			break;
			
			default:
				$filetypeClass = 'php';
			break;
		
		}	
	?>
	<li class="<?php echo $alt_link; ?> file <?php echo $filetypeClass;?>" style="background-image: url(<?php 
		if ($filetypeClass == 'image' && $config['show_thumbnails'])
		{		
			// Change width/height vars on editor.class.php:274
			 echo safe_filename($file['thumbnail']);
			 
		} ?>);">
		<div class="filetype">
			<a href="<?php echo safe_filename($config['site_url'].$dir.$file['name']); ?>" class="view">View</a>
		</div>
		<div class="filemeta">
		
		<?php
		//if(!empty($file['title']))
		//{
		?>
			<span class="filetitle iseditable" id="<?php echo safe_plain($dir.$file['name']); ?>"><?php echo safe_plain($file['title']); ?></span> 
		<?php
		//}
		?>
		
		<?php
		// display file name only to users where rename files is enabled, or where there is no file title
		if (empty($file['title']) || $_SESSION['amalia_auth']['permissions']['rename-files']) {
			?>		
			<em id="filename-<?php echo md5($dir.$file['name']);?>" class="filename"><?php echo safe_plain($file['name']); ?></em>
			<?php
		
		}
		
		?>
		
		</div>

		<ul class="ftools">
			<?php
			if($_SESSION['amalia_auth']['permissions']['delete-files'] === '1') {
			?>
			<form id="delete-file-form-<?php echo md5($dir.$file['name']);?>" style="display:inline" method="post" action="<?php echo print_internal_link('delete', 'file='.urlencode(safe_plain($dir.$file['name'])));?>">
				<li class="delete closed-delete" title="Delete">
					<div class="holder ftool">
						Recycle
						<div class="confirmation">Are you sure you want to recycle this file? <a href="javascript:void();" onclick="$('#delete-file-form-<?php echo md5($dir.$file['name']);?>').submit();">Yes</a> or <a href="javascript:void()">No</a></div>
					</div>
				</li>
			</form>

			<?php
			}
			?>
			<?php
			
			if($_SESSION['amalia_auth']['permissions']['edit-pages'] === '1' && $file['type'] == 'dynamic_file') {
			?>
				<li class="edit">
					<a href="<?php echo print_internal_link('edit', 'file='.urlencode(safe_filename($dir.$file['name'])));?>" title="Edit" class="ftool">Edit</a>
				</li>
			<?php
			}
			
			if ($_SESSION['amalia_auth']['permissions']['rename-files'] === '1') {
			?>
				<li class="rename">
					<a href="#rename-form" title="Rename" class="rename-box ftool" onclick="populateRenameForm('<?php echo safe_filename($dir.$file['name']);?>', '<?php echo safe_filename($file['name']);?>');">Rename</a>
				</li>
			<?php
			}
			?>
		</ul>
	</li>
							<?php

		$i++;
		}
	} //end cycle through the $result array
	
	
}
else
{

}
?>
</ul>

<div id="rename-form" style="display:none">
	<div style="background: #FFF; border: 1px solid #CCC; padding: 5px 8px 8px 8px; font-family: arial, sans-serif;">
		<h1>Rename Thickbox form</h1><br />
		<div class="warning">
			<p>File renaming is intended for debug use only, not for regular use in a production environment.</p>
		</div><br />
		<form name="renameform" method="post" action="javascript:;" onsubmit="javascript:runRename();">
		
			<input type="hidden" name="file" value="<?php echo safe_filename($filename);?>" id="oldfile" />
			
			<p>Be advised that if you rename a file, any links to it on your site must be updated manually.</p>
			<br />
			<p>Rename this file: <input type="text" id="newname" name="newname" value="<?php echo safe_filename(pathinfo($filename, PATHINFO_BASENAME));?>" /></p>
			<br />
			<p><input type="submit" name="submit" value="Rename" style="font-size:14pt;" />
			<input type="button" name="cancel" value="Cancel" style="font-size:14pt;" class="closeDOMWindow" /></p>
		
		</form>
	</div>
</div>

<script type="text/javascript">
	$('.rename-box').openDOMWindow({ 
		eventType:'click', 
		loader:1, 
		loaderImagePath:'common/img/loadingAnimation.gif', 
		loaderHeight:208, 
		loaderWidth:13 
	});
</script>