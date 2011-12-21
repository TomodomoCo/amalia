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
?><p id="desc">Activate, deactivate, and adjust the settings of installed plugins.</p>

<script type="text/javascript">
<?php
if (empty($msg))
{
?>$(function() {
	$('#message').hide();
});<?php
}
?>



</script>

<div id="message">
<?php
	
switch($msg)
{
	case 'dacd':
		echo 'Plugin deactivated successfully.';
	break;
		
	case 'dacf':
		echo 'Failed to deactivate plugin. You may need to do this manually through the enabledPlugins.txt file.';
	break;
		
	case 'actd':
		echo 'Plugin activated successfully.';
	break;
		
	case 'actf':
		echo 'Failed to activate plugin. You may need to do this manually through the enabledPlugins.txt file.';
	break;
	
	default:
	break;
		
}
	
?>
</div>

<ul class="pluginlist">
<?php


if (is_array($pluginInfo) && count($pluginInfo) > 0)
{
	foreach($pluginInfo as $plloop => $plugin)
	{
	
		$tmt = time().microtime().rand(0,getrandmax()).$plloop; // identifier to check form validity (formsec)
		$stm = base64_encode($tmt);
		$formsec = hash(HASH_ALGO, $tmt.$config['salt']);
		
		$actdo = $plugin['isActivated'] ? 'do=deactivate-plugin' : 'do=activate-plugin';
		
		?><li class="<?php echo ($plloop % 2) ? 'alt1': 'alt2'; ?> plugin">
			<div class="pluginmeta">
				<div class="filetype">
					<a href="<?php echo safe_filename($config['site_url'].$dir.$file['name']); ?>" class="view">View</a>
				</div>
				<span class="filetitle"><?php echo safe_plain($plugin['friendlyName'], false);?></span>
				<p><strong>by <?php echo safe_plain($plugin['author']).' of '.safe_plain($plugin['company']);?></strong> &#8212; <?php echo safe_plain($plugin['description']);?></p>
			</div>
			
			<input type="hidden" id="<?php echo safe_plugin_identifier($plugin['identifier']);?>_actstate" value="<?php echo $plugin['isActivated'] ? 'act' : 'deact';?>" />
			<input type="hidden" id="<?php echo safe_plugin_identifier($plugin['identifier']);?>_formsec" name="formsec" value="<?php echo $formsec; ?>" />
			<input type="hidden" id="<?php echo safe_plugin_identifier($plugin['identifier']);?>_stm" name="stm" value="<?php echo $stm; ?>" />
			
			<input type="hidden" id="<?php echo safe_plugin_identifier($plugin['identifier']);?>_newactstate" value="<?php echo $plugin['isActivated'] ? 'true' : 'false';?>" />
		
			<ul class="ftools">
				<li>
					<div class="yesno">
						<div class="input-l-s"></div>
						<div class="input-m">
							<!-- Start yes/no -->
							<div class="yesno-container <?php echo $plugin['isActivated'] ? 'yes-now' : 'no-now';?>">
								<div class="yes">
									<div class="yes-l"></div>
									<div class="yes-m">Enabled</div>
									<div class="yes-r"></div>
								</div>
								<div class="no">
									<div class="no-l"></div>
									<div class="no-m">Disabled</div>
									<div class="no-r"></div>
								</div>
								<a class="change" href="javascript:;" onclick="toggleField('#<?php echo safe_plugin_identifier($plugin['identifier']);?>_newactstate');togglePlugin('<?php echo safe_plugin_identifier($plugin['identifier']);?>')"><span>Change status</span></a>
							</div>
							<!-- End yes/no -->
						</div>
						<div class="input-r"></div>
					</div>
				</li>
			</ul>
		</li>
		
		<?php
		
		
		
	}
	
}
?>

</ul>


<?php
$tmt = time().microtime().rand(0,getrandmax()).'deacall';
?>
<form onsubmit="return confirm('Are you sure you want to deactivate all plugins?\n\nThis operation cannot be undone &mdash; the state of the currently enabled plugins will not be remembered and you will have to re-enable each plugin individually.')" method="post" action="<?php print_internal_link('settings', 'do=deactivate-all-plugins');?>">
	<input type="hidden" name="formsec" value="<?php echo hash(HASH_ALGO, $tmt.$config['salt']);?>" />
	<input type="hidden" name="stm" value="<?php echo base64_encode($tmt);?>" />
	<button type="submit" value="Deactivate All Plugins"><div class="button-side"></div>Deactivate All Plugins</button>
</form>

