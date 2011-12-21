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




class Hook
{
	
	public $identifier;
	
	public function __construct($hookIdentifier, $data = false, &$modifiedData = NULL)
	{
		
		global $h2F;
		
		$this->identifier = $hookIdentifier;
		
		// has the hooks to functions table been created yet?
		if (!is_array($h2F) || count($h2F) < 1)
		{
			// get everything rolling
			instantiate_enabled_plugins();			
		}		
		
		// are there any attached functions for this hook?
		if (!is_array($h2F[$this->identifier]) || count($h2F[$this->identifier]) < 1)
		{
			// no, so nothing to do
			
				// give back the unmodified data to core (core assumes it might have been modified)
				$modifiedData = $data;
				
				return false;
		}
		
		// if so, go through by each plugin
		foreach($h2F[$this->identifier] as $pluginIdentifier => $pluginFunctions)
		{
			
			if (!is_array($pluginFunctions) || count($pluginFunctions) < 1)
			{
				// for some reason, the plugin is here, but no attached functions
				continue;
			}
			
			foreach($pluginFunctions as $attachedFunction)
			{
				if (!is_array($attachedFunction) || count($attachedFunction) < 1)
					continue;
					
				// work out exactly which class/function we have to call, by
				// looking up plugin's class first
				$fnClass = plugin_identifier_to_classname($pluginIdentifier);
				
				// are we capable of calling what we're being asked to?
				try {
					if (!is_callable( array($fnClass, $attachedFunction['function']) ))
					{
						throw new PluginException('Plugin '.$pluginIdentifier.', Hook '.$this->identifier.":\ncannot call ".$fnClass.'::'.$attachedFunction['function'].' because it is not a callable function (check function name spelling, is it public?).');
					}
				}
				catch (PluginException $e)
				{
					$e->friendly_error($pluginIdentifier);
					continue;					
				}
				
				// if it wants data, call it passing in whatever and pushing data back out, or just call it.				
				if ($attachedFunction['wantsData'])
				{
					//DEBUG echo 'DEBUG: about to call '.$fnClass.'::'.$attachedFunction['function'].' with data';
					$modifiedData = call_user_func( array($fnClass, $attachedFunction['function']), $data);					
				}
				else
				{				
					//DEBUG echo 'DEBUG: about to call '.$fnClass.'::'.$attachedFunction['function'].' no data';
					call_user_func( array($fnClass, $attachedFunction['function']));	
				}
										
			}
			
		}
		
		
	}
		
};


?>