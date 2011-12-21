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

// this is a thickbox iframe
include 'thickbox_header.php';


// there was an error, print it:
if (!empty($message))
{
		?><div id="error"><?php echo $message;?></div><?php	
}


$allowedExtensions = array_flip($config['allowed']);

$maxUploadFileSize = str_replace('M', '', ini_get('upload_max_filesize'));
$maxPostSize = str_replace('M', '', ini_get('post_max_size'));

?>

	</head>
	<body class="ovl">
			
		<h1 class="w-top" id="ovl-top">File upload (simple) <a href="<?php print_internal_link('upload', 'dir='.urlencode(safe_filename($dir)));?>">Use standard mode</a></h1>
		
		<div id="ovl-content">
			<div class="warning">
				<p>There are <strong>serious security risks</strong> associated with uploading files.</p>
				<p>Please make sure your files have been scanned with a reputable virus scanner before you upload.</p>
			</div>
			
			<div id="message" style="color:#ff0000;"><?php if (!empty($msg)) { echo $msg; } ?></div>
			
			<form enctype="multipart/form-data" method="post" action="<?php echo print_internal_link('upload', 'type=simple&dir='.urlencode(safe_filename($dir))); ?>" class="media-upload-form type-form validate" id="file-form">
			
				<input type="hidden" name="type" value="pageload" />
				<div>
					<p>Upload a file:</p>
					<p id="file-form-upload-field"><input type="file" name="file" /></p>
					<!-- <p id="file-form-upload-field-2"><input type="file" name="file2" /></p>
					<p id="file-form-upload-field-3"><input type="file" name="file3" /></p>
					<p id="file-form-upload-field-4"><input type="file" name="file4" /></p>
					<p id="file-form-upload-field-5"><input type="file" name="file5" /></p> -->
				</div>
				
				<!-- <p>The upload process will take a long time, especially with large files and slow connections. No progress meter is shown on the simple uploader, so please be patient and do not close this browser window until you are notified that the upload is complete.</p>
				
				Feel free to uncomment this when there is a good way to display this info
				<p>You may upload files of the following types:
				<?php if (is_array($allowedExtensions) && count($allowedExtensions) > 0)
				{
					?><?php
					foreach($allowedExtensions as $ext)
					{
					
						?><?php echo safe_plain($ext);?>, <?php
					
					}
					
					?></ul><?php
				}
				?>
				</p>-->
				
				<div id="shelf">
				
					<p>You may upload files up to <?php echo min($maxUploadFileSize, $maxPostSize);?> MB in size. If you need to upload larger files, speak to your system administrator about the PHP settings.</p>
				
					<button type="submit" name="submit" id="file-upload-submit" value="Upload"><div class="button-side"></div>Upload</button>
					
					<button type="submit" value="Done uploading" onclick="javascript:window.parent.location.replace('<?php print_internal_link('dir', 'dir='.urlencode(safe_filename($dir)), 'js');?>');"><div class="button-side"></div>Done Uploading</button>
				</div>
			</div>
		</form>
		
		<div id="ovl-btm"></div>
		
		<div id="ovl-tl"></div>
		<div id="ovl-tr"></div>
		<div id="ovl-br"></div>
		<div id="ovl-bl"></div>
	
	</body>
</html>