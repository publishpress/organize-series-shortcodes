(function(){
	tinymce.create('tinymce.plugins.osquicktags', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(os_ed, url) {
			os_ed.addButton('os_series_post_list_box', {
				title : 'Add OS Series Post List Box',
				image : url + '/../images/series_post_list_box-20x20.png',
				onclick : function() {
					os_CustomButtonClick('series_post_list_box');
				}
			});
			os_ed.addButton('os_post_list_box', {
				title : 'Add OS Post List Box',
				image : url + '/../images/post_list_box-20x20.png',
				onclick : function() {
					os_CustomButtonClick('post_list_box');
				}
			});
			os_ed.addButton('os_series_toc', {
				title : 'Add OS Series TOC',
				image : url + '/../images/series_toc-20x20.png',
				onclick : function() {
					os_CustomButtonClick('series_toc');
				}
			});
			os_ed.addButton('os_series_meta', {
				title : 'Add OS Series AbstractMeta',
				image : url + '/../images/series_meta-20x20.png',
				onclick : function() {
					os_CustomButtonClick('series_meta');
				}
			});
			os_ed.addButton('os_series_nav', {
				title : 'Add OS Series Navigation Strip',
				image : url + '/../images/series_nav-20x20.png',
				onclick : function() {
					os_CustomButtonClick('series_nav');
				}
			});
		},
		/**
		 * Creates control instances based in the incoming name. This method is normally not
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
				longname : "Organize Series Shortcodes",
				author : 'Organize Series',
				authorurl : 'http://www.organizeseries.com/',
				infourl : 'http://www.organizeseries.com/',
				version : "1.0"
			};
		}
	});
	
	tinymce.PluginManager.add('os_quicktags', tinymce.plugins.osquicktags);
})()