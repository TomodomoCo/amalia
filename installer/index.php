<?php
/**
 * Amalia2
 *
 * The way "real people" manage websites
 *
 * @package	Amalia2
 * @author		Amalia Dev Team
 * @copyright	Copyright (c) 2007-2011, Chris Van Patten, Nick Sampsell, Peter Upfold
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

// ------------------------------------------------------------------------

/**
 * Installer Main File
 * 
 * The installer for Amalia.
 *
 * @package	Amalia2
 * @category	Setup
 * @author		Amalia Dev Team
 */

ini_set("session.cookie_httponly", 1);
session_name('AmaliaInstall-'.md5($_SERVER['SERVER_NAME'].__FILE__));
session_start();

define('IN_AMALIA_INSTALLER', true);

require('serverchecks.php');
include('helper.php');

if (isset($_GET['rewriting']) && $_GET['rewriting'] == 'off')
{
	$_SESSION['disable_mod_rewrite'] = true;
	$checks['mod_rewrite'] = false;
}

print_header(0);

?>
<div id="viewer">
<div id="installer_wrapper">
	<div id="step0" class="step">
		<div class="install">
		<h2>Welcome to Amalia</h2>
				<div class="install-inner">
				
					<noscript><p><strong style="color:#ff0000;">Hold on!</strong> You need JavaScript enabled in your browser
					in order to use the installer, and to use Amalia once installed. Please enable
					JavaScript in your browser or set exceptions for
					<em>http://<?php echo htmlentities(strip_tags($_SERVER['SERVER_NAME']), ENT_QUOTES, 'UTF-8', false);?>/</em> and for <em>http://ajax.googleapis.com/</em>.</p></noscript>

					<?php
					// check for existing amalia-config.php file in one dir up
					$installedAlready = file_exists(htmlentities(strip_tags(str_replace('installer', '', dirname(__FILE__).'amalia-config.php'))));
					if ($installedAlready) {
					
						?><p><strong style="color:#ff0000;">Amalia is already installed!</strong></p>
						
						<p>Amalia appears to be already installed.</p>
						
						<p><strong>If you have already installed Amalia, please
						remove the <em>installer</em> directory</strong> to prevent others accessing the installer (a security
						risk).</p>
						
						<p><strong>If you need to reinstall</strong>, you will need to back up and then
						<strong>delete the following files:</strong></p>
						
						<p>
						<ul>
							<li><em>amalia-config.php</em> in your Config Path</li>
							<li><em>amalia-users.txt</em> in your Users Path</li>
							<li><em>amalia-users-permissions.txt</em> in your Users Path</li>
							<li>All files in your Site Path.</li>
						</ul>
						</p>
						<p style="color:#ff0000;">Please back up these files first, then delete them before <a href="index.php">proceeding
						with this installer</a>.</p>
						<?php
					}
					else
					{
					
						// all in order
						?>
					<p>Welcome to the Amalia installation wizard!</p>
					<p>We&rsquo;ll begin by running a few tests to make sure your server supports Amalia.</p>
					<p><strong>Note:</strong> Amalia is free and open source software, but it comes with no warranty. Please read the <a href="http://getamalia.com/license.html" target="_blank">license</a> to understand your rights and responsibilities.</p>
					<p>Click the arrow to the right to begin...</p>
					<?php			
					
					}
					?>
				</div>
			</div>
			<?php if (!$installedAlready)
			{
			?>	<div class="install-nav">
				<a href="javascript:void()" class="install-next">Next</a>
			<?php
			}
			?>
			</div>
	</div>
	<div id="step1" class="step">
	<div class="install">
	<h2>Testing requirements...</h2>
				<div class="install-inner" style="overflow: hidden;">

					<ul id="requirements">
						<?php
						
						// Run the checks
											
						
						/***** PHP VERSION *****/	
						if ($checks['php'])
						{
							?><li class="g">PHP <span>Pass</span></li><?php
						}
						
						
						else
						{
							?><li class="r"><a href="javascript:void();" onclick="requirementsMsg(0);">PHP <span>Fail</span></a>
							</li><?php
						}

						/***** CURL SUPPORT *****/
						if ($checks['curl'])
						{
							?><li class="g">File Transfer <span>Pass</span></li><?php
						}
						else
						{
							?><li class="r"><a href="javascript:void();" onclick="requirementsMsg(1);">File Transfer <span>Fail</span></a></li><?php
						}


						/***** FILE AND DIR PERMISSIONS *****/
						if ($checks['permissions'])
						{
							?><li class="g">File Permissions <span>Pass</span></li><?php
						}
						else
						{
							?><li class="r"><a href="javascript:void();" onclick="requirementsMsg(2);">File Permissions <span>Fail</span></a></li><?php
						}

						/***** FILE MANAGEMENT *****/
						if ($checks['fileman'])
						{
							?><li class="g">Files &amp; Uploads <span>Pass</span></li><?php
						}
						else
						{
							?><li class="r"><a href="javascript:void();" onclick="requirementsMsg(3);">Files &amp; Uploads  <span>Fail</span></a></li><?php
						}

						/***** GD SUPPORT *****/
						if ($checks['gd'])
						{
							?><li class="g">Image Support <span>Pass</span></li><?php
						}
						else
						{
							?><li class="r"><a href="javascript:void();" onclick="requirementsMsg(4);">Image Support <span>Failed</span></a></li><?php
						}

						/***** MOD_REWRITE *****/
						if ($checks['mod_rewrite'])
						{
							?><li class="g">Pretty URLs <span>Pass</span></li><?php
						}
						else
						{
							?><li class="y"><a href="javascript:void();" onclick="requirementsMsg(5);">Pretty URLs</a>
							<p style="font-size:11pt;">Pretty URLs will be disabled. You can enable them manually later.</p>
							<span>Fail</span></li><?php
						}
						
						// did all checks pass?
						$passedAllChecks = true;
						$passedAllChecksNoWarn = true;
						foreach($checks as $whichCheck => $req)
						{
						// loop through all checks, if any are false, we didn't pass
							if ($whichCheck == 'mod_rewrite' && $req != true)
							{
								$passedAllChecksNoWarn = false;
							}
							if ($req != true && $whichCheck != 'mod_rewrite') // ignore mod_rewrite failure, it is non-fatal
							{
								$passedAllChecks = false;
								$passedAllChecksNoWarn = false;
							}
						}
						
						?>
						
						
					</ul>
					<div id="requirements-overlay" style="display:none;">
						<script type="text/javascript">
						var passedAllChecks = <?php echo ($passedAllChecks ? 'true' : 'false');?>;
						var passedAllChecksNoWarn = <?php echo ($passedAllChecksNoWarn ? 'true': 'false');?>;
						</script>
						<input type="hidden" name="enablerewrite" value="<?php echo ($passedAllChecksNoWarn ? 'true': 'false'); ?>" />
						<?php
												
						if ($passedAllChecks)
						{
							?><p><strong>Congratulations, you passed!</strong></p>
							<p>Continue by clicking the arrow to the right.</p><?php
						}
						?>						
					</div>
				</div>
			</div>
			<div class="install-nav">
				<a href="javascript:void()" class="install-prev">Previous</a>
				<a href="javascript:void()" class="install-next">Next</a>
			</div>
	</div>
	<div id="step2" class="step">
	<div class="install">
	<h2>Server information</h2>
				<div class="install-inner">

					<p>Amalia uses the following information so it knows where files are located. Click on the arrow to the right once you&rsquo;ve double checked everything.</p>
					<ul id="install-serverconfig" class="form">
						<?php
						$cfgPathDefault = htmlentities(strip_tags(str_replace('installer', '', dirname(__FILE__))));
						?>
						<li>
							<label class="label" for="configpath">Config Path</label>
							<div class="input">
								<div class="input-l"></div>

								<div class="input-m">
									<input type="text" name="configpath" value="<?php echo rtrim($cfgPathDefault, '/'); ?>" id="configpath"  />
								</div>
								<div class="input-r"></div>
							</div>
						</li>
						
						
						<?php
						// config URL default value
						$cfgURLDefault = htmlentities(strip_tags($_SERVER['SERVER_NAME']), ENT_QUOTES, 'UTF-8', false);
						$cfgURLDefault .= htmlentities(strip_tags(str_replace('installer', '', dirname($_SERVER['SCRIPT_NAME']))), ENT_QUOTES, 'UTF-8', false);
						?>
						<li>
							<label class="label" for="configurl">Config URL</label>
							<div class="input">
								<div class="input-l"></div>

								<div class="input-m">
									<input type="text" name="configurl" value="http://<?php echo rtrim($cfgURLDefault, '/'); ?>" id="configurl"  />
								</div>
								<div class="input-r"></div>
							</div>
						</li>
						<li>
							<label class="label" for="sitepath">Site Path</label>

							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="text" name="sitepath" value="" id="sitepath"  />
								</div>
								<div class="input-r"></div>
							</div>
						</li>
						<li>

							<label class="label" for="siteurl">Site URL</label>
							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="text" name="siteurl" value="http://<?php echo htmlentities(strip_tags($_SERVER['SERVER_NAME']));?>" id="siteurl"  />
								</div>
								<div class="input-r"></div>
							</div>

						</li>
						<li>

							<label class="label" for="siteurl">Users Path</label>
							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="text" name="userspath" value="" id="userspath"  />
								</div>
								<div class="input-r"></div>
							</div>

						</li>
					</ul>
				</div>
			</div>
			<div class="install-nav">
				<a href="javascript:void()" class="install-prev">Previous</a>
				<a href="javascript:void()" class="install-next">Next</a>
			</div>
	</div>
	
	
	<div id="step3" class="step">
	<div class="install">
	<h2>Account setup</h2>
				<div class="install-inner">

					<p>Fill in the below information to set up your Amalia account. When you're ready, press the arrow to the right to finish installation.</p>
					<ul id="install-accountsetup" class="form">
						<li>
							<label class="label" for="username">Username</label>
							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="text" name="username" value="" id="username" maxlength="15" />

								</div>
								<div class="input-r"></div>
							</div>
						</li>
						<li>
							<label class="label" for="email">Email</label>
							<div class="input">
								<div class="input-l"></div>

								<div class="input-m">
									<input type="text" name="email" value="" id="email" maxlength="63" />
								</div>
								<div class="input-r"></div>
							</div>
						</li>
						<li>
							<label class="label" for="Password">Password</label>

							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="password" name="password" value="" id="password"  />
								</div>
								<div class="input-r"></div>
							</div>
						</li>
						<li>

							<label class="label" for="password">Retype</label>
							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="password" name="password_retype" value="" id="password_retype"  />
								</div>
								<div class="input-r"></div>
							</div>

						</li>
						
					</ul>
				</div>
			</div>
			<div class="install-nav">
				<a href="javascript:void()" class="install-prev">Previous</a>
				<a href="javascript:void()" class="install-next" id="save">Next</a>
			</div>
	</div>
	<div id="step4" class="step">
	<div class="install">
	<h2>Finishing up</h2>
				<div class="install-inner">
					<div id="install-inner-loading">
					<p style="text-align:center; font-size:24pt; font-weight:bold;">Installing now...</p>
					<p style="text-align:center"><img src="../common/img/loadingAnimation.gif" alt="Loading" /></p>
					</div>
					<div id="install-inner-success" style="display:none;">
						<p>Congratulations! Amalia has been successfully installed!</p>
						<p>There&rsquo;s one more step you have to take &#8212; <strong>delete the <em>&ldquo;installer&rdquo;</em> folder from your server</strong>. This will help prevent your copy of Amalia from being compromised.</p>
						<p>When you&rsquo;re ready, log in and start working:</p>
						<p id="install-panel-loc"><a href="#">[placeholder]</a></p>
	
						<p>(You may want to bookmark that page so you don&rsquo;t lose it.)</p>
						<p>Thank you for choosing Amalia!</p>
					</div>
					<div id="install-inner-failure" style="display:none;">
						<p>Unfortunately, the installer has run into a problem. Please review this below, correct the problem if you can on the previous pages and rerun the install.</p>					
						<p id="install-inner-failure-detail" style="color:#ff0000; font-weight:bold;"></p>
						<p>For your information:</p>
						<p>
						<strong>Config Path:</strong> <span id="install-inner-failure-configpath"></span><br />
						<strong>Config URL:</strong> <span id="install-inner-failure-configurl"></span><br />
						<strong>Site Path:</strong> <span id="install-inner-failure-sitepath"></span><br />
						<strong>Site URL:</strong> <span id="install-inner-failure-siteurl"></span><br />
						<strong>Users Path:</strong> <span id="install-inner-failure-userspath"></span><br />
						
						</p>
					</div>
				</div>
			</div>
			<div class="install-nav">
				<a href="javascript:void()" class="install-prev">Previous</a>
			</div>
	</div>
	</div>
	</div>
	<?php	

echo get_footer();