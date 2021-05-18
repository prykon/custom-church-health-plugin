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

    private function display_item_divs() {
        $items = get_option('custom_church_health_icons', null );
        $item_count = count( $items ) - 2;
        
        switch ( $item_count ) {
            case 1:
                $grid_template = [1];
                break;
            case 2:
                $grid_template = [
                    0,0,0,
                    1,0,1,
                    0,0,0,
                ];
                break;
            case 3:
                $grid_template = [
                    0,1,0,
                    1,0,1,
                ];
                break;
            case 4:
                $grid_template = [
                    1,1,
                    1,1,
                ];
                break;
            case 5:
                $grid_template = [
                    1,0,1,
                    0,1,0,
                    1,0,1,
                ];
                break;
            case 6:
                $grid_template = [
                    1,0,1,
                    1,0,1,
                    1,0,1,
                ];
                break;
            case 7:
                $grid_template = [
                    1,0,1,
                    1,1,1,
                    1,0,1,
                ];
                break;
            case 8:
                $grid_template = [
                    1,1,1,
                    1,0,1,
                    1,1,1,
                ];
                break;
            case 9:
                $grid_template = [
                    1,1,1,
                    1,1,1,
                    1,1,1,
                ];
                break;
        }

        $i = 1;
        $output = '';
        foreach ( $grid_template as $grid_item ) {
            if ( $grid_item === 0 ) {
                $output .= '<div class="custom-church-health-item"></div>';
            } else if ( $grid_item === 1 ) {
                $output .= '<div class="custom-church-health-item" style="background-color:rgba(200, 200, 200, 0.8);border:1px solid rgba(0, 0, 0, 0.8);">' . $i . '</div>';
                $i++;
            }
        }
        echo $output;
    }

    private function display_item_css() {
        $item_count = count( get_option( 'custom_church_health_icons', null ) ) - 2;

        $output = 'display:grid;';
        switch ( $item_count ) {
            case 1:
                $output .= 'grid-template-columns:auto;';
                break;

            case 4:
                $output .= 'grid-template-columns:auto auto;';
                break;            

            default:
                $output .= 'grid-template-columns:auto auto auto;';
                break;
        }
        $output .= 'justify-content: space-evenly;';
        echo $output;
    }

    public function dt_add_section( $section, $post_type ) {
        if ( $section === 'custom_church_health_tile' ): ?>
        <style>
            .custom-church-health-item {
                margin: auto;
                height:75px;
                width:75px;
                border-radius: 100%;
                font-size: 30px;
                text-align: center;
            }

            .custom-church-health-circle {
                height:302px;
                width:302px;
                border-radius:100%;
                border-width: 3px;
                border-color: darkgray;
                border-style: dashed;
                margin:auto;
            }

            .custom-church-health-grid {
                height:75%;
                width:75%;
                margin-top: 12.5%;
                margin-left: auto;
                margin-right: auto;
                <?php self::display_item_css(); ?>
            }
        </style>
        <div>
            <div class="custom-church-health-circle">
                <div class="custom-church-health-grid">
                    <?php self::display_item_divs(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
                <!--
                <div style="display:flex;flex-wrap:wrap;margin-top:10px" class=" js-progress-bordered-box half-opacity">
                    <?php foreach ( $fields['custom_church_health_tile_multiselect']['default'] as $key => $option ) : ?>
                        <div class="group-progress-button-wrapper">
                            <button  class="group-progress-button" id="<?php echo esc_html( $key ) ?>">
                                <img src="<?php echo esc_html( $option['icon'] ?? "" ) ?>">
                            </button>
                            <p><?php echo esc_html( $option['label'] ) ?> </p>
                        </div>
                    <?php endforeach; ?>
                </div>
                -->
        <?php
    }
}
Custom_Church_Health_Tile_Tile::instance();
