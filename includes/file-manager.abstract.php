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
 * File Manager
 *
 * @package	Amalia2
  * @category	File Manager
 * @author		Amalia Dev Team
 */
abstract class amalia_file_manager {

	protected $file_location;
	protected $loaded_data;
	protected $save_data;
	protected $hd;
	protected $attributes;
	protected $is_dir = false;
	
	//construct the appropriate variables
	public function __construct($file_location = null, $attributes = array()) {
	

			//make sure file is NOT a directory synonym ie /. or ..
			if((strpos($file_location, "/.") !== false) && (strpos($file_location, "..") !== false))
			{
				//if it is, throw an Exception
				friendly_fatal('File or directory is invalid ('.safe_plain($file_location).').');
				die();
			} 
			else 
			{
				//other wise set the variables
				$this->file_location = safe_plain($file_location);
				$this->attributes = $attributes;
				
				//check to see if the file is or is not a directory
				if(is_dir($this->file_location)) 
				{
					$this->is_dir = true;
				} else 
				{
					$this->is_dir = false;
				}
				
				
			} //end /. or .. check
	} //end constructor
	
	public function read() {
		
		//make sure the file is not a directory
		if($this->is_dir === false) {
			//make sure the file is readable
			if(!is_readable($this->file_location)) {
				
				//try to chmod the file to 644
				//catch any exceptions
				try {
					if (!chmod($this->file_location, 644))
					{
						list_file_error('Unable to change permissions on file: '.safe_plain($this->file_location));
						return false;					
					}
				}
				catch(Exception $e) {
					list_file_error('Unable to permissions on file: '.safe_plain($this->file_location).' '.$e->getMessage());
					return false;
				}
			}
		
			//try to open the file
			//catch any exceptions
			try {
				
				$this->hd = fopen($this->file_location,'r');
				if (!$this->hd)
				{
					list_file_error('Unable to open file: '.safe_plain($this->file_location));
					return false;
				}
			
			}
			catch(Exception $e) {
			
				list_file_error('Unable to open file: '.safe_plain($this->file_location).' '.$e->getMessage());
				return false;		
			
			}
			
			//try to read the file
			//catch any exceptions
			try {
				$this->loaded_data = fread($this->hd, filesize($this->file_location));
			}
			catch(Exception $e) {
				list_file_error('Unable to read file:'.safe_plain($this->file_location).' '.$e->getMessage());
				return false;
			}	
			
			return $this->loaded_data;
		} else {
			list_file_error('Cannot write into directory:'.safe_plain($this->file_location));
			return false;
		}
	}
	
	public function write($save_data = null) {

		//make sure the file is not a directory
		if($this->is_dir === false) {
			//make sure the variable is not null
			if($save_data != null) {
			
				$this->save_data = $save_data;
				
				//make sure the file is writable
				if(!is_writable($this->file_location)) {
					
					//try to chmod the file to 644
					//catch any exceptions
					try {
						chmod($this->file_location, 644);
					}
					catch(Exception $e) {
						list_file_error('Unable to chmod file:'.safe_plain($this->file_location).' '.$e->getMessage());
						return false;
					}
				}
			
				//try to open the file
				//catch any exceptions
				try {
					
					$this->hd = fopen($this->file_location,'w');
				
				}
				catch(Exception $e) {
				
					list_file_error('Unable to open file:'.safe_plain($this->file_location).' '.$e->getMessage());
					return false;		
				
				}
				
				//try to read the file
				//catch any exceptions
				try {
					fwrite($this->hd, $this->save_data);
				}
				catch(Exception $e) {
					list_file_error('Unable to read file:'.safe_plain($this->file_location).' '.$e->getMessage());
					return false;
				}

				
				return true;
				
			} else {
				list_file_error('Unable to write to file:'.safe_plain($this->file_location));
				return false;
			}
		} else {
			list_file_error('Cannot write into directory:'.safe_plain($this->file_location));
			return false;
		}
	}
	
	
	
	public function delete() {
	
		//try to delete the file
		//catch any exceptions
		try {
			if(is_dir($this->file_location))
			{
				$this->del_recursive($this->file_location.'/');
			}
			else
			{
				unlink($this->file_location);
			}
		}
		catch(Exception $e) {
			list_file_error('Unable to delete file:'.safe_plain($this->file_location).' '.$e->getMessage());
			return false;
		}
		
		return true;
	
	
	}
	
