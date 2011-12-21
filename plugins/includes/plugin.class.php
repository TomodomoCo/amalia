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



class Plugin
{
	
	public $identifier;
	public $friendlyName;
	public $description;
	public $version;
	public $author;
	public $company;
	public $copyright;
	public $url;
	
	public function __construct()
	{
		
		// should be overridden by plugin subclasses
		
		
		// this is where hook calls go
		
	}
	
	protected function attach_to_hook($hook, $function, $wantsData)
	{
		
		global $h2F;
		
		if (!is_array($h2F[$hook]) || count($h2F[$hook]) < 1)
		{
			// create blank node for this whole hook if not there
			$h2F[$hook] = array();
		}
		
		if (!is_array($h2F[$hook][$this->identifier]) || count($h2F[$hook][$this->identifier]) < 1)
		{
			// create blank node for this plugin ID if not there for current hook
			$h2F[$hook][$this->identifier] = array();	
		}
		
		$existingHookCount = count($h2F[$hook][$this->identifier]);
		// number of attached functions for this hook and this plugin
		// in most cases will just be zero
		
		
		// add a node to the table for this function
		$h2F[$hook][$this->identifier][$existingHookCount] = array(
			'function' => $function,
			'wantsData' => $wantsData
		);
		
		// all done -- the lookup table now knows about this function.
		// It is attached and ready to run.		
		
	}
	
	
};


?>