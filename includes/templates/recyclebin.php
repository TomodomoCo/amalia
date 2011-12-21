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

if (is_array($files) && count($files) > 0)
{
?>
	<ul class="filebrowser">
		<?php
		
		foreach($files as $loop => $file) {
		
		if ($loop % 2) {
			$alt_link = 'alt2';
		} else {
			$alt_link =  'alt1';
		}
		
		$tmt = time().microtime().rand(0,getrandmax()).$loop; // identifier to check delete form validity (formsec)
		$stm = base64_encode($tmt);
		$formsec = hash(HASH_ALGO, $tmt.$config['salt']);
		
		?>
			
		<li class="<?php echo safe_plain($file['type']);?> <?php echo $alt_link; ?>"<?php
		
			if ($file['type'] == 'file image' && $config['show_thumbnails'])
			{
				// Change width/height vars on recyclebin.class.php:135
				 ?> style="background-image: url(<?php echo safe_filename($file['thumbnail']); ?>);"
				 
			 <?php } ?>>
			
			<div class="filetype">
				
			</div>
		
			<span class="filetitle">
			<?php
			// display file title, or original path if unavailable
			if (!empty($file['title']))
			{
				echo safe_plain($file['title']);
			}
			else {
				echo safe_filename($file['originalPath']);
			}
			?>
			</span>
			
			<em class="filename expiration">expires <?php echo date('Y-m-d H:i', $file['expiryTime']); ?></em>
			
			<form name="<?php echo safe_plain($file['identifier']);?>_deleteform" id="<?php echo safe_plain($file['identifier']);?>_deleteform" method="post" action="<?php print_internal_link('recyclebin', 'do=delete');?>" style="display:inline;">
				<input type="hidden" name="stm" value="<?php echo $stm;?>" />
				<input type="hidden" name="formsec" value="<?php echo $formsec;?>" />
				<input type="hidden" name="identifier" value="<?php echo safe_plain($file['identifier']);?>" />
				<input type="hidden" name="magic" value="Delete" />
			
				<ul class="ftools">
					<li class="delete closed-delete" title="Delete">
						<div class="holder ftool">
							Recycle
							<div class="confirmation">Are you sure you want to delete this file? <a href="javascript:void();" onclick="$('#<?php echo safe_plain($file['identifier']);?>_deleteform').submit();" title="Delete">Yes</a> or <a href="javascript:void()" title="Cancel">No</a></div>
						</div>
					</li>
					<li class="restore">
						<a href="<?php print_internal_link('recyclebin', 'do=restore&identifier='.urlencode(safe_plain($file['identifier']) ) ); ?>" title="Restore" class="ftool">Restore</a>
					</li>
				</ul>
				
				<!-- <ul class="ftools deletetools">
					<li class="deleteitem">
						<div class="delete-holder">
							<button type="submit" name="submit" value="Delete"><div class="button-side"></div>Delete</button>
						</div>
					</li>
					<li class="restoreitem">
						<div class="restore-holder">
							<button value="Restore" onclick="window.parent.location.replace('');"><div class="button-side"></div>Restore</button>
						</div>
					</li>
				</ul> -->
				
			</form>
		
		</li><?php	
	
	}	
	?>
	</ul>
	<?php
	
}


// empty recycle bin button

$tmt = time().microtime().rand(0,getrandmax()).'empty'; // identifier to check delete form validity (formsec)
$stm = base64_encode($tmt);
$formsec = hash(HASH_ALGO, $tmt.$config['salt']);

?><form name="empty_form" id="empty_form" method="post" action="<?php print_internal_link('recyclebin', 'do=empty');?>" onsubmit="return confirm('Are you sure you want to empty the Recycle Bin?\n\nThis action will delete all files in the Recycle Bin and cannot be undone.');">
	<input type="hidden" name="stm" value="<?php echo $stm;?>" />
	<input type="hidden" name="formsec" value="<?php echo $formsec;?>" />
	<button type="submit" name="submit" value="Empty Recycle Bin"><div class="button-side"></div>Empty Recycle Bin</button>
</form>