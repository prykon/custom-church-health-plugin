<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class Custom_Church_Health_Tile_Menu
 */
class Custom_Church_Health_Tile_Menu {

    public $token = 'custom_church_health_tile';

    private static $_instance = null;

    /**
     * Custom_Church_Health_Tile_Menu Instance
     *
     * Ensures only one instance of Custom_Church_Health_Tile_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return Custom_Church_Health_Tile_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {

        add_action( "admin_menu", array( $this, "register_menu" ) );

    } // End __construct()


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_submenu_page( 'dt_extensions', 'Custom Church Health Tile', 'Custom Church Health Tile', 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu() {}

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        if ( isset( $_GET["tab"] ) ) {
            $tab = sanitize_key( wp_unslash( $_GET["tab"] ) );
        } else {
            $tab = 'general';
        }

        $link = 'admin.php?page='.$this->token.'&tab=';

        ?>
        <div class="wrap">
            <h2>Custom Church Health Tile</h2>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_attr( $link ) . 'general' ?>"
                   class="nav-tab <?php echo esc_html( ( $tab == 'general' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">General</a>
                <a href="<?php echo esc_attr( $link ) . 'help' ?>" class="nav-tab <?php echo esc_html( ( $tab == 'help' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">Help</a>
            </h2>

            <?php
            switch ($tab) {
                case "general":
                    $object = new Custom_Church_Health_Tile_Tab_General();
                    $object->content();
                    break;
                case "help":
                    $object = new Custom_Church_Health_Tile_Tab_Help();
                    $object->content();
                    break;
                default:
                    break;
            }
            ?>

        </div><!-- End wrap -->

        <?php
    }
}
Custom_Church_Health_Tile_Menu::instance();

/**
 * Class Custom_Church_Health_Tile_Tab_General
 */
class Custom_Church_Health_Tile_Tab_General extends Disciple_Tools_Abstract_Menu_Base {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    private function show_tiles() {
        $plugin_base_url = self::get_plugin_base_url();
        ?>
        <form method="post">
            <table>
        <?php
            $icons = get_option( 'custom_church_health_icons', null );
            if ( !empty( $icons ) ) {
                foreach( $icons as $icon ) :
                    ?>
                    <tr>
                        <td style="vertical-align:middle;"><img src="<?php echo esc_attr( $plugin_base_url . '/assets/images/' . $icon['icon'] . '.svg' ); ?>" width="35px" height="35px"></td>
                        <td style="vertical-align:middle;"><?php echo esc_html( $icon['label'] ); ?></td>
                        <td style="vertical-align:middle;"><?php echo esc_html( $icon['description'] ); ?></td>
                        <td style="vertical-align:middle;">
                            <button type="submit" class="button" name="delete_key" value="<?php echo esc_html( $icon['key'] ); ?>"><?php esc_html_e( 'Delete', 'disciple_tools' ) ?></button>
                        </td>
                    </tr>
                    <?php
                endforeach;
            } else {
                ?>
                <tr>
                    <td align="center">
                        <i><?php esc_html_e( 'No custom items yet...', 'disciple_tools' ); ?></i>
                    </td>
                </tr>
                <?php
            }
        ?>
            </table>
        </form>
        <?php
    }

    private function create_new_icon() {
        ?>
        <style>
            .image_icon {
                height: 35px;
                width: 35px;
                margin: auto;
            }
            .custom_icon {
                height: 50px;
                width: 50px;
                border: none;
                background-color: transparent;
                display: inline-grid;
            }
            .selected{
                border: 1px solid black;
            }
            .custom_icon:hover{
                background-color: lightgray;
            };
        </style>
        <form method="post">
            <table>
                <tr>
                    <th>Label</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="new_label" required>
                    </td>
                    <td>
                        <input type="text" name="new_description" required>
                    </td>
                    <td>
                        <input type="hidden" id="new_icon" name="new_icon" required>
                    </td>
                </tr>
                <tr>
                    <th>Icon</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php self::show_icons(); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <button type="submit" class="button" name="add_icon"><?php esc_html_e( 'Add', 'disciple_tools' ); ?></button>
                    </td>
                </tr>
            </table>
        </form>
        <?php
    }

