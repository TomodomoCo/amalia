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

var editorHasBeenModified = false;
var shouldWarnAboutUnsavedChanges = true;

window.onload = function()
{

	tinyMCE.init({
		mode : 'exact',
		elements: 'editor',
		theme: 'advanced',
		plugins: 'inlinepopups,amalia_image,amalia_linker',
		
		// theme options
		theme_advanced_resizing: true,
		theme_advanced_toolbar_location : 'top',
		theme_advanced_toolbar_align : 'left',
		theme_advanced_buttons1 : 'cut,copy,paste,pastetext,|,undo,redo,|,amalia_linker,unlink,|,amalia_image,|,bullist,numlist,|,code',
		theme_advanced_buttons2 : 'formatselect,removeformat,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor',
		theme_advanced_buttons3 : '',
		
		theme_advanced_blockformats : 'p,h2,h3,blockquote,code',
		paste_remove_styles : 'true',
		paste_remove_styles_if_webkit : 'true',
		
		relative_urls : false,		
		content_css: THEMES_URL + '/style.css',
		
		setup : function(ed) {
		
			ed.onChange.add(function(ed, item) {
				editorHasBeenModified = true;
			});
		
		},

	});

};

// warn if leaving without saving changes
window.onbeforeunload = function() {

	if (shouldWarnAboutUnsavedChanges && editorHasBeenModified)
	{
	
		return 'You have unsaved changes on this page. If you click OK, these changes will be lost.';
	
	}

};


$(document).ready(function()
{
	$('.dropdown-m ul').css('display','block');
	$('a.dropdown-r').css('display','block');
	
	$('a.dropdown-r').click(function()
	{
		$('ul.select').toggleClass('selected');
	});
	$('.select li').click(function()
	{
		if($('ul.select').hasClass('selected'))
		{
		$('ul.select').removeClass('selected');
		var template = $(this).attr('id');
		$('input#template').val(template);
		
		$(this).prependTo('ul.select');
		}
	});
	$('.select li').each(function()
	{
		if($(this).hasClass('picked'))
		{
			$(this).prependTo('ul.select');
		}
	});
});
