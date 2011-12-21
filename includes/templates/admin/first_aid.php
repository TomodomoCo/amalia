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
?><p id="desc">At-a-glance health details and repair tools for your Amalia installation.</p>

<?php
	$tick = '<span style="color:green" class="tick">&#10003;</span>';
	$cross = '<span style="color:red" class="tick">&#10007;</span>';
?>

<ul class="permission-checks">
	<li><div>
		<strong>PHP</strong>
		<?php echo ($envChecks['php']) ? $tick : $cross; ?>
		<span><?php echo PHP_VERSION;?> installed</span>
	</div></li>
	
	<?php if (function_exists('curl_version')) { ?>
		<li><div>
			<strong>cURL</strong>
			<?php echo ($envChecks['curl']) ? $tick : $cross; ?>
			<span><?php $v = curl_version(); echo $v['version']; ?> installed</span>
		</div></li>
	<?php } else { ?>
		<li class="failed"><div>
			<strong>cURL</strong>
			<?php echo ($envChecks['curl']) ? $tick : $cross; ?>
			<span>Not installed</span>
		</div></li>
	<?php } ?>
	
	<?php if (function_exists('gd_info')) { ?>
		<li><div>
			<strong>GD Graphics</strong>
			<?php echo ($envChecks['gd']) ? $tick : $cross; ?>								
			<span>
			<?php $v = gd_info();
				echo $v['GD Version'].' installed';
				echo ($v['JPG Support'] || $v['JPEG Support']) ? ' + JPEG' : ' + no JPEG';
				echo ($v['PNG Support']) ? ' + PNG' : ' + no PNG';
				echo ($v['GIF Read Support'] && $v['GIF Create Support']) ? ' + GIF' : ' + no GIF';	?>
		</div></li>			
	<?php } else { ?>
		<li class="failed"><div>
			<strong>GD Graphics Library</strong>
			<?php echo ($envChecks['curl']) ? $tick : $cross; ?>
			<span>Not installed</span>
		</div></li>
	<?php } ?>
	
	<?php if (isset($permissions_errors) && count($permissions_errors) > 0) { ?>
		<li class="failed"><div>
			<strong>Permissions</strong>
			<?php echo $cross;?>
			<span>Permissions errors detected.</span>
		</div></li>
	<?php } else { ?>
		<li><div>
			<strong>Permissions</strong>
			<?php echo $tick;?>
			<span>All permissions OK</span>
		</div></li>
	<?php } ?>	
	<!-- 
	Fancy stuff
	<li><div>
		<strong>Permissions</strong>
		<?php if (isset($permissions_errors) && count($permissions_errors) > 0) 
			{ 
				?><ul class="errorlist" style="color:#ff0000;"><?php
			foreach($permissions_errors as $err)
			{ 
				?><li><?php echo $err; /* already sanitised */ ?></li><?php
			} 
				?></ul><?php
			} else {
				?><?php echo $tick;?>
				<span>All permissions OK</span><?php
			}
		?>
	</div></li> -->
	<br clear="all" />
</ul>

<ul id="firstaid-config" class="form">
	<li id="clickreveal">
		Click to reveal details about your Amalia configuration.
	</li>
	<li>
		<label class="label" for="operating-sys">Operating Sys.</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="operating-sys" value="<?php echo safe_plain($envChecks['os']); ?>" id="operating-sys" readonly="readonly" />
				<abbr title="<?php echo safe_plain($envChecks['os_moreinfo']);?>"></abbr>
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="config-path">Config Path</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="config-path" value="<?php echo safe_filename($config['config_path']);?>" id="config-path" readonly="readonly" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="site-path">Site Path</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="site-path" value="<?php echo safe_filename($config['site_path']);?>" id="site-path" readonly="readonly" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="users-path">Users Path</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="users-path" value="<?php echo safe_filename($config['users_path']);?>" id="users-path" readonly="readonly" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="config-url">Config URL</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="config-url" value="<?php echo safe_plain($config['config_url']);?>" id="config-url" readonly="readonly" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="site-url">Site URL</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="site-url" value="<?php echo safe_plain($config['site_url']);?>" id="site-url" readonly="readonly" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="urlrewriting">URL Rewriting</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="urlrewriting" value="<?php echo ($config['mod_rewrite']) ? 'On' : 'Off' ; ?>" id="urlrewriting" readonly="readonly" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
</ul>

<div class="repair">
	<form name="repairpermissions" method="post" action="<?php print_internal_link('settings', 'do=first-aid');?>">
		<?php
			// form security stuff
			$tmt = time().'_'.microtime().rand(0,getrandmax());
			$stm = base64_encode($tmt);
			$formsec = hash(HASH_ALGO, $tmt.$config['salt']);
		?>
		<input type="hidden" name="stm" value="<?php echo $stm;?>" />
		<input type="hidden" name="formsec" value="<?php echo $formsec;?>" />
		<input type="hidden" name="repair_action" value="permissions" />
		
		<button type="submit" name="submit" value="submit"><div class="button-side"></div>Repair Permissions</button>
		<!-- <h2>Repair Permissions</h2> -->
		<p>First Aid will attempt to resolve any incorrect permissions that are preventing Amalia from
		operating normally.</p>
	</form>
</div>

<div class="repair">
	<form name="repairconfig" method="post" action="<?php print_internal_link('settings', 'do=first-aid');?>">
		<?php
			// form security stuff
			$tmt = time().'_'.microtime().rand(0,getrandmax());
			$stm = base64_encode($tmt);
			$formsec = hash(HASH_ALGO, $tmt.$config['salt']);
		?>
		<input type="hidden" name="stm" value="<?php echo $stm;?>" />
		<input type="hidden" name="formsec" value="<?php echo $formsec;?>" />
		<input type="hidden" name="repair_action" value="configfile" />
		
		<button type="submit" name="submit" value="submit"><div class="button-side"></div>Repair Configuration File</button>
		<!-- <h2>Repair Configuration File</h2> -->
		<p>First Aid can recreate a new configuration file. You may lose some non-critical settings (such as number of days before recycle).
		<strong>You will lose any custom configuration edits.</strong></p>
	</form>
</div>
	
<?php if ($_GET['showhidden'] == 'true') { ?>
<div class="repair">
	<form name="resetusers" method="post" action="<?php print_internal_link('settings', 'do=first-aid');?>" onsubmit="return confirm('This action WILL DELETE all users from Amalia, except the currently logged in user.\n\nThis action cannot be undone.');">
		<?php
			// form security stuff
			$tmt = time().'_'.microtime().rand(0,getrandmax());
			$stm = base64_encode($tmt);
			$formsec = hash(HASH_ALGO, $tmt.$config['salt']);
		?>
		<input type="hidden" name="stm" value="<?php echo $stm;?>" />
		<input type="hidden" name="formsec" value="<?php echo $formsec;?>" />
		<input type="hidden" name="repair_action" value="resetusers" />
		
		<button type="submit" name="submit" value="submit"><div class="button-side"></div>Reset All Users</button>
		<h2>Reset All Users</h2>
		<p>First Aid can delete all of the users currently registered in Amalia (except the one currently logged in) and recreate the Users file and Users Permissions file. <strong>You will lose any registered users other than this one.</strong></p>
	</form>
</div>
<?php } ?>