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
?>

<p id="desc">Editing user <em><?php echo safe_plain($user['username']);?></em> with user ID <?php echo safe_plain($userid);?>.</p>
<?php
if (!defined('IN_AMALIA'))
{
	// template not to be viewed outside Amalia
	header('HTTP/1.0 403 Forbidden');
	die('Forbidden');
}

if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}

/* Expects $user to be a user record in the
format of admin.class.php::list_users()

Expects the called user ID (checked for numeric
validity) in $userid

*/

if (!empty($message))
{
	// validation errors as an unordered list will go here
	?><div><?php echo $message;?></div><?php	
}
?>
<form method="post" autocomplete="off" action="<?php print_internal_link('settings', 'do=edit-user&userid='.safe_plain($userid));?>">

<ul id="edituser-form" class="form">
	<li>
		<label class="label" for="username">Username</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="username" id="username" value="<?php echo safe_plain($user['username']);?>" maxlength="15" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="password">Password</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="password" name="password" id="password" value="<?php if (!empty($user['hashed_password'])) { ?>dummy_amalia<?php } ?>" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="password_retype">Retype</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="password" name="password_retype" id="password_retype" value="" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="password">Email</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="email" id="email" value="<?php echo safe_plain($user['email']);?>" maxlength="63" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
	<li>
		<label class="label" for="fname">Real Name</label>
		<div class="input">
			<div class="input-l"></div>
			<div class="input-m">
				<input type="text" name="fname" id="fname" value="<?php echo safe_plain($user['fname']);?>" maxlength="63" />
			</div>
			<div class="input-r"></div>
		</div>
	</li>
</ul>

<h2>Permissions</h2>

<?php

if ($userid != $_SESSION['amalia_auth']['id'])
{

?>

<p><input type="checkbox" value="permission_create-pages" name="permission_create-pages"<?php echo ($user['permissions']['create-pages']) ? ' checked="true" ': '';?> /> Create Pages </p>

<p><input type="checkbox" value="permission_edit-pages" name="permission_edit-pages"<?php echo ($user['permissions']['edit-pages']) ? ' checked="true" ': '';?> /> Edit Pages </p>

<p><input type="checkbox" value="permission_delete-files" name="permission_delete-files"<?php echo ($user['permissions']['delete-files']) ? ' checked="true" ': '';?> /> Delete Files </p>

<p><input type="checkbox" value="permission_upload-files" name="permission_upload-files"<?php echo ($user['permissions']['upload-files']) ? ' checked="true" ': '';?> /> Upload Files </p>

<p><input type="checkbox" value="permission_rename-files" name="permission_rename-files"<?php echo ($user['permissions']['rename-files']) ? ' checked="true" ': '';?> /> Rename Files </p>

<p><input type="checkbox" value="permission_manage-plugins" name="permission_manage-plugins"<?php echo ($user['permissions']['manage-plugins']) ? ' checked="true" ': '';?> /> Manage Plugins</p>

<p><input type="checkbox" value="permission_manage-users" name="permission_manage-users"<?php echo ($user['permissions']['manage-users']) ? ' checked="true" ' : '' ; ?>/> Manage Users</p>

<p><input type="checkbox" value="permission_configure-amalia" name="permission_configure-amalia"<?php echo ($user['permissions']['configure-amalia']) ? ' checked="true" ' : '';?>/> Configure Amalia</p>

<?php

}

else {

	?>
	<input type="hidden" name="should_not_edit_permissions" value="true" />
	<p>You cannot edit your own permissions.</p><?php

}


?>

<input type="submit" name="submit" value="Edit User" />
<input type="button" name="cancel" value="Cancel" onclick="window.location.replace('<?php print_internal_link('settings', 'do=manage-users', 'js');?>');" />

</form>