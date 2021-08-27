<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Custom_Group_Health_Plugin_Tile
{
    private static $_instance = null;
    public static function instance(){
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    public function __construct(){
        add_action( 'display_item_divs', [ $this, 'display_item_divs' ], 10, 4 );
        add_filter( 'dt_details_additional_tiles', [ $this, 'dt_details_additional_tiles' ], 20, 2 );
        add_action( 'dt_details_additional_section', [ $this, 'dt_add_section' ], 30, 2 );
        add_action( 'wp_enqueue_scripts', [ $this, 'load_tile_script' ], 99 );
    }

    public function load_tile_script() {
        if ( is_singular( 'groups' ) && get_the_ID() && DT_Posts::can_view( 'groups', get_the_ID() ) ){
            wp_enqueue_script( 'custom-tile-js', self::get_plugin_base_url() . '/assets/js/custom-tile.js', 'jquery', filemtime( plugin_dir_path( __FILE__ ) . '../assets/js/custom-tile.js' ), false );
        }

        // $custom_health_fields['health_metrics']['default'][ 'prykon' ] = 'foo';
        // wp_enqueue_script( 'dt_groups', get_template_directory_uri() . '/dt-groups/groups.js', [
        //         'jquery',
        //         'details'
        //     ] );
        // wp_localize_script( 'dt_groups', 'post_type_fields', $custom_health_fields );
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
    public function dt_details_additional_tiles( $tiles, $post_type = '' ) {
        if ( $post_type === 'groups' ){
            $new_tiles = [];
            foreach ( $tiles as $index => $value ) {
                if ( $index !== 'health-metrics') {
                    $new_tiles[$index] = $value;
                } else {
                    $new_tiles['custom-health-metrics'] = [ 'label' => __( 'Group Health', 'disciple_tools' ) ];
                }
            }
            $tiles = $new_tiles;
        }
        return $tiles;
    }

    private function display_item_divs() {
        $post_id = GET_THE_ID();
        if ( empty( $post_id )){
            return;
        }

        $plugin_base_url = self::get_plugin_base_url();
        $custom_field_options = dt_get_option( 'dt_field_customizations' );
        if ( empty( $custom_field_options ) ) {
            echo '<div class="custom-group-health-item" id="health-metrics" style="filter: opacity(0.35);"><img src="' . esc_attr( $plugin_base_url . '/assets/images/warning.svg' ) . '">' . esc_html( 'Empty', 'disciple_tools' ) . '</div>';
            return;
        }

        $i = 0;
        $output = '';
        $plugin_base_url = self::get_plugin_base_url();

        $practiced_items = get_post_meta( $post_id, 'health_metrics' );

        if ( empty( $practiced_items ) ) {
            $practiced_items = [];
        }
        foreach ( $custom_field_options['groups']['health_metrics']['default'] as $key => $value ) {
            // Check if custom church health item is being practiced by group
            $value['label'] = esc_html( str_replace( 'church_', '', $value['label'] ) );

            if ( in_array( $key, $practiced_items ) ) {
                $item_opacity = '';
            } else {
                $item_opacity = 'half-opacity';
            }
            ?>
            <div class="custom-group-health-item <?php echo esc_html( $item_opacity ?? '' ); ?>" id="icon_<?php echo esc_attr( strtolower( $key ) ) ?>" title="<?php echo esc_attr( $value['description'] ); ?>"><img src="<?php echo esc_attr( $plugin_base_url . '/assets/images/' . $value['image'] . '.svg' ); ?>"></div>
            <?php
        }
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
        $custom_field_options = dt_get_option( 'dt_field_customizations' );

        if ( empty( $custom_field_options['groups']['health_metrics']['default'] ) ) {
            return;
        }

        $practiced_items = get_post_meta( $post_id, 'health_metrics' );
        if ( $practiced_items === null ) {
            $practiced_items = [];
        }

        foreach ( $custom_field_options['groups']['health_metrics']['default'] as $key => $value ) :
            $value['label'] = str_replace( 'church_', '', $value['label'] ); ?>
            <div class="summary-tile">
                <?php
                if ( in_array( $key, $practiced_items ) ) {
                    echo '<div class="summary-icons" id="' . esc_attr( $key ) . '" title="' . esc_attr( $value['description'] ) . '">';
                } else {
                    echo '<div class="summary-icons half-opacity" id="' . esc_attr( $key ) . '" title="' . esc_attr( $value['description'] ) . '">';
                }
                echo '<img src="' . esc_attr( $plugin_base_url . '/assets/images/' . $value['image'] . '.svg' ) .'">';
                echo '</div>';
                echo '<div class="summary-label"><p>' . esc_html( $value['label'] ) . '</p></div>';
                echo '</div>';
        endforeach;

        echo '<div class="summary-tile">';
        if ( in_array( 'church_commitment', $practiced_items ) ) {
                    echo '<div class="summary-icons" id="church_commitment">';
        } else {
                    echo '<div class="summary-icons" id="church_commitment" style="background-color: #b2c6d6">';
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

        $custom_field_options = dt_get_option( 'dt_field_customizations' );

        if ( empty( $custom_field_options['groups']['health_metrics']['default'] ) ) {
            $item_count = 0;
        } else {
            $item_count = count( $custom_field_options['groups']['health_metrics']['default'] );
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

            .custom-group-health-item {
                display: none;
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

            .custom-group-health-item img {
                height: <?php echo esc_attr( $health_item_size ); ?>px;
                width: <?php echo esc_attr( $health_item_size ); ?>px;
            }

            .custom-group-health-circle {
                display: block;
                margin:auto;
                height:300px;
                width:300px;
                border-radius:100%;
                border: 3px darkgray dashed;
            }

            .custom-group-health-grid {
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
            <div class="custom-group-health-circle <?php echo esc_attr( $health_church_commitment ); ?>" id="custom-group-health-items-container">
                <div class="custom-group-health-grid">
                    <?php self::display_item_divs(); ?>
                </div>
            </div>
        </div>
        <div class="summary-grid" align="center">
            <?php
                self::display_item_overview();
            ?>
        </div>
    <?php endif;
    }
}
Custom_Group_Health_Plugin_Tile::instance();
