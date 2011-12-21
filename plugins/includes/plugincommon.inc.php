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



// common, non-class functions that plugin stuff will want to use

function instantiate_enabled_plugins()
{
	
	
	global $plugins, $config, $hasInstantiatedPlugins;
	
	if ($hasInstantiatedPlugins)
	{
		return;
	}
	
	$fh = fopen($config['config_path'].'/plugins/enabledPlugins.txt', 'r');
	
	if (!$fh)
	{
		throw new Exception("Could not open enabled plugins file for reading.");
		return;
	}
	
	$enabledRaw = fread($fh, filesize($config['config_path'].'/plugins/enabledPlugins.txt'));
	fclose($fh);
	
	// parse enabled plugins file
	$enabledPlugins = explode("\n", $enabledRaw);
	// filter out commented lines
	$enabledPlugins = array_filter($enabledPlugins, 'filter_commented_line');
	
	if (count($enabledPlugins) < 1)
		return;
	
	foreach($enabledPlugins as $identifier)
	{
		
		$identifier = safe_plugin_identifier($identifier);
		
		if (!empty($identifier))
		{
		
			if (!(include($config['config_path'].'/plugins/'.$identifier.'.php')))
			{
				friendly_fatal('Unable to load the plugin '.$identifier.'. The plugin file could not be found, or there was a permissions problem.');
			}
		
		}
		
		$className = plugin_identifier_to_classname($identifier);
		
		$plugins[count($plugins)] = new $className;
		
		// instantiating the plugin will automatically do the attaching
		// of hooks to functions
	}
	
	$hasInstantiatedPlugins = true;
	
	
}

function enumerate_disabled_plugins()
{
	
	// enumerate and list metadata of plugins which are 'commented out' in the activation file
	
	global $config;
	
	$fh = fopen($config['config_path'].'/plugins/enabledPlugins.txt', 'r');
	
	if (!$fh)
	{
		throw new Exception("Could not open enabled plugins file for reading.");
		return;
	}
	
	$disabledRaw = fread($fh, filesize($config['config_path'].'/plugins/enabledPlugins.txt'));
	fclose($fh);
	
	// parse plugins file
	$disabledPlugins = explode("\n", $disabledRaw);
	// filter out lines that aren't commented
	$disabledPlugins = array_filter($disabledPlugins, 'filter_non_commented_lines');
	
	if (count($disabledPlugins) < 1)
		return false;
	
	$disabledPluginsData = array();
	$i = 0; //counter

	foreach($disabledPlugins as $identifier)
	{
		
		// remove hash from start of identifier
		$identifier = str_replace('#', '', $identifier);
		
		// go through and only list real plugin identifiers that actually
		// have files (i.e. not other comments)
		
		if (!preg_match("/[a-z._\-]/i", $identifier)) { // check RDS format
			continue; // discard; don't care about this line if it's not right
		}
		
		$identifier = safe_plugin_identifier($identifier); // just to be sure, since we're about to include
		
		// can we go and load this plugin in?
		try {
			$fh = @fopen($config['config_path'].'/plugins/'.$identifier.'.php', 'r');
		}
		catch (Exception $e)
		{
			continue; // didn't work, don't care
		}
		
		if (!$fh)
			continue; // again, it didn't work, but I don't care
			
		// read the file in, we want to get yummy metadata
		$pluginRawFile = fread($fh, filesize($config['config_path'].'/plugins/'.$identifier.'.php'));
		fclose($fh);
		
		// parsing metadata? fun...
		//TODO: this needs to be robust enough to support different quote styles etc. :(
		
		$disabledPluginsData[$i]['identifier'] = $identifier;
		
		preg_match('/public \$friendlyName = \"(.*)\";/', $pluginRawFile, $match);
		$disabledPluginsData[$i]['friendlyName'] = safe_plain($match[1]);
		
		preg_match('/public \$description = \"(.*)\";/', $pluginRawFile, $match);
		$disabledPluginsData[$i]['description'] = safe_plain($match[1]);
		
		preg_match('/public \$version = \"(.*)\";/', $pluginRawFile, $match);
		$disabledPluginsData[$i]['version'] = safe_plain($match[1]);
		
		preg_match('/public \$author = \"(.*)\";/', $pluginRawFile, $match);
		$disabledPluginsData[$i]['author'] = safe_plain($match[1]);
		
		preg_match('/public \$company = \"(.*)\";/', $pluginRawFile, $match);
		$disabledPluginsData[$i]['company'] = safe_plain($match[1]);
		
		preg_match('/public \$copyright = \"(.*)\";/', $pluginRawFile, $match);
		$disabledPluginsData[$i]['copyright'] = safe_plain($match[1]);

		preg_match('/public \$url = \"(.*)\";/', $pluginRawFile, $match);
		$disabledPluginsData[$i]['url'] = safe_plain($match[1]);
		
		$i++;
				
	}
	
	return $disabledPluginsData;
	
}


