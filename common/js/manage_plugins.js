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

// JQuery AJAX for activating and deactivating plugins
//TODO: handle Ajax errors gracefully, or even at all!

function togglePlugin(pluginIdentifier)
{

	var formsec = document.getElementById(pluginIdentifier + '_formsec').value;
	var stm = document.getElementById(pluginIdentifier +  '_stm').value;
	var actstate = document.getElementById(pluginIdentifier + '_actstate').value;

	if (actstate == 'act') {
	
		deactivatePluginAj(pluginIdentifier, formsec, stm);
		$('#' + actstate).val('deact');
	}
	else {
		activatePluginAj(pluginIdentifier, formsec, stm);
		$('#' + actstate).val('act');
	}

}


function activatePluginAj(pluginIdentifier, formsec, stm)
{

	$.ajax({
	
		type: "POST",
		url: js_internal_link('settings', 'do=activate-plugin'),
		data: "type=ajax&plugin=" + encodeURIComponent(pluginIdentifier) + "&formsec=" + encodeURIComponent(formsec) + "&stm=" + encodeURIComponent(stm),
		success: function(msg) { },
		
		failure: function(msg) { alert('Activating the plugin failed.'); }
		
	});
	
	return false;
	
}

function deactivatePluginAj(pluginIdentifier, formsec, stm)
{

	$.ajax({
	
		type: "POST",
		url: js_internal_link('settings', 'do=deactivate-plugin', true),
		data: "type=ajax&plugin=" + encodeURIComponent(pluginIdentifier) + "&formsec=" + encodeURIComponent(formsec) + "&stm=" + encodeURIComponent(stm),
		success: function(msg) { },
		
		failure: function(msg) { alert('Activating the plugin failed.'); }
	
	
	});
	
	return false;
	
}
