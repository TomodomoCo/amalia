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

// Demonstration EndPoint plugin

class com_getamalia_DemoEndpointPlugin extends Plugin
{
	
	public $identifier = "com.getamalia.DemoEndpointPlugin";
	public $friendlyName = "Demo Endpoint Plugin";
	public $description = "Provides some example endpoints for testing the endpoint architecture.";
	public $version = 1.0;
	public $author = "Peter Upfold";
	public $company = "Amalia";
	public $copyright = "&copy; 2010";
	public $url = "http://getamalia.com";
	
	public function __construct()
	{
		
		$ep_test = new Endpoint('viewer', 'test_endpoint', $this->identifier, 'test_callback', 'Test Plugin Endpoint!');
		$ep_form = new Endpoint('viewer', 'form', $this->identifier, 'form', 'Form');
		$ep_process_form = new Endpoint('data_processor', 'process_form', $this->identifier, 'process_form');
		$ep_erase_data = new Endpoint('data_processor', 'erase_data', $this->identifier, 'erase_data');
	}
	
	public function test_callback()
	{
		echo '<h1>This is the callback for the <a href="'.print_endpoint_link('com.getamalia.DemoEndpointPlugin', 'test_endpoint', null, true).'">test endpoint.</a></h1>';
		return true;
	}
	
	public function form()
	{
	
		echo $this->identifier;
	
		// shows a form to submit data to the data processor endpoint
		
		if (!empty($_SESSION['com.getamalia.DemoEndpointPlugin.previouslyProcessedData']))
		{
			?><p>Previously processed data:
			<?php echo safe_plain($_SESSION['com.getamalia.DemoEndpointPlugin.previouslyProcessedData']);?></p>
			
			<a href="<?php print_endpoint_link('com.getamalia.DemoEndpointPlugin', 'erase_data');?>">Erase this data?</a>
			
			<?php
		}
		
		if ($_GET['data'] == 'hasReturned')
		{
			?><p>Has returned.</p><?php
		}
		
		?>
		<form method="post" action="<?php print_endpoint_link($this->identifier, 'process_form');?>">
		<p>This is a test form to show you how plugins can use endpoints to display forms and then process data, all within the Amalia
		interface.</p>
		<p>Enter some text:</p>
		<p><textarea name="text" style="width:350px;height:160px"></textarea></p>
		<p><input type="submit" value="Send to processor" /></p>
		</form>
		<?php
	
	}
	
	public function process_form()
	{

	
		// expects data via POST
		
		$_SESSION['com.getamalia.DemoEndpointPlugin.previouslyProcessedData'] = implode(',', $_POST);
		
		/*print_r($_POST);
		print_r($_SESSION);die();*/
		
		redirect_to_endpoint('com.getamalia.DemoEndpointPlugin', 'form', 'hasReturned');
	
	}
	
	public function erase_data()
	{
		
		unset($_SESSION['com.getamalia.DemoEndpointPlugin.previouslyProcessedData']);
		redirect_to_endpoint('com.getamalia.DemoEndpointPlugin', 'form');
	}
	
	
}

?>