function deactivate_plugin($identifier)
{
	// deactivate a plugin in the enabled plugins file
	global $config;
	
	$fh = fopen($config['config_path'].'/plugins/enabledPlugins.txt', 'r');
	
	if (!$fh)
	{
		throw new Exception("Could not open enabled plugins file for reading.");
		return false;
	}
	
	$list = fread($fh, filesize($config['config_path'].'/plugins/enabledPlugins.txt'));
	fclose($fh);
	
	$list = explode("\n", $list);

	// parse through list for this plugin and deactivate
	
	$match = false;
	
	if (is_array($list) && count($list) > 0)
	{
		
		foreach($list as $key => $line)
		{
			if ($line == safe_plugin_identifier($identifier))
			{
				
				$list[$key] = '#'.safe_plugin_identifier($identifier); // replace this line with the identifier, commented out				
				$match = true;
				
				break; // we found it, stop searching!
				
			}			
		}
		
		if (!$match)
			return false;
			
	}
	else
		return false;	
	
	// write the new list back to the file
	$fh = fopen($config['config_path'].'/plugins/enabledPlugins.txt', 'w');

	if (!$fh)
	{
		throw new Exception("Could not open enabled plugins file for writing. Check permssions?");
		return false;
	}
	
	$newContents = implode("\n", $list);
	
	$res = fwrite($fh, $newContents);
	fclose($fh);
	
	return $res;
		
}

function activate_plugin($identifier)
{
	// activate a plugin in the enabled plugins file
	global $config;
	
	// does plugin exist on disk?
	if (!file_exists($config['config_path'].'/plugins/'.safe_plugin_identifier($identifier).'.php'))
	{
		throw new Exception('The plugin specified does not exist in the plugins folder.');
		return false;
	}
	
	$fh = fopen($config['config_path'].'/plugins/enabledPlugins.txt', 'r');
	
	if (!$fh)
	{
		throw new Exception("Could not open enabled plugins file for reading.");
		return false;
	}
	
	$list = fread($fh, filesize($config['config_path'].'/plugins/enabledPlugins.txt'));
	fclose($fh);
	
	$list = explode("\n", $list);

	// parse through list for this plugin and deactivate
	
	$match = false;
	
	if (is_array($list) && count($list) > 0)
	{
		
		foreach($list as $key => $line)
		{
			if ($line == '#'.safe_plugin_identifier($identifier))
			{
				
				$list[$key] = safe_plugin_identifier($identifier); // replace this line with the identifier without # char				
				$match = true;
				
				break; // we found it, stop searching!
				
			}			
		}
		
		if (!$match)
			return false;
			
	}
	else
		return false;	
	
	// write the new list back to the file
	$fh = fopen($config['config_path'].'/plugins/enabledPlugins.txt', 'w');

	if (!$fh)
	{
		throw new Exception("Could not open enabled plugins file for writing. Check permssions?");
		return false;
	}
	
	$newContents = implode("\n", $list);
	
	$res = fwrite($fh, $newContents);
	fclose($fh);
	
	return $res;
		
}

function deactivate_all_plugins()
{
	// deactivate all plugins in the enabled plugins file
	global $config;
	
	$fh = fopen($config['config_path'].'/plugins/enabledPlugins.txt', 'r');
	
	if (!$fh)
	{
		throw new Exception("Could not open enabled plugins file for reading.");
		return false;
	}
	
	$list = fread($fh, filesize($config['config_path'].'/plugins/enabledPlugins.txt'));
	fclose($fh);
	
	$list = explode("\n", $list);

	// parse through list for all valid plugin identifiers and deactivate
	
	if (is_array($list) && count($list) > 0)
	{
		
		foreach($list as $key => $line)
		{
			if ($line == preg_match("/[^a-z._\-]/i", $line))
			{
				
				$list[$key] = '#'.safe_plugin_identifier($line);	
				
			}
		}			
	}
	else
		return false;	
	
	// write the new list back to the file
	$fh = fopen($config['config_path'].'/plugins/enabledPlugins.txt', 'w');

	if (!$fh)
	{
		throw new Exception("Could not open enabled plugins file for writing. Check permssions?");
		return false;
	}
	
	$newContents = implode("\n", $list);
	
	$res = fwrite($fh, $newContents);
	fclose($fh);
	
	return $res;
		
}

