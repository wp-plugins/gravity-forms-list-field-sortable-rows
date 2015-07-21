<?php
/*
Plugin Name: Sortable List Fields Rows for Gravity Forms
Description: Allows list field rows to be sorted by drop and dragging their position.
Version: 1.1
Author: Adrian Gordon
Author URI: http://www.itsupportguides.com 
License: GPL2
Text Domain: itsg_field_sortable
*/

load_plugin_textdomain( 'itsg_field_sortable', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

if (!class_exists('ITSG_GF_List_Field_Stortable')) {
    class ITSG_GF_List_Field_Stortable
    {

		/**
         * Construct the plugin object
         */
		 public function __construct()
        {
            // register actions
            if ((self::is_gravityforms_installed())) {
				// start the plugin
				add_action('gform_enqueue_scripts', array(&$this,'enqueue_scripts'), 90, 2);
				add_action('gform_field_appearance_settings', array(&$this,'field_sortable_settings') , 10, 2 );
				add_action('gform_editor_js', array(&$this,'field_sortable_editor_js'));
				add_filter('gform_tooltips', array(&$this,'field_sortable_tooltip'));
				add_action('gform_field_css_class', array(&$this,'field_sortable_custom_class'), 10, 3);
			}
		}

		/* 
		 * Enqueue scripts required to run sortable feature on front end form - only runs if list field has 'sortable' option enabled
		 */
		function enqueue_scripts($form, $is_ajax) {
			foreach ( $form['fields'] as $field ) {
				if ( 'list' == $field['type'] && true == $field['itsg_field_sortable'] ) {
					wp_enqueue_script('jquery');
					wp_enqueue_script('jquery-ui-sortable');
					wp_enqueue_style('list_field_sortable_style',  plugins_url( '/css/list-field-sortable-style.css', __FILE__ ));
					add_action('wp_footer',array(&$this,'sortable_javascript'));
					break;
				}
			}
		}
		
		/* 
		 * In page jQuery - handles the binding of the sortable plugin
		 */
		function sortable_javascript() { 
			?>
			<script>
				jQuery(document).ready(function($) {
					// return a helper with preserved width of cells - this keeps the row width when being sorted
					var fixHelper = function(e, ui) {
						ui.children().each(function() {
							jQuery(this).width($(this).width());
						});
						return ui;
					};
					// bind sortable plugin to the list
					jQuery( ".itsg_field_sortable tbody" ).sortable({
					  placeholder: "ui-state-highlight",
					  helper: fixHelper
					});
				});
			</script> <?php
		}
		
		/*
          * Adds custom sortable setting for field
          */
        public static function field_sortable_settings($position, $form_id)
        {      
            // Create settings on position 50 (top position)
            if ($position == 50) {
				?>
				<li class="itsg_field_sortable field_setting">
					<input type="checkbox" id="itsg_field_sortable" onclick="SetFieldProperty('itsg_field_sortable', this.checked);">
					<label class="inline" for="itsg_field_sortable">
					<?php _e("Sortable", "itsg_field_sortable"); ?>
					<?php gform_tooltip("itsg_field_sortable");?>
					</label>
					</li>
			<?php
            }
        } // END field_sortable_settings
		
		/*
         * JavaSript to handle sortable field option to field in the Gravity forms editor
         */
        public static function field_sortable_editor_js()
        {
		?>
		<script type='text/javascript'>
			jQuery(document).ready(function($) {
				//adding setting to fields of type "list"
				fieldSettings["list"] += ", .itsg_field_sortable";
				//set field values when field loads		
				jQuery(document).bind("gform_load_field_settings", function(event, field, form){
					jQuery("#itsg_field_sortable").prop('checked', field["itsg_field_sortable"] );
				});
			});
		 
		</script>
		<?php
        } // END field_appearance_editor_js

		 /*
         * Tooltip for for sortable option
         */
		public static function field_sortable_tooltip($tooltips){
			$tooltips["itsg_field_sortable"] = "<h6>Sortable</h6>Makes list field sortable by drop and drag.";
			return $tooltips;
		} // END field_sortable_tooltip
		
		/*
         * Adds custom CSS class to field if sortable is enabled
         */
        public static function field_sortable_custom_class($classes, $field, $form)
        {
           if ("on" == $field['itsg_field_sortable']) {
                $classes .= " itsg_field_sortable";
            }     
            return $classes;
        } // END field_sortable_custom_class
		
		/*
         * Check if GF is installed
         */
        private static function is_gravityforms_installed()
        {
            return class_exists('GFAPI');
        } // END is_gravityforms_installed
	}
    $ITSG_GF_List_Field_Stortable = new ITSG_GF_List_Field_Stortable();
}