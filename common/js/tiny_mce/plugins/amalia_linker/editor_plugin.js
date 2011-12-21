/*
Amalia. A content management system "for the rest of us".

Copyright (C) 2007-2011 Chris Van Patten, Nick Sampsell and Peter Upfold. 

As this is a plugin for TinyMCE, this file falls under the scope of the GNU
Lesser General Public License.

<https://www.gnu.org/licenses/lgpl-2.1.html>

*/
(function(){tinymce.PluginManager.requireLangPack('amalia_linker');tinymce.create('tinymce.plugins.AmaliaLinkerPlugin',{init:function(ed,url){ed.addCommand('mceAmaliaInsertLink',function(){ed.windowManager.open({file:url+'/insertlink.php?dir='+encodeURIComponent(dirlink),width:550+parseInt(ed.getLang('example.delta_width',0)),height:300+parseInt(ed.getLang('example.delta_height',0)),inline:1},{plugin_url:url,})});ed.addButton('amalia_linker',{title:'Insert Link',cmd:'mceAmaliaInsertLink',image:url+'/img/link.png'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('example',n.nodeName=='A')})},createControl:function(n,cm){return null},getInfo:function(){return{longname:'Amalia Linker Plugin 2.0',author:'Peter Upfold',authorurl:'http://getamalia.com/',infourl:'http://getamalia.com/',version:"1.0"}}});tinymce.PluginManager.add('amalia_linker',tinymce.plugins.AmaliaLinkerPlugin)})();