	private function del_recursive($current_dir)
	{
        if($dir = @opendir($current_dir)) {
            while (($f = readdir($dir)) !== false) {
                if($f > '0' and filetype($current_dir.$f) == "file") {
                    unlink($current_dir.$f);
                } elseif($f > '0' and filetype($current_dir.$f) == "dir") {
                    $this->del_recursive($current_dir.$f."\\");
                }
            }
            closedir($dir);
            rmdir($current_dir);
        }
	}
	
	
	public function rename() {
	

		if(array_key_exists('new_location',$this->attributes) && !empty($this->attributes['new_location'])) {
		
			try {
				rename($this->file_location, safe_plain($this->attributes['new_location']));
			}
			catch(Exception $e) {
				list_file_error('Unable to rename file or directory:'.safe_plain($this->file_location).' '.$e->getMessage());
				return false;
			}
		
			return true;
			
		
		} else {
			list_file_error('Unable to rename file or directory:'.safe_plain($this->file_location));
			return false;
		}
	
	}
	public function create() {
	
		//cycle through the $this->attributes array and convert to string
		
		if(file_exists($this->file_location) == false) 
		{
		
			if($this->is_dir === true) 
			{
			
				
			
				try 
				{
				
					mkdir($this->file_location);
				
				}
				catch(Exception $e) 
				{
					list_file_error('Unable to create directory:'.safe_plain($this->file_location).' '.$e->getMessage());
					return false;
				}
					
					
			
			} 
			else 
			{
				
				//try to open the file
				//catch any exceptions
				try {
					
					fopen($this->file_location,'w');
				
				}
				catch(Exception $e) {
				
					list_file_error('Unable to create file:'.safe_plain($this->file_location).' '.$e->getMessage());
					return false;		
				
				}
			
			}
		
			return true;
		
		
	} 
	else 
	{
		list_file_error('Unable to create file:'.safe_plain($this->file_location).'.  File/folder already exists');
		return false;	
	}
	
	
	}
	
	
	
        function list_files_in_dir($dir) 
		{
			global $config;
			//make sure the directory does not start with a period or contain two ..
			if ((strpos($directory, '..') !== false) && (strpos($directory, '/.') !== false)) 
			{
				list_file_error('The directory cannot start with a period or contain two simultaneous periods.');
				return false;
			}
        
			$list = array(); // will have all our files in later
       
       
			// check directory validity
			if (!is_dir($dir))
			{
				list_file_error('The directory '.safe_plain($dir).' does not exist, or it is not a directory.');
				return false;
			}
       
			// try to make a directory handle to open the dir
			try {
				$dh = opendir($dir);           
			}
			catch (Exception $e)
			{
				list_file_error('Unable to get directory handle for '.safe_plain($dir).' '.$e->getMessage());
				return false;          
			}
			// in case it raises just an E_WARNING
			if (!$dh)
			{
				list_file_error('Unable to get directory handle for '.safe_plain($dir));
				return false;          
			}
		   
        // if we're here, we now have a directory handle, so let's go fishing
       
        $i = 0;
       
        while (false !== ($file = readdir($dh)))
        {
               
                // if the file is a dotfile, ignore and continue to next item
                if (strpos($file, '.') === 0)
				{
					continue;
				}
				
				// if this is the config path, then hide it from output				
				if (strpos(rtrim($dir, '/').'/'.$file.'/', $config['config_path'].'/') === 0)
				{
					continue;
				}
               
                // if this 'file' is actually a subdir, add it to our list as a directory item
                if (is_dir($dir.'/'.$file))
                {
					$list[$i]['name'] = $file;
					$list[$i]['type'] = 'dir';
                }
                else
                {
					// work out filetype
					$list[$i]['name'] = $file;
				   
					$dotpos = strrpos($file, ".");
					if ($dotpos === false) {
						$list[$i]['type'] = 'unknown';
					} else {
						if(function_exists('pathinfo')) {
							$pi = pathinfo($file);
							$ext = $pi['extension'];
						} else {
							$ext = substr($file, $dotpos+1);
						}
						
						$ext = strtolower($ext);
						   
						if(in_array($ext,$config['file_types']['images'])) {
							$list[$i]['type'] = 'image';
						} else if(in_array($ext,$config['file_types']['dynamic_files'])) {
							$list[$i]['type'] = 'dynamic_file';
						} else if(in_array($ext,$config['file_types']['static_files'])) {
							$list[$i]['type'] = 'file';
						} else if(in_array($ext,$config['file_types']['video'])) {
							$list[$i]['type'] = 'video';
						} else if(in_array($ext,$config['file_types']['audio'])) {
							$list[$i]['type'] = 'audio';
						} else {
							$list[$i]['type'] = 'unknown';
						}
                    }          
                } // end is not directory        
               
                $i++;  
               
        }
       
       
              return $list;  
       
	}	

}

?>