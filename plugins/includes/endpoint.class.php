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


// ------------------------------------------------------------------------

/**
 * Endpoint Class
 *
 * Allows plugins to create their own 'pages' within the Amalia interface
 * for custom interfaces and completely custom functionality.
 *
 * @package	Amalia2
 * @category	Amalia Generation
 * @author		Amalia Dev Team
 */
 

// example of creating an instance
// $ep_show_charts = new Endpoint('viewer', 'show_charts', $this->identifier, 'show_charts');
 
class Endpoint {


	public $identifier; 	// in valid alphanum and underscore format?
	public $plugin;			// identifier of the plugin this belongs to
	public $type;			// enum 'viewer','data_processor'
	public $calling_class;	// the class of the plugin that set me up
	public $function_to_call;// which function in the plugin to execute
	public $amalia_pagetitle = null; // a friendly title for the Amalia interface
	
	public function __construct($type, $endPointIdentifier, $pluginIdentifier, $function, $amalia_pagetitle = null)
	{
		
		global $E2F;
	
		// set up my vars
		$this->identifier = safe_plugin_identifier($endPointIdentifier);
		$this->plugin = safe_plugin_identifier($pluginIdentifier);
		$this->type = ($type == 'viewer') ? 'viewer' : 'data_processor'; // enforce either one or the other
		$this->amalia_pagetitle = $amalia_pagetitle;

		$calling_class = plugin_identifier_to_classname($this->plugin);

		// check class/fn is callable
		// are we capable of calling what we're being asked to?
		try {
			if (!is_callable( array($calling_class, $function )) )
			{
				throw new PluginException($pluginIdentifier);
			}
		}
		catch (PluginException $e)
		{
			$e->friendly_error($identifier);
			return false;
		}
		
		$this->calling_class = $calling_class;
		$this->function_to_call = $function;
		
		// add me to E2F with all required data, ready for the router in case it calls me
		$E2F[$this->plugin][$this->identifier] = array(
		
										'type' 			=> $this->type,
										'identifier' 	=> $this->identifier,
										'plugin' 		=> $this->plugin,
										'class'			=> $this->calling_class,
										'function'		=> $this->function_to_call,
										'amalia_pagetitle'	=> $this->amalia_pagetitle,
	
		);
		
		// done
		return true;		
	
	}

	

};