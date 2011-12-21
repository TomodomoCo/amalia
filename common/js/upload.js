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

// upload code

$(document).ready(function(){

	$('#file-upload-field').uploadify({
		'uploader': CONFIG_URL + '/common/js/uploadify/uploadify.swf',
		'script': js_internal_link('upload', 'type=ajax'),
		'folder': 'dummy',
		'cancelImg': CONFIG_URL + '/common/js/uploadify/cancel.png',
		'multi': true,
		'scriptData': {
			'submit': 'uploadify submitted form',
			'amalia_session_override': session_id_override,
			'type': 'ajax',
			'dir': dir,
		},
		
		'onComplete': function(event, queueID, fileObj, response, data) {
	
			var responseObj = eval('(' + response + ')'); // bring in the JSON
					
			if (responseObj.type == 'success')
			{
				
				var successMessage = '<li>File "' + fileObj.name + '" uploaded successfully.';
			
				$("#file-upload-info").append(successMessage);						
			}
			else {
				$("#file-upload-info").append('<li>' + responseObj.error_message + '</li>');
			}
			
			return true;
		},
		
		'onError': function(event, queueID, fileObj, errorObj) {
			alert('There was an error communicating with the server to complete the upload.\n\nThe error information is ' + errorObj.info);
		},
		
	});

});