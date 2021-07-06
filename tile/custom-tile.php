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
        add_filter( 'dt_details_additional_tiles', [ $this, 'dt_details_additional_tiles' ], 20, 2 );
        add_action( 'dt_details_additional_section', [ $this, 'dt_add_section' ], 30, 2 );
        add_action( 'display_item_divs', [ $this, 'display_item_divs' ], 10, 4 );
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
            $new_tiles = [];
            foreach ( $tiles as $index => $value ){
                if( $index !== 'health-metrics') {
                    $new_tiles[$index] = $value;
                } else {
                    $new_tiles['custom-health-metrics'] = [ 'label' => __( 'Custom Church Health Tile', 'disciple_tools' ) ];
                }
            }
            $tiles = $new_tiles;
            //$tiles['custom-health-metrics'] = [ 'label' => __( 'Custom Church Health Tile', 'disciple_tools' ) ];
        }
        return $tiles;
    }

    /**
     * @param array $fields
     * @param string $post_type
     * @return array
     */
    private function display_item_divs() {
        $post_id = GET_THE_ID();
        if ( empty( $post_id )){
            return;
        }
        
        $plugin_base_url = self::get_plugin_base_url();
        $items = get_option('custom_church_health_icons', null );
        if ( empty( $items ) ) {
            echo '<div class="custom-church-health-item" id="health-metrics" style="filter: opacity(0.35);"><img src="' . esc_attr( $plugin_base_url . '/assets/images/warning.svg' ) . '">' . esc_html( 'Empty', 'disciple_tools' ) . '</div>';
            return;
        }

        $i = 0;
        $output = '';
        $plugin_base_url = self::get_plugin_base_url();

        $practiced_items = get_post_meta( $post_id, 'health_metrics' );
        
        if ( empty( $practiced_items ) ) {
            $practiced_items = [];
        }
        foreach ( $items as $item ) {
            // Check if custom church health item is being practiced by group
            $item['label'] = str_replace( 'church_', '', $item['label'] );
            $item_opacity = 'half-opacity';

            if ( in_array( $item['key'], $practiced_items ) ) {
                $item_opacity = '';
            }
            $output .= '<div class="custom-church-health-item ' . $item_opacity . '" id="icon_' . strtolower( esc_attr( $item['label'] ) ) .'" title="' . esc_attr( $item['label'] ) . '"><img src="' . esc_attr( $plugin_base_url . '/assets/images/' . $item['icon'] . '.svg' ) . '"></div>';
        }
        echo $output;
    }

    private function get_plugin_base_url() {
        // Remove '/admin/' subdirectory from plugin base url
        $plugin_base_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
        $plugin_base_url = explode( '/', $plugin_base_url );
        array_pop( $plugin_base_url );
        $plugin_base_url = implode( '/', $plugin_base_url );
        return $plugin_base_url;
    }

    public function display_item_overview() {
        $post_id = GET_THE_ID();
        if ( empty( $post_id )){
            return;
        }

        $plugin_base_url = self::get_plugin_base_url(); 
        $items = get_option('custom_church_health_icons', null );

        if ( empty( $items ) ) {        
            return;
        } else {
            $items = array_values( $items );
        }

        $practiced_items = get_post_meta( $post_id, 'health_metrics');
        if ( $practiced_items === null ) {
            $practiced_items = [];
        }

        foreach ( $items as $item  ) : 
            $item['label'] = str_replace( 'church_', '', $item['label'] ); ?>
            <div class="summary-tile">
                <?php
                if ( in_array( $item['key'] , $practiced_items ) ) {
                    echo '<div class="summary-icons" id="' . esc_attr( $item['key'] ) . '" title="' . esc_attr( $item['description'] ) . '">';
                } else {
                    echo '<div class="summary-icons half-opacity" id="' . esc_attr( $item['key'] ) . '" title="' . esc_attr( $item['description'] ) . '">';
                }
                echo '<img src="' . esc_attr( $plugin_base_url . '/assets/images/' . $item['icon'] . '.svg' ) .'">';
                echo '</div>';
                echo '<div class="summary-label"><p>' . esc_html( $item['label'] ) . '</p></div>';
                echo '</div>';
        endforeach;

        echo '<div class="summary-tile">';
        if ( in_array( 'church_commitment' , $practiced_items ) ) {
                    echo '<div class="summary-icons" id="church_commitment" title="' . __( 'Group identifies itself as a Church', 'disciple_tools' ) . '">';
                } else {
                    echo '<div class="summary-icons" id="church_commitment" title="' . __( 'Group identifies itself as a Church', 'disciple_tools' ) . '" style="background-color: #b2c6d6">';
                }
        echo '<img src="' . esc_attr( $plugin_base_url ) . '/assets/images/circle.svg">';
        echo '</div>';
        echo '<div class="summary-label"><p>' . esc_html( 'Church Commitment', 'disciple_tools' ) . '</p></div>';
        echo '</div>';
    }

    public function dt_add_section( $section, $post_type ) {
        $post_id = GET_THE_ID();
        if ( empty( $post_id )){
            return;
        }

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

        $practiced_items = get_post_meta( $post_id, 'health_metrics' );
        if ( in_array( 'church_commitment', $practiced_items ) ) {
            $health_church_commitment = 'committed';  
        } else {
            $health_church_commitment = '';  
        }

        if ( $section === 'custom-health-metrics' ): ?>
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

            .committed {
                border: 3px #4caf50 solid !important;
            }

            .half-opacity {
                opacity: 0.4;
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
            .summary-grid {
                display: flex;
                flex-wrap: wrap;
                margin-top: 20px;
            }
        </style>
        <div>
            <div class="custom-church-health-circle <?php echo $health_church_commitment; ?>" id="custom-church-health-items-container">
                <div class="custom-church-health-grid">
                    <?php self::display_item_divs(); ?>
                </div>
            </div>
        </div>
        <div class="summary-grid" align="center">
            <?php self::display_item_overview(); ?>
        </div>
    <?php 
        $plugin_base_url = self::get_plugin_base_url();
        echo '<script src="' . esc_attr( $plugin_base_url ) . '/assets/js/custom-tile.js"></script>';
        endif;
    }
}
Custom_Church_Health_Tile_Tile::instance();
