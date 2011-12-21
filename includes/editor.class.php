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
 * Editor Class
 *
 * @package	Amalia2
 * @category	Amalia File Managment
 * @author		Amalia Dev Team
 */
 if ( ! defined('IN_AMALIA'))
{ // this prevents viewing of include files directly
	header('HTTP/1.0 403 Forbidden');
	die('<h1>Forbidden</h1>');	
}
require_once('file-manager.abstract.php');
class amalia_editor extends amalia_file_manager 
{
	
	

	public function __construct($file, $attributes = array()) 
	{
		global $config;
	
		parent::__construct($file, $attributes);
		
		if (strpos($file.'/', $config['config_path'].'/') !== false)
		{
			friendly_fatal('You cannot edit files that are in the Config Path.');
			die();
		}
		
	}
	
	public function prepare_for_editor() 
	{
		//load the parent function
		$text = parent::read();
			
			//parse the file and set into the $vars variable
			preg_match('/\include \'(.*)\';/', $text, $match);
			$vars['theme'] = $match[1];
			//title
			preg_match('/\$amalia_title = \'(.*)\';/', $text, $match);
			$vars['title'] = $match[1];
			$vars['title'] = str_replace("\\\'", "\'", $vars['title']);
			//keywords
			preg_match('/\$amalia_keywords = \'(.*)\';/', $text, $match);
			$vars['keywords'] = $match[1];
			$vars['keywords'] = str_replace("\\\'", "\'", $vars['keywords']);
			//description
			preg_match('/\$amalia_description = \'(.*)\';/', $text, $match);
			$vars['description'] = $match[1];
			$vars['description'] = str_replace("\\\'", "\'", $vars['description']);
			//content
			preg_match('/\$amalia_content = <<<AMALIA_CONTENT_END(.*)AMALIA_CONTENT_END/Us', $text, $match);
			$vars['content'] = $match[1];
			$vars['content'] = str_replace("\\\$", "\$", $vars['content']);
			
			// protect
			$vars['theme'] = safe_filename($vars['theme']);
			$vars['title'] = safe_plain($vars['title']);
			$vars['keywords'] = safe_plain($vars['keywords']);
			$vars['description'] = safe_plain($vars['description']);
			
			//return the vars array
			return $vars;
		
		
	
	}
	
	public function fetch_all_themes() 
	{
	
		global $config;
	
		// does the themes directory exist?
		if (!file_exists($config['themes_path']))
		{
			friendly_error('Themes', 'The themes directory '.$config['themes_path'].' does not exist. Please create it, or edit the location of the themes directory in the configuration.');
			//return array(); // return empty array
		}
	
		$all = parent::list_files_in_dir($config['themes_path']);
		$i = 0;
		if (is_array($all) && count($all) > 0) // make sure we actually can foreach, to avoid ugly errors
		{
			foreach($all as $all2) 
			{
				if($all2['type'] == 'dynamic_file') 
				{
					if(($all2['type'] != 'dir') && ($all2['type'] != 'unknown')) 
					{
						if(strstr($all2['name'], '.xml') === FALSE) {
							$all3[$i] = $all2['name'];
						}
					}
				}
				$i++;
			}
			
			return $all3;
		
		}
		else
		{
			return array(); // return empty array if there were no templates
		}
	}
	
	public function save($savedata) 
	{
	
		$page = parent::write($this->format_for_save($savedata));
		return $page;	
	}
	
	private function format_for_save($raw) 
	{
	
		global $config;
	
		$themestripped = str_replace('template_','',$_POST['theme']);
	
		if (empty($themestripped))
		{
			$_POST['theme'] = 'default'; // assumption if there is no theme
		}
		
		$themeToWrite = str_replace('template_','',$_POST['theme']);
		$themeToWrite = str_replace('.php', '', $themeToWrite);
		$themeToWrite = safe_plain($themeToWrite);
		
		// handle weird characters in the meta fields
		$raw['title'] = htmlentities(strip_tags($raw['title']), ENT_QUOTES, 'UTF-8', false);
		$raw['keywords'] = htmlentities(strip_tags($raw['keywords']), ENT_QUOTES, 'UTF-8', false);
		$raw['description'] = htmlentities(strip_tags($raw['description']), ENT_QUOTES, 'UTF-8', false);
		
		// escape dollar signs in the content, or even inside a heredoc they are considered viable
		$raw['content'] = str_replace('$', '&#36;', $raw['content']);
		
		// escape backslashes which may potentially be used as arbitrary Unicode character entities in a heredoc
		$raw['content'] = str_replace('\\', '&#92;', $raw['content']);
		
		$errorReporting = (DEBUG) ? 'error_reporting(E_ALL ^ E_NOTICE);' : 'error_reporting(0);';
	
		$template = '<?php' ."\n" .
			$errorReporting . "\n" .			
			'@include("'.$config['themes_path'].'/functions.php");'."\n" .
			'$amalia_title = \''.$raw['title'].'\';'."\n" .
			'$amalia_keywords = \''.$raw['keywords'].'\';'."\n" .
			'$amalia_description = \''.$raw['description'].'\';'."\n" .
			'$amalia_content = <<<AMALIA_CONTENT_END'."\n".
			$raw['content']."\n".
			'AMALIA_CONTENT_END;'."\n".
			'include \''.$config['themes_path'].'/'.$themeToWrite.'.php\';'."\n" .
			'?>';
			
			
		return $template;
			
	}
	
