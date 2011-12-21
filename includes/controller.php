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
 * Controller Class
 *
 * @package	Amalia2
 * @category	Amalia Generation
 * @author		Amalia Dev Team
 */
 
if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}
set_exception_handler('catch_exceptions');
class amalia_controller 
{

	private $handler;
	private $handler2;
	private $sitemap;

	public function __construct() 
	{
		global $config;
		
		include('editor.class.php');
		include('useradmin.class.php');
		include('sitemap.php');
		
		if(isset($_SESSION['amalia_auth']))
		{
			if(!file_exists($config['site_path'].'/sitemap.xml'))
			{
				$this->handler = new amalia_sitemaps($config['site_path'].'/sitemap.xml');
				$this->handler->update_sitemap();
			}
		}
	}
	
	public function login() 
	{
		
		global $config, $auth, $route;
		
		if (isset($_SESSION['error'])) {
			echo $_SESSION['error'];
		}
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Login');
		
		
		$d['message'] = '';
		$d['tmt'] = time().'_'.microtime().rand(0,getrandmax()); 

		if (isset($_POST['action']) && $_POST['action'] == 'login')
		{
			
			// check formsec, reject otherwise
			$form_stm = base64_decode($_POST['stm']);
			
			// check for recent stm
			$rStm = explode('_', $form_stm);		
			
			// rebuild formsec
			$new_formsec = hash(HASH_ALGO, $form_stm.$config['salt']);
			
			if ($new_formsec != $_POST['formsec'] || $rStm[0]+60*10 < time()) // disallow login attempt if form load was >10 mins ago
			{
				$d['message'] = 'Unable to submit form due to security exception. Please try logging in again.';		
			}
			else
			{
			
				if (!$auth->login($_POST['username'], $_POST['password']))
				{
					
					// login failure
					$d['message'] = 'Your username or password was incorrect.';
					
				}
				else
				{
					// auth::login set the session, so redirect to the index page
					redirect();
				}
			}
				
		}
		
		$route->view('login',$d);



	}
	
	public function logout() 
	{
		global $auth;
		$auth->logout();
		redirect();
	}
	
	public function file_manager() 
	{
		//put the variables into the function scope
		global $route, $config;
		//opens directory and puts result into handler
		
		$base = safe_filename($config['site_path']);
		
		if (empty($base))
		{
			friendly_fatal('The Site Path is not set correctly and Amalia cannot display the file manager.');
			die();
		}

		// set title for header		
		define('AMALIA_PAGETITLE', 'File Browser');
		
		$this->handler = new amalia_editor($base);
		
		$_GET['dir'] = safe_filename($_GET['dir']);
		
		if($_GET['dir'] === '/')
		{
			$d['dir'] = '/';
		}
		else
		{
			$d['dir'] = $_GET['dir'].'/';
		}
		
		if (isset($_SESSION['msg']))
		{
			$d['msg'] = $_SESSION['msg'];
			unset($_SESSION['msg']);
		}
		
		// identify whether there are files in the recycle bin (for pretty recycle bin icon)
		require_once 'recyclebin.class.php';
		$recycler = new amalia_recyclebin;
		$d['recycled'] = $recycler->listRecycledFiles();
		
		$d['result'] = $this->handler->list_files($d['dir']);
		$this->handler2 = new amalia_sitemaps($config['site_path'].'/sitemap.xml');
		$d['sitemap'] = $this->handler2->getSitemap();
		$route->view('index',$d);
	}
	
	public function edit() 
	{
		//put variables into function scope
		global $route, $auth, $config;
		
		//checks to see if the user can edit pages
		// if yes, continue.  Otherwise end script.
		if(!$auth->has_perm('edit-pages')) 
		{
			$route->view('noperms');
			die();
		}
		
		if (!file_exists(safe_filename($config['site_path'].'/'.$_GET['file'])))
		{
			friendly_fatal('That file does not exist.');
			die();
		}
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Edit');
		
		//opens file and puts result into handler
		$this->handler = new amalia_editor(safe_filename($config['site_path'].'/'.$_GET['file']));
		
		//takes the handler data and prepares it for entry into the form.
		$d = $this->handler->prepare_for_editor();

		// some injection prevention

		$d['file'] = safe_filename($_GET['file']);
		$_POST['theme'] = safe_filename($_POST['theme']);
		
		$_POST['content'] = str_replace('AMALIA_CONTENT_END', '', $_POST['content']); // prevent PHP injection into pages
		
		//fetches all themes for use in editor
		$d['templates'] = $this->handler->fetch_all_themes();
		
		//checks to see if save button was clicked (or the CKEditor save button was clicked)
		if(isset($_POST['save']) || (strtolower($_SERVER['REQUEST_METHOD']) == 'post' && !empty($_POST['content'])  ) ) 
		{
			// run any post-save plugins to filter form data before we save
			// again, pretty uglys
			$hk_editor_is_saving_form = new Hook('editor_is_saving_form', $_POST, $modifiedData);
			$_POST['keywords'] = $modifiedData['keywords'];
			$_POST['description'] = $modifiedData['description'];
			$_POST['content'] = $modifiedData['content'];
						
			$_POST['content'] = str_replace('AMALIA_CONTENT_END', '', $_POST['content']); // prevent PHP injection into pages
			
			if (!in_array(str_replace('template_', '', $_POST['theme']), $d['templates']))
			// reject invalid templates and assume default
			{
				$_POST['theme'] = 'template_default.php';
			}
			

			//saves data into file.
			$result = $this->handler->save($_POST);
			if($result == true) 
			{
				$d['message'] = 'Your page was saved successfully';
			} 
			else 
			{
				$d['message'] = 'Your page could not be saved at this time.  Please try again.';
			}
			
			$this->handler = new amalia_sitemaps($config['site_path'].'/sitemap.xml');
			$this->handler->update_sitemap();	
			
			//redirects to form with new data.
			redirect('edit', 'file='.urlencode(safe_plain($d['file'])));
			die();
		}
		//displays the form
		$route->view('editor',$d);
		
		
	}

	
function create() 
{
		//put variables into function scope
		global $auth, $route, $config;
		
		$dir = safe_filename($_GET['dir']);
		
		// if the user supplied no custom file name (i.e. it wasn't displayed to them),
		// create a file name based on the URL
		if (!isset($_POST['filename']))
		{
			$_POST['filename'] = generate_filename_from_title($_POST['title']);
		}
		
		// now validate that filename		
		$_POST['filename'] = valid_new_filename($_POST['filename']); // strip extraneous from proposed filename
		
		// set title for header
		define('AMALIA_PAGETITLE', 'Create page');
		
		//checks to see if user has permission to create pages
		//if yes, continue, otherwise exit.
		if(!$auth->has_perm('create-pages')) 
		{
			$route->view('noperms', null, false);
			die();
		}
			
			//passes directory urlvar into view
			$d['dir'] = $dir;
			
			//checks to see if form was submitted
			if(isset($_POST['submit']) && !empty($_POST['submit']) && !$cannotCreateFile) 
			{
			
				if(empty($dir))
				{
					$dir = '/';
				}
				
				// standardise directory slashes
				$dir = ltrim($dir, '/');
				$dir = rtrim($dir, '/');
				$newFilename = ltrim($_POST['filename'], '/');
				$_POST['theme'] = 'default.php'; // set to use default theme, well, by default
				
				
				// does the requested directory exist?
				if (!file_exists($config['site_path'].'/'.$dir))
				{
					$cannotCreateFile = true;
					$_SESSION['msg'] = 'The directory "'.$dir.'" does not exist.';
				}
				else if (file_exists($config['site_path'].'/'.$dir.'/'.$_POST['filename']))
				{
					$cannotCreateFile = true;
					$_SESSION['msg'] = 'A file with that name already exists. Please choose a new name.';
				}
				else {					
				
					// is there an index.php? If not, make sure this file is created with that name.
					if (!file_exists($config['site_path'].'/'.$dir.'/index.php'))
					{
						$_POST['filename'] = 'index.php';
					}
					
					//prepare file for creation.
					$this->handler = new amalia_editor(safe_plain($config['site_path'].'/'.$dir.'/'.$_POST['filename']));
					
					//create the file
					$result = $this->handler->createFile($_POST);
				}
				
				if($result == true) {
					//if file was created successfully, redirect to editor
					
					$dirForRedirect = ($dir == '/' || empty($dir)) ? '/' : '/'.$dir.'/';
					
					redirect('edit', 'file='.urlencode(safe_plain($dirForRedirect.$_POST['filename'])), true, true);
					die();
					
				} 
				else 
				{
					//if file was not created, reload form with submitted values
					if (empty($_SESSION['msg'])) {
						$_SESSION['msg'] = 'Unable to save the new page. Please check the Permissions on your Site Path.';
					}
					redirect('dir', 'dir='.urlencode($dir));
					die();
				}
			
				
				$this->handler = new amalia_sitemaps($config['site_path'].'/sitemap.xml');
				$this->handler->update_sitemap();	
			}
				
		redirect('dir', 'dir='.urlencode($dir));
		die();
	}
	
