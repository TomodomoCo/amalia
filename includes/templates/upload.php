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

?>		<!-- scripts for file uploader -->
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
	</head>
	<body class="ovl">
			
		<h1 class="w-top" id="ovl-top">File upload (standard) <a href="<?php print_internal_link('upload', 'type=simple&dir='.urlencode(safe_filename($dir)));?>">Use simple mode</a></h1>
		
		<div id="ovl-content">
			<div class="warning">
				<p>There are <strong>serious security risks</strong> associated with uploading files.</p>
				<p>Please make sure your files have been scanned with a reputable virus scanner before you upload.</p>
			</div>
			
			<noscript>
				<h1>Please use the <a href="<?php echo print_internal_link('upload', 'type=simple&dir='.urlencode(safe_filename($dir)));?>">simple uploader</a>.</h1>
			</noscript>
			
			<div id="message" style="color:#ff0000;"><?php if (!empty($msg)) { echo $msg; } ?></div>
			<div id="file-upload-info" style="padding:0px; margin:0px; font-weight:bold"><ul></ul></div>
			<div>
				<p>Upload some files:</p>
				<input type="file" id="file-upload-field" name="file" value="" />
			</div>
			
			<!--
			
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
			
		</div>
		<div id="shelf">
			<p>You may upload files up to <?php echo min($maxUploadFileSize, $maxPostSize);?> MB in size. If you need to upload larger files, speak to your system administrator about the PHP settings.</p>
		
			<button type="submit" id="file-upload-submit" name="submit" value="Upload" onclick="$('#file-upload-field').uploadifyUpload();"><div class="button-side"></div>Upload</button>
			
			<button type="submit" value="Done uploading" onclick="javascript:window.parent.location.replace('<?php print_internal_link('dir', 'dir='.urlencode(safe_filename($dir)), 'js');?>');"><div class="button-side"></div>Done Uploading</button>
		</div>
		<div id="ovl-btm"></div>
		
		<div id="ovl-tl"></div>
		<div id="ovl-tr"></div>
		<div id="ovl-br"></div>
		<div id="ovl-bl"></div>
	</body>
</html>