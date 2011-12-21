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
 * File Uploader Backend Class
 *
 * @package	Amalia2
 * @category	Amalia Generation
 * @author		Amalia Dev Team
 */


// portability -- requires safe_filename and string_to_filename from common.php

if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}

class amalia_uploader {

	private $files = false;
	private $destination = false;
	private $validationSuccess = false;
	
	public $lastFileName = false;

	public function __construct($files, $destination)
	{
		global $config;
		
		/* Constructor
		
			Pass $_FILES into $files

			Pass the destination directory for all of the files to upload
			to $destination.
			
			Your implementation should try/catch a new instance of uploader,
			friendly_fatal any caught exceptions, then call validateFiles. 
			
			If that succeeds returing === true, call writeValidatedFiles to save to destination.
			
			If it fails, you get an array of error strings to show the user. You still may, if you want,
			call writeValidatedFiles to write any/all files that did pass validation. Just make sure
			you do so after calling validateFiles.
		
		*/
	
		// no proper file uploaded
		if (!isset($files) || !is_array($files) || count($files) < 1)
		{
			throw new Exception('No uploaded files to process.');
			return false;
		}
		
		$this->files = $files;
		
		if (count($this->files) < 1)
		{
			throw new Exception('No uploaded files to process.');
			return false;		
		}
		
		// check destination dir to ensure validity of that location
		if (!file_exists($destination))
		{
			throw new Exception('The folder to which you are trying to upload this file does not exist.');
			return false;	
		}
		else {
			$this->destination = rtrim($destination, '/');
		}
		
		// protect the Config Path from editing
		if (strpos($this->destination.'/', $config['config_path'].'/') !== false)
		{
			throw new Exception('You cannot upload files to the Config Path.');
			return false;
		}
				
		return true;
		
	}
	
	public function validateFiles()
	{
	
		global $config;
	
		/* validateFiles
			
			Checks the validity of the files uploaded.
			
			Returns boolean true if it's all good, or an array containing
			strings detailing the errors otherwise.
			
			Your implementation MUST MUST check for true with ===. Anything
			else should be assumed to be an array of error strings which
			you can display back to the user.			
			
		*/
		
		$errors = array();
	
		foreach($this->files as $loop => $file)
		{
		
			$filenameForError = safe_filename(basename($file['name']));
		
			// check for upload errors
			if ($file['error'] != UPLOAD_ERR_OK)
			{
			
				switch($file['error'])
				{
					case UPLOAD_ERR_INI_SIZE:
						$errors[count($errors)] = 'The uploaded file "'.$filenameForError.'" is too large. (Upload size was restricted by php.ini setting.)';
						$this->removeFileFromList($loop);
					break;
					
					case UPLOAD_ERR_FORM_SIZE:
						$errors[count($errors)] = 'The uploaded file "'.$filenameForError.'" is too large. (Exceeded MAX_FILE_SIZE given by form.)';
						$this->removeFileFromList($loop);
					break;
					
					case UPLOAD_ERR_PARTIAL:
						$errors[count($errors)] = 'The file "'.$filenameForError.'" was only partially uploaded. Please try the upload again.';
						$this->removeFileFromList($loop);
					break;
					
					case UPLOAD_ERR_NO_FILE:
						//$errors[count($errors)] = 'No file "'.$filenameForError.'" was actually uploaded. Please try the upload again.';
						// ignore this type of error -- it could be an empty field
						$this->removeFileFromList($loop);
					break;
					
					case UPLOAD_ERR_NO_TMP_DIR:
						$errors[count($errors)] = 'Unable to save the uploaded file "'.$filenameForError.'" to the server. Please ask an administrator to check your PHP configuration and the permissions on any temporary folders (UPLOAD_ERR_NO_TMP_DIR).';
						$this->removeFileFromList($loop);
					break;
					
					case UPLOAD_ERR_CANT_WRITE:
						$errors[count($errors)] = 'Unable to save the uploaded file "'.$filenameForError.'" to the server. The file could not be written to disk. Please ask an administrator to check the permissions on any temporary folders (UPLOAD_ERR_CANT_WRITE).';
						$this->removeFileFromList($loop);
					break;
					
					case UPLOAD_ERR_EXTENSION:
						$errors[count($errors)] = 'The file you uploaded "'.$filenameForError.'" has the wrong file extension. Certain types of files are prevented from being uploaded. Please check the file extension matches the file type and is for an allowed type (UPLOAD_ERR_EXTENSION).';
						$this->removeFileFromList($loop);
					break;
					
					default:
						$errors[count($errors)] = 'The upload of "'.$filenameForError.'" failed for an unknown reason.';
						$this->removeFileFromList($loop);
					break;
			
				}
				
				// skip remainder of validation on this file, it will not be helpful
				continue;
							
			} // end file upload error code not OK

					
			// check validity of uploaded file
			if (!is_uploaded_file($file['tmp_name']))
			{
				$errors[count($errors)] = 'The file you uploaded "'.$filenameForError.'" has not been stored properly.';
				$this->removeFileFromList($loop);
				continue; // break this loop as we're done with this file
			}		

			// check user supplied file mime type against allowed file upload types
			if (!in_array($file['type'], $config['allowed']))
			{
				$errors[count($errors)] = 'The file you uploaded "'.$filenameForError.'" is not of an allowed type. Certain types of files are prevented from being uploaded. Please check the file extension matches the file type and is for an allowed type (failed client mime check against allowed in config).';	
				$this->removeFileFromList($loop);
				// don't process this file any more, it won't help
				continue;
			}
			
			// swap keys to values of allowed, and check file extension
			$allowedExtensions = array_flip($config['allowed']);
			
			if (!in_array(strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION)), $allowedExtensions) )
			{
				$errors[count($errors)] = 'The file you uploaded "'.$filenameForError.'" is not of an allowed type. Certain types of files are prevented from being uploaded. Please check the file extension matches the file type and is for an allowed type (failed extension check against allowed in config).';	
				$this->removeFileFromList($loop);
				// don't process this file any more, it won't help
				continue;				
			}
			
