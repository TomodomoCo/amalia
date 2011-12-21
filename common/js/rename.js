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


function runRename() {
	
		var filenameWithPath = $('#oldfile').val();
		var newName = $('#newname').val();
	
		$.ajax({
		
			type: "POST",
			url: js_internal_link('rename'),
			dataType: "json",
			data: "type=ajax&file=" + encodeURIComponent(filenameWithPath) + "&newname=" + encodeURIComponent(newName),
			success: function(response) {
	
				if (response.type == 'success')
				{			
					//$('#' + sender).html(response.new_name);					
					// go redirect
					window.parent.location.replace(js_internal_link('dir', 'dir=' + encodeURIComponent(thisDir)));
				}
				else {
					alert(response.error_message);	
				}
				
			},
			
			error: function(xhr, error)
			{
					
				if (xhr.statusText == 'OK')
				{
					if (DEBUG)
					{
						alert('Received a malformed response from the server.');
						alert(xhr.responseText);
					}
					else
					{
						alert('Unable to rename the file, an internal error occurred.');
					}
				}
				else {		
					alert('Unable to contact the server to rename the file. The error is ' + xhr.statusText);		
				}
			
			},
		
		});
	
	}
	
	function populateRenameForm(fullPath, filenameOnly)
	{
	
		$('#oldfile').val(fullPath);
		$('#newname').val(filenameOnly);
	
	}