	function upload() 
	{
	
		require('upload.class.php');
	
		//put variables into function scope
		global $auth, $route, $config;
		
		//check to see if user has permission to upload files
		if(!$auth->has_perm('upload-files')) 
		{
			$route->view('noperms', null, false);
			die();
		}
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Upload files');
		
		//hides header and footer from template
		$d['hidehead'] = true;
		$d['hidefoot'] = true;
			
		// trim slashes of sides of directory for standardisation
		if (empty($_GET['dir']) && !empty($_POST['dir']))
		{
			$dir = $_POST['dir'];
		}
		else if (empty($_POST['dir']) && !empty($_GET['dir']))
		{
			$dir = $_GET['dir'];
		}
		else {
			$dir = '';
		}
		$dir = rtrim(safe_filename($dir, '/'));
		$dir = ltrim($dir,  '/');
		
		// send to template	
		$d['dir'] = $dir;
		
		// protect the Config Path from editing
		if (strpos($config['site_path'].'/'.$dir.'/', $config['config_path'].'/') !== false)
		{
			$d['msg'] = 'You cannot upload files to the Config Path. Your files will be uploaded to the top level of your site.';
			$d['dir'] = '';
		}
		
		// check directory existence
		if (!file_exists($config['site_path'].'/'.$d['dir']))
		{
		
			$d['msg'] = 'The specified folder to upload the files in does not exist.';
			$d['dir'] = '';
				
			if ($_GET['type'] == 'simple')
			{
				$route->view('upload_fallback', $d, false);								
			}
			else
			{
				$route->view('upload', $d, false);
			}		
			
			die();
		}
			
		$isAjax = ($_POST['type'] == 'ajax') ? true : false;
		
		// has the file been uploaded?
		if ((isset($_POST['submit']) && !empty($_POST['submit'])) || !empty($_FILES))
		{

			if ($isAjax)
				header("Content-Type: application/json; charset=UTF-8");
			
			$destinationDirectory = $config['site_path'].'/'.$dir;
			
			// protect the Config Path from editing
			if (strpos($destinationDirectory.'/', $config['config_path'].'/') !== false)
			{
				if ($isAjax)
				{
					echo '{
							"type": "failure",
							"error_message": "You cannot upload files to the Config Path.",
					}
					';
					die();
				}
				else {
					header('HTTP/1.0 400 Bad Request');
					friendly_fatal('You cannot upload files to the Config Path.');
					die();
				}
			}


			try {
				$uploader = new amalia_uploader($_FILES, $destinationDirectory);			
			}
			catch (Exception $e)
			{
				if (!$isAjax)
				{
					header('HTTP/1.0 500 Internal Server Error');
					friendly_fatal('Unable to create an uploader. Does the directory exist? '.$e);
					die();
				}
				else {
					echo '{
							"type": "failure",
							"error_message": "Unable to create an uploader. Does the directory exist?",
					}
					';
					die();
				}
			}
			
			$validateResult = $uploader->validateFiles();
			
