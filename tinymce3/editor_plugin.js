// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {

 // add image for products page short tag

 	tinymce.create('tinymce.plugins.ecommerce_search_image', {
		init : function(ed, url) {
			var pb = '<img style="border:1px dashed #888;padding:5% 25%;background-color:#F2F8FF;" src="' + url + '/ecommerce_search_shortcode.jpg" class="ecommerce_search_image mceItemNoResize" title="Do not remove this image unless you know what you are doing." />', cls = 'ecommerce_search_image', sep = ed.getParam('ecommerce_search_image', '[ecommerce_search]'), pbRE;

			pbRE = new RegExp(sep.replace(/[\?\.\*\[\]\(\)\{\}\+\^\$\:]/g, function(a) {return '\\' + a;}), 'g');

			// Register commands
			ed.addCommand('ecommerce_search_image', function() {
				ed.execCommand('mceInsertContent', 0, pb);
			});

			ed.onInit.add(function() {
				//ed.dom.loadCSS(url + "/css/content.css");
				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG' && ed.dom.hasClass(o.node, cls))
							o.name = 'ecommerce_search_image';
					});
				}
			});

			ed.onClick.add(function(ed, e) {
				e = e.target;

				if (e.nodeName === 'IMG' && ed.dom.hasClass(e, cls))
					ed.selection.select(e);
			});

			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('ecommerce_search_image', n.nodeName === 'IMG' && ed.dom.hasClass(n, cls));
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(pbRE, pb);
			});

			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="ecommerce_search_image') !== -1)
							im = sep;

						return im;
					});
			});
		},

		getInfo : function() {
			return {
				longname : 'Insert ecommerce_search Image',
				author : 'A3 Revolution',
				authorurl : 'http://a3rev.com',
				infourl : 'http://a3rev.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});


	tinymce.PluginManager.add('ecommerce_search_image', tinymce.plugins.ecommerce_search_image);
	
})();