	public function createFile($savedata) 
	{
		$exists = parent::create();
		if($exists == true) 
		{
			$page = parent::write($this->format_for_create($savedata));
			return $page;
		} 
		else 
		{
			return false;
		}
	}
	
	private function format_for_create($raw) 
	{
		global $config;
		
		$errorReporting = (DEBUG) ? 'error_reporting(E_ALL ^ E_NOTICE);' : 'error_reporting(0);';
		
		$template = '<?php' ."\n" .			
			$errorReporting . "\n" .
			'@include("'.$config['themes_path'].'/functions.php");'."\n" .
			'$amalia_title = \''.safe_plain($raw['title']).'\';'."\n" .
			'include \''.$config['themes_path'].'/'.safe_plain($_POST['theme']).'\';'."\n" .
			'?>';
			
			
		return $template;	
	}
	
	public function delete() 
	{
		$t = parent::delete();
		return $t;
	}
	
	
	public function list_files($dir = '/') 
	{
		global $config;
	
		// standardise directory slashes
		$dir = ltrim($dir, '/');
		$dir = rtrim($dir, '/');
		$dir = '/'.$dir.'/';
		if ($dir == '//')
			$dir = '/';
		
		// check validity of site path
		$base = safe_filename($config['site_path']);
		if (empty($base))
		{
			friendly_fatal('The Site Path is not set correctly and Amalia cannot list files.');
			die();
		}
		
		if (strpos($base.$dir.'/', $config['config_path'].'/') !== false)
		{
			// disallow access to config path via the file browser
			// (where the config path is one level under the site path)
			
			friendly_fatal('You cannot edit files in the Config Path using the File Browser.');
			die();
			
		}
	
		$fileList = parent::list_files_in_dir($base.$dir);
		
		//cycle though the $fileList array
		$i = 0;
		
		if (is_array($fileList) && count($fileList) > 0)
		{
			foreach($fileList as $file) {
			
				$filename = pathinfo($file['name']);
				
				if($file['name'] != 'sitemap.xml' && $file['name'] != 'robots.txt') 
				{
					if(strpos($config['themes_path'],$file['name']) === false) 
					{
					
						$finishedFileList[$i]['name'] = $file['name'];
						$finishedFileList[$i]['type'] = $file['type'];
						
						// get page title
						if ($file['type'] == 'dynamic_file')
						{
							$string = file_get_contents($config['site_path'].$dir.$file['name']);			
							preg_match('/\$amalia_title = \'(.*)\';/', $string, $match);
							$finishedFileList[$i]['title'] = str_replace("\\\'", "\'", $match[1]);
							
							$finishedFileList[$i]['type_priority'] = 1;
				
						}
						
						
						else { // arbitrary non-page file titles
						
							// check to see if there is an image title file for this image
							$filetitle = @file_get_contents($config['config_path'].'/userfiles/filedata/'.$dir.$file['name'].'.amaliatitle');			
								
							if (!empty($filetitle))
							{
								$finishedFileList[$i]['title'] = safe_plain($filetitle);
							}
							
							// set sort priority
							switch ($file['type'])
							{
								case 'image':
									$finishedFileList[$i]['type_priority'] = 2;
								break;
								default:
									$finishedFileList[$i]['type_priority'] = 3;
								break;
							
							}
							
						
						}
						
						// load thumbnail support in
						if($file['type'] === 'image') 
						{
								$finishedFileList[$i]['thumbnail'] = $thumb_url = 'includes/etc/timthumb.php?src='.urlencode(safe_filename($dir.$file['name'])).'&amp;width=24&amp;height=17&amp;zc=1&amp;q=80';
								
						}
						
						
					}
				}
				$i++;
			}
			
			// finishedFileList now contains our finished file listing
			
			// sort by file title if possible
			if (!isset($config['browser_sort_by_filename_only']) || !$config['browser_sort_by_filename_only'])
			{
				if (is_array($finishedFileList) && count($finishedFileList) > 0)
				{
					//usort($finishedFileList, 'sort_file_listing_by_type_then_title');
					// sort criteria					
					foreach($finishedFileList as $key => $row)
					{
						$title[$key] = strtolower($row['title']);
						$type_priority[$key] = $row['type_priority'];
					}
					
					array_multisort($type_priority, SORT_ASC, $title, SORT_STRING, $finishedFileList);
					
				}
			}
			
			return $finishedFileList;
		}
	}

}

?>