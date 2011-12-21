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
<script type="text/javascript">
// provide dirlink to editor JS for setting up CKEditor
  <?php
  
  // get current dir by slicing off last element of the file path, just like getting one dir up							
	$filePath = urldecode(safe_filename($_GET['file']));
	$filePathExpl = explode('/', $filePath);
	
	if (is_array($filePathExpl) && count($filePathExpl) > 0)
	{
		$filePathExpl = array_slice($filePathExpl, 0, count($filePathExpl)-1);
		$dirlink = implode('/', $filePathExpl);
		$dirlink = urlencode(safe_filename($dirlink));
	}
	
	echo "var dirlink = '$dirlink';";
  
  
  ?>
</script>
<?php
if (!empty($message))
{
	// there was an error, print it:
	?><div id="error"><?php echo $message;?></div><?php	
}
$keywordsNum = (isset($knum) && $knum > 0) ? $knum : 0;
?>
<form action="<?php echo print_internal_link('edit','file='.urlencode($file));?>" method="post" onsubmit="shouldWarnAboutUnsavedChanges = false;">
<?php 

// this plugin implementation 'style' is not finalised -- shuffling around with the data like this
// may be done differently if I can think of a better way --Peter

// $pData is an array containing all the stuff that plugins might want to modify
// it will be sent to the plugin
$pData['keywords'] = $keywords;
$pData['description'] = $description;
$pData['content'] = $content;

$hk_going_to_print_form = new Hook('editor_going_to_print_form', $pData, $modifiedData);

$keywords = $modifiedData['keywords'];
$description = $modifiedData['description'];
$content = $modifiedData['content'];

?>
					<div id="filetitle"><input type="text" name="title" value="<?php echo $title;?>" /></div>
					<ul id="fileeditor" class="form">
						<li id="site-content">
							<textarea name="content" class="editor" id="editor" style="width:100%; height:280px;"><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8', false);?></textarea><!--NOTE: static width and height here, probably need moving to CSS -Peter -->
						</li>
						<li>
							<label class="label" for="keywords">Keywords</label>
							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="text" name="keywords" value="<?php echo $keywords;?>" id="keywords"  />

								</div>
								<div class="input-r"></div>
							</div>
						</li>
						<li>
							<label class="label" for="description">Description</label>
							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="text" name="description" value="<?php echo $description;?>" id="description"  />
								</div>
								<div class="input-r"></div>
							</div>
						</li>
						<li id="theme-drop">
							<label class="label" for="theme">Theme</label>
							<input type="hidden" id="theme" name="theme" value="" />
							<div class="input">
								<div class="input-l"></div>
								<div class="dropdown-m">
									<ul class="select" style="display: none"> <!-- selected -->
									<?php
									//cycle through the themes
									if (is_array($templates) && count($templates) > 0)
									{
										foreach($templates as $template) {
	
											if($config['themes_path'].'/'.$template == $theme) {
												echo '<li id="template_'.safe_plain($template).'" class="picked">'.ucwords(preg_replace('/[_|-]/',' ',(str_replace('.php','',$template)))).'</li>'."\n";
											} else {
												echo '<li id="template_'.safe_plain($template).'">'.ucwords(preg_replace('/[_|-]/',' ',(str_replace('.php','',$template)))).'</li>';
											}
	
										}
									}
									else
									{
										echo 'No themes installed! Check Themes folder in your settings!';
									}

									?>
									</ul>
									<noscript>
									<select name="template" id="template" class="select">
									<?php
									//cycle through the themes
									if (is_array($templates) && count($templates) > 0)
										{
										
										sort($templates); // alpha sort the templates
										
										foreach($templates as $tmp) {
	
											if($config['themes_path'].'/'.safe_plain($tmp) == $theme) {
												
												echo '<option value="'.$tmp.'" selected="selected">'.ucwords(preg_replace('/[_|-]/',' ',(str_replace('.php','',$tmp)))).'</option>';
	
											}
											else
											{
												echo '<option value="'.safe_plain($tmp).'">'.ucwords(preg_replace('/[_|-]/',' ',(str_replace('.php','',$tmp)))).'</option>';										
											}
										}
									}
									else
									{
										echo 'No themes installed! Check Themes folder in your settings!';
									}

									?>
									</select>
									</noscript>
								</div>
								<a class="dropdown-r" href="javascript::void()" style="display: none"></a>
								<div class="input-r"></div>
							</div>
							<input type="hidden" id="template" name="theme" value="<?php echo str_replace($config['themes_path'].'/','template_',$theme); ?>" />
						</li>
						<li id="page-save">
							<button type="submit" name="save" value="save"><div class="button-side"></div>Save</button>
						</li>
					</ul>
</form>