function load_endpoint($pluginIdentifier, $endpointIdentifier, $getdata)
{
	global $config, $E2F;
	

	// parses a plugin endpoint request from the URL fragments passed to it,
	// works out which plugin endpoint to execute based on E2F table and
	// passes execution to the same.
	
	$pluginIdentifier = safe_plugin_identifier($pluginIdentifier);
	$endpointIdentifier = safe_plugin_identifier($endpointIdentifier);
	
	// instantiate plugins. This has the effect of creating the E2F table, as
	// the plugins are responsible for declaring endpoints. At endpoint construction,
	// they are automatically added to the table.
	instantiate_enabled_plugins();
	
	// E2F table now created and ready.
	
	// is there any entry in E2F for the given plugin identifier?
	if (!is_array($E2F[$pluginIdentifier]) || (count($E2F[$pluginIdentifier]) < 1) )
	{
		return false; // return back to router, we can't load the endpoint
	}
	
	// is there an entry for this specific endpoint in the E2F?
	if (!is_array($E2F[$pluginIdentifier][$endpointIdentifier]) || (count($E2F[$pluginIdentifier][$endpointIdentifier]) < 1) )
	{
		return false; // go back to router with fail
	}

	// there is a record for this endpoint, so pull that out.
	$endpoint = $E2F[$pluginIdentifier][$endpointIdentifier];
	
	// check the record for validity
	if (!isset($endpoint['type']) || ($endpoint['type'] != 'viewer' && $endpoint['type'] != 'data_processor') )
	{
		friendly_fatal('Unable to load the page. Endpoint '.$endpointIdentifier.' of plugin '.$pluginIdentifier.' does not have a valid type declaration.');
		die();
	}
	
	if ($endpoint['identifier'] != $endpointIdentifier)
	{
		friendly_fatal('Unable to load the page. Endpoint '.$endpointIdentifier.' of plugin '.$pluginIdentifier.' does not have a valid endpoint identifier declaration.');
		die();
	}
	
	if ($endpoint['plugin'] != $pluginIdentifier)
	{
		friendly_fatal('Unable to load the page. Endpoint '.$endpointIdentifier.' of plugin '.$pluginIdentifier.' does not have a valid plugin identifier declaration.');
		die();
	}
	
	if (!isset($endpoint['class']) || empty($endpoint['class']))
	{
		friendly_fatal('Unable to load the page. Endpoint '.$endpointIdentifier.' of plugin '.$pluginIdentifier.' does not have a valid attached class.');
		die();
	}
	
	if (!isset($endpoint['function']) || empty($endpoint['function']))
	{
		friendly_fatal('Unable to load the page. Endpoint '.$endpointIdentifier.' of plugin '.$pluginIdentifier.' does not have a valid attached function.');
		die();
	}
	
	// is the specific class/function callable?
	try {
			if (!is_callable( array($endpoint['class'], $endpoint['function']) ))
			{
				throw new PluginException($pluginIdentifier);
			}
		}
		catch (PluginException $e)
		{
			$e->friendly_error($pluginIdentifier);
			return false;
		}
		
	
	if ($endpoint['type'] == 'viewer')
	{
		// do header stuff
		if (!empty($endpoint['amalia_pagetitle']))
			define('AMALIA_PAGETITLE', $endpoint['amalia_pagetitle']);
			
		require_once($config['config_path'].'/includes/templates/header_in.php');
	}
	
	// pass execution to plugin
	call_user_func( array($endpoint['class'], $endpoint['function']));	
	
	if ($endpoint['type'] == 'viewer')
	{
		// do footer stuff?
		require_once($config['config_path'].'/includes/templates/footer_in.php');
	}
	
	return true;

}


function safe_plugin_identifier($identifier)
{
	
	// allow plugin identifiers only to fit these chars
	// prevent people from ../'ing in identifiers and bad stuff
	
	return preg_replace("/[^a-z._\-]/i", "", $identifier);
	
}

function plugin_identifier_to_classname($identifier)
{
	
	// convert a plugin's native RDS identifier to a class name compliant one
	
	return str_replace('.', '_', $identifier);	
	
}

?>