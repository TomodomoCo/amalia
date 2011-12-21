#!/usr/bin/env php
<?php
/*
Git Revision Number Updater for Footer

	This is probably a pretty awful, hacky, way to have the git commit number/hash thing
	put in the footer. We have to run this script manually on each (significant) commit,
	but at least it gives us some way of tracking git revision versions in the footer
	of Amalia admin pages, when debug is on.
	
	This file is therefore meant to be run only via CLI, in the context of the git repository.
	You should probably remove it from your web-based installation.
	
	I realise this is probably not the best solution -- but many others I researched looked
	worse. :(

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

define('VERSION_FILE', dirname(__FILE__).'/includes/common.php');
define('INSTALLER_FILE', dirname(__FILE__).'/installer/serverchecks.php');

$opts = getopt('', array('update'));

if (isset($opts['update']))
{

	$vNumber = array();
	
	exec("git show --abbrev-commit | egrep \"^commit\" | awk '{print $2}'", $vNumber);
	
	$vn = preg_replace("/[^0-9A-Fa-f]/", '', $vNumber[0]);
	
	if (!empty($vn))
	{
	
		try {
	
			$vFile = file_get_contents(VERSION_FILE);
			
			$new = preg_replace('/\$rev = \'(.*)\'; \/\/ git-rev-auto-update-line/', '$rev = \''.$vn.'\'; // git-rev-auto-update-line', $vFile);
			
			$fh = fopen(VERSION_FILE, 'w');
			fwrite($fh, $new);
			fclose($fh);
			
			$iFile = file_get_contents(INSTALLER_FILE);
			$new = preg_replace('/\$rev = \'(.*)\'; \/\/ git-rev-auto-update-line/', '$rev = \''.$vn.'\'; // git-rev-auto-update-line', $iFile);
			
			$fh = fopen(INSTALLER_FILE, 'w');
			fwrite($fh, $new);
			fclose($fh);			
			
			
			echo 'All done.';
		
		}
		catch (Exception $e)
		{
			echo 'Unable to do it. Error code: '.$e->getCode().' and message "'.$e->getMessage().'".';
		}
	
	}
	else {
		echo 'Sorry, garbage from git.';
	}

}
else {
	echo 'This is supposed to only be run from a CLI. To update the Git revision number, run me with --update.';
}

?>
