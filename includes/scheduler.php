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
 * Scheduler
 *
 * Handles scheduled tasks -- checking when they were last run, and
 * running them if necessary.
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

define('SCHEDULER_FILE', $config['config_path'].'/amalia-scheduler.txt');
define('SCHEDULER_TIMEINTERVAL', 60*30); // number of seconds before scheduled tasks must be run again

class amalia_scheduler
{

	public function __construct()
	
	{
		/* ideally, all of the scheduler start up stuff should be done
		   here in the constructor, to minimise necessary stuff that is done
		   in index.php or whatever.
		*/
		
		if ($this->shouldRunTasksNow())
		{
			$this->runTasks();
			$this->updateLastRunTime();
		}
	
	}
	
	public function forceRun()
	{
		$this->runTasks();
		$this->updateLastRunTime();
	}


	private function runTasks()
	{
		// place any scheduled tasks to run here.
		
		// recycle bin stuff
		require_once 'recyclebin.class.php';
		$recycler = new amalia_recyclebin();
		$recycler->deleteExpiredFiles();
		
		unset($_SESSION['version_outofdate']);
		// run version check
		if (!check_version())
		{
			// out of date
			$_SESSION['version_outofdate'] = true;
		}
	
	}


	private function shouldRunTasksNow()
	{
		// check last run time of scheduled tasks based on the file

		// returns true if run is required, false if we ran them recently
		
		$fh = @fopen(SCHEDULER_FILE, 'r');
		
		if (!$fh) { // unable to open file or file does not exist
			return true; // therefore, should run the tasks!
		}
		
		$schFile = @fread($fh, @filesize(SCHEDULER_FILE));
		
		if ($schFile === false) // fread couldn't read the file
		{
			return true; // so run the tasks
		}
		
		@fclose($fh);
		
		$schArray = explode("\n", $schFile);
		$schArray = array_filter($schArray, 'filter_commented_line');
		
		// we are only expecting one line after the comment filter, so check for that
		if (count($schArray) != 1)
		{
			// this scheduler file is corrupted somehow, so delete it
			// and run the tasks, something else will recreate it later		
			@unlink(SCHEDULER_FILE);
			return true;
		}
	
		// re-key the array such that we know item zero is the number we want
		$schArray = array_values($schArray);
	
		// filter any non-numeric data from this line before the comparison
		$lastRunTime = (int) $schArray[0];
		
		// compare times
		if ($lastRunTime + SCHEDULER_TIMEINTERVAL < time())
		{
			return true;
		}		
		else
		{
			return false;
		}
	
	}
	
	private function updateLastRunTime()
	{
		// update scheduler file with current time
		// assumes we just ran the damn thing!
		
		// if file doesn't exist, this creates it, otherwise overwrites
		$fh = @fopen(SCHEDULER_FILE, 'w');
		
		if (!$fh)
		{
			friendly_fatal("Scheduler unable to write to the scheduler file: ".safe_plain(SCHEDULER_FILE).". Please check the Config Path permissions.");
			die();
		}
		
		$fileContents = <<<EOT
# Amalia Scheduler File
#
# Please do not edit this file. This file is used to determine
# whether certain scheduled tasks need to be run and contains
# a timestamp of the last time they were run.
#
# This file will be overwritten regularly by Amalia, so do not
# put anything important in this file.
EOT;

		$fileContents .= "\n".time();
		
		
		// write the new file
		try {
			if (!@fwrite($fh, $fileContents))
			{
				friendly_fatal("Scheduler unable to write to the scheduler file: ".safe_plain(SCHEDULER_FILE).". Please check the Config Path permissions.");
				die();
			}
		}
		catch (Exception $e)
		{
			friendly_fatal("Scheduler unable to write to the scheduler file: ".safe_plain(SCHEDULER_FILE).". Please check the Config Path permissions.");
			die();
		}
		
		return true;
	
	}


};

?>