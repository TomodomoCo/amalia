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

(function($) {
    $.fn.templatedSelect = function(options) {
        if (this.length == 0)
            return;

        //define the default options
        var defaults = {
            selectHandleImage: "selectHandle.gif",
            backgroundColor: "#FFFFFF",
            width: "85px",
			maxHeight: "100px",
            getOption: function(value, text) {
                return text;
            }
        };

        //merge passed in options with the defaults
        var opts = $.extend(defaults, options);

        //keep a reference back to the select which is being replaced
        var $originalSelect = this;

        //create a div to contain everything
        var $container = $(document.createElement('div'))
			.css({
			    width: opts.width,
			    backgroundColor: opts.backgroundColor
			})
			.hover(
				function() {
				    $selectBox.css("border-color", "#000000");
				},
				function() {
				    if (!$menuItems.is(":visible"))
				        $selectBox.css("border-color", "#C0C0C0");
				})
			.attr('id', "imageSelect_container_" + this.attr('id'));

        //create the box the user will see
        var $selectBox = $(document.createElement('div'))
			.css({
			    border: "solid 1px #C0C0C0",
			    overflow: "hidden",
			    width: "100%",
				backgroundColor: "#E0E0E0"
			})
			.click(function(e) {
			    ToggleMenuItems();
			});

        //create a placeholder for the selected item. When the user selects an item, the html will be copied from the menu item
        var $selectedItem = $(document.createElement('div'))
			.css({ 
				margin: "1 16 1 1",
				padding: "4 0 4 4",
				backgroundColor: "#fff"
			});

        //create a handle to let the user click to show the selection item list
        var $selectHandle = $(document.createElement('div'))
			.css({
			    float: "right",
			    cursor: "hand",
				width: "16px",
				textAlign: "center",
				background: "url('" + opts.selectHandleImage + "') no-repeat center"
			});

        var $menuItems = $(document.createElement('div'))
			.css({
			    position: "absolute",
			    marginTop: "-1px",
			    border: "solid 1px #000000",
			    backgroundColor: opts.backgroundColor,
			    zIndex: "999",
			    maxHeight: opts.maxHeight,
			    overflow: "auto"
			});

        var $clear = $(document.createElement('div'))
			.css({
			    clear: "both",
			    height: "1px",
			    border: "none",
			    margin: 0,
			    padding: 0
			})
			.html("&nbsp;");

        $originalSelect.children("option").each(function(i, selected) {
            var $menuItem = $(document.createElement('div'))
				.css("padding", "4px")
				.html(opts.getOption($(this).val(), $(this).text()))
				.val($(this).val())
				.click(function(e) {
				    ToggleMenuItems();
				    $originalSelect.val($(this).val());
				    $selectedItem.html($(this).html());
				})
				.hover(
					function() {
					    $(this).css("background-color", "#81BEF7");
					},
					function() {
					    $(this).css("background-color", opts.backgroundColor);
					})
				.appendTo($menuItems);
        });

        //preset the selectedItem
        $selectedItem.html(
			$menuItems.children("div:eq(" + $originalSelect[0].selectedIndex + ")").html()
		);

        //put everything together
        $selectBox.appendTo($container);
		$selectHandle.appendTo($selectBox);
        $selectedItem.appendTo($selectBox);
        $menuItems.appendTo($container);

        //hide the original select and put ours in
        $originalSelect.hide();
        $container.insertBefore($originalSelect);

        //set the width and height of the UI components so everything lines up nicely
        if ($menuItems.height() > parseInt($menuItems.css("maxHeight"))) {
            $menuItems.height($menuItems.css("maxHeight"));
		}
		$selectHandle.height($selectBox.height() - 2);
        $menuItems.width($selectBox.width());
        $menuItems.hide();

        //hack: When inside of a hidden element, widths and heights don't calculate propertly. 
        //apply the widths and heights once on focus until a better solution is found.
        if ($selectBox.width() == 0) {
            $container.one("mouseover", function() {
                $menuItems.width($selectBox.width());
            });
        }

        function ToggleMenuItems() {
            if ($menuItems.is(":visible")) {
                $menuItems.hide();
                $selectBox.css("border", "solid 1px #C0C0C0");
            } else {
                $menuItems.show();
                $selectBox.css("border", "solid 1px #000000");
            }
        }

    }

})(jQuery);