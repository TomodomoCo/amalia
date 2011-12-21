/*
Amalia. A content management system "for the rest of us".

Copyright (C) 2007-2011 Chris Van Patten, Nick Sampsell and Peter Upfold. 

As this is a plugin for TinyMCE, this file falls under the scope of the GNU
Lesser General Public License.

<https://www.gnu.org/licenses/lgpl-2.1.html>

*/

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('amalia_linker');

	tinymce.create('tinymce.plugins.AmaliaLinkerPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceAmaliaInsertLink', function() {
				ed.windowManager.open({
					file : url + '/insertlink.php?dir=' + encodeURIComponent(dirlink),
					width : 550 + parseInt(ed.getLang('example.delta_width', 0)),
					height : 300 + parseInt(ed.getLang('example.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('amalia_linker', {
				title : 'Insert Link',
				cmd : 'mceAmaliaInsertLink',
				image : url + '/img/link.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('example', n.nodeName == 'A');
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Amalia Linker Plugin 2.0',
				author : 'Peter Upfold',
				authorurl : 'http://getamalia.com/',
				infourl : 'http://getamalia.com/',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('amalia_linker', tinymce.plugins.AmaliaLinkerPlugin);
})();