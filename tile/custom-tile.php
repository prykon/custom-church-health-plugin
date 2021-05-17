<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Custom_Church_Health_Tile_Tile
{
    private static $_instance = null;
    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct(){
        add_filter( 'dt_details_additional_tiles', [ $this, "dt_details_additional_tiles" ], 10, 2 );
        add_filter( "dt_custom_fields_settings", [ $this, "dt_custom_fields" ], 1, 2 );
        add_action( "dt_details_additional_section", [ $this, "dt_add_section" ], 30, 2 );
    }

    /**
     * This function registers a new tile to a specific post type
     *
     * @todo Set the post-type to the target post-type (i.e. contacts, groups, trainings, etc.)
     * @todo Change the tile key and tile label
     *
     * @param $tiles
     * @param string $post_type
     * @return mixed
     */
    public function dt_details_additional_tiles( $tiles, $post_type = "" ) {
        if ( $post_type === "groups" ){
            $tiles["custom_church_health_tile"] = [ "label" => __( "Custom Church Health Tile", 'disciple_tools' ) ];
        }
        return $tiles;
    }

    /**
     * @param array $fields
     * @param string $post_type
     * @return array
     */
    public function dt_custom_fields( array $fields, string $post_type = "" ) {
        /**
         * @todo set the post type
         */
        if ( $post_type === "groups" ){
            /**
             * @todo Add the fields that you want to include in your tile.
             *
             * Examples for creating the $fields array
             * Contacts
             * @link https://github.com/DiscipleTools/disciple-tools-theme/blob/256c9d8510998e77694a824accb75522c9b6ed06/dt-contacts/base-setup.php#L108
             *
             * Groups
             * @link https://github.com/DiscipleTools/disciple-tools-theme/blob/256c9d8510998e77694a824accb75522c9b6ed06/dt-groups/base-setup.php#L83
             */

            $custom_items = get_option( 'custom_church_health_icons', null );

            $fields["custom_church_health_tile_multiselect"] = [
                'name' => __( 'Custom Church Health', 'disciple_tools' ),
                'description' => _x( "Track the progress and health of a group/church.", 'Optional Documentation', 'disciple_tools' ),
                'default' => [],
                'tile' => 'custom_church_health_tile',
                'type' => 'multi_select',
                'hidden' => false,
                'icon' => get_template_directory_uri() . '/dt-assets/images/edit.svg',
            ];
            
            foreach ( $custom_items as $item ) {
                $fields['custom_church_health_tile_multiselect']['default'][ $item['key'] ]['label'] = $item['label'];
            }
        }
        return $fields;
    }

    public function dt_add_section( $section, $post_type ) {
        return;
    }
}
Custom_Church_Health_Tile_Tile::instance();
