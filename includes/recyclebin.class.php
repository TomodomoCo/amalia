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
 * Recycle Bin Class
 *
 * Deals with the deletion of files, their eventual permanent unlinking
 * and recovering files from the bin.
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

class amalia_recyclebin {

	private $recycleBinDir;
	private $recycleBinCat;
	private $deleteAfter;

	public function __construct()
	{
		global $config;
		
		$this->recycleBinDir = $config['config_path'].'/userfiles/recycle';
		$this->recycleBinCat = $this->recycleBinDir.'/amalia-recycle-catalogue.txt';
		
		$this->deleteAfter = (60 * 60 * 24 * $config['recycle_days']);
	
		// get the catalogue file ready
		if (!file_exists($this->recycleBinCat))
		{
			$this->createRecycleBinCatalogue();
		}
	
	}
	
	public function listRecycledFiles()
	{
		
		global $config;
		
		// get list of files
		try {
			$fh = @fopen($this->recycleBinCat, 'r');
	
			if (!$fh)
			{
				friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}
		
		$recycledFiles = array();
		
		$catalogue = fread($fh, @filesize($this->recycleBinCat));
		$catalogueArray = explode("\n", $catalogue);
		$catalogueArray = array_filter($catalogueArray, 'filter_commented_line');
		
		fclose($fh);
		
		if (count($catalogueArray) > 0)
		{
		
			foreach($catalogueArray as $key => $line)
			{
			
				$lineExpl = explode(';', $line);
				
				$extension = strtolower(pathinfo($this->recycleBinDir.'/'.$lineExpl[3], PATHINFO_EXTENSION));
				if (is_dir($this->recycleBinDir.'/'.$lineExpl[3]))
				{
					$type = 'folder';
				}
				else if ($extension == 'php') {
					$type = 'file';
				} else if (in_array($extension, $config['file_types']['images'])) {
					$type = 'file image';
				} else if (in_array($extension, $config['file_types']['dynamic_files'])) {
					$type = 'dynamic_file';
				} else if (in_array($extension, $config['file_types']['static_files'])) {
					$type = 'file';
				} else if (in_array($extension, $config['file_types']['video'])) {
					$type = 'file video';
				} else if (in_array($extension, $config['file_types']['audio'])) {
					$type = 'file audio';
				} else {
					$type = 'unknown';
				}

				
				if (count($lineExpl) > 3)
				{		
					$recycledFiles[count($recycledFiles)] = array (
					
						'identifier' => $lineExpl[0],
						'deletionTime' => $lineExpl[1],
						'expiryTime' => (int) $lineExpl[1] + $this->deleteAfter,
						'originalPath' => $lineExpl[2],
						'currentPath' => $lineExpl[3],
						'title' => $lineExpl[4],
						'type' => $type,
						'thumbnail' => 'includes/etc/timthumb.php?src='.urlencode(safe_filename('/userfiles/recycle/'.$lineExpl[3])).'&amp;width=24&amp;height=17&amp;zc=1&amp;q=80&amp;cfgp=1',
					);
				}
				
			}
		}
		
		if (count($recycledFiles) > 0)
		{
			return $recycledFiles;
		}
		else {
			return false;
		}
	
	}
	
	
	public function recycleFile($file)
	{
	
		global $config;
	
		// $file should be the relative path of said file from Site Path
		
		$fileIdentifier = uniqid(); // identifier for the recycle bin catalogue

		$filePath = safe_filename($config['site_path'] . '/' . ltrim(rtrim($file, '/'), '/'));
		
		if (!file_exists($filePath))
		{			
			friendly_fatal('The file '.safe_filename($file).' does not exist.');
			return false;
		}
		
		if (is_dir($filePath))
		{
			return $this->recycleDirectory($filePath);
		}
		
		$extension = safe_filename(pathinfo($file, PATHINFO_EXTENSION));
		$filenameNoExt = safe_filename(pathinfo($file, PATHINFO_FILENAME));
		
		$fullNewPath = $this->recycleBinDir.'/'.$filenameNoExt.'_'.$fileIdentifier.'.'.$extension;
		
		// get the title of this file
		$fileTitle = file_get_contents($filePath);			
		$fileTitle = preg_match('/\$amalia_title = \'(.*)\';/', $fileTitle, $match);
		$fileTitle = $match[1];
		if (empty($fileTitle))
		{
			$fileTitle = file_get_contents(safe_filename($config['config_path'].'/userfiles/filedata/'.ltrim(rtrim($file, '/').'.amaliatitle')));
			
			// delete the old amaliatitle file for cleanup. We can restore that too later if the user restores.
			@unlink(safe_filename($config['config_path'].'/userfiles/filedata/'.ltrim(rtrim($file, '/').'.amaliatitle')));
			
		}
		
		// copy the file to the Recycle Bin directory
		
		// regenerate the unique ID if there is a clash (unlikely)
		while (file_exists($fullNewPath))
		{
			$fileIdentifier = uniqid('rekey_', true);
			$fullNewPath = $this->recycleBinDir.'/'.$filenameNoExt.'_'.$fileIdentifier.'.'.$extension;
		}
		
		// copy the file from its current location to the new recycle bin location
		try {
			if (!@copy($filePath, $fullNewPath))
			{
				friendly_fatal('Unable to copy the '.safe_plain($filePath).' to the Recycle Bin path at '.$fullNewPath.'. Please check permissions on the Recycle Bin folder.');
				die();
			}
		}
		catch (Exception $e)
		{
				friendly_fatal('Unable to copy the '.safe_plain($filePath).' to the Recycle Bin path at '.$fullNewPath.'. Please check permissions on the Recycle Bin folder.');
				die();
		}
		
		// add this file to the Recycle Bin catalogue so that we know about it there
		try {
			$fh = @fopen($this->recycleBinCat, 'r');
	
			if (!$fh)
			{
				friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}
		
		$catalogueArray = explode("\n", fread($fh, filesize($this->recycleBinCat)));
		fclose($fh);
		
		// new recycle cat line
		$newCatEntry = array (
			safe_usersline($fileIdentifier),	// file unique ID
			safe_usersline(time()), 			// original deletion time (now)			
			safe_usersline($file),				// original relative file path (as given to this function via param)
			safe_usersline(pathinfo($fullNewPath, PATHINFO_BASENAME)), // the recycled file path now, including extension
			safe_usersline(safe_plain_xmldoc($fileTitle), false),
		);
		
		$newCatEntry = implode(';', $newCatEntry); // make into semicolon separated string
		
		// append the new line to the end of the file
		$catalogueArray[count($catalogueArray)] = $newCatEntry;
		$newCatalogueFile = implode("\n", $catalogueArray);		
		
		// write the new file back to disk
		try {
			$fh = @fopen($this->recycleBinCat, 'w');
	
			if (!$fh)
			{
				friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}		
		
		fwrite($fh, $newCatalogueFile);
		fclose($fh);
		
		// delete the source file from its original location
		@unlink($filePath);
		
		return true;
	
	}
	
	private function recycleDirectory($directory)
	{
	
		global $config;
	
		// private function -- interface should always use recycleFile, which passes over to this
		// if the 'file' actually is a directory
		
		// because this is private, we therefore safely assume $directory has been cleansed, is_dir is true
		// and file_exists is true
		
		
		// first, make a new directory in the recycle bin which will be the destination
		// this directory will contain the recycled item's unique id in the dir name (as for a file)
		
		$directoryIdentifier = uniqid();
		$fullDestDirPath = $this->recycleBinDir.'/'.pathinfo($directory, PATHINFO_BASENAME).'_'.$directoryIdentifier;
		
		while (file_exists($fullDestDirPath))
		{
			// regenerate unique id if there is an unlikely clash
			$directoryIdentifier = uniqid('rekey_', true);
			$fullDestDirPath = $this->recycleBinDir.'/'.$directory.'_'.$directoryIdentifier;
		}
		
		
		
		// attempt creation of destination dir
		try {
			if (!@mkdir($fullDestDirPath, 0755))
			{
				friendly_fatal('Unable to create a destination directory in the Recycle Bin in order to recycle '.safe_filename($directory).'. Please check the permissions on the Recycle Bin folder.');
				die();
			}
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to create a destination directory in the Recycle Bin in order to recycle '.safe_filename($directory).'. Please check the permissions on the Recycle Bin folder.');
			die();
		}
		
		
		// enter the source folder, and recursively copy all its contents to our new destdir
		$this->recursiveCopyContentsOfDir($directory, $fullDestDirPath);

		// get the folder title		
		$relativeDirPath = ltrim(rtrim(str_replace($config['site_path'], '', $directory), '/'), '/');
		$folderTitle = file_get_contents(safe_filename($config['config_path'].'/userfiles/filedata/'.$relativeDirPath.'.amaliatitle'));
		// delete the old amaliatitle file for cleanup. We can restore that too later if the user restores.
		@unlink(safe_filename($config['config_path'].'/userfiles/filedata/'.$relativeDirPath.'.amaliatitle'));
		
		// add this new recycled folder to the catalogue
		try {
			$fh = @fopen($this->recycleBinCat, 'r');
	
			if (!$fh)
			{
				friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}
		
		$catalogueArray = explode("\n", fread($fh, filesize($this->recycleBinCat)));
		fclose($fh);
		
		// new recycle cat line
		$newCatEntry = array (
			safe_usersline($directoryIdentifier),	// file unique ID
			safe_usersline(time()), 			// original deletion time (now)			
			safe_usersline('/'.ltrim(str_replace($config['site_path'], '', $directory), '/')),	// original relative file path (as given to this function via param)
			safe_usersline(pathinfo($fullDestDirPath, PATHINFO_BASENAME)), // the recycled file path now, including extension
			safe_usersline(safe_plain_xmldoc($folderTitle), false),
		);
		
		$newCatEntry = implode(';', $newCatEntry); // make into semicolon separated string
		
		// append the new line to the end of the file
		$catalogueArray[count($catalogueArray)] = $newCatEntry;
		$newCatalogueFile = implode("\n", $catalogueArray);		
		
		// write the new file back to disk
		try {
			$fh = @fopen($this->recycleBinCat, 'w');
	
			if (!$fh)
			{
				friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}		
		
		fwrite($fh, $newCatalogueFile);
		fclose($fh);
		
		// recursively delete the original source directory, now that we have copied it
		// and registered it with the catalogue
		$this->recursiveDeleteDir($directory);
		
		return true;
	
	}
	
	private function recursiveCopyContentsOfDir($source, $destination)
	{
	
		// private function -- assumes clean, safe, valid $source and $destination.
		// do the checking in the calling function
		
		try {
			$handle = @opendir($source);
			
			if (!$handle)
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			return false;
		}
		
		// loop through contents
		while ( ($file = readdir($handle)) !== false)
		{
		
			if ($file == '.' || $file == '..')
			{
				continue; // ignore self and parent directory!
			}
		
			if (!is_dir($source.'/'.$file))
			{
				// normal file that we can go ahead and copy to the destination
				try {
					if (!@copy($source.'/'.$file, $destination.'/'.$file))
					{
						friendly_fatal('Unable to copy '.safe_filename($source.'/'.$file).' to '.safe_filename($destination.'/'.$file).'. Please check the permissions.');
						die();
					}
				}
				catch (Exception $e)
				{
					friendly_fatal('Unable to copy '.safe_filename($source.'/'.$file).' to '.safe_filename($destination.'/'.$file).'. Please check the permissions.');
					die();
				}
			
			}
			
			else {
			
				// make a subdir in the destination with this dirname
				try {
					if (!@mkdir($destination.'/'.$file))
					{
						friendly_fatal('Unable to make directory '.safe_filename($destination.'/'.$file).' Please check the permissions.');
						die();
					}
				}
				catch (Exception $e)
				{
					friendly_fatal('Unable to make directory '.safe_filename($destination.'/'.$file).' Please check the permissions.');
					die();
				}
				
				// recursively run me to copy the stuff inside that to the new subdir inside destination
				$this->recursiveCopyContentsOfDir($source.'/'.$file, $destination.'/'.$file);
			
			}
		
		}
		
		return true;
	
	}
	
	private function recursiveDeleteDir($dir)
	{
	
		// private function -- assumes clean, safe, valid $dir
		// do the checking in the calling function
		
		try {
			$handle = @opendir($dir);
			
			if (!$handle)
			{
				return false;
			}
		}
		catch (Exception $e)
		{
			return false;
		}
		
		// loop through contents
		while ( ($file = readdir($handle)) !== false)
		{
		
			if ($file == '.' || $file == '..')
			{
				continue; // ignore self and parent directory!
			}
		
			if (!is_dir($dir.'/'.$file))
			{
				// normal file that we can go ahead and unlink
				
				@unlink($dir.'/'.$file);
			
			}
			
			else {
				
				// recursively run me to delete the stuff inside that
				$this->recursiveDeleteDir($dir.'/'.$file);
			
			}
		
		}
		
		// once we're done, rmdir this dir
		@rmdir($dir);
		
		return true;
	
	}
	
	public function deleteExpiredFiles()
	{
		
		// work out what files, if any, are expired and delete them
		
		try {
			$fh = @fopen($this->recycleBinCat, 'r');
	
			if (!$fh)
			{
				friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}
		
		$catalogueArray = explode("\n", fread($fh, filesize($this->recycleBinCat)));
		$catalogueArray = array_filter($catalogueArray, 'filter_commented_line');
		fclose($fh);
		
		if (count($catalogueArray) > 0)
		{
		
			foreach($catalogueArray as $lineNo => $line)
			{
			
				$lineExpl = explode(';', $line);
				
				if (count($lineExpl) > 3)
				{		
					$expiryTime = (int) $lineExpl[1] + $this->deleteAfter; // [1] is the original deletion time
					
					if (time() > $expiryTime)
					{
						// time to die!
						$this->deleteRecycledFile($lineExpl[0]); // delete, sending along identifier			
					
					}
				}
			}
		}
	
	}


	private function createRecycleBinCatalogue()
	{
		
		// create a blank recycle bin catalogue file if there is none
		
		try {
			$fh = @fopen($this->recycleBinCat, 'w');
			if (!$fh)
			{
				friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();	
		}
		
		$catalogueFile = <<<EOT
# Amalia Recycle Bin Catalogue File
#
# DO NOT EDIT THIS FILE
# To restore deleted files, go to the Recycle
# Bin within Amalia and use the Restore function.
#
# This file is encoded in UTF-8.
#
# unique ID ; deletion time ; original file path ; current path ; title
EOT;

		fwrite($fh, $catalogueFile);
		fclose($fh);
	
	}
	
	
	public function restoreRecycledFile($fileIdentifier)
	{
	
		global $config;
	
		// move a file back to its original location and remove from catalogue
		
		// pull up the catalogue file and find the filename and line no. for this identifier
		try {
			$fh = @fopen($this->recycleBinCat, 'r');
	
			if (!$fh)
			{
				friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}
		
		$catalogueArray = explode("\n", fread($fh, filesize($this->recycleBinCat)));
		fclose($fh);
		
		// DON'T FILTER LINES
		
		if (count($catalogueArray) > 0)
		{
			foreach($catalogueArray as $lineNo => $line)
			{
			
				$lineExpl = explode(';', $line);
				
				if (count($lineExpl) > 3)
				{
					if ($lineExpl[0] == $fileIdentifier)
					{
						$fileToMove = $lineExpl[3]; // current binned relative file path
						$fileDestination = $lineExpl[2]; // original location, hence destination for restore
						$lineToDelete = $lineNo; // no. of the line we are on, so we can remove and rewrite
												 // the catalogue
						$titleForRestore = $lineExpl[4]; // file title so we can restore the .amaliatitle meta file
						
						break;
					}
				}
			
			}	
		}
		
		if (empty($fileToMove) || empty($fileDestination) || empty($lineToDelete))
		{
			friendly_fatal('Recycle Bin: Unable to find the file with identifier '.safe_plain($fileIdentifier).' in the catalogue in order to restore it.');
			die();
		}
		
		// remove that line from the catalogue and rewrite it
		unset($catalogueArray[$lineToDelete]);
		
		$newCatFile = implode("\n", $catalogueArray);
		
		// write the new file back to disk
		try {
			$fh = @fopen($this->recycleBinCat, 'w');
	
			if (!$fh)
			{
				friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}		
		
		fwrite($fh, $newCatFile);
		fclose($fh);
		
		// now copy the file from its current location (the bin) back to the destination
		
		$destinationWithPath = safe_filename($config['site_path'].'/'.ltrim($fileDestination, '/'));
		$fileToMove = safe_filename($this->recycleBinDir.'/'.ltrim($fileToMove, '/'));
		
		// if file to move back to original is a folder, pass off responsibility to restoreRecycledDirectory()
		if (is_dir($fileToMove))
		{
			return $this->restoreRecycledDirectory($fileToMove, $destinationWithPath, $titleForRestore);
		}
		
		if (!file_exists(pathinfo($destinationWithPath, PATHINFO_DIRNAME))) // does the original location still exist? if not, create it again
		{
			try {
				if (!@mkdir(pathinfo($destinationWithPath, PATHINFO_DIRNAME), 0755, true))
				{
					friendly_fatal('Unable to copy the '.safe_plain($fileToMove).' out of the Recycle Bin path at '.safe_plain($destinationWithPath).'. Could not recreate the original folder for restore. Please check permissions on the Site Path folder.');
					die();
				}
			}
			catch (Exception $e)
			{
				friendly_fatal('Unable to copy the '.safe_plain($fileToMove).' out of the Recycle Bin path at '.safe_plain($destinationWithPath).'. Could not recreate the original folder for restore. Please check permissions on the Site Path folder.');
				die();
			}			
		}
		
		
		// go ahead and do the copy of the recycled file to the original location
		try {
			if (!@copy($fileToMove, $destinationWithPath))
			{
				friendly_fatal('Unable to copy the '.safe_plain($fileToMove).' out of the Recycle Bin path at '.safe_plain($destinationWithPath).'. Please check permissions on the Site Path folder.');
				die();
			}
		}
		catch (Exception $e)
		{
				friendly_fatal('Unable to copy the '.safe_plain($fileToMove).' to the Recycle Bin path at '.$fullNewPath.'. Please check permissions on the Recycle Bin folder.');
				die();
		}
		
		// see if we should re-create a title metafile and do so
		// get the title of this file
		$fileTitle = file_get_contents($fileToMove);			
		$fileTitle = preg_match('/\$amalia_title = \'(.*)\';/', $fileTitle, $match);
		$fileTitle = $match[1];
		if (empty($fileTitle)) // if it's not empty, it's a page, so doesn't have a file to recreate
		{
			// so in this case, we do have a metafile to create
			
			createAmaliaTitleFile($fileDestination, $titleForRestore);
			
		}
		
		// file copied back to original location, so delete the file from the recycle bin
		
		@unlink($fileToMove);
		
		
		return true;
	
	
	}
	
	private function restoreRecycledDirectory($directory, $destination, $titleForRestore = '')
	{
		global $config;
		
		// private function -- assumes clean, safe, valid $destination.
		// do the checking in the calling function
		
		// note that unlike recycleDirectory, this function does not handle
		// catalogue updating -- that is done universally before this is called
		// by restoreRecycledFile()
		
		// does the current original location (destination) already exist? if not, create it
		if (!file_exists($destination))
		{
			try {
				if (!@mkdir($destination))
				{
					friendly_fatal('Unable to copy the '.safe_plain($directory).' out of the Recycle Bin path at '.safe_plain($destinationWithPath).'. Could not recreate the original folder for restore. Please check permissions on the Site Path folder.');
					die();
				}
			}
			catch (Exception $e)
			{
				friendly_fatal('Unable to copy the '.safe_plain($directory).' out of the Recycle Bin path at '.safe_plain($destinationWithPath).'. Could not recreate the original folder for restore. Please check permissions on the Site Path folder.');
				die();
			}
		}
		
		// copy binned folder back to its original location (destination)
		if ($this->recursiveCopyContentsOfDir($directory, $destination))
		{
		
			// recreate a title file
			$relativeDestination = ltrim(rtrim(str_replace($config['site_path'], '', $destination), '/'), '/');
			createAmaliaTitleFile($relativeDestination, $titleForRestore);

		
			// recycle binned contents copied, the catalogue has already been updated
			// so all that is left to do is to delete the recycle binned copy.
		
			$this->recursiveDeleteDir($directory);
			
			return true;	
		
		}
		else {
			return false;
		}
	
	}
	
	public function deleteRecycledFile($fileIdentifier)
	{
		// only to be run internally, once it has been determined this file expired!
		// or after user's specific delete request and much validation
		
		// expects file identifier as per the catalogue file
		
		// pull up the catalogue file and find the filename and line no. for this identifier
		try {
			$fh = @fopen($this->recycleBinCat, 'r');
	
			if (!$fh)
			{
				friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}
		
		$catalogueArray = explode("\n", fread($fh, filesize($this->recycleBinCat)));
		fclose($fh);
		
		// DON'T filter commented lines, or we will delete the wrong line later and not preserve comments
		
		if (count($catalogueArray) > 0)
		{
			foreach($catalogueArray as $lineNo => $line)
			{
			
				$lineExpl = explode(';', $line);
				
				if (count($lineExpl) > 3)
				{
					if ($lineExpl[0] == $fileIdentifier)
					{
						$fileToDelete = $lineExpl[3]; // current binned relative file path
						$lineToDelete = $lineNo; // no. of the line we are on, so we can remove and rewrite
												 // the catalogue
						break;
					}
				}
			
			}	
		}
		
		if (empty($fileToDelete) || !isset($lineToDelete))
		{
			friendly_fatal('Recycle Bin: Unable to find the file with identifier '.safe_plain($fileIdentifier).' in the catalogue in order to delete it.');
			die();
		}
		
		// remove that line from the catalogue and rewrite it
		unset($catalogueArray[$lineToDelete]);
		
		$newCatFile = implode("\n", $catalogueArray);
		
		// write the new file back to disk
		try {
			$fh = @fopen($this->recycleBinCat, 'w');
	
			if (!$fh)
			{
				friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to write the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}		
		
		fwrite($fh, $newCatFile);
		fclose($fh);
		
		// is this a directory? if so, we need to recursively delete the dir
		if (is_dir($this->recycleBinDir.'/'.ltrim($fileToDelete, '/')))
		{
			return $this->recursiveDeleteDir($this->recycleBinDir.'/'.ltrim($fileToDelete, '/'));
		}
		
		// finally, unlink the file on disk
		@unlink($this->recycleBinDir.'/'.ltrim($fileToDelete, '/'));
		
		return true;		
	
	}
	
	public function emptyRecycleBin()
	{
		
		// delete all files in catalogue
		
		try {
			$fh = @fopen($this->recycleBinCat, 'r');
	
			if (!$fh)
			{
				friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
				die();
			}
			
		}
		catch (Exception $e)
		{
			friendly_fatal('Unable to read the Recycle Bin catalogue file at '.safe_filename($this->recycleBinCat).'. Please check permissions on the Recycle Bin folder and the catalogue file.');
			die();
		}
		
		$catalogueArray = explode("\n", fread($fh, filesize($this->recycleBinCat)));
		// AGAIN, DON'T DELETE THE COMMENTED LINES
		fclose($fh);
		
		if (count($catalogueArray) > 0)
		{
		
			foreach($catalogueArray as $lineNo => $line)
			{
			
				$lineExpl = explode(';', $line);
				
				if (count($lineExpl) > 3 && !preg_match("/^\#/", $line))
				{		
					// time to die!
					$this->deleteRecycledFile($lineExpl[0]); // delete, sending along identifier			
					
				}
			}
		}
	
	}
	
	
	public function clearThumbnailCache()
	{
		global $config;
	
		// just put in here for convienience
		
		if (file_exists($config['config_path'].'/userfiles/thumbcache'))
		{
		
			$this->recursiveDeleteDir($config['config_path'].'/userfiles/thumbcache');
			
			@mkdir($config['config_path'].'/userfiles/thumbcache');
			@chmod($config['config_path'].'/userfiles/thumbcache', 0755);

			return true;
		
		}
		
		return false;
	
	}



};



?>