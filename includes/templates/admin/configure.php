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
?><p id="desc">Run maintenance tasks, change settings, and manage your installation.</p>

<?php echo $msg; ?>

<ul class="tools horiz-tools">
	<li style="float: left;"><a id="sched-tasks" href="<?php print_internal_link('settings', 'do=runscheduled');?>"><span>Run Scheduled Tasks</span></a></li>
	<li style="float: left;"><a id="thumb-cache" href="<?php print_internal_link('settings', 'do=clear-thumbnail-cache');?>"><span>Clear Thumbnail Cache</span></a></li>
</ul>

<form action="<?php echo print_internal_link('settings','do=configure'); ?>" method="post">

<?php

$tmt = time().microtime().rand(0,getrandmax()).$loop; // identifier to check delete form validity (formsec)
$stm = base64_encode($tmt);
$formsec = hash(HASH_ALGO, $tmt.$config['salt']);
?>

<input type="hidden" name="stm" value="<?php echo $stm;?>" />
<input type="hidden" name="formsec" value="<?php echo $formsec;?>" />

<ul class="form">
	
	<?php
	
	/*<li>
		<label class="label yesno-l" for="debug">Yes or No</label>
		<div class="yesno">
			<div class="input-l"></div>
			<div class="input-m">
				<!-- Start yes/no -->
				<div class="yesno-container yes-now">
					<!-- 
						Use "yes-now" or "no-now" to set button state.
						My jQuery code will check for class and 
						determine which way to slide everything.
					-->
					<div class="yes">
						<div class="yes-l"></div>
						<div class="yes-m">Yes</div>
						<div class="yes-r"></div>
					</div>
					<div class="no">
						<div class="no-l"></div>
						<div class="no-m">No</div>
						<div class="no-r"></div>
					</div>
					<a class="change" href="#"><span>Change status</span></a>
				</div>
				<!-- End yes/no -->
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	*/
	?>
	
	
	<!-- hidden data field for debug mode -->
	<input type="hidden" name="debug" id="debug_field" value="<?php echo (DEBUG) ? 'true': 'false'; ?>" />
	
	<li>
		<label class="label yesno-l" for="debug">Debug Mode</label>
		<div class="yesno">
			<div class="input-l"></div>
			<div class="input-m">
				<!-- Start yes/no -->
				<div class="yesno-container <?php echo (DEBUG) ? 'yes-now' : 'no-now';?>">
					<!-- 
						Use "yes-now" or "no-now" to set button state.
						My jQuery code will check for class and 
						determine which way to slide everything.
					-->
					<div class="yes">
						<div class="yes-l"></div>
						<div class="yes-m">On</div>
						<div class="yes-r"></div>
					</div>
					<div class="no">
						<div class="no-l"></div>
						<div class="no-m">Off</div>
						<div class="no-r"></div>
					</div>
					<a class="change" href="javascript:;" onclick="toggleField('#debug_field');"><span>Change status</span></a>
				</div>
				<!-- End yes/no -->
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	
	<!-- hidden data field for show thumbnails  -->
	<input type="hidden" name="thumbnails" id="thumbnails_field" value="<?php echo ($config['show_thumbnails']) ? 'true': 'false'; ?>" />
	
	<li>
		<label class="label yesno-l" for="thumbnails">Thumbnails</label>
		<div class="yesno">
			<div class="input-l"></div>
			<div class="input-m">
				<!-- Start yes/no -->
				<div class="yesno-container <?php echo ($config['show_thumbnails']) ? 'yes-now' : 'no-now';?>">
					<!-- 
						Use "yes-now" or "no-now" to set button state.
						My jQuery code will check for class and 
						determine which way to slide everything.
					-->
					<div class="yes">
						<div class="yes-l"></div>
						<div class="yes-m">On</div>
						<div class="yes-r"></div>
					</div>
					<div class="no">
						<div class="no-l"></div>
						<div class="no-m">Off</div>
						<div class="no-r"></div>
					</div>
					<a class="change" href="javascript:;" onclick="toggleField('#thumbnails_field');"><span>Change status</span></a>
				</div>
				<!-- End yes/no -->
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	
	<!-- hidden data field for pretty URLs  -->
	<input type="hidden" name="rewrite" id="rewrite_field" value="<?php echo ($config['mod_rewrite']) ? 'true': 'false'; ?>" />
	
	<li>
		<label class="label yesno-l" for="rewrite">Pretty URLs</label>
		<div class="yesno">
			<div class="input-l"></div>
			<div class="input-m">
				<!-- Start yes/no -->
				<div class="yesno-container <?php echo ($config['mod_rewrite']) ? 'yes-now' : 'no-now';?>">
					<!-- 
						Use "yes-now" or "no-now" to set button state.
						My jQuery code will check for class and 
						determine which way to slide everything.
					-->
					<div class="yes">
						<div class="yes-l"></div>
						<div class="yes-m">On</div>
						<div class="yes-r"></div>
					</div>
					<div class="no">
						<div class="no-l"></div>
						<div class="no-m">Off</div>
						<div class="no-r"></div>
					</div>
					<a class="change" href="javascript:;" onclick="toggleField('#rewrite_field');"><span>Change status</span></a>
				</div>
				<!-- End yes/no -->
			</div>
			<div class="input-r"></div>
		</div>
	</li>
		
	<li>
		<button type="submit" name="submit" id="submit" value="Save Changes"><div class="button-side"></div>Save Changes</button>
	</li>

</ul>
</form>