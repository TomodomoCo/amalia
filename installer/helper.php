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
 * Installer Helper
 * 
 * Provides a header and footer for the installer for Amalia.
 *
 * @package	Amalia2
 * @category	Setup
 * @author		Amalia Dev Team
 */


function print_header($step)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" type="text/css" href="../common/css/style.css" />
		<script src="../common/js/scroller.js" type="text/javascript"></script>

		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
		<script type="text/javascript">
		
			var hasValidatedLastPage = false;
			var installData = '';
			var page = 0;
		 
			$(document).ready(function(){
			
				$("input#rename-submit").mouseover(function () {
					$("#rename-form").css("background","url(images/form.png) center -49px no-repeat");
				}).mousedown(function(){
					$("#rename-form").css("background","url(images/form.png) center bottom no-repeat");
				}).mouseup(function(){
					$("#rename-form").css("background","url(images/form.png) center -49px no-repeat");
				}).mouseout(function(){
					$("#rename-form").css("background","url(images/form.png) center 0px no-repeat");
				});
				
				$.fn.wait = function(time, type) {
					time = time || 500;
					type = type || "fx";
					return this.queue(type, function() {
						var self = this;
						setTimeout(function() {
							$(self).dequeue();
						}, time);
					});
				};
				
				 $("#h1").animate({
					width: "73px"
				}, 800, function(){
					
				});
				
				$("#menu-arrow").animate({
					width: "25px",
					opacity: "1.0"
				}, 800, function(){
					$("#menu").css("visibility","visible")
					$(this).fadeTo(0, 0.0);
					$("#h1").fadeTo(0, 0.0);
				});
			$('#viewer').scrollLeft('0');
			var page = '0';
			$('.the_step').html(page);
			var variable = $('#viewer').scrollLeft();
			var variable_init = variable;
			$('.install-next').click(
				function() {
					if (validateInstallerPage(page)) // validate page first
					{
						if (variable < 3550) {
							variable += 710;
							$("div#viewer").animate({
								scrollLeft: variable
							});
						}
						if(page <= 4)
						{
							page++;
							$('.the_step').html(page);
						}
					}
				}
			);
			
			$('.install-prev').click(
				function() {
				
					hasValidatedLastPage = false;

					$("#install-inner-failure").hide();
					$("#install-inner-success").hide();					
					$("#install-inner-loading").show();
					
					if (variable > variable_init) {
						variable -= 710;
						$("div#viewer").animate({
							scrollLeft: variable
						});
					}
					if(page > 0)
					{
						page--;
						$('.the_step').html(page);
					}
				}
			);
			
			$('#save').click(function()
			{
				if (hasValidatedLastPage)
				{				
					installData = $('input').serialize();
					
					$.ajax({
					
						type: "POST",
						url: "install.php",
						dataType: "json",
						data: installData,
						success: function(response) {
							if (response.type == 'success')
							{
								// display install success page
								$("#install-inner-loading").hide();
								$("#install-inner-failure").hide();
								$("#install-inner-success").show();
								
							}
							else { // assume failure, display failure finish screen
								$("#install-inner-loading").hide();
								$("#install-inner-success").hide();
								$("#install-inner-failure").show();
								
								$("#install-inner-failure-detail").html((response.error_message));
								
								$("#install-inner-failure-configpath").html($("#configpath").val());
								$("#install-inner-failure-configurl").html($("#configurl").val());
								$("#install-inner-failure-sitepath").html($("#sitepath").val());
								$("#install-inner-failure-siteurl").html($("#siteurl").val());
								$("#install-inner-failure-userspath").html($("#userspath").val());
								
							}
						},
						error: function(xhr) {
							if (xhr.statusText == 'OK')
							{
								alert('Received a malformed response from the server.');
							}
							else {	
								alert('Unable to contact the server to complete installation. The error is ' + xhr.statusText);
							}
						},
										
					});
				}
			});
			
			
							
				
				
		});


		function validateInstallerPage(page)
		{
		
			// callback function. The jQuery should call this, passing in page, whenever
			// the user moves forward. This will then handle local form data validation.
			
			switch (page)
			{
			
				case 0:
				// INTRO PAGE
					if (passedAllChecksNoWarn)
						window.setTimeout(function() {$("#requirements-overlay").show('slow');}, 550);
					return true;
				break;
				
				case 1:
				// SERVER REQUIREMENTS CHECKS
				
				
					if (passedAllChecksNoWarn)
					{
						return true;
					}
					else if (passedAllChecks)
					{
						return true;
					}
					else {
						alert("At the moment, your server does not meet the requirements for Amalia.\n\nPlease see the documentation for further assistance."); //TODO: nicer message
						return false;
					}
				break;
				
				
				case 2:
				// SERVER INFORMATION SCREEN
					if ($("#configpath").val().length < 1)
					{
						alert("Please enter a Config Path.");
						return false;
					}
					
					if ($("#configurl").val().length < 1)
					{
						alert("Please enter a Config URL.");
						return false;
					}
					
					if ($("#sitepath").val().length < 1)
					{
						alert("Please enter a Site Path.");
						return false;
					}
					
					if ($("#siteurl").val().length < 1)
					{
						alert("Please enter a Site URL.");
						return false;
					}
					
					if ($("#userspath").val().length < 1)
					{
						alert("Please enter a Users Path.");
						return false;
					}
					
					
					return true;
				
				break;
				
				case 3:
				// ACCOUNT SETUP
					if ($("#username").val().length < 1) {
						alert("Please enter a username.");
						return false;
					}
					
					if ($("#email").val().length < 1) {
						alert("Please enter an email address.");
						return false;
					}
					
					var emailMatch = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
					if (!emailMatch.test($("#email").val()))
					{
						alert("Please enter a valid email address.");
						return false;
					}
					
					if ($("#password").val().length < 1)
					{
						alert("Please enter a password.");
						return false;
					}
					
					if ($("#password_retype").val().length < 1)
					{
						alert("Please retype your password in the Retype field.");
						return false;
					}
					
					if ($("#password").val() != $("#password_retype").val())
					{
						alert("The two passwords that were entered were not the same. Please retype your password.");
						return false;					
					}
					
					
					// length checks and further regexing
					if ($("#username").val().length > 15)
					{
						alert("Usernames must be less than 16 characters long.");
						return false;
					}
					
					if ($("#email").val().length > 63)
					{
						alert("The email address must be less than 64 characters long.");
						return false;
					}
					
					var usernameMatch = /^([a-z0-9_\-])+$/;
					if (!usernameMatch.test($("#username").val().toLowerCase()))
					{
						alert("Usernames may only contain letters and numbers, underscores (_) and hyphens (-). All usernames are lowercase.");
						return false;
					}
					
					// set up the link on the next page for 'log in and start working'
					$("#install-panel-loc").html('<a href="' + $("#configurl").val() + '">' + $("#configurl").val() + '</a>');
					
					hasValidatedLastPage = true;
					return true;			
					
				
				break;	
			
			
				default: // assume 0
					if (passedAllChecksNoWarn)
						window.setTimeout(function() {$("#requirements-overlay").show('slow');}, 550);
					return true;
				break;
			
			}
		
		}
		
		function requirementsMsg(reqId)
		{
		
			switch (reqId)
			
			{
			
				case 0:
					alert('Amalia requires at least PHP 5.2.3. Please ensure your server supports this release of PHP and is not configured to run .php scripts under PHP 4.\n\nPlease also make sure magic_quotes_gpc and magic_quotes_runtime are disabled. These options are incompatible with Amalia.');
				break;
				
				case 1:
					alert('Amalia requires PHP has support for CURL installed and enabled. You may need to contact your web host for assistance.');
				break;
				
				case 2:
					alert('Amalia needs to be able to write to the directory in which it was uploaded. Make sure the web server user "<?php echo htmlentities(strip_tags(exec("whoami")), ENT_QUOTES, 'UTF-8', false);?>" has write permissions on the directory.');
				break;
				
				case 3:
					alert('Amalia needs PHP\'s basic file management features to be available and working. It also requires MIME Content-Type detection to be supported (the function mime_content_type must be usable).');
				break;
				
				case 4:
					alert('Amalia needs GD support built in to PHP, including support for PNG and JPEG.');
				break;
				
				case 5:
					alert('Amalia needs your server to support mod_rewrite in order to operate exactly as intended. This feature is not required, however, and it will be switched off until you re-enable it manually later.\n\nYou may need to consult your web host for assistance to enable mod_rewrite on your server before enabling the "Pretty URLs" feature within Amalia.');
				break;
			
			
			}
		
		
		}
		
				
		</script>
		<title>Amalia</title>
	</head>
	<body class="installer">
	<div id="wrap">

		<div id="bookshelf">
			<div id="l-end"></div>
			<div id="books">
				<div id="off">
					<div id="off-r"><h1 id="h1"><a href="#" id="menu-button">Amalia</a></h1></div>
				</div>
				<span id="hello">Step <strong class="the_step">{$step}</strong> of <strong>4</strong></span>

			</div>
			<div id="r-end"></div>
		</div>
		<div id="gradient"></div>
		<div id="content">
<?php
}
function get_footer()
{
?>
		</div>

		<div id="gradient-bottom"></div>
		<div id="footer">
			<div id="fl-end"></div>
			<div id="f-mid">
				<div id="f-inner">&copy; 2007-<?php /* COPYRIGHT_MAX_DATE */ ?>2011 <a href="http://chrisvanpatten.com/">Chris Van Patten</a>, <a href="http://peter.upfold.org.uk/">Peter Upfold</a> and <a href="http://nicksampsell.com/">Nick Sampsell</a> &mdash; <strong>Version:</strong> <?php echo AMALIA_VERSION;?></div>
			</div>

			<div id="fr-end"></div>
		</div>
	</div>
	</body>
</html>
<?php
}