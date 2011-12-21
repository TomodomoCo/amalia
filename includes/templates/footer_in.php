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
?>				</div>
			</div>
			<ul id="sidebar">
			<?php
			if(file_exists('installer'))
			{
			?>
			<li class="red"><h2 class="w-top">Security Notice</h2>
					<div class="w-mid">
						The <code>installer</code> folder has not been deleted.  This potentially can lead to security problems.  Please delete the folder, or let the system try to <a href="<?php print_internal_link('settings', 'do=delete-installer-directory'); ?>">delete&nbsp;it.</a>
						<?php /* onclick="return confirm('DEBUG: Do not actually delete installer if you are running an SVN copy!!\n\nProceed?');"*/ ?>
					</div>

					<div class="w-btm"></div>
				</li>
			<?php
			}
			
			if ($_SESSION['version_outofdate'] == true)
			{
			?>
				<!-- YOU'RE ANNOYING. -->
				<!-- <li class="red"><h2 class="w-top">Outdated installation</h2>
					<div class="w-mid">
						This installation of Amalia is outdated. Please consult <a href="http://getamalia.com/">the website</a> regarding an upgrade.
					</div>

					<div class="w-btm"></div>
				</li> -->
			<?php
			}
			?>
				<li><h2 class="w-top">Tools</h2>
					<div class="w-mid">
					<?php
					if ($bodyClass == 'browser') // $bodyClass defined at top of header_in
					{
					?>
						<ul class="tools">
							<li><a href="#create-file" id="new-file"><span>Create a page</span></a></li>
							<li><a href="#upload-file" id="upl-file"><span>Upload a file</span></a></li>
							
							<!-- <li><a href="<?php echo print_internal_link('upload', 'dir='.urlencode(safe_filename($_GET['dir']))); ?>" id="upl-file"><span>Upload a file</span></a></li> -->
							<li><a href="#create-folder" id="new-fold"><span>Create a folder</span></a></li>
							<li><a href="<?php echo print_internal_link('recyclebin');?>" id="recyclebin<?php echo (is_array($recycled)) ? '-full' : '-empty'; ?>"><span><strong><?php echo (is_array($recycled)) ? count($recycled) : '0' ;?></strong> items in the Recycle Bin</span></a></li>
						</ul>
						<!-- <script type="text/javascript">
						$('#upl-file').openDOMWindow({
							eventType: 'click',
							windowSource: 'iframe',
							loader: 1,
							loaderImagePath: 'common/img/loadingAnimation.gif',
							loaderWidth:208,
							loaderHeight:13,
						});
						</script> -->
						
					<?php
					}
					else if ($bodyClass == 'recyclebin')
					{
					?>
						<ul class="tools">
							<li><a href="<?php echo print_internal_link('dir', 'dir='.$dirlink); ?>" id="go-browser"><span>Return to File Browser</span></a></li>
						</ul>
					<?php
					}
					else if ($bodyClass == 'settings')
					{
					?>
						<ul class="tools">

							<li><a href="<?php print_internal_link('settings');?>" id="status"><span>Status</span></a></li>
							<?php
							if ($perm['configure-amalia'] == 1) {
							?>
							<li><a href="<?php echo print_internal_link('settings','do=configure');?>" id="config"><span>Configuration</span></a></li>
							<?php 
							} 
							if ($perm['manage-plugins'] == 1) 
							{ 
							?>
							<li><a href="<?php echo print_internal_link('settings','do=manage-plugins');?>" id="plugin"><span>Manage plugins</span></a></li>
							<?php 
							} 
							if ($perm['manage-users'] == 1) 
							{ 
							?>
							<li><a href="<?php echo print_internal_link('settings','do=manage-users');?>" id="user"><span>Manage users</span></a></li>
							<?php
							}
							// hook to allow plugins to add new settings links
							$hk_settings_is_showing_tasks = new Hook('settings_is_showing_tasks');
							?>
						</ul>						
					<?php
					}
					else if ($bodyClass == 'editor')
					{
					?>
						<ul class="tools">
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
							
							?>
							<li><a href="<?php echo print_internal_link('dir', 'dir='.$dirlink); ?>" id="go-browser"><span>Return to File Browser</span></a></li>
							<li><a href="<?php echo $config['site_url'].'/'.ltrim($_GET['file'], '/');?>" id="view-page"><span>View this page</span></a></li>
						</ul>

					<?php
					}
					?>
					</div>
					<div class="w-btm"></div>
				</li>
			</ul>
		</div>
		<div id="gradient-bottom"></div>
		<div id="footer">

			<div id="fl-end"></div>
			<div id="f-mid">
				<div id="f-inner">
					<span id="version">
						<strong>Version:</strong> <?php echo AMALIA_VERSION;?>
						<?php
						if (DEBUG)
						{
						?>
						&mdash;
						<?php echo number_format(execEndTime(), 3);?>s
						<?php
						}
						?>
					</span>
					&copy; 2007-<?php /* COPYRIGHT_MAX_DATE */ ?>2011 Amalia <a href="http://getamalia.com/team.html">developer team</a> and <a href="http://getamalia.com/credits.html">others</a>
				<br />
				<?php $hk_footer_is_showing_copyright = new Hook('footer_is_showing_copyright', false);?>
				</div>
			</div>
			<div id="fr-end"></div>
		</div>
	</div>

	</body>
</html>