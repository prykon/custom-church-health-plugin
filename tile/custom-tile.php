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

    public function get_practiced_items( $post_id ) {
        global $wpdb;

        $practiced_items = $wpdb->get_col(
            $wpdb->prepare( "
                SELECT meta_value FROM $wpdb->postmeta
                WHERE meta_key = 'health_metrics'
                AND post_id = %s
                ", $post_id )
            );

        return $practiced_items;
    }

    /**
     * @param array $fields
     * @param string $post_type
     * @return array
     */
    private function display_item_divs() {
        $plugin_base_url = self::get_plugin_base_url();
        $items = get_option('custom_church_health_icons', null );
        if ( empty( $items ) ) {
            echo '<div class="custom-church-health-item" style="filter: opacity(0.35);"><img src="' . esc_attr( $plugin_base_url . '/assets/images/warning.svg' ) . '">' . esc_html( 'Empty', 'disciple_tools' ) . '</div>';
            return;
        }

        $i = 0;
        $output = '';
        $plugin_base_url = self::get_plugin_base_url();

        foreach ( $items as $item ) {

            // Check if custom church health item is being practiced by groud
            $item_opacity = 0.35;
            
            $practiced_items = self::get_practiced_items( 27 );

            if ( !empty( $practiced_items ) && in_array( $item , $practiced_items ) ) {
                $item_opacity = 1;
            }

            $output .= '<div class="custom-church-health-item" syle="filter: opacity(' . $item_opacity . ')" title="' . esc_attr( $item['label'] ) . '"><img src="' . esc_attr( $plugin_base_url . '/assets/images/' . $item['icon'] . '.svg' ) . '"></div>';
        }

        echo $output;
    }

    private function get_plugin_base_url(){
        // Remove '/admin/' subdirectory from plugin base url
        $plugin_base_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
        $plugin_base_url = explode( '/', $plugin_base_url );
        array_pop( $plugin_base_url );
        $plugin_base_url = implode( '/', $plugin_base_url );
        return $plugin_base_url;
    }

    public function display_item_overview() {
        $plugin_base_url = self::get_plugin_base_url(); 
        $items = get_option('custom_church_health_icons', null );
        if ( empty( $items ) ) {        
            return;
        }

        // @todo Get practiced items from db
        $practiced_items = [];

        $items = array_values( $items );

        foreach ( $items as $item  ) : ?>
            <div class="summary-tile">
                <?php
                if ( in_array( $item , $practiced_items ) ) {
                    echo '<div class="summary-icons" id="' . esc_attr( $item['key'] ) . '" title="' . esc_attr( $item['description'] ) . '">';
                } else {
                    echo '<div class="summary-icons" id="' . esc_attr( $item['key'] ) . '" title="' . esc_html( trim( $item['description'] ) ) . '" style="background-color: #b2c6d6">';
                }
                echo '<img src="' . esc_attr( $plugin_base_url . '/assets/images/' . $item['icon'] . '.svg' ) .'">';
                echo '</div>';
                echo '<div class="summary-label"><p>' . esc_html( trim( $item['label'] ) ) . '</p></div>';
                echo '</div>';
        endforeach;
    }

    public function dt_add_section( $section, $post_type ) {       
        $items = get_option('custom_church_health_icons', null );

        if ( empty( $items ) ) {        
            $item_count = 0;
        } else {
            $item_count = count( $items );
        }

        $health_item_size = 50;
        
        switch ( $item_count) {
            case $item_count <= 4:
                $health_item_size = 75;
                break;

            case $item_count > 4 && $item_count <= 8:
                $health_item_size = 67.5;
                break;

            case $item_count > 9:
                $health_item_size = 55;
                break;
        }

        if ( $section === 'custom_church_health_tile' ): ?>
        <style>
            .practicing {
                filter: none !important;
            }
            .custom-church-health-item {
                margin: auto;
                position: absolute;
                height: <?php echo esc_attr( $health_item_size ); ?>px;
                width: <?php echo esc_attr( $health_item_size ); ?>px;
                border-radius: 100%;
                font-size: 16px;
                color: black;
                text-align: center;
                font-style: italic;
            }

            .custom-church-health-item img {
                height: <?php echo esc_attr( $health_item_size ); ?>px;
                width: <?php echo esc_attr( $health_item_size ); ?>px;
            }
            .custom-church-health-circle {
                display: block;
                margin:auto;
                height:300px;
                width:300px;
                border-radius:100%;
                border: 3px darkgray dashed;
            }
            .custom-church-health-grid {
                display: inline-block;
                position: relative;
                height:75%;
                width:75%;
                margin-top: 12.5%;
                margin-left: auto;
                margin-right: auto;
            }
            .summary-tile {
                flex: 1 0 80px;
                text-align: center;
            }
            .summary-icons {
                cursor: pointer;
                height: 60px;
                width: 65px;
                margin: auto;
                display: grid;
                text-align: center;
                background-color: #3f729b;
                border-radius: 5px;
            }
            
            .summary-icons img {
                filter: invert(100%);
                margin: auto;
                height: 40px;
                width: 50px;
            }
            .summary-label {
                width:;
            }
            .summary-grid {
                display: flex;
                flex-wrap: wrap;
                margin-top: 20px;
                /*grid-template-columns:auto auto;*/
            }
        </style>
        <div>
            <div class="custom-church-health-circle" id="custom-church-health-items-container">
                <div class="custom-church-health-grid">
                    <?php self::display_item_divs(); ?>
                </div>
            </div>
        </div>
        <div class="summary-grid" align="center">
            <?php self::display_item_overview(); ?>
        </div>
    <?php endif; ?>
                
        <?php
        $plugin_base_url = self::get_plugin_base_url();
        echo '<script src="' . esc_attr( $plugin_base_url ) . '/assets/js/custom-tile.js"></script>';
    }
}
Custom_Church_Health_Tile_Tile::instance();
