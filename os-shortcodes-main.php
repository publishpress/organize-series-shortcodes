<?php
/**
 * This file loads the main class for enabling shortcodes
 * 
*/

if ( !class_exists('os_Shortcodes') ) {
	
class os_Shortcodes {
	public $domain = 'organize-series-shortcodes';  //textdomain for localization
	
	//other addon support flags
	public $series_multiples = false;
	public $series_grouping = false; //TODO add support for this.
	
	//constructor
	function os_Shortcodes() {
		$this->register_shortcodes(); //let's get the various shortcodes setup
		add_action('admin_init', array( &$this, 'admin_init' ) );
		add_action('init', array( &$this, 'register_textdomain') );
		
		//check for other addons and flag for integration
		add_action('plugins_loaded', array( &$this, 'addon_support') );
	}
	
	function addon_support() {
		if ( function_exists('os_multiples_remove_actions') )
			$this->series_multiples = true;
		
		if ( function_exists('orgseries_grouping_taxonomy') ) {
			$this->series_grouping = true;
		}
	}
	
	function register_textdomain() {
		$dir = OS_SHORTCODE_PATH . 'lang';
		load_plugin_textdomain($this->domain, false, $dir);
	}
	
	function register_shortcodes() {
		add_shortcode( 'series_post_list_box', array( &$this, 'series_post_list_box'  ) ); 
		add_shortcode( 'post_list_box', array( &$this, 'series_post_list' ) );
		add_shortcode( 'series_toc', array( &$this, 'series_toc' ) );
		add_shortcode( 'series_meta', array( &$this, 'series_meta' ) );
		add_shortcode( 'series_nav', array( &$this, 'series_nav' ) );
	}
	
	function admin_init() {
		if ( current_user_can('edit_posts') && current_user_can('edit_pages') ) {
			if ( in_array(basename($_SERVER['PHP_SELF']), array('post-new.php', 'page-new.php', 'post.php', 'page.php') ) ) {
				add_filter('mce_buttons', array( &$this, 'filter_mce_button' ));
				add_filter('mce_external_plugins', array( &$this, 'filter_mce_plugin') );
				add_action('admin_head', array(&$this, 'add_simple_buttons'));
				add_action('edit_form_advanced', array( &$this, 'advanced_buttons'));
				add_action('edit_page_form', array( &$this, 'advanced_buttons'));
			}
		}
	}
	
	function filter_mce_button($buttons) {
		array_push($buttons, '|', 'os_series_post_list_box', 'os_post_list_box', 'os_series_toc', 'os_series_meta', 'os_series_nav' );
		return $buttons;
	}
	
	function filter_mce_plugin($plugins) {
		$plugins['os_quicktags'] = OS_SHORTCODE_URL . 'js/os_editor_plugin.js';
		return $plugins;
	}
	
	function advanced_buttons() {
		$series_list = $this->get_series_list();
		$series_titles = $series_list['series_titles'];
		$series_ids = $series_list['series_ids'];
		?>
		<script type="text/javascript">
			var os_defaultSettings = {},
			outputOptions = '',
			selected ='',
			content = '';
		
		os_defaultSettings['series_post_list_box'] = {
			description:  'This shortcode will output a table of contents box for the selected series.  The layout of this box can be controlled via the "Series Post List Template" on your series options page',
			series: {
				name: 'Series',
				defaultvalue: '0',
				description: 'If you want to display the post list box for a specific series then choose which series here.  Otherwise the series for this post (if it\'s assigned to a series) will be displayed',
				type: 'select',
				options: '<?php echo $series_titles; ?>',
				option_values: '<?php echo $series_ids; ?>'
			}
		};
	
		os_defaultSettings['post_list_box'] = {
			description:  'This shortcode will output a list of posts for the selected series.  The layout of this box can be controlled via the "Series Post List Post Title Template" on your series options page. NOTE: If this template is list items,  make sure you surround this shortcode with the proper html tags',
			series: {
				name: 'Series',
				defaultvalue: '0',
				description: 'If you want to display the list of posts for a specific series then choose which series here.  Otherwise the series for this post (if it\'s assigned to a series) will be displayed',
				type: 'select',
				options: '<?php echo $series_titles; ?>',
				option_values: '<?php echo $series_ids; ?>'
			}
		};
		
		os_defaultSettings['series_toc'] = {
			description:  'This shortcode will output a table of contents listing all the series.  The layout is controlled by the "Series Table of Contents Listings:" template in your Series Options page. Notice you can finetune which series get selected via the options below',
			orderby: {
				name: 'Order by',
				defaultvalue: 'term_id',
				description: 'Select how you want the list of series to be ordered',
				type: 'select',
				options: 'name|count|slug|series id',
				option_values: 'name|count|slug|term_id'
			},
			order: {
				name: 'Order',
				defaultvalue: 'DESC',
				description: 'Select how the series are ordered',
				type: 'select',
				options: 'Descending|Ascending',
				option_values: 'DESC|ASC'
			},
			hide_empty: {
				name: 'Hide Empty Series?',
				defaultvalue: 'true',
				description: 'Select if you want empty series to be kept hidden',
				type: 'select',
				options: 'Yes|No',
				option_values: 'true|false'
			},
			exclude: {
				name: 'Exclude these series:',
				defaultvalue: '0',
				description: 'Select any series you want to be excluded from the TOC',
				type: 'multiselect',
				options: '<?php echo $series_titles; ?>',
				option_values: '<?php echo $series_ids; ?>'
			},
			include: {
				name: 'Include these series:',
				defaultvalue: '0',
				description: 'Select any series you want to be INCLUDED in the TOC',
				type: 'multiselect',
				options: '<?php echo $series_titles; ?>',
				option_values: '<?php echo $series_ids; ?>'
			},
			number: {
				name: 'Number of Series:',
				defaultvalue: '',
				description: 'Indicate how many series you want displayed (leave blank for all)',
				type: 'text'
			},
			offset: {
				name: 'Offset:',
				defaultvalue: '',
				description: 'You can select the offset for the number of series (useful for paging).  No offset if left blank',
				type: 'text'
			},
			search: {
				name: 'Search:',
				defaultvalue: '',
				description: 'You can return any series that match this search string (matched against series names) - case insensitive',
				type: 'text'
			}
		};
		
		os_defaultSettings['series_meta'] = {
			description:  'This shortcode will output a the series meta information for a series.  If the template textarea is left blank here, the layout of this box will be controlled via the "Series Meta Template" on your series options page. However, you can use the %tokens% legend (see series options page) to customize the series meta for this post.',
			series: {
				name: 'Series',
				defaultvalue: '0',
				description: 'If you want to display the series meta for a specific series then choose which series here.  Otherwise the series for this post (if it\'s assigned to a series) will be displayed',
				type: 'select',
				options: '<?php echo $series_titles; ?>',
				option_values: '<?php echo $series_ids; ?>'
			},
			content: {
				name: 'Series Meta Template',
				defaultvalue: '',
				description: 'Use this area to customize the layout of the series meta display if you want something different than found in the "Series Meta Template" in the Series options page.  You can use %tokens% here',
				type: 'textarea'
			}
		};
		
		os_defaultSettings['series_nav'] = {
			description:  'This shortcode will output a the series navigation strip for a post in a series.  If the template textarea is left blank here, the layout of this box will be controlled via the "Series Post Navigation Template" on your series options page. However, you can use the %tokens% legend (see series options page) to customize the series navigation for this post.',
			content: {
				name: 'Series Post Navigation Template',
				defaultvalue: '',
				description: 'Use this area to customize the layout of the series navigation display if you want something different than found in the "Series Post Navigation Template" in the Series options page.  You can use %tokens% here',
				type: 'textarea'
			}
		};
		
		function os_CustomButtonClick(tag){
			
			var index = tag;
				for (var index2 in os_defaultSettings[index]) {
					if ( typeof os_defaultSettings[index][index2]['type'] == 'undefined' ) continue;
					
					if (os_defaultSettings[index][index2]['clone'] === 'cloned')
						outputOptions += '<tr class="cloned">\n';
					else
						outputOptions += '<tr>\n';
					outputOptions += '<th><label for="os-' + index2 + '">'+ os_defaultSettings[index][index2]['name'] +'</label></th>\n';
					outputOptions += '<td>';
					
					if (os_defaultSettings[index][index2]['type'] === 'select' || os_defaultSettings[index][index2]['type'] === 'multiselect') {
						var optionsArray = os_defaultSettings[index][index2]['option_values'].split('|');
						var seriestitlesArray = os_defaultSettings[index][index2]['options'].split('|');
						
						if (os_defaultSettings[index][index2]['type'] === 'multiselect') {
							outputOptions += '\n<select multiple="multiple" name="os-'+index2+'" id="os-'+index2+'">\n';
						} else {
							outputOptions += '\n<select name="os-'+index2+'" id="os-'+index2+'">\n';
						}
						var options_count = 0;
						for (var index3 in optionsArray) {
							selected = (optionsArray[index3] === os_defaultSettings[index][index2]['defaultvalue']) ? ' selected="selected"' : '';
							outputOptions += '<option value="'+optionsArray[index3]+'"'+ selected +'>'+seriestitlesArray[options_count]+'</option>\n';
							options_count ++;
						}
						
						outputOptions += '</select>\n';
					}
					
					if (os_defaultSettings[index][index2]['type'] === 'text') {
						cloned = '';
						if (os_defaultSettings[index][index2]['clone'] === 'cloned') cloned = "[]";
						outputOptions += '\n<input type="text" name="os-'+index2+cloned+'" id="os-'+index2+'" value="'+os_defaultSettings[index][index2]['defaultvalue']+'" />\n';
					}
					
					if (os_defaultSettings[index][index2]['type'] === 'textarea') {
						cloned = '';
						if (os_defaultSettings[index][index2]['clone'] === 'cloned') cloned = "[]";
						outputOptions += '<textarea name="os-'+index2+cloned+'" id="os-'+index2+'" cols="40" rows="10">'+os_defaultSettings[index][index2]['defaultvalue']+'</textarea>';
					}
					
					outputOptions += '\n<br/><small>'+ os_defaultSettings[index][index2]['description'] +'</small>';
					outputOptions += '\n</td>\n</tr>';
					
				}
			
		
			var os_width = jQuery(window).width(),
				os_tbHeight = jQuery(window).height(),
				os_tbWidth = ( 720 < os_width ) ? 720 : os_width;
			
			os_tbWidth = os_tbWidth - 80;
			os_tbHeight = os_tbHeight - 84;

			var tbOptions = "<div id='os_shortcodes_div'><form id='os_shortcodes'><p>"+os_defaultSettings[index]['description']+"</p><table id='shortcodes_table' class='form-table os-"+ tag +"'>";
			tbOptions += outputOptions;
			tbOptions += '</table>\n<p class="submit">\n<input type="button" id="os-shortcodes-submit" class="button-primary" value="Ok" name="submit" /></p>\n</form></div>';
			
			var form = jQuery(tbOptions);
			
			var table = form.find('table');
			form.appendTo('body').hide();
			
			
			form.find('#os-shortcodes-submit').click(function(){
							
				var shortcode = '['+tag;
								
				for( var index in os_defaultSettings[tag]) {
					var value = table.find('#os-' + index).val();
					if (index === 'content') { 
						content = value;
						continue;
					}
					
					if (os_defaultSettings[tag][index]['clone'] !== undefined) {
						content = 'cloned';
						continue;
					} 
					
					if ( (index == 'exclude' || index == 'include') && value == 0 ) continue;
					
					if ( value !== os_defaultSettings[tag][index]['defaultvalue'] )
						shortcode += ' ' + index + '="' + value + '"';
						
				}
				
				shortcode += '] ' + "\n";
				
				if (content != '') {
					shortcode += content;
					shortcode += '[/'+tag+'] ' + "\n";
				}

				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode + ' ');
				
				tb_remove();
			});
			
			tb_show( 'OS ' + tag + ' Shortcode', '#TB_inline?width=' + os_tbWidth + '&height=' + os_tbHeight + '&inlineId=os_shortcodes_div' );
			jQuery('#os_shortcodes_div').remove();
			outputOptions = '';
		}
		</script>
		<?php
	}
	
	function add_simple_buttons() {
		wp_print_scripts('quicktags');
		$output = "<script type='text/javascript'>\n
	/* <![CDATA[ */ \n";
		
		$buttons = array();
		
		$buttons[] = array(
			'name' => 'series_post_list_box',
			'options' => array(
				'display_name' => 'series_post_list_box',
				'open_tag' => '\n[series_post_list_box]',
				'close_tag' => '',
				'key' => ''
				)
			);
		
		$buttons[] = array(
			'name' => 'post_list_box',
			'options' => array(
				'display_name' => 'post_list_box',
				'open_tag' => '\n[post_list_box]',
				'close_tag' => '',
				'key' => ''
				)
			);
		
		$buttons[] = array(
			'name' => 'series_toc',
			'options' => array(
				'display_name' => 'series_toc',
				'open_tag' => '\n[series_toc]',
				'close_tag' => '',
				'key' => ''
				)
			);
			
		$buttons[] = array(
			'name' => 'series_meta',
			'options' => array(
				'display_name' => 'series_meta',
				'open_tag' => '\n[series_meta]',
				'close_tag' => '[/series_meta]\n',
				'key' => ''
				)
			);
			
		$buttons[] = array(
			'name' => 'series_nav',
			'options' => array(
				'display_name' => 'series_nav',
				'open_tag' => '\n[series_nav]',
				'close_tag' => '[/series_nav]\n',
				'key' => ''
				)
			);
			
		for ($i=0; $i <= (count($buttons)-1); $i++) {
			$output .= "edButtons[edButtons.length] = new edButton('ed_{$buttons[$i]['name']}'
				,'{$buttons[$i]['options']['display_name']}'
				,'{$buttons[$i]['options']['open_tag']}'
				,'{$buttons[$i]['options']['close_tag']}'
				,'{$buttons[$i]['options']['key']}'
			); \n";
		}
	
		$output .= "\n /* ]]> */ \n
		</script>";
		echo $output;
	}
	
	//used for getting the list of series and setting up an array with keys, "series_titles", and "series_ids" for setting up the javascript select options.
	function get_series_list() {
		$series_get = get_series();
		$series_list = array();
		$series_list['series_titles']= 'Auto/None';
		$series_list['series_ids'] = '0';
		
		foreach ($series_get as $series) {
			$series_list['series_titles'] .= '|'.$series->name;
			$series_list['series_ids'] .= '|'.$series->term_id;
		}
		
		return $series_list;
	}
	
	function series_post_list_box($atts, $content = null) {
			global $orgseries;
			//check to see if we've included a series id, if we have then we want to get the postlist for that series.
			if ( !empty($atts) && array_key_exists('series', $atts) ) {
				$settings = $orgseries->settings;
				$output =  token_replace(stripslashes($settings['series_post_list_template']), 'post-list', $atts['series']);
				$output = str_replace('%postcontent%','',$output);
				return $output;
			}
			$output =  wp_postlist_display();
			$output = str_replace('%postcontent%','',$output);
			return $output;
	}
	
	function series_post_list($atts, $content = null) {
		global $orgseries;
		//check to see if we've included a series id. If we have then we want to get the postlist for that series.
		if ( !empty($atts) && array_key_exists('series', $atts) ) {
				$settings = $orgseries->settings;
				$output = get_series_posts($atts['series']);
				$output = str_replace('%postcontent%','',$output);
				return $output;
		}
		$output = get_series_posts();
		$output = str_replace('%postcontent%','',$output);
		return $output;
	}
	
	function series_toc( $atts, $content = null ) {
		$default_atts = array(
			'orderby' => 'name', //can be name, count, term_group, slug, or term_id (nothing)
			'order' => 'ASC', // or DESC
			'hide_empty' => true,  //true => no empty series
			'exclude' => array(), //comma delimited string of seris_ids to exclude.
			'exclude_tree' =>  array(), //in case of multiple series, same as exclude except descendent series will be excluded as well.
			'include' => array(), //comma delimited string of series_ids to include
			'number' => '', //maximum number of series to return
			'offset' => '', //number by which to offset the series query
			'slug' => '', //returns only series which match the slug indicated.
			'hierarchical' => true, //whehter to include series that have non-empty descendants (even if 'hide_empty' is set to true)
			'search'  => '', //will return series that match the search string (by name) - case-insensitive
			'name__like' => '', //returned series names will begin with the value of 'name__like', case-insensitive
			'child_of' => 0, //will return children of the indicated series_ids
			'parent' => '' //will return the direct parent of the indicated series_id
		);
		$args = shortcode_atts($default_atts, $atts);
		$series_list = get_series($args);
		$output = '';
		$referral = '';
		
		foreach ( $series_list as $series ) {
			$output .= wp_serieslist_display_code($series, $referral, false);
		}
		
		return $output;
	}
	
	function series_meta( $atts, $content ) {
		global $orgseries;
		$settings = $orgseries->settings;
		
		if ( empty($content) ) {
			if ( !empty($atts) && array_key_exists('series', $atts) ) {
				$output = token_replace(stripslashes($settings['series_meta_template']), 'other', 0, $atts['series']);
				$output = str_replace('%postcontent%','',$output);
				
			} else {
				$output = wp_seriesmeta_write();
				$output = str_replace('%postcontent%','',$output);
			}
			return $output;
		}
		
		//we've got a custom template within $content (using corresponding %tokens% as listed on the series_options page) //TODO let's include the token legend in the 'help' dropdown on the add/edit post screen?
		
		//if we have a series id, let's return the custom-template using that series ID.
		if ( !empty($atts) && array_key_exists('series', $atts) ) {
			$output = token_replace($content,'other',0,$atts['series']);
			$output = str_replace('%postcontent%','',$output);
			return $output;
		}
		
		$serarray = get_the_series();
		$output = '';
		
		if ( !empty($serarray) ) {
			foreach ($serarray as $series) {
				$serID = $series->term_id;
				$output .= token_replace($content, 'other', 0, $serID);
			}
			$output = str_replace('%postcontent%','',$output);
		}
		return $output;
	}
	
	function series_nav( $atts, $content ) {
		//if empty content then let's just output the default nav template (from series options page)
		if ( empty($content) ) {
			$output = wp_assemble_series_nav();
			$output = str_replace('%postcontent%','',$output);
			return $output;
		}
		
		//we've got a custom tempalte within $content (usering corresponding %tokens% as listed on the series_options page)
		$series = get_the_series();
		$output = '';
		if ( !empty($series) ) {
			foreach ( $series as $ser ) {
				$series_id = $ser->term_id;
				$output .= token_replace($content, 'other', $series_id);
			}
			$output = str_replace('%postcontent%','',$output);
		}
		return $output;
	}
	
} //end os_Shortcodes class
} //end class check
?>