			if ($validateResult === true)
			{
				$uploader->writeValidatedFiles();	

				// no errors, so redirect back to folder, or give out the all good signal
				if (!$isAjax)
				{
					redirect('dir', 'dir='.safe_filename($_GET['dir']), true, true);
					die();
				}
				else
				{
					echo '{
						"type": "success",
						"message": "All uploads successful.",
					}';
					die();
				}	
			}
			else
			{				
				if (is_array($validateResult) && count($validateResult) > 0)
				{
				
					if (!$isAjax)
					{					
						$errorstring = '<ul>';
						
						foreach ($validateResult as $error)
						{
							$errorstring .= '<li> '.$error.'</li>'."\n";
						}
						
						$errorstring .= 'All valid files uploaded';
						
						$errorstring .= '</ul>';
						
						echo 'Some errors were experienced uploading files. '."\n".$errorstring;
					}
					else {
					
						$errorstring = '';
					
						foreach ($validateResult as $error)
						{
							$errorstring .= ' '.$error."\n";
						}
					
					
						echo '{
							"type": "failure",
							"error_message": "'.safe_plain($errorstring).'",
						}';					
					}
									
				}
			}		
			
			// attempt to upload all files that did validate.
			// If there are none, nothing will happen!
			
			$uploader->writeValidatedFiles();				
			die();
		
		}
		
		// no upload yet, just show form
		
		if ($_GET['type'] == 'simple')
		{
			$route->view('upload_fallback', $d, false);								
		}
		else
		{
			$route->view('upload', $d, false);
		}
		

				
		
	}
	
	public function minibrowser()
	{
		// mini browser for uploading and choosing images and pages from within the editor/CKEditor
	
		global $route, $auth, $config;
		
		if (!$auth->has_perm('edit-pages'))
		{
			$route->view('noperms');
			die();	
		}
		
		$d['dir'] = safe_filename($_GET['dir']);
		$d['dir'] = rtrim($d['dir'], '/');
		$d['dir'] = ltrim($d['dir'], '/');
		
		if (!empty($d['dir']))
		{
			// check directory exists, and reset to zero if it doesn't
			
			if (!file_exists($config['site_path'].'/'.$d['dir']))
			{
				$d['dir'] = '';			
			}
		
		}
		
		// get the file listing via the editor class
		$handler = new amalia_editor(safe_plain($config['site_path'].'/'.$d['dir']));
		$fileList = $handler->list_files('/'.$d['dir']);
		
		$d['files'] = $fileList;
		
		if (!empty($_GET['filetype']))
		{
			$d['desiredFiletype'] = $_GET['filetype'];
		}
		else {
			$d['desiredFiletype'] = 'image'; // assumed to be image
		}
				
		$route->view('minibrowser', $d, false);
	
	}
	
	public function miniuploader()
	{
	
		global $route, $auth, $config;
		
		require('upload.class.php');
		
		if (!$auth->has_perm('upload-files'))
		{
			$route->view('noperms');
			die();
		}
		
		$dir = urldecode($_GET['dir']);
		$dir = ltrim($dir, '/');
		$dir = rtrim($dir, '/');
		$dir = safe_filename($dir);	
		
		// FILE HAS BEEN UPLOADED, SO PROCESS
		if (isset($_FILES['upload']))
		{
			
			if (!isset($_FILES) || empty($_FILES)) {
			
				?>No file was sent for upload.<?php		
				die();
			
			}
			
			// check directory existence
			if (!file_exists($config['site_path'].'/'.$dir))
			{
			
				?>The specified folder to upload the files in does not exist.</script></body></html><?php		
				die();
			}
			
			
			$destinationDirectory = $config['site_path'].'/'.$dir;
					
			// protect the Config Path from editing
			if (strpos($destinationDirectory.'/', $config['config_path'].'/') !== false)
			{
			
				?>You cannot upload files to the Config Path<?php		
				die();
			
			}
			
			try {
			
				$uploader = new amalia_uploader($_FILES, $destinationDirectory);		
			}
			catch (Exception $e)
			{
				?>Unable to create an uploader. Does the directory exist?<?php		
				die();	
			}
			
			$validateResult = $uploader->validateFiles();
			
			if ($validateResult === true)
			{
				$uploader->writeValidatedFiles();
				
				$dirlink = '/'.$dir.'/';
				$dirlink = str_replace('//', '/', $dirlink);
				
				// no errors, so get the new file URL and send back to editor
				$newFileURL = safe_filename($config['site_url'].$dirlink.ltrim($uploader->lastFileName, '/'));
				
				$newTitle = safe_plain($_POST['title']);
							
				createAmaliaTitleFile($dirlinkComp.ltrim($uploader->lastFileName, '/'), $newTitle);			
				
				// show template, passing it the new URL so it can notify TinyMCE
				$d['uploadCompleted'] = true;
				$d['newFileURL'] = $newFileURL;	
				$d['newTitle'] = $newTitle;						
				$route->view('miniuploader', $d, false);
				die();
			
			}
			
			else {
			
				echo safe_plain($validateResult);
				die();
			
			}	
		}
		// NO FILE YET UPLOADED, SHOW A FORM
		else {
		
			$d['dir'] = $dir;
		
			$route->view('miniuploader', $d, false);
		}
	
	}
	
	public function edit_title()
	{
	
		// backend for inline page title editing
		
		global $route, $auth, $config;
		
		// first things first, permission check
		if (!$auth->has_perm('edit-pages'))
		{
			header('HTTP/1.0 403 Forbidden');
			die('Your user does not have the required permissions to edit files.');
		
		}
		
		/* example data
		Array
			(
			    [update_value] => ofddfgfgd
			    [element_id] => /secondpage.php
			    [original_html] => Second Page
			)
			
			so element_id contains the page url
			update_value contains the new page title
			original_html contains the original title in full text for restoring if needed
		*/
		
		
		// work out which file we want from element_id
		
		$relativeFilename = safe_filename($_POST['element_id']);
		$filename = rtrim($config['site_path'], '/').'/'.ltrim($relativeFilename, '/'); // concat with site path to get FQ name
		$newTitle = safe_plain($_POST['update_value']);
		$originalTitle = safe_plain($_POST['original_html']);
		
		// check existence of the file
		if (!file_exists($filename))
		{
			header('HTTP/1.0 400 Bad Request');
			die('The page does not appear to exist. Please check that it has not been deleted.');					
		}
		
		// protect the Config Path from editing
		if (strpos($filename, $config['config_path'].'/') !== false)
		{
			header('HTTP/1.0 400 Bad Request');
			die('You cannot edit files in the Config Path.');
		}
		
		// attempt to open the file for reading, pull in all the data
		$fh = @fopen($filename, 'r');
		if (!$fh)
		{
			header('HTTP/1.0 500 Internal Server Error');
			die('Unable to open the page. Please check the permissions on your Site Path.');		
		}
		
		$fileContents = fread($fh, filesize($filename));
		fclose($fh);
		
	
		// craft a new title line
		$titleLine = '$amalia_title = \''.htmlentities($newTitle, ENT_QUOTES, 'UTF-8', false).'\';';
		
		// search for the existing title line and remove it
		$fileAsArray = explode("\n", $fileContents);
		$oldTitleLineNo = false;
				
		if (is_array($fileAsArray) && count($fileAsArray) > 0)
		{
			foreach($fileAsArray as $lineNo => $line)
			{
				if (preg_match('/\$amalia_title = \'(.*)\';/', $line)) {
					$oldTitleLineNo = $lineNo;
					break;				
				}
			}
		}
		
		if ($oldTitleLineNo === false) // we didn't find the old title line!
		{
			// therefore we assume it is a non-page and instead update the userfiles/filedata title copy
			
			createAmaliaTitleFile($relativeFilename, $newTitle);
			
		}
		else {

			// is a page, so write this title back to the page file		
			
			$fileAsArray[$oldTitleLineNo] = $titleLine; // replace title line, we'll now recombine file
			$newFileContents = implode("\n", $fileAsArray);
			
			// open file for writing, and dump it all back to disk with new title line
			$fh = @fopen($filename, 'w');
			if (!$fh)
			{
				header('HTTP/1.0 500 Internal Server Error');
				die('Unable to save the new title to the page. Please check the permissions on your Site Path.');		
			}
			
			fwrite($fh, $newFileContents);
			fclose($fh);
	
			// update the sitemap		
			$this->handler = new amalia_sitemaps($config['site_path'].'/sitemap.xml');
			$this->handler->update_sitemap();
			
		}
		
		
		// I think we're done
		die(safe_plain($newTitle));
	
	}
	
	public function create_folder()
	{
	
		global $route, $auth, $config;
		
		if (!$auth->has_perm('create-pages'))
		{
			$route->view('noperms', null, false);
			die();		
		}
		
		// only alphanum allowed for folder
		$newFolderName = valid_new_filename($_POST['folder_name'], '');
		$newFolderTitle = safe_plain($_POST['folder_name']);
		$dir = safe_filename($_POST['dir']);
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Create folder');
		
		if (isset($_POST['submit']) && !empty($_POST['submit']))
		{
			// form has been submitted, process the new folder create		
			
			// check formsec to ensure form was genuinely submitted from the create folder page
			if (empty($_POST['formsec']) || empty($_POST['stm']))
			{
				header("HTTP/1.1 400 Bad Request");
				die("Form was not submitted properly.");
			}
			
			// recreate formsec from stored stm and salt, then compare with POST-provided formsec
			$stm = base64_decode($_POST['stm']);
		
			// check for recent stm
			$rStm = explode('_', $stm);
			if ($rStm[0]+60*60*3 < time()) // more than 3 hrs old? throw out!	
			{
				header("HTTP/1.1 400 Bad Request");
				die("Form was not submitted properly.");
			}
					
			$newFormsec = hash(HASH_ALGO, $stm.$config['salt']);
			
			if ($newFormsec != $_POST['formsec'])
			{
				header("HTTP/1.1 400 Bad Request");
				die("Form was not submitted properly.");
			}
			// finished checking formsec
			
			// normal data validation
			
			if (!isset($_POST['folder_name']) || empty($_POST['folder_name']))
			{
				$_SESSION['msg'] = 'You did not enter a name for the new folder.';
				redirect('dir', 'dir='.urlencode($dir));
				die();
			}
			
			$dir = ltrim($dir, '/');
			
			// protect the Config Path from new folder creation
			if (strpos($config['site_path'].'/'.$dir.'/', $config['config_path'].'/') !== false)
			{
				$_SESSION['msg'] = 'You cannot create a folder in the Config Path.';
				redirect('dir', 'dir='.urlencode($dir));
				die();
			}
				
			// check existence of parent dir
			if (!file_exists($config['site_path'].'/'.$dir))
			{
			
				$_SESSION['msg'] = 'That parent directory does not exist.';
				redirect('dir', 'dir='.urlencode($dir));
				die();
					
			}
			
			// does the folder already exist?
			if (file_exists($config['site_path'].'/'.$dir.'/'.$newFolderName))
			{
				$_SESSION['msg'] = 'A folder with that name already exists. Please choose a new name.';
				redirect('dir', 'dir='.urlencode($dir));
				die();
			}
			
			// attempt the folder create (recursively for any now-deleted parent dirs)
			
			try {
			
				if (!@mkdir($config['site_path'].'/'.$dir.'/'.$newFolderName, 0755, true ))
				{
				
					$_SESSION['msg'] = 'Unable to create the new folder. Please check the permissions on your Site Path.';
					redirect('dir', 'dir='.urlencode($dir));
					die();						
				
				}
				
				else {
				
					ltrim(rtrim($newFolderName, '/'), '/');
					ltrim(rtrim($dir, '/'), '/');	
					
					$dirForRedirect = ($dir == '/' || empty($dir)) ? '/' : '/'.$dir.'/';
					
					// set the folder title
					createAmaliaTitleFile($dirForRedirect.$newFolderName, $newFolderTitle);
					
					// successful create, redir to browser in new folder
					redirect('dir','dir='.urlencode($dirForRedirect.$newFolderName), true, true);
				}
				
			
			}
			
			catch (Exception $e)
			{
			
					// failed
					$_SESSION['msg'] = 'Unable to create the new folder. Please check the permissions on your Site Path.';
					redirect('dir', 'dir='.urlencode($dir));
					die();							
			
			}		
						
		//TODO: remove this legacy code
		} else {
		
			// show form
			
			// check existence of parent dir
			$_GET['dir'] = ltrim($_GET['dir'], '/');
			if (!file_exists($config['site_path'].'/'.safe_filename($_GET['dir'])))
			{
			
				$d['msg'] = 'That parent directory does not exist.';
				// deliberately not give dir back, it's bad!
				$d['folder_name'] = '';
				$route->view('create_folder', $d, false);
				die();
					
			}
			
			$d['dir'] = $_GET['dir'];
			$d['folder_name'] = '';
			$route->view('create_folder', $d, false);	
			
		
		}
	
	}
	
	public function rename()
	{
	//put variables into function scope
		global $route, $auth, $config;
		
		if(!$auth->has_perm('rename-files')) 
		{
			$route->view('noperms');
			die();
		}
		
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
		{
			// process the actual rename
			
			header('Content-Type: application/json; charset=UTF-8');
			
			$fileToRename = safe_filename($_POST['file']);
			
			if ($fileToRename == '/' || empty($fileToRename))
			{
				jsonFailWithError('You cannot rename the base directory.');
				return false;
			}
			
			$fileToRename = ltrim($fileToRename, '/');
			
			// protect the Config Path from editing
			if (strpos($config['site_path'].'/'.$fileToRename.'/', $config['config_path'].'/') !== false)
			{
				jsonFailWithError('You cannot rename files in the Config Path.');
				return false;
			}
			
			// do some processing on the file extension and containing directory to ensure it stays the same
			$oldPathinfo = pathinfo($config['site_path'].'/'.$fileToRename);
			$oldExtension = $oldPathinfo['extension'];
			$oldDir = $oldPathinfo['dirname'];
			// we just want the relative path for oldDir, strip out site path info
			$oldDir = str_replace($config['site_path'], '', $oldDir);
			$oldDir = ltrim(rtrim($oldDir, '/'), '/');
			
			$newName = valid_new_filename($_POST['newname'], $oldExtension);
		
			// check new name does not already exist
			if (file_exists($config['site_path'].'/'.$oldDir.'/'.$newName))
			{
				jsonFailWithError("A file with that new name already exists.");
				return false;
			
			}
			
			// does the old file exist?
			if (!file_exists($config['site_path'].'/'.$fileToRename))
			{
				jsonFailWithError("That file does not exist to rename.");
				return false;		
			}
			
			// attempt the renaming
			try {
			
				if (rename($config['site_path'].'/'.$fileToRename, $config['site_path'].'/'.$oldDir.'/'.$newName))
				{
				
					// update the sitemap		
					$this->handler = new amalia_sitemaps($config['site_path'].'/sitemap.xml');
					$this->handler->update_sitemap();	
				
					// success message
					?>{
		"type": "success",
		"message": "Operation completed successfully.",
		"new_name": "<?php echo str_replace('"', '', safe_plain($newName));?>"
	}<?php
				}
				
				else {
				
					jsonFailWithError("Unable to execute the rename operation.");
				}		
			}	
			catch (Exception $e)
			{		
				jsonFailWithError("Unable to execute the rename operation.");			
			}
		} // end process the rename
		
		else {
		
			//TODO: remove me, I am now redundant
			
			
			// show the thickbox form
			define('AMALIA_PAGETITLE', 'Rename');
			
			if (empty($_GET['file']) || $_GET['file'] == '/')
			{
				redirect('dir');
				die();
			}
			
			$d['filename'] = $_GET['file'];
						
			$route->view('rename', $d, false);		
		
		}
			
	}
	
	public function delete()
	{
	
		//put variables into function scope
		global $auth, $route, $config;
		
		//check to see if user has permission to delete files
		if(!$auth->has_perm('delete-files')) 
		{
			$route->view('noperms');
			die();
		}
		
		// enforce POST
		if (strtolower($_SERVER['REQUEST_METHOD']) != 'post')
		{
			die('Form must be submitted via POST in order to delete a file.');
		}
		
		$file = safe_filename($_GET['file']);
		
		if (empty($file))
		{
			die('No file specified for delete.');
		}
		
		// protect the Config Path from deletion
		if (strpos($config['site_path'].'/'.ltrim($file, '/').'/', $config['config_path'].'/') !== false)
		{
			die('You cannot edit files in the Config Path.');
		}
		
		require_once 'recyclebin.class.php';
		$recycler = new amalia_recyclebin();	
		$recycler->recycleFile($file);
		
		// redirect back to dir
		
		// get current dir by slicing off last element of the file path, just like getting one dir up					
		$filePath = urldecode(safe_filename($_GET['file']));
		$filePathExpl = explode('/', $filePath);
		
		if (is_array($filePathExpl) && count($filePathExpl) > 0)
		{
			$filePathExpl = array_slice($filePathExpl, 0, count($filePathExpl)-1);
			$dirlink = implode('/', $filePathExpl);
			$dirlink = urlencode(safe_filename($dirlink));
		}		
		redirect('dir', 'dir='.$dirlink);
	
	}
	
	/*
	OLD DELETE/RECYCLE FUNCTION
	public function delete() 
	{
		//put variables into function scope
		global $auth, $route, $config;
		
		//check to see if user has permission to upload files
		if(!$auth->has_perm('delete-files')) 
		{
			$route->view('noperms');
			die();
		}	
		
		//move the file to the recycle bin
		$this->handler = new amalia_editor(safe_plain($config['site_path'].$_GET['file']),array('new_location'=>$config['config_path'].'/userfiles/recycle'.$_GET['file']));

		//actually move the file
		$result = $this->handler->delete();
		
		//if the result is good, return success message
		if($result === true) 
		{
			$_SESSION['message']['success'] = safe_plain($_GET['file']) .' was moved to the recycle bin where it will remain for '. $config['recycle_days'] .' days.  After the '. $config['recycle_days'] .' days, the file will be deleted and will no longer be recoverable.';
			
			$this->handler = new amalia_sitemaps($config['site_path'].'/sitemap.xml');
			$this->handler->update_sitemap();	
			
		} 
		else 
		{
			//otherwise return error message.
			$_SESSION['message']['error'] = 'The file could not be moved to the recycle bin.  Please try again later.';
		}
		//redirect to file manager.
		
		// get current dir by slicing off last element of the file path, just like getting one dir up
							
		$filePath = urldecode(safe_filename($_GET['file']));
		$filePathExpl = explode('/', $filePath);
		
		if (is_array($filePathExpl) && count($filePathExpl) > 0)
		{
			$filePathExpl = array_slice($filePathExpl, 0, count($filePathExpl)-1);
			$dirlink = implode('/', $filePathExpl);
			$dirlink = urlencode(safe_filename($dirlink));
		}		
		redirect('dir', 'dir='.$dirlink);
	}*/
	
	
	
	/* Now for the administration functions */
	
	
	public function admin() 
	{
		//TODO: is this obsoleted?
		
		
		//put the variables into the function scope
		global $config, $auth, $route;
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Settings: Status');
		
		// pull permissions so the admin view can only show relevant admin panels
		$d['perm']['manage-users'] = $auth->has_perm('manage-users');
		$d['perm']['configure-amalia'] = $auth->has_perm('configure-amalia');
		$d['perm']['manage-plugins'] = $auth->has_perm('manage-plugins');
			
		$route->view('admin/admin', $d);
	
	}
	
	public function manage_users() 
	{
		//put the variables into the function scope
		global $config, $auth, $route;
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Settings: Manage users');
				
		if(!$auth->has_perm('manage-users')) 
		{
			$route->view('noperms');
			die();
		}
		
		
		// did the user submit a multiple delete form?
		if (isset($_POST['delete-checked-users']) && $_POST['delete-checked-users'] == 'true')
		{
		
			if (is_array($_POST['all']) && count($_POST['all']) > 0)
			{ // loop through checked boxes of user IDs
			
				$this->handler = new amalia_useradmin;
		
				foreach ($_POST['all'] as $userIdToDelete)
				{
				
					$result = $this->handler->delete_user_record($userIdToDelete);
				
				}
			
			}	
			
			redirect('settings', 'do=manage-users');
			die();
		}
		
		// pull permissions so the admin view can only show relevant admin panels
		$d['perm']['manage-users'] = $auth->has_perm('manage-users');
		$d['perm']['configure-amalia'] = $auth->has_perm('configure-amalia');
		$d['perm']['manage-plugins'] = $auth->has_perm('manage-plugins');
			
		$this->handler = new amalia_useradmin;
		$users = $this->handler->list_users();

		// list_users now automatically gets the data in a friendly array format
		// with named keys (like 'id', 'username' etc.)
						
		$d['users'] = $users;
						
		$d['page'] = 'manage_users';
		$route->view('admin/manage_users',$d);
	}
	
	public function edit_user_fname()
	{
	
		global $config, $auth, $route;
		
		// PERMISSIONS NOTICE
		// this particular function is designed to be used by all users,
		// hence no permissions check, but only on their own user account.
		
		if (empty($_POST['fname']))
		{
			header('HTTP/1.0 400 Bad Request');
			die('You must enter a name.');
		}
		
		$userID = $_SESSION['amalia_auth']['id']; // act on the user's own ID
		
		$this->handler = new amalia_useradmin;
		
		if ($this->handler->set_user_fname($userID, $_POST['fname']))
		{
			die('Success.');
		}
		else {
			header('HTTP/1.0 400 Bad Request');
			die('Unable to set your name. Please check for Permissions errors.');
		}
	
	}
	
	public function edit_user() 
	{
		//put the variables into the function scope
		global $config, $auth, $route;
		
		if(!$auth->has_perm('manage-users')) 
		{
			$route->view('noperms');
			die();
		}
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Settings: Edit user');	
		
		// pull permissions so the admin view can only show relevant admin panels
		$d['perm']['manage-users'] = $auth->has_perm('manage-users');
		$d['perm']['configure-amalia'] = $auth->has_perm('configure-amalia');
		$d['perm']['manage-plugins'] = $auth->has_perm('manage-plugins');
		
		// make sure any user id is what we're expecting
		$_GET['userid'] = preg_replace('/[^0-9]/', '', $_GET['userid']);

		if (empty($_GET['userid']) && $_GET['userid'] !== '0')
		{
			friendly_fatal('Invalid or empty user ID was supplied for edit.');
			die();		
		}
		
		$d['userid'] = $_GET['userid'];
		
		$this->handler = new amalia_useradmin;
		$users = $this->handler->list_users();
		
		$userToEdit = array();		
		
		// get the individual user we care about into one var to send to form
		if (is_array($users) && count($users) > 0)
		{
			foreach($users as $user)
			{
				if ($user['id'] == $d['userid'])
				{
					$userToEdit = $user;
					break;
				
				}
			}		
		}
		
		if (empty($userToEdit))
		{
			// unable to get the specified user id
			friendly_fatal('Unable to find the user with that user ID.');
			die();		
		}
		
		// we now have this user in $userToEdit, but we also want its permissions
		$permissions = $this->handler->get_permissions_for_user($d['userid']);
		
		
		$d['user'] = $userToEdit;
		$d['user']['permissions'] = $permissions;
		
		if (!empty($_POST['submit']))
		{
			// process form
			
			$validateResult = $this->handler->validate_user_form($_POST);
			
			if ($validateResult === true)
			{
				// no errors, write the new user record to disk
				
				$this->handler->update_user_record($d['userid'], $_POST);

				redirect('settings', 'do=manage-users');
				die();				

			}
			else {
				// display the form back with errors in tow
				if (is_array($validateResult) && count($validateResult) > 0)
				{
					$message = '<ul>';
					foreach($validateResult as $error)
					{
						$message .= '<li>'.$error.'</li>';
					
					}
					$message .=  '</ul>';				
				}
				
				$d['message'] = $message;
				$d['page'] = 'edit_user';
				$route->view('admin/admin',$d);
				
				die();
			
			}
		}

		$d['page'] = 'edit_user';
		$route->view('admin/admin',$d);
	
	}
	public function create_user() 
	{
		//put the variables into the function scope
		global $config, $auth, $route;
		
		if(!$auth->has_perm('manage-users')) 
		{
			$route->view('noperms');
			die();
		}	
		
		// pull permissions so the admin view can only show relevant admin panels
		$d['perm']['manage-users'] = $auth->has_perm('manage-users');
		$d['perm']['configure-amalia'] = $auth->has_perm('configure-amalia');
		$d['perm']['manage-plugins'] = $auth->has_perm('manage-plugins');
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Settings: Create user');
		
		if (!empty($_POST['submit']))
		{		
			// process form
			$this->handler = new amalia_useradmin;
			
			$validateResult = $this->handler->validate_user_form($_POST);
			
			if ($validateResult === true)
			{
				// no errors, write the new user record to disk
				
				$this->handler->new_user_record($_POST);

				redirect('settings', 'do=manage-users');
				die();				

			}
			else {
				// display the form back with errors in tow
				if (is_array($validateResult) && count($validateResult) > 0)
				{
					$message = '<ul>';
					foreach($validateResult as $error)
					{
						$message .= '<li>'.$error.'</li>';
					
					}
					$message .=  '</ul>';				
				}
				
				$d['user'] = $_POST;
				
				// reconstruct the selected permissions on the new form
				// so that the user doesn't have to reselect them
				$d['user']['permissions']['create-pages'] = ($_POST['permission_create-pages'] == 'permission_create-pages') ? true : false;
				$d['user']['permissions']['edit-pages'] = ($_POST['permission_edit-pages'] == 'permission_edit-pages') ? true : false;
				$d['user']['permissions']['delete-files'] = ($_POST['permission_delete-files'] == 'permission_delete-files') ? true : false;
				$d['user']['permissions']['upload-files'] = ($_POST['permission_upload-files'] == 'permission_upload-files') ? true : false;
				$d['user']['permissions']['rename-files'] = ($_POST['permission_rename-files'] == 'permission_rename-files') ? true : false;				
				$d['user']['permissions']['manage-plugins'] = ($_POST['permission_manage-plugins'] == 'permission_manage-plugins') ? true : false;
				$d['user']['permissions']['manage-users'] = ($_POST['permission_manage-users'] == 'permission_manage-users') ? true : false;
				$d['user']['permissions']['configure-amalia'] = ($_POST['permission_configure-amalia'] == 'permission_configure-amalia') ? true : false;
				
				$d['message'] = $message;
				$d['page'] = 'create_user';
				$route->view('admin/admin',$d);		
				die();
			
			}
			
			
		}
		
	
		$d['page'] = 'create_user';
		$route->view('admin/admin',$d);
	
	}

	public function delete_user() 
	{
		//put the variables into the function scope
		global $config, $auth, $route;
		
		if(!$auth->has_perm('manage-users')) 
		{
			$route->view('noperms');
			die();
		}

		$this->handler = new amalia_useradmin;
		
		$result = $this->handler->delete_user_record($_GET['userid']);
		
		redirect('settings', 'do=manage-users');
		
	}
	
	public function manage_plugins() 
	{
		//put the variables into the function scope
		global $config, $auth, $route, $h2F, $plugins;
		
		if(!$auth->has_perm('manage-plugins')) 
		{
			$route->view('noperms');
			die();
		}
		
		// pull permissions so the admin view can only show relevant admin panels
		$d['perm']['manage-users'] = $auth->has_perm('manage-users');
		$d['perm']['configure-amalia'] = $auth->has_perm('configure-amalia');
		$d['perm']['manage-plugins'] = $auth->has_perm('manage-plugins');	
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Settings: Manage plugins');
		
		// go and get the list of enabled plugins

		// create hooks to functions if not already done
		if (!is_array($h2F) || count($h2F) < 1)
		{
			instantiate_enabled_plugins();			
		}
		
		// instantiating the plugins now means that $plugins
		// globvar contains all the *activated* plugin objects, from
		// which we can derive the information
		
		$d['pluginInfo'] = array();
		$i = 0; //counter
		
		foreach($plugins as $pn)
		{
			
			$d['pluginInfo'][$i]['identifier'] = $pn->identifier;
			$d['pluginInfo'][$i]['friendlyName'] = $pn->friendlyName;
			$d['pluginInfo'][$i]['description'] = $pn->description;
			$d['pluginInfo'][$i]['version'] = $pn->version;
			$d['pluginInfo'][$i]['author'] = $pn->author;
			$d['pluginInfo'][$i]['company'] = $pn->company;
			$d['pluginInfo'][$i]['copyright'] = $pn->copyright;
			$d['pluginInfo'][$i]['url'] = $pn->url;
			$d['pluginInfo'][$i]['isActivated'] = true;
			
			$i++;
			
		}		
		
		// look up disabled plugins
		$disabledPlugins = enumerate_disabled_plugins();
		
		if (is_array($disabledPlugins) && count($disabledPlugins) > 0)
		{
			foreach ($disabledPlugins as $pn)
			{
				$d['pluginInfo'][$i] = $pn;
				$d['pluginInfo'][$i]['isActivated'] = false;
				$i++;
			}						
		}
		
		$d['msg'] = safe_plain($_GET['msg']);
		
		$d['page'] = 'manage_plugins';
		$route->view('admin/admin',$d);
	}
	
	public function deactivate_plugin() 
	{
				
		global $config, $auth, $route;
		
		$isAjax = ($_POST['type'] == 'ajax') ? true : false; // is this request an Ajax one?
		
		if(!$auth->has_perm('manage-plugins')) 
		{
			if ($isAjax) {
				header("HTTP/1.1 403 Forbidden");			}
			else {
				$route->view('noperms');
			}
				
			die();
		}
		
		if (empty($_POST['plugin']))
		{
			header("HTTP/1.1 400 Bad Request");
			die("No plugin specified.");
		}		
		
		$identifier = safe_plugin_identifier($_POST['plugin']);
		
		// check formsec to ensure form was genuinely submitted from the plugins page
		if (empty($_POST['formsec']) || empty($_POST['stm']))
		{
			header("HTTP/1.1 400 Bad Request");
			die("Form was not submitted properly.");
		}
		
		// recreate formsec from stored stm and salt, then compare with POST-provided formsec
		$stm = base64_decode($_POST['stm']);
		$newFormsec = hash(HASH_ALGO, $stm.$config['salt']);
		
		if ($newFormsec != $_POST['formsec'])
		{
			header("HTTP/1.1 400 Bad Request");
			die("Form was not submitted properly.");
		}
		// finished checking formsec
		
		if (deactivate_plugin($identifier))
		{
			if ($isAjax)
			{
				die("Plugin deactivated.");
			}
			else
			{
				header('Location: '.$config['config_url'].'/index.php?action=admin&do=manage-plugins&msg=dacd');
				die();
			}			
		}
		else
		{
			if ($isAjax)
			{
				header("HTTP/1.1 500 Internal Server Error");
				die();
			}
			else {
				header('Location: '.$config['config_url'].'/index.php?action=admin&do=manage-plugins&msg=dacf');
				die();
			}
		}	
	}
	
	public function activate_plugin() 
	{

		global $config, $auth, $route;
		
		$isAjax = ($_POST['type'] == 'ajax') ? true : false; // is this request an Ajax one?
		
		if(!$auth->has_perm('manage-plugins')) 
		{
			if ($isAjax) {
				header("HTTP/1.1 403 Forbidden");			}
			else {
				$route->view('noperms');
			}
				
			die();
		}
		
		if (empty($_POST['plugin']))
		{
			header("HTTP/1.1 400 Bad Request");
			die("No plugin specified.");
		}		
		
		$identifier = safe_plugin_identifier($_POST['plugin']);
		
		// check formsec to ensure form was genuinely submitted from the plugins page
		if (empty($_POST['formsec']) || empty($_POST['stm']))
		{
			header("HTTP/1.1 400 Bad Request");
			die("Form was not submitted properly.");
		}
		
		// recreate formsec from stored stm and salt, then compare with POST-provided formsec
		$stm = base64_decode($_POST['stm']);
		$newFormsec = hash(HASH_ALGO, $stm.$config['salt']);
		
		if ($newFormsec != $_POST['formsec'])
		{
			header("HTTP/1.1 400 Bad Request");
			die("Form was not submitted properly.");
		}
		// finished checking formsec
		
		if (activate_plugin($identifier))
		{
			if ($isAjax)
			{
				die("Plugin activated.");
			}
			else
			{
				header('Location: '.$config['config_url'].'/index.php?action=admin&do=manage-plugins&msg=actd');
				die();
			}			
		}
		else
		{
			if ($isAjax)
			{
				header("HTTP/1.1 500 Internal Server Error");
				die("Could not enable plugin.");
			}
			else {
				header('Location: '.$config['config_url'].'/index.php?action=admin&do=manage-plugins&msg=actf');
				die();
			}
		}	
	}
	
	public function deactivate_all_plugins()
	{
		
		global $config, $auth, $route;
		
		if(!$auth->has_perm('manage-plugins')) {
			$route->view('noperms');
			die();
		}
		
		// check formsec to ensure form was genuinely submitted from the plugins page
		if (empty($_POST['formsec']) || empty($_POST['stm']))
		{
			die("Form was not submitted properly.");
		}
		
		// recreate formsec from stored stm and salt, then compare with POST-provided formsec
		$stm = base64_decode($_POST['stm']);
		$newFormsec = hash(HASH_ALGO, $stm.$config['salt']);
		
		if ($newFormsec != $_POST['formsec'])
		{
			die("Form was not submitted properly.");
		}
		// finished checking formsec
		
		
		// do the business
		deactivate_all_plugins();
		
		redirect('settings', 'do=manage-plugins&msg=dacd');
		die();
		
		
	}
	
	public function configure() 
	{
		//put the variables into the function scope
		global $config, $auth, $route;
		
		if (!$auth->has_perm('configure-amalia'))
		{
			$route->view('noperms');
			die();
		}
		
		// pull permissions so the admin view can only show relevant admin panels
		$d['perm']['manage-users'] = $auth->has_perm('manage-users');
		$d['perm']['configure-amalia'] = $auth->has_perm('configure-amalia');
		$d['perm']['manage-plugins'] = $auth->has_perm('manage-plugins');
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Settings: Configuration');
		
		if (!empty($_POST['submit']))
		{
		
			// recreate formsec from stored stm and salt, then compare with POST-provided formsec
			$stm = base64_decode($_POST['stm']);
			$newFormsec = hash(HASH_ALGO, $stm.$config['salt']);
			
			if ($newFormsec != $_POST['formsec'])
			{
				die("Form was not submitted properly.");
			}
			// finished checking formsec
			
			require 'configure.php';
			$result = update_config_file($_POST);
			
			if ($result === true)
			{
				redirect('settings', 'do=configure');
				die();
			}
			else {
			
				// loop errors
				if (count($result) > 0)
				{
					foreach($result as $error) {
						$d['msg'] .= '<li>'.safe_plain($error).'</li>';
					}
				}
				
			}
		
		}
		
		$d['page'] = 'configure';
		$d['data'] = $config;
		$route->view('admin/admin',$d);
		
	}	
	
	
	public function delete_installer_directory()
	{
	
		global $config, $auth, $route;
		
		if (!$auth->has_perm('configure-amalia'))
		{
			$route->view('noperms');
			die();
		}
		
		require 'configure.php';

		recursiveDeleteInstallerDir($config['config_path'].'/installer');
		deleteGitRevisionFooterFile();
		
		redirect();		
	
	}
	
	public function recycle_bin()
	{
	
		global $config, $auth, $route;
		
		if (!$auth->has_perm('delete-files'))
		{
			$route->view('noperms');
			die();
		}
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Recycle Bin');
		
		
		require_once 'recyclebin.class.php';
		$recycler = new amalia_recyclebin();
							
		$recycledFiles = $recycler->listRecycledFiles();
		$d['files'] = $recycledFiles;
		$route->view('recyclebin', $d);

		
	}
	
	public function restore_recycled()
	{
		global $config, $auth, $route;
		
		if (!$auth->has_perm('delete-files'))
		{
			$route->view('noperms');
			die();
		}
	
		if (empty($_GET['identifier']))
		{
			die('Nothing specified for restore.');
		}
		
		$identifier = safe_usersline($_GET['identifier']);
		
		require_once 'recyclebin.class.php';
		$recycler = new amalia_recyclebin();
							
		$recycledFiles = $recycler->restoreRecycledFile($identifier);
		
		redirect('recyclebin');
		die();
	
	}
	
	public function delete_recycled()
	{
		global $config, $auth, $route;
		
		if (!$auth->has_perm('delete-files'))
		{
			$route->view('noperms');
			die();
		}
		
		// permanently delete a file in the recycle bin
		
		
		if ($_POST['magic'] != 'Delete' || empty($_POST['identifier']))
		{
			redirect('recyclebin');
			die();
		}
		
		// check formsec, reject otherwise
		$form_stm = base64_decode($_POST['stm']);
		// rebuild formsec
		$new_formsec = hash(HASH_ALGO, $form_stm.$config['salt']);
		
		if ($new_formsec != $_POST['formsec'])
		{
			die('Unable to submit form due to security exception.');
		}
		
		$identifier = safe_usersline($_POST['identifier']);
		
		require_once 'recyclebin.class.php';
		$recycler = new amalia_recyclebin();
							
		$recycledFiles = $recycler->deleteRecycledFile($identifier);
		
		redirect('recyclebin');
		die();		
				
	
	}
	
	public function empty_recyclebin()
	{
		global $config, $auth, $route;
		
		if (!$auth->has_perm('delete-files'))
		{
			$route->view('noperms');
			die();
		}
		
		// permanently delete all files in the recycle bin
		
		
		if ($_POST['submit'] != 'Empty Recycle Bin')
		{
			redirect('recyclebin');
			die();
		}
		
		// check formsec, reject otherwise
		$form_stm = base64_decode($_POST['stm']);
		// rebuild formsec
		$new_formsec = hash(HASH_ALGO, $form_stm.$config['salt']);
		
		if ($new_formsec != $_POST['formsec'])
		{
			die('Unable to submit form due to security exception.');
		}
		
		require_once 'recyclebin.class.php';
		$recycler = new amalia_recyclebin();
							
		$recycledFiles = $recycler->emptyRecycleBin();		
		redirect('recyclebin');
		die();		
				
	
	}
	
	public function force_run_scheduler()
	{
	
			require_once 'includes/scheduler.php';
			$scheduler = new amalia_scheduler();
			
			$scheduler->forceRun();
			
			redirect('settings', 'do=configure&msg=didrunsch');
			
			die();
	
	}
	
	public function clear_thumbnail_cache()
	{
				
		require_once 'recyclebin.class.php';
		$recycler = new amalia_recyclebin();
		
		if ($recycler->clearThumbnailCache())
		{
		
			redirect('settings', 'do=configure&msg=didclearthumbs');
			die();
		}	
		else
		{
			redirect('settings', 'do=configure&msg=failclearthumbs');	
			die();
		}
	
	}
	
	public function first_aid()
	{
		global $config, $auth, $route;
		
		if (!$auth->has_perm('configure-amalia'))
		{
			$route->view('noperms');
			die();
		}
		
		// set title for header		
		define('AMALIA_PAGETITLE', 'Settings: Status');
		
		// pull permissions so the admin view can only show relevant admin panels
		$d['perm']['manage-users'] = $auth->has_perm('manage-users');
		$d['perm']['configure-amalia'] = $auth->has_perm('configure-amalia');
		$d['perm']['manage-plugins'] = $auth->has_perm('manage-plugins');
	
		require 'firstaid.php';
		
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' && !empty($_POST['submit']))
		{
		
			// process a repair
			
			// check formsec, reject otherwise
			$form_stm = base64_decode($_POST['stm']);
			
			// check for recent stm
			$rStm = explode('_', $form_stm);		
			
			// rebuild formsec
			$new_formsec = hash(HASH_ALGO, $form_stm.$config['salt']);
			
			if ($new_formsec != $_POST['formsec'] || $rStm[0]+60*5 < time()) // disallow attempt if form load was >10 mins ago
			{
				die('Unable to submit form due to security exception. Please try again.');
			}

			
			switch ($_POST['repair_action']) {
			
				case 'permissions':
				
					// run permissions repair and display

					$d['repair_action'] = 'permissions';
					
					$repairResult = repairPermissions();
					if (is_array($repairResult))
					{
						$d['repair_errors'] = $repairResult;
					}
					
					$d['page'] = 'first_aid_results/permissions';
					
				
				break;
				
				case 'configfile':
					
					// run the rewrite of the config file and prepare the right template
				
					$d['result'] = rewriteConfigFile();
					$d['page'] = 'first_aid_results/repair_config';
				break;
				
				case 'resetusers':
				
					$d['result'] = resetAllUsers();
					$d['page'] = 'first_aid_results/reset_users';
				
				break;
				
				default:
					die('No repair action specified.');
				break;
			
			}
			
			$route->view('admin/admin', $d);			
		
		}
		else 
		{
		 	// show normal page
			
			// go ahead and check the permissions on everything
			$permissionsResult = checkPermissions();
			
					
			if (is_array($permissionsResult))
			{
				// pass this to the view for display
				$d['permissions_errors'] = $permissionsResult;
			}
			
			
			// do operating environment checks
			$d['envChecks'] = operatingEnvironmentChecks();
			
			$d['page'] = 'first_aid';
			$route->view('admin/admin',$d);
		
		}
	
	}
	
	
}



?>