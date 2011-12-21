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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		
		<meta name="robots" content="noindex,noarchive" />
		
		<link rel="stylesheet" type="text/css" href="<?php echo $config['config_url'];?>/common/css/style.css" />
		
		<title>Login &#8212; Amalia</title>
		
	</head>
	<body>
	<div id="wrap">
		<div id="bookshelf">
			<div id="l-end"></div>

			<div id="books">
				<div id="off">
					<div id="off-r"><h1 id="h1"><a href="#" id="menu-button">Amalia</a></h1></div>
				</div>
			</div>
			<div id="r-end"></div>
		</div>
		<div id="content">
			
			<?php
			if (!empty($message))
			{
				// there was an error, print it:
				?><div id="loginerror"><?php echo $message;?></div><?php	
			}
			else { // show the no JavaScript error immediately on the login page
				?><noscript><div id="loginerror"><p><strong style="color:#ff0000;">Hold on!</strong> You need JavaScript enabled in your browser
					in order to use Amalia properly. Please enable JavaScript in your browser or set exceptions for
					<em>http://<?php echo safe_plain($_SERVER['SERVER_NAME']);?>/</em>
					and for <em>http://ajax.googleapis.com/</em>.</p>
			</div></noscript><?php
			}
			?>
			<form style="width: 500px; margin: 0 auto;" action="<?php echo print_internal_link('login');?>" method="post" autocomplete="off" name="login">
				<fieldset id="login">
					<ul class="form">
						<li>
							<label class="label" for="username">Username</label>
							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="hidden" name="action" value="login" />
									<input type="hidden" name="formsec" value="<?php echo hash(HASH_ALGO, $tmt.$config['salt']);?>" />
									<input type="hidden" name="stm" value="<?php echo base64_encode($tmt);?>" />
									<input type="text" id="username" name="username" value=""/>
								</div>
								<div class="input-r"></div>
		
							</div>
						</li>
						<li>
							<label class="label" for="password">Password</label>
							<div class="input">
								<div class="input-l"></div>
								<div class="input-m">
									<input type="password" id="password" name="password" value="" />
		
								</div>
								<div class="input-r"></div>
							</div>
						</li>
						<li>
							<button type="submit" name="submit" value="submit"><div class="button-side"></div>Login</button>
						</li>
					</ul>
				</fieldset>
			</form>
			
		</div>
		<div id="footer">

			<div id="fl-end"></div>
			<div id="f-mid">
				<div id="f-inner">&copy; 2007-<?php /* COPYRIGHT_MAX_DATE */ ?>2011 <a href="http://chrisvanpatten.com/">Chris Van Patten</a>, <a href="http://peter.upfold.org.uk/">Peter Upfold</a> and <a href="http://nicksampsell.com/">Nick Sampsell</a> &mdash; <strong>Version:</strong> <?php echo AMALIA_VERSION;?>
				<?php
				if (DEBUG)
				{
				?>
				&mdash;
				<?php echo number_format(execEndTime(), 3);?>s
				<?php
				}
				?>
				<br />
				<?php $hk_footer_is_showing_copyright = new Hook('footer_is_showing_copyright', false);?>
				</div>
			</div>
			<div id="fr-end"></div>
		</div>
	</div>

	</body>
</html>