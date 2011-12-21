<?php
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

// some Amalia initialisation?

	// grab config and print_internal_link support
	define('IN_AMALIA','true');
	require('../../../../../includes/common.php');
	require('../../../../../amalia-config.php');
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<!-- all temporary style and stuff -->
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Amalia Insert Link</title>
		<script type="text/javascript" src="<?php echo $config['config_url']; ?>/common/js/tiny_mce/tiny_mce_popup.js"></script>
		<script type="text/javascript">
		function insertExternalLink(file, desc) {
			
			var ed = tinyMCEPopup.editor, dom = ed.dom;
	
			var n = tinyMCEPopup.editor.selection.getNode();
			
			if (n.nodeName == 'IMG') {
			
			tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {
				href : file,
				title : desc,
				}, dom.createHTML('img', {src : n.src, alt : n.alt})
			));
			
			} else {
			
			tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('a', {
				href : file,
				title : desc,
				}, tinyMCEPopup.editor.selection.getContent({format:'text'})
			));
			}
			
			tinyMCEPopup.close();
			
		}
		</script>
	<body>
		<h1>Insert Link</h1>
		
		<p>Please type in or paste the link below.</p>
		
		<form name="linkform" onsubmit="insertExternalLink(document.getElementById('link').value, document.getElementById('desc').value);">
		
			<p>URL: <input type="text" name="link" id="link" value="http://" /></p>
			<p>Link Description: <input type="text" name="desc" id="desc" value="" /></p>
			
			
			<input type="submit" name="submit" value="Insert Link" />
					
		</form>
		
	</body>
</html>		