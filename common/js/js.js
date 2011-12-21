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

$(document).ready(function(){

	$(".checkall").click(function(){
		var checked_status = this.checked;
		$("input[type=checkbox]").each(function() {
			this.checked = checked_status;
		});
	});
  
  	$('#scroll-outer').jScrollPane({
  		showArrows:false,
  		scrollbarWidth:15,
  		dragMinHeight:50,
  		dragMaxHeight:50
  	});
	
	$(".iseditable").editInPlace({
		url: js_internal_link('edit-title'),
		field_type: 'text',
		show_buttons: true,
		success: function(response) {},
		error: function(xhr) {
			alert('There was a problem and this file\'s title could not be changed.');
			if (DEBUG)
			{
				alert(xhr.responseText);
			}
		}
	});
	
	$("#h1").animate({
		width: "73px"
	}, 800, function(){
		
	})
	
	$("#menu-arrow").animate({
		width: "25px",
		opacity: "1.0"
	}, 800, function(){
		$("#menu").css("visibility","visible")
		$(this).fadeTo(0, 0.0);
		$("#h1").fadeTo(0, 0.0);
	})
	
	// Delete slider

	$(".file .holder").mousedown(function(){
		$(this).animate({ 
			width: "348px",
			// left: "-320px",
		}, 100, function(){
			
		})
		.addClass("open-delete")
		.removeClass("closed-delete")
	});
	
	$(".user .holder").mousedown(function(){
		$(this).animate({ 
			width: "348px",
			// left: "-320px",
		}, 100, function(){
			
		})
		.addClass("open-delete")
		.removeClass("closed-delete")
	});
	
	$(".folder .holder").mousedown(function(){
		$(this).animate({ 
			width: "363px",
			// left: "-335px",
		}, 100, function(){
			
		})
		.addClass("open-delete")
		.removeClass("closed-delete")
	});

	$(".delete .holder .confirmation a").click(function(){
		$(this).parents(".delete .holder").animate({ 
			width: "28px",
			left: "0px",
		}, 300, function (){
			
		})
		.removeClass("open-delete")
		.addClass("closed-delete")
	});
	
	$(".holder").mouseover(function(){
		$(this).css("border","1px solid #999").css("background-color","#DDD");
	}).mouseout(function(){
		if(!$(this).hasClass('open-delete'))
		{
		$(this).css("border","1px solid transparent").css("background-color","transparent");
		}
	});
	
	// Slide open site config details in /settings
	
	$("#firstaid-config").click(function(){
		if ($(this).hasClass('open')) {
			$(this).animate({ 
				height: "26px",
				overflow: "hidden",
			}, 300, function (){
				
			}).addClass('closed').removeClass('open');
		} else {
			$(this).animate({ 
				height: "583px",
				overflow: "auto",
			}, 300, function (){
				
			}).addClass('open').removeClass('closed');
		}
	});
	
	// Form focus
	
	var ICanHasFocus = false;
	
	$("#hello input[type='text']").hover(function(){
		$(this).css("border-bottom","1px solid #333")
	}, function(){
		if(ICanHasFocus) {} else {
			$(this).css("border-bottom","1px solid transparent")
		}
	}).focus(function(){
		$(this).css("border-bottom","1px solid #333")
		ICanHasFocus = true;
	}).blur(function(){
		$(this).css("border-bottom","1px solid transparent")
		ICanHasFocus = false;
	})
	
	// check for what is/isn't already checked and match it on the fake ones
	$("input:checkbox").each( function() {
		(this.checked) ? $("#enable"+this.id).addClass('unlock') : $("#enable"+this.id).removeClass('unlock');
	});
	// function to 'check' the fake ones and their matching checkboxes
	$(".lock").click(function(){
		($(this).hasClass('unlock')) ? $(this).removeClass('unlock') : $(this).addClass('unlock');
		$(this.hash).trigger("click");
		return false;
	});
	
	$("input#rename-submit").mouseover(function () {
		$("#rename-form").css("background","url(images/form.png) center -49px no-repeat");
	}).mousedown(function(){
		$("#rename-form").css("background","url(images/form.png) center bottom no-repeat");
	}).mouseup(function(){
		$("#rename-form").css("background","url(images/form.png) center -49px no-repeat");
	}).mouseout(function(){
		$("#rename-form").css("background","url(images/form.png) center 0px no-repeat");
	});	

	$('#menu').click(function()
	{
		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}
	});
	$('#menu').click(function(e){
		e.stopPropagation();
	});
	$(document).click(function(){
		$('#menu').removeClass('active');
	});
	
	$('a.change').click(function()
	{
		if($(this).parent().hasClass('no-now')){
			$(this).find('span').animate({
				left: "106px"
			}, 120, function(){
			
			});
			$(this).parent().find('.no').animate({
				width: "35px",
				left: "108px"
			}, 120, function(){
			
			});
			$(this).parent().find('.yes').animate({
				width: "108px"
			}, 120, function(){
			
			});
			$(this).parent().addClass('yes-now').removeClass('no-now');
		} else {
			$(this).find('span').animate({
				left: "0"
			}, 120, function(){
			
			});
			$(this).parent().find('.no').animate({
				width: "108px",
				left: "35px"
			}, 120, function(){
			
			});
			$(this).parent().find('.yes').animate({
				width: "35px"
			}, 120, function(){
			
			});
			$(this).parent().addClass('no-now').removeClass('yes-now');
		}
	});
	
	// New File
	$("#new-file").click(function(){
		$("#create-file").animate({ 
			height: "26px",
			margin: "10px 0 0",
			padding: "15px 0 18px",
		}, 200, function (){
		
		}).animate({ 
			borderTopColor: "#BBB",
			borderRightColor: "#BBB",
			borderBottomColor: "#BBB",
			borderLeftColor: "#BBB",
		}, 250, function (){
		
		}).addClass('create-file-open');
		$("#white").delay(200).fadeIn(150);
		$("#create-file .filetype").delay(200).fadeIn(250);
		$("#create-file form").delay(200).fadeIn(250);
		
		// Hide create folder form
		$("#create-folder .filetype").fadeOut(250);
		$("#create-folder form").fadeOut(250);
		$("#create-folder").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({ 
			height: "0",
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('create-folder-open');
		
		// Hide upload file
		$("#upload-file .filetype").fadeOut(250);
		$("#upload-file .filemeta").fadeOut(250);
		$("#upload-file button").fadeOut(250);
		$("#upload-file").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('upload-file-open');
	});
	
	// New Folder
	$("#new-fold").click(function(){
		$("#create-folder").animate({ 
			height: "26px",
			margin: "10px 0 0",
			padding: "15px 0 18px",
		}, 200, function (){
		
		}).animate({ 
			borderTopColor: "#BBB",
			borderRightColor: "#BBB",
			borderBottomColor: "#BBB",
			borderLeftColor: "#BBB",
		}, 250, function (){
		
		}).addClass('create-folder-open');
		$("#white").delay(200).fadeIn(150);
		$("#create-folder .filetype").delay(200).fadeIn(250);
		$("#create-folder form").delay(200).fadeIn(250);
		
		// Hide create file
		$("#create-file .filetype").fadeOut(250);
		$("#create-file form").fadeOut(250);
		$("#create-file").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({ 
			height: "0",
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('create-file-open');
		
		// Hide upload file
		$("#upload-file .filetype").fadeOut(250);
		$("#upload-file .filemeta").fadeOut(250);
		$("#upload-file button").fadeOut(250);
		$("#upload-file").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('upload-file-open');
	});
	
	// Upload File
	$("#upl-file").click(function(){
		$("#upload-file").animate({
			margin: "10px 0 0",
			padding: "15px 0 5px",
		}, 200, function (){
		
		}).animate({ 
			borderTopColor: "#BBB",
			borderRightColor: "#BBB",
			borderBottomColor: "#BBB",
			borderLeftColor: "#BBB",
		}, 250, function (){
		
		}).addClass('upload-file-open');
		$("#white").delay(200).fadeIn(150);
		$("#upload-file .filetype").delay(200).fadeIn(250);
		$("#upload-file .filemeta").delay(200).fadeIn(250);
		$("#upload-file button").delay(200).fadeIn(250);
		
		// Hide create file
		$("#create-file .filetype").fadeOut(250);
		$("#create-file form").fadeOut(250);
		$("#create-file").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({ 
			height: "0",
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('create-file-open');
		
		// Hide create folder
		$("#create-folder .filetype").fadeOut(250);
		$("#create-folder form").fadeOut(250);
		$("#create-folder").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({ 
			height: "0",
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('create-folder-open');
	});
	
	// Fade out white
	$("#white").click(function(){
		$(this).delay(250).fadeOut(450);
		$("#create-file .filetype").fadeOut(250);
		$("#create-file form").fadeOut(250);
		$(".lock").fadeOut(250);
		$("#create-file").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({ 
			height: "0",
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('create-file-open');
		
		// Hide create file
		$("#create-file .filetype").fadeOut(250);
		$("#create-file form").fadeOut(250);
		$("#create-file").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({ 
			height: "0",
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('create-file-open');
		
		// Hide create folder
		$("#create-folder .filetype").fadeOut(250);
		$("#create-folder form").fadeOut(250);
		$("#create-folder").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({ 
			height: "0",
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('create-folder-open');
		
		// Hide upload file
		$("#upload-file .filetype").fadeOut(250);
		$("#upload-file .filemeta").fadeOut(250);
		$("#upload-file button").fadeOut(250);
		$("#upload-file").animate({ 
			borderTopColor: "#FFF",
			borderRightColor: "#FFF",
			borderBottomColor: "#FFF",
			borderLeftColor: "#FFF",
		}, 250, function (){
		
		}).delay(200).animate({
			margin: "0",
			padding: "0",
		}, 250, function (){
		
		}).removeClass('upload-file-open');
	});
	
	function autoFill(id, v){
		$(id).attr({ value: v }).focus(function(){
			if($(this).val()==v){
				$(this).val("");
			}
		}).blur(function(){
			if($(this).val()==""){
				$(this).val(v);
			}
		});
	}
	
	autoFill($("#hello input[type='text']"), "What should we call you?");
	autoFill($("#file_title"), "File name...");
	autoFill($("#folder_name"), "Folder name...");
	
	
	// 'What should we call you?' form submit
	
	$('#hello-edit').submit(function() {
		var newFname = $("#hello input[type='text']").val();
		$.ajax({
			url: js_internal_link('settings', 'do=edit-user&hello-fname-editor=true'),
			type: 'POST',
			data: 'fname=' + encodeURIComponent(newFname),
			success: function(msg)
			{
				//TODO: Chris, can you make this reset to the normal static state in a pretty way?
				
				$('#hello input[type="submit"]').toggle(); // hide submit button
				// change form to static text
				autoFill($("#hello input[type='text']"), newFname);
			},
			error: function(msg)
			{
				alert(msg.responseText);
			},		
		});
		return false; // prevent page from reloading
	});
	
	if ( window.addEventListener ) {
		var kkeys = [], konami = "38,38,40,40,37,39,37,39,66,65";
		window.addEventListener("keydown", function(e){
		
			kkeys.push( e.keyCode );
			if ( kkeys.toString().indexOf( konami ) >= 0 )
			{
				alert('All your Konami are belong to Amalia.');
				window.location.replace(CONFIG_URL + '/includes/etc/template_functions.php?do=ohaithur');
			}
		}, true);
      }

});


function toggleField(fieldSelector)
{
	// toggle the hidden field behind an 'on/off' switch.
	// Should be included as part of the a class='change'
	
	var currentValue = $(fieldSelector).val();
	
	if (currentValue == 'true')
	{
		$(fieldSelector).val('false'); // set to false
	}
	else {
		$(fieldSelector).val('true'); // set to true
	}

}