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

    /**
     * @param array $fields
     * @param string $post_type
     * @return array
     */
    private function display_item_divs() {
        $items = get_option('custom_church_health_icons', null );
        if ( empty( $items ) ) {
            $grid_template = [
                0,0,0,
                0,1,0,
                0,0,0,
            ];
        } else {
            $items = array_values( $items );
            $item_count = count( $items );
            
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
        }

        $i = 0;
        $output = '';
        $plugin_base_url = self::get_plugin_base_url();

        foreach ( $grid_template as $grid_item ) {
            if ( $grid_item === 0 ) {
                $output .= '<div class="custom-church-health-item"></div>';
            } else if ( $grid_item === 1 ) {
                if ( $items ) {
                    $output .= '<div class="custom-church-health-item" title="' . esc_attr( $items[$i]['label'] ) . '"><img src="' . esc_attr( $plugin_base_url . '/assets/' . $items[$i]['icon'] . '.svg' ) . '"></div>';
                    $i++;
                } else {
                    $output .= '<div class="custom-church-health-item"><img src="' . esc_attr( $plugin_base_url . '/assets/warning.svg' ) . '">' . esc_html( 'Empty', 'disciple_tools' ) . '</div>';
                }
            }
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

    private function display_item_css() {
        $items = get_option( 'custom_church_health_icons', null );
        if ( !empty( $items) ) {
            $item_count = count( $items );
        } else {
            $item_count = 0;
        }

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

    public function display_item_overview() {
        $plugin_base_url = self::get_plugin_base_url(); 
        $items = get_option('custom_church_health_icons', null );
        if ( empty( $items ) ) {        
            return;
        }

        $items = array_values( $items );

        foreach ( $items as $item ) : ?>
            <div style="display:inline;">
                <div class="summary-icons" title="<?php esc_attr_e( $item['description'] ); ?>">
                    <img src="<?php echo esc_attr( $plugin_base_url . '/assets/'. $item['icon'] . '.svg' ); ?>">
                </div>
                    <div><?php esc_html_e( $item['label'] ); ?></div>
            </div>
        <?php endforeach;
    }

    public function dt_add_section( $section, $post_type ) {
        if ( $section === 'custom_church_health_tile' ): ?>
        <style>
            .practicing {
                filter: none !important;
            }
            .custom-church-health-item {
                filter: opacity(0.35);
                margin: auto;
                height:65px;
                width:65px;
                border-radius: 100%;
                font-size: 16px;
                color: black;
                text-align: center;
                font-style: italic;
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

            .summary-icons {
                height: 50px;
                width: 50px;
                display: grid;
                grid-template-columns:auto auto auto;
                text-align: center;
                color: darkgray;
            }
        </style>
        <div>
            <div class="custom-church-health-circle">
                <div class="custom-church-health-grid">
                    <?php self::display_item_divs(); ?>
                </div>
            </div>
        </div>
        <div>
            <?php self::display_item_overview(); ?>
        </div>
    <?php endif; ?>
                
        <?php
    }
}
Custom_Church_Health_Tile_Tile::instance();