    private function get_plugin_base_url(){
        // Remove '/admin/' subdirectory from plugin base url
        $plugin_base_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
        $plugin_base_url = explode( '/', $plugin_base_url );
        array_pop( $plugin_base_url );
        $plugin_base_url = implode( '/', $plugin_base_url );
        return $plugin_base_url;
    }

    private function show_icons() {
        $plugin_base_url = self::get_plugin_base_url();        

        $icons = [
            [ 'file_name' => 'baptism', 'name' => 'Baptism', ],
            [ 'file_name' => 'bible', 'name' => 'Bible', ],
            [ 'file_name' => 'candle', 'name' => 'Candle', ],
            [ 'file_name' => 'children', 'name' => 'Children', ],
            [ 'file_name' => 'church-building', 'name' => 'Church Building', ],
            [ 'file_name' => 'communion', 'name' => 'Communion', ],
            [ 'file_name' => 'covid', 'name' => 'Covid', ],
            [ 'file_name' => 'cross', 'name' => 'Cross', ],
            [ 'file_name' => 'devotional', 'name' => 'Devotional', ],
            [ 'file_name' => 'dove', 'name' => 'Dove', ],
            [ 'file_name' => 'evangelism', 'name' => 'Evangelism', ],
            [ 'file_name' => 'exclamation', 'name' => 'Exclamation', ],
            [ 'file_name' => 'fasting', 'name' => 'Fasting', ],
            [ 'file_name' => 'fire', 'name' => 'Fire', ],
            [ 'file_name' => 'give', 'name' => 'Give', ],
            [ 'file_name' => 'gospel', 'name' => 'Gospel', ],
            [ 'file_name' => 'grapes', 'name' => 'Grapes', ],
            [ 'file_name' => 'islam', 'name' => 'Islam', ],
            [ 'file_name' => 'jesus-fish', 'name' => 'Ichtys', ],
            [ 'file_name' => 'judaism', 'name' => 'Judaism', ],
            [ 'file_name' => 'lightsaber', 'name' => 'Lightsaber', ],
            [ 'file_name' => 'love', 'name' => 'Love', ],
            [ 'file_name' => 'money', 'name' => 'Money', ],
            [ 'file_name' => 'network', 'name' => 'Network', ],
            [ 'file_name' => 'praise', 'name' => 'Praise', ],
            [ 'file_name' => 'prayer', 'name' => 'Prayer', ],
            [ 'file_name' => 'pulpit', 'name' => 'Pulpit', ],
            [ 'file_name' => 'ramadan', 'name' => 'Ramadan', ],
            [ 'file_name' => 'repentance', 'name' => 'Repentance', ],
            [ 'file_name' => 'sparkles', 'name' => 'Sparkles', ],
            [ 'file_name' => 'video-call', 'name' => 'Video Call', ],
            [ 'file_name' => 'warning', 'name' => 'Warning Sign', ],
            [ 'file_name' => 'water', 'name' => 'Water', ],
            [ 'file_name' => 'wheat', 'name' => 'Wheat', ],
        ];

        foreach ( $icons as $icon ):
        ?>
        <div class="custom_icon" title="<?php esc_attr_e( $icon['name'] ); ?>" data-name="<?php esc_attr_e( $icon['file_name'] ); ?>">
            <img src="<?php echo esc_attr( untrailingslashit( $plugin_base_url ) ) . '/assets/images/' . esc_attr( $icon['file_name'] ) . '.svg'; ?>" class="image_icon">
        </div>
        <?php
            endforeach;
        ?>
        <script type="text/javascript">
            jQuery( '.custom_icon' ).on( 'click', function() {
                jQuery( '.custom_icon' ).each( function( i, v ) {
                    jQuery(v).removeClass( 'selected' );
                });
                jQuery( this ).addClass( 'selected' );
                jQuery( '#new_icon' ).val( jQuery( this ).data( 'name' ) );
            });
        </script>
        <?php
    }

