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


require_once('files.inc.php');

//assure the action supergloabl equals resize
if($_GET['action'] == 'resize') 
{
	//get the file
	$file = $_GET['file'];
	
	//get the thumbnail directory
	$thumbdir = $_GET['thumbdir'];

	ob_start(); //again I don't know why this is here
	// prevents the page from being progressively sent to the browser until the proper
	// image processing is complete. Only once ob_flush is called, is the output is sent
	// from the server to the browser.
	
	//if the thumbnaildir does not exist, create it
	if(!file_exists($thumbdir)) 
	{
		mkdir($thumbdir);
	} //end file_exists conditional
	
	//get the pathinfo for the file
	$finfo = pathinfo($file);

	//Name you want to save your file as
	$save = $thumbdir.'/'.$finfo['filename'].'_thumb.'.$finfo['extension'];

	//set the new size of the image to 45% of its original dimensions
	$size = 0.45;
	//set the proper header
	switch($finfo['extension']) 
	{
		case 'jpg':
			header('Content-type: image/jpeg');
		break;
		case 'jpeg':
			header('Content-type: image/jpeg');
		break;
		case 'gif':
			header('Content-type: image/gif');
		break;
		case 'png':
			header('Content-type: image/png');
		break;
	} //end header type conditional
	
	//set the width and height variables depending on the result of getimagesize()
	list($width, $height) = getimagesize($file) ;
	
	//alter the width per the $size variable
	$modwidth = $width * $size;
	
	//alter the height per the $size variable
	$modheight = $height * $size;
	
	//create a new base image
	$tn = imagecreatetruecolor($modwidth, $modheight) ;

	//set the appropriate imagecreatefrom* depending on extension
	switch($finfo['extension']) 
	{
		case 'jpg':
			$image = imagecreatefromjpeg($file);
		break;
		case 'jpeg':
			$image = imagecreatefromjpeg($file);
		break;
		case 'gif':
			$image = imagecreatefromjgif($file);
		break;
		case 'png':
			$image = imagecreatefrompng($file);
		break;
	}//end imagecreatefrom* conditional
	
	//copy and resize the new image
	imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;

	// Here we are saving the .jpg, you can make this gif or png if you want
	//the file name is set above, and the quality is set to 100%

	switch($finfo['extension']) {
		case 'jpg':
			imagejpeg($tn, $save, 100);
		break;
		case 'jpeg':
			imagejpeg($tn, $save, 100);
		break;
		case 'gif':
			imagegif($tn, $save, 100);
		break;
		case 'png':
			imagepng($tn, $save, 100);
		break;
	} //end image* creation switch
	
	ob_flush(); //not sure why this is here, leaving it to be safe


} //end resize conditional
?>