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

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the names of the authors or copyright holders shall not be used in commercial advertising or to otherwise promote the sale, commercial use or other commercial dealings regarding this Software without prior written authorization from the the authors or copyright holders. Non-commercial use of the authors and copyright holders' names is permitted, but it may be revoked on a case-by-case basis if the authors wish to disconnect themselves from a particular use.
*/

// Demonstration word filter plugin.

class com_getamalia_EditorWordFilter extends Plugin
{
	
	public $identifier = "com.getamalia.EditorWordFilter";
	public $friendlyName = "Editor Word Filter";
	public $description = "Automatically filter and replace specified words in any pages that the user edits.";
	public $version = 1.0;
	public $author = "Peter Upfold";
	public $company = "Amalia";
	public $copyright = "&copy; 2010";
	public $url = "http://getamalia.com";
	
	
	public function __construct()
	{
		
		$this->attach_to_hook('editor_is_saving_form', 'filterWords', true);
	}
	
	public function filterWords($data)
	{
	
		$wordsToFilter = array(
			0 => array (
				'search' => 'circus',
				'replace' => 'cirque',
			),
			
			1 => array (
				'search' => 'liter',
				'replace' => 'litre',
			),
		);
	
		// this function receives $data['keywords'], $data['description'] and $data['content']
		// it must return an array with the same
		
		if (is_array($wordsToFilter) && count($wordsToFilter) > 0)
		{
		
			foreach($wordsToFilter as $pair)
			{
			
				$search = $pair['search'];
				$replacement = $pair['replace'];
				
				$data['content'] = str_ireplace($search, $replacement, $data['content']);
			
			}		
		
		}
		
		return array(
			'keywords' => $data['keywords'],
			'description' => $data['description'],
			'content' => $data['content'],
		);
	
	}
		
}

?>