			// use fileinfo to check
			/*
			REMOVED IN FAVOUR OF ALTERNATIVE CHECKS -- THIS IS BUGGY AND UNPREDICTABLE RIGHT NOW
			if (class_exists('finfo'))
			{
			
				try {
			
					$finfo = new finfo(FILEINFO_MIME, '/usr/share/misc/magic');
				
				}
				
				catch (Exception $e)
				{ // don't care if it fails, we go to  mime_content_type 
				}
				
				try {
				
					if ($finfo)	
					{
					
						// check mimetype validity with mime_content_type
						$allowedTypesNotOctet = array_slice($config['allowed'], 0, count($config['allowed'])-1);
						
						// get mimetype
						$mimeType = @$finfo->file($file['tmp_name']);
						
						if (!in_array($mimeType, $allowedTypesNotOctet))
						{
							$errors[count($errors)] = 'The file you uploaded "'.$filenameForError.'" is not of an allowed type. Certain types of files are prevented from being uploaded. Please check the file extension matches the file type and is for an allowed type (failed check against allowed in config) (finfo).';	
							$this->removeFileFromList($loop);
							// don't process this file any more, it won't help
							continue;
						}
											
					
					}
				}
				catch (Exception $e)
				{ // again, we don't care if this fails yet
				}
			
			}
			*/
			
			if (function_exists('mime_content_type'))
			{	
				// check mimetype validity with mime_content_type
				$allowedTypesNotOctet = array_slice($config['allowed'], 0, count($config['allowed'])-1);
				
				$mimeType = mime_content_type($file['tmp_name']);
				// we don't care about charset, so cut off anything after a ';' character
				if (strpos($mimeType, ';') !== false)
				{
					$mimeType = substr($mimeType, 0, strpos($mimeType, ';'));
				}
				
	
				if (!in_array($mimeType, $allowedTypesNotOctet))
				{
					$errors[count($errors)] = 'The file you uploaded "'.$filenameForError.'" is not of an allowed type. Certain types of files are prevented from being uploaded. Please check the file extension matches the file type and is for an allowed type (failed check against allowed in config) (mime_content_type).';	
					$this->removeFileFromList($loop);
					// don't process this file any more, it won't help
					continue;
				}
			}
			else {
				
				// unable to check validity of file server-side, refuse to upload
				
				$errors[count($errors)] = 'Unable to verify the file type of "'.$filenameForError.'" on the server. For security reasons, the file upload cannot therefore be allowed. Please contact a server administrator (no mime_content_type()).';	
				$this->removeFileFromList($loop);
				// don't process this file any more, it won't help
				continue;								
				
			
			}
	
			$filename = valid_new_filename(basename($file['name']), pathinfo($file['name'], PATHINFO_EXTENSION));
			
			// does a file with this name already exist in the destination?
			if (file_exists(safe_filename($this->destination.'/'.$filename)))
			{	
				$filename = strtolower(pathinfo($filename, PATHINFO_FILENAME) . '_copy_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION));
			}
			
			// write new name back to source array
			$this->files[$loop]['new_name'] = safe_filename($filename);
		
		} // finished looping file
		
		if (count($errors) > 0)
		{
			return $errors;
		}
		
		else
		{
			return true;		
		}
	
	} // end validation function
	
	public function writeValidatedFiles()
	{
		global $config;
	
		if (is_array($this->files) && count($this->files) > 0)
		{
			
			foreach ($this->files as $file)
			{
			
			
				if (!move_uploaded_file($file['tmp_name'],
				safe_filename($this->destination.'/'.$file['new_name']) ) )
				{	
					throw new Exception('Unable to save file to destination folder. Please check the permissions on the destination folder.');
					return false;		
				
				}
				
				// attempt to chmod this new uploaded file to avoid permissions problems
				// when the web server tries to read the file
				
				@chmod(safe_filename($this->destination.'/'.$file['new_name']), 0644);
				
				// create a file title based on the filename
				$newTitle = safe_plain(pathinfo($file['name'], PATHINFO_FILENAME));
				$relativeFilename = str_replace($config['site_path'], '', $this->destination);
				$relativeFilename = ltrim($relativeFilename, '/');
				$relativeFilename = '/'.$relativeFilename; // standardise LHS slashes
				$relativeFilename = rtrim($relativeFilename, '/');
				$relativeFilename .= '/'; // standardise RHS slashes
				if ($relativeFilename == '//') // if we have double-slash (one top dir only), change to single dir
				{
					$relativeFilename = '/';
				}
				
				createAmaliaTitleFile($relativeFilename.$file['new_name'], $newTitle);
				
				$this->lastFileName = $file['new_name'];
				
			}
		}
		
		return true;
	
	}

	private function removeFileFromList($index)
	{
	
		/* remove the indexed file from the list of files to write.
		   This way, all the files that didn't have errors can still
		   be saved if desired. */
		
		unset($this->files[$index]);		
	
	}

};

?>