    private function add_new_church_health_icons(){
        ?>
        <form method="post">
            <table>
                <tr>
                    <td style="vertical-align: middle">
                        <label for="tile-select"><?php esc_html_e( 'Create new Church Health Icon', 'disciple_tools' ) ?></label>
                    </td>
                    <td>
                        <button type="submit" class="button" name="show_add_new_icon"><?php esc_html_e( 'Create', 'disciple_tools' ) ?></button>
                    </td>
                </tr>
            </table>
        </form>
        <?php
    }

    private function process_add_icon() {
        if ( !empty( $_POST['new_icon'] ) ) {
            $new_icon = sanitize_text_field( wp_unslash( $_POST['new_icon'] ) );
        } else {
            self::admin_notice( __( 'Error: Item image missing. Item was not created', 'disciple_tools' ), 'error' );    
        }

        if ( !empty( $_POST['new_label'] ) ) {
            $new_label = sanitize_text_field( wp_unslash( $_POST['new_label'] ) );
        } else {
            self::admin_notice( __( 'Error: Item label missing. Item was not created', 'disciple_tools' ), 'error' );
        } 

        if ( !empty( $_POST['new_description'] ) ) {
            $new_description = sanitize_text_field( wp_unslash( $_POST['new_description'] ) );
        } else {
            self::admin_notice( __( 'Error: Item description missing. Item was not created', 'disciple_tools' ), 'error' );
        } 

        $new_key = sanitize_key( strtolower( str_replace( ' ', '_', $new_label ) ) );

        //add option
        $all_items = get_option( 'custom_church_health_icons', null );

        $new_item = array(
            'key' => $new_key,
            'icon' => $new_icon,
            'label' => $new_label,
            'description' => $new_description
        );

        $all_items[] = $new_item;

        update_option( 'custom_church_health_icons', $all_items );
        
        self::admin_notice( __( 'Icon created successfully', 'disciple_tools' ), 'success' );
    }


    private function process_delete_icon() {
        if ( !empty( $_POST['delete_key'] ) ) {
            $delete_key = sanitize_text_field( wp_unslash( $_POST['delete_key'] ) );
            $all_items = get_option( 'custom_church_health_icons', null );
            
            if ( is_array( $all_items ) ) {
                foreach ( $all_items as $item ) {
                    if ( $item['key'] == $delete_key ) {
                        $delete_index = array_search( $item, $all_items);
                        unset( $all_items[$delete_index] );
                        update_option( 'custom_church_health_icons', $all_items );
                        self::admin_notice( __( 'Icon deleted successfully', 'disciple_tools' ), 'success' );
                    }
                }
            }
        }
    }

    public function main_column() {
        if ( isset( $_POST['add_icon'] ) ) {
            self::process_add_icon();
        }

        else if ( isset( $_POST['delete_key'] ) ) {
            self::process_delete_icon();
        }
        ?>
        <!-- Box -->
        <form method="post">
            <?php
                // Load tiles
                $this->box( 'top', __( 'Manage Custom Church Health Tiles' ) );
                $this->show_tiles();
                $this->box( 'bottom' );

                $this->box( 'top', __( 'Add new Church Health Icons' ) );
                $this->add_new_church_health_icons();
                $this->box( 'bottom' );
            
            // Show add tile module
            if ( isset( $_POST['show_add_new_icon'] ) ) {
                $this->box( 'top', __( 'Create new item', 'disciple_tools' ) );
                $this->create_new_icon();
                $this->box( 'bottom' );
            }
            ?>
        </form>
        <br>
        <!-- End Box -->
        <?php
    }

    public function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Information</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }

    /**
     * Display admin notice
     * @param $notice string
     * @param $type string error|success|warning
     */
    public static function admin_notice( string $notice, string $type ) {
        ?>
        <div class="notice notice-<?php echo esc_attr( $type ) ?> is-dismissible">
            <p><?php echo esc_html( $notice ) ?></p>
        </div>
        <?php
    }
}


/**
 * Class Custom_Church_Health_Tile_Tab_Help
 */
class Custom_Church_Health_Tile_Tab_Help {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Help</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        In order to bla bla bla
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }

    public function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Information</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}

