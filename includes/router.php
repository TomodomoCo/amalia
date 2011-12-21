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

// ------------------------------------------------------------------------

/**
 * Router
 *
 * @package	Amalia2
  * @category	Router
 * @author		Amalia Dev Team
 */
class amalia_router 
	{

	public function __construct() 
	{
		global $auth, $config;
		
		// only run scheduled tasks if the user is logged in, and not doing something important
		if ($auth->check_auth() && empty($_POST['submit']))
		{
			require('includes/scheduler.php');
			$scheduler = new amalia_scheduler();
		}
		
	}
	
	/* ok, so lets see, how am I going to do this....
	
	index.php/action/file/key:val/key:val/
	
	so, I need to break the url down into
	index.php (loader)
	/action/ determins which controller to use
	/file/ is what file we're going to use.  If outside root, convert slashses to hyphens in urls or whatever they normally are
	key:val allows for additional information.  For example, foo:bar would create a variable $foo = 'bar';
	I'm not sure how necessary the last is, but who knows, maybe someone could find a use for it
	
	*/
	
	public function load_controller() 
	{
			global $auth, $config;
			
			include('controller.php');
			
			$class = new amalia_controller();
			
		//assures user is logged in
		if ($auth->check_auth()) 
		{
			//checks to see if action superglobal is set
			if (isset($_GET['action']) && !empty($_GET['action'])) 
			{
				//goes to appropriate page
				switch($_GET['action']) 
				{
					//login
					case 'login':
						$class->login();
					break;
					//logout
					case 'logout':
						$class->logout();
					break;
					//create a page
					case 'create':
						$class->create();					
					break;
					case 'create-folder':
						$class->create_folder();
					break;
					case 'rename':
						$class->rename();
					break;
					case 'edit-title':
					// ajax inline page title editing
						$class->edit_title();
					break;
					break;
					//edit a page
					case 'edit':
						$class->edit();					
					break;
					//delete a  page
					case 'delete':
						$class->delete();					
					break;
					//file uploader
					case 'upload':
						$class->upload();					
					break;
					
					// mini browser for picking images from editor
					case 'minibrowser':
						$class->minibrowser();
					break;
					
					case 'miniuploader': // mini uploader for CKEditor inline uploading
						$class->miniuploader();
					break;
					
					case 'recyclebin': // recycle bin functions
						switch($_GET['do'])
						{
							
							case 'restore': // restore one file
							$class->restore_recycled();
							break;
							
							case 'delete': // delete one file
							$class->delete_recycled();
							break;
							
							case 'empty': // empty bin
							$class->empty_recyclebin();
							break;
						
							default: // show listing
							$class->recycle_bin();
							break;
						
						}

					break;		

					
					//administration panel
					case 'settings':
						switch($_GET['do']) 
						{
							case 'manage-users':
								$class->manage_users();
							break;
							case 'edit-user':
								// allow the editing of one's own fName the first time
								if ($_GET['hello-fname-editor'] == 'true')
								{
									$class->edit_user_fname();
								}
								else {
									$class->edit_user();
								}
							break;
							case 'create-user':
								$class->create_user();
							break;
							case 'delete-user':
								$class->delete_user();
							break;							
							case 'manage-plugins':
								$class->manage_plugins();
							break;
							case 'deactivate-plugin':
								$class->deactivate_plugin();
							break;
							case 'activate-plugin':
								$class->activate_plugin();
							break;
							case 'deactivate-all-plugins':
								$class->deactivate_all_plugins();
							break;
							case 'configure':
								$class->configure();
							break;
							case 'runscheduled':
								$class->force_run_scheduler();
							break;
							case 'clear-thumbnail-cache':
								$class->clear_thumbnail_cache();
							break;
							case 'delete-installer-directory':
								$class->delete_installer_directory();
							break;
							case 'first-aid':
								$class->first_aid();
							break;
							default:
								$class->first_aid();
							break;
						} //end admin switch
					break;
					
					//if all else fails, display the file manager
					default:
						$class->file_manager();
					break;
				
				} //end action switch
			}
			else if (isset($_GET['plugin']) && !empty($_GET['plugin']) &&
					isset($_GET['_e']) && !empty($_GET['_e']) )
			{
				// desired URL is a plugin endpoint.
				if (!load_endpoint($_GET['plugin'], $_GET['_e'], $_GET))
				{
					$class->file_manager(); // fall back to file manager if we can't load the endpoint
					//TODO: an error message instead??
				}
				else {
					//TODO: what to do here, on success?
					die();
				}
			
			} 
			else 
			{
				//if action is not set, display the file manager
				$class->file_manager();
			} //end action conditional
		} 
		else 
		{
			$class->login();
		} //end check_auth conditional

	} //end load_controller function
	
	public function view($view, $vars = null, $showHeaderAndFooter = true) {
	
		//make sure the $auth and $config variables are readable within the function
		global $auth, $config, $friendly_errors;
		
		//header('Content-Type: text/html; charset=UTF-8');
		
		//extract the variables ie $vars['hello'] = 'world' becomes $hello = 'world';
		if($vars !== null) 
		{
			extract($vars);
		}
		//ob_start(); //I don't think this is needed, but I'll keep it just to be safe
		if($auth->check_auth()) 
		{
			if ($showHeaderAndFooter) {
				//include the template header
				include('templates/header_in.php');
			}
			
			
			//if the template exists, load it
			if(file_exists($config['config_path'].'/includes/templates/'.safe_filename($view).'.php')) 
			{
				include('templates/'.safe_filename($view).'.php');
			} 
			else 
			{
				//otherwise throw a 404
				echo safe_filename($view);
				include('templates/404.php');
			}
			
			if ($showHeaderAndFooter) {			
				//include the footer
				include('templates/footer_in.php');
			}
			
		} 
		else 
		{
			//if the user is not logged in, display the login page
			include('templates/login.php');
		}
		//ob_flush(); //I don't think its needed but we'll keep it just in case.
		
		
	
	} //end view function
	
	

} //end router class

