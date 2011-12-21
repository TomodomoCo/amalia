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


require_once('file-manager.abstract.php');

class amalia_sitemaps extends amalia_file_manager {
	public $the_file;
	
	public function __construct($file, $attributes = array()) {
		parent::__construct($file, $attributes);
		$this->the_file = $file;
	}
	
	public function update_sitemap()
	{
	
		global $config;
$sitemap = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
EOT;
$sitemap .= "\n";

	foreach($this->process_dir($config['site_path'], $config['site_url'], true) as $e)
	{

	
		//$sitemap .= '<!--'.print_r($e,True).'-->'; //DEBUG 
		
		
		// ignore all files which are not pages (dynamic file)
		$ext = pathinfo($e['filename'], PATHINFO_EXTENSION);
		if (!in_array($ext, $config['file_types']['dynamic_files']))
		{
			continue; // skip to next item
		}
		

		$sitemap .= "\t".'<url>'."\n";
		// THERE IS NO TITLE ATTRIBUTE!
		//$sitemap .= "\t"."\t".'<title><![CDATA['.safe_plain_xmldoc($e['title']).']]></title>'."\n"; 
		$sitemap .= "\t"."\t".'<loc><![CDATA['.safe_plain_xmldoc($e['url'].'/'.$e['filename']).']]></loc>'."\n";
		$sitemap .= "\t"."\t".'<lastmod>'.safe_plain_xmldoc(date('Y-m-d', $e['modtime'])).'</lastmod>'."\n";
		$sitemap .= "\t"."\t".'<priority>0.5</priority>'."\n";
		$sitemap .= "\t"."\t".'<changefreq>daily</changefreq>'."\n";
		$sitemap .= "\t".'</url>'."\n";
	}
	$sitemap .= '</urlset>';
		if(file_exists($this->the_file))
		{
			parent::write($sitemap);
		}
		else
		{
			parent::create();
			parent::write($sitemap);
		}
		
		@chmod($this->the_file, 0644);
		
	}
	
	

	
	function process_dir($dir,$siteurl,$recursive = FALSE) {
	global $config;
	
	
    if (is_dir($dir)) {
      for ($list = array(),$handle = opendir($dir); (FALSE !== ($file = readdir($handle)));) {
      
		$path = $dir.'/'.$file;
        
        // only process files that aren't current dir, dotfiles, in the config path, files that
        // exist and that are not themes or robots.txt
                
        if ( (strpos($file, '.') !== 0) &&
        (strpos($dir, $config['config_path']) !== 0) && // TODO: potential issue with non-standard config path install bases
        (file_exists($path)) &&
        ($path != $config['themes_path']) &&
        ($file != 'robots.txt') ) {        
        
          if (is_dir($path) && ($recursive) && ($path != $config['themes_path'])) {
            // new url is site url plus current dir path with site path stripped from it
            $newUrl = $config['site_url'] . str_replace($config['site_path'], '', $path);
            $list = array_merge($list, $this->process_dir($path, $newUrl, TRUE));
          } else {
          
            $entry = array('filename' => $file, 'dirpath' => $dir,'url'=> $siteurl);

	
			  $entry['modtime'] = filemtime($path);
			  if($path != $config['themes_path'])
			  {
				  $handle2 = fopen($dir.'/'.$file,'r');
				  if (filesize($dir.'/'.$file) > 0)
				  {
				  	$temp = fread($handle2, filesize($dir.'/'.$file));
				  }
				  fclose($handle2);
				  preg_match('/\$amalia_title = \'(.*)\';/', $temp, $match);
					
				  $entry['title'] = str_replace("\\\'", "\'", $match[1]);
			 }

						do if (!is_dir($path)) {

			  $entry['size'] = filesize($path);
			  if (strstr(pathinfo($path,PATHINFO_BASENAME),'log')) {
				if (!$entry['handle'] = fopen($path,'r')) $entry['handle'] = "FAIL";
			  }
 

              break;
            } else {

              break;
            } while (FALSE);
            $list[] = $entry;
          }
        
        } // end if
        //DEBUG
        /*else {
        	echo $file.' failed the test. | ';
        	echo 'dotfile: ';
        	echo ((strpos($file, '.') !== 0)) ? ' was ok' : 'not ok';
        	echo ' | config path: ';
        	echo ((strpos($dir, $config['config_path']) !== 0)) ? 'was ok ' : 'not ok';
        	echo ' | exists: ';
        	echo (file_exists($path)) ? 'was ok ' : 'not ok';
        	echo ' |  themes path: ';
        	echo (($path != $config['themes_path'])) ? 'was ok' : 'not ok';
        	echo ' | robots: ';
        	echo (($file != 'robots.txt')) ? 'was ok': 'not ok';
        	echo '<br>';
        	
        }*/
        // END DEBUG        
      }
      closedir($handle);
      return $list;
    } else return FALSE;
  }

  
  public function getSitemap()
  {
	require("xml_parser.php");
	$xml = new ParseXml();
	$xml->LoadFile($this->the_file, 3);
	return $xml->ToArray(); 
	
  }
}