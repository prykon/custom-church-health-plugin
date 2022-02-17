<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class Custom_Group_Health_Plugin_Menu
 */
class Custom_Group_Health_Plugin_Menu {

    public $token = 'custom_group_health_plugin';

    private static $_instance = null;

    /**
     * Custom_Group_Health_Plugin_Menu Instance
     *
     * Ensures only one instance of Custom_Group_Health_Plugin_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return Custom_Group_Health_Plugin_Menu instance
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

        self::check_default_template();

    } // End __construct()


    /**
     * Method that checks if there's no selected
     * custom group health icons and if so sets the default DT template
     * @access public
     * @since 1.1
     */
    public static function check_default_template() {
        $icons = get_option( 'custom_group_health_icons', null );
        if ( empty( $icons ) ) {
            Custom_Group_Health_Plugin_Tab_General::admin_notice( __( 'No custom icons detected. Setting DT default template', 'custom-group-health-plugin' ), 'warning' );
            $object = new Custom_Group_Health_Plugin_Tab_Templates();
            $object->set_template( 'dt_default_template' );

        }
        return;
    }

    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_submenu_page( 'dt_extensions', 'Custom Group Health Plugin', 'Custom Group Health Plugin', 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple.Tools Theme fully loads.
     */
    public function extensions_menu() {}

    public static function get_plugin_base_url(){
        // Remove '/admin/' subdirectory from plugin base url
        $plugin_base_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
        $plugin_base_url = explode( '/', $plugin_base_url );
        array_pop( $plugin_base_url );
        $plugin_base_url = implode( '/', $plugin_base_url );
        return $plugin_base_url;
    }

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple.Tools and allows admins, strategists and dispatchers into the wp-admin
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
            <h2><?php esc_html_e( 'Custom Group Health Plugin', 'custom-group-health-plugin' ); ?></h2>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_attr( $link ) . 'general' ?>" class="nav-tab <?php echo esc_html( ( $tab == 'general' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'General', 'custom-group-health-plugin' ) ?></a>
                <a href="<?php echo esc_attr( $link ) . 'templates' ?>" class="nav-tab <?php echo esc_html( ( $tab == 'templates' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Templates', 'custom-group-health-plugin' ) ?></a>
                <a href="<?php echo esc_attr( $link ) . 'help' ?>" class="nav-tab <?php echo esc_html( ( $tab == 'help' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Help', 'custom-group-health-plugin' ) ?></a>
            </h2>

            <?php
            switch ($tab) {
                case "general":
                    $object = new Custom_Group_Health_Plugin_Tab_General();
                    $object->content();
                    break;
                case "templates":
                    $object = new Custom_Group_Health_Plugin_Tab_Templates();
                    $object->content();
                    break;
                case "help":
                    $object = new Custom_Group_Health_Plugin_Tab_Help();
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
Custom_Group_Health_Plugin_Menu::instance();

/**
 * Class Custom_Group_Health_Plugin_Tab_General
 */
class Custom_Group_Health_Plugin_Tab_General extends Disciple_Tools_Abstract_Menu_Base {
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
        $plugin_base_url = Custom_Group_Health_Plugin_Menu::get_plugin_base_url();
        ?>
        <form method="post">
            <?php wp_nonce_field( 'delete_key', 'delete_key_nonce' ); ?>
            <table>
        <?php
        $icons = get_option( 'custom_group_health_icons', null );
        if ( !empty( $icons ) ) {
            foreach ( $icons as $icon ) :
                ?>
                <tr>
                    <td style="vertical-align:middle;"><img src="<?php echo esc_attr( $plugin_base_url . '/assets/images/' . $icon['icon'] . '.svg' ); ?>" width="35px" height="35px"></td>
                    <td style="vertical-align:middle;"><?php echo esc_html__( str_replace( 'church_', '', $icon['label'] ), 'custom-group-health-plugin' ); ?></td>
                    <td style="vertical-align:middle;"><?php echo esc_html__( $icon['description'], 'custom-group-health-plugin' ); ?></td>
                    <td style="vertical-align:middle;">
                        <button type="submit" class="button" name="delete_key" value="<?php echo esc_html( $icon['key'] ); ?>"><?php esc_html_e( 'Delete', 'custom-group-health-plugin' ) ?></button>
                    </td>
                </tr>
                    <?php
                endforeach;
        } else {
            ?>
                <tr>
                    <td align="center">
                        <i><?php esc_html_e( 'No custom items yet...', 'custom-group-health-plugin' ); ?></i>
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
            <?php wp_nonce_field( 'create_icon', 'create_icon_nonce' ); ?>
            <table>
                <tr>
                    <th><?php esc_html_e( 'Label', 'custom-group-health-plugin' ); ?></th>
                    <th><?php esc_html_e( 'Description', 'custom-group-health-plugin' ); ?></th>
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
                    <th><?php esc_html_e( 'Icon', 'custom-group-health-plugin' ); ?></th>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php self::show_icons(); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <button type="submit" class="button" name="add_icon"><?php esc_html_e( 'Add', 'custom-group-health-plugin' ); ?></button>
                    </td>
                </tr>
            </table>
        </form>
        <?php
    }

    private function show_icons() {
        $plugin_base_url = Custom_Group_Health_Plugin_Menu::get_plugin_base_url();

        $icons = [
            [ 'file_name' => 'twelve-attenders', 'name' => 'Attenders', ],
            [ 'file_name' => 'twelve-baptism', 'name' => 'Baptism', ],
            [ 'file_name' => 'twelve-give', 'name' => 'Give', ],
            [ 'file_name' => 'twelve-gospel', 'name' => 'Gospel', ],
            [ 'file_name' => 'twelve-holy-spirit', 'name' => 'Holy Spirit', ],
            [ 'file_name' => 'twelve-lords-supper', 'name' => "Lord's Supper", ],
            [ 'file_name' => 'twelve-love', 'name' => 'Love', ],
            [ 'file_name' => 'twelve-make-disciples', 'name' => 'Make Disciples', ],
            [ 'file_name' => 'twelve-prayer', 'name' => 'Prayer', ],
            [ 'file_name' => 'twelve-repent', 'name' => 'Repent', ],
            [ 'file_name' => 'twelve-signs-wonders', 'name' => 'Signs and Wonders', ],
            [ 'file_name' => 'twelve-word', 'name' => 'Word', ],
            [ 'file_name' => 'twelve-worship', 'name' => 'Worship', ],
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
        <div class="custom_icon" title="<?php echo esc_attr( $icon['name'] ); ?>" data-name="<?php echo esc_attr( $icon['file_name'] ); ?>">
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
        $items = get_option( 'custom_group_health_icons', null );
        if ( !empty( $items ) ) {
            $item_count = count( $items );
        } else {
            $item_count = 0;
        }
        ?>
        <form method="post">
            <?php wp_nonce_field( 'health_edit', 'health_edit_nonce' ); ?>
            <table>
                <tr>
                    <td style="vertical-align: middle">
                        <?php if ( $item_count < 12 ) : ?>
                            <p for="tile-select"><?php esc_html_e( 'Create new Group Health Icon', 'custom-group-health-plugin' ) ?></p>
                        <?php else : ?>
                            <p for="tile-select"><i><?php esc_html_e( 'You can only create up to 12 custom group health icons', 'custom-group-health-plugin' ) ?></i></p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ( $item_count < 12 ) : ?>
                            <button type="submit" class="button" name="show_add_new_icon"><?php esc_html_e( 'Create', 'custom-group-health-plugin' ) ?></button>
                        <?php else : ?>
                            <button class="button" name="show_add_new_icon" disabled><?php esc_html_e( 'Create', 'custom-group-health-plugin' ) ?></button>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </form>
        <?php
    }

    private function process_add_icon() {
        if ( !isset( $_POST['create_icon_nonce'] ) || !wp_verify_nonce( sanitize_key( $_POST['create_icon_nonce'] ), 'create_icon' ) ) {
            return;
        }
        if ( !empty( $_POST['new_icon'] ) ) {
            $new_icon = sanitize_text_field( wp_unslash( $_POST['new_icon'] ) );
        } else {
            self::admin_notice( __( 'Error: Item image missing. Item was not created', 'custom-group-health-plugin' ), 'error' );
        }

        if ( !empty( $_POST['new_label'] ) ) {
            $new_label = sanitize_text_field( wp_unslash( $_POST['new_label'] ) );
        } else {
            self::admin_notice( __( 'Error: Item label missing. Item was not created', 'custom-group-health-plugin' ), 'error' );
        }

        if ( !empty( $_POST['new_description'] ) ) {
            $new_description = sanitize_text_field( wp_unslash( $_POST['new_description'] ) );
        } else {
            self::admin_notice( __( 'Error: Item description missing. Item was not created', 'custom-group-health-plugin' ), 'error' );
        }

        $new_key = sanitize_key( strtolower( str_replace( ' ', '_', $new_label ) ) );

        //add option
        $all_items = get_option( 'custom_group_health_icons', null );

        $new_item = array(
            'key' => 'church_' . $new_key,
            'icon' => $new_icon,
            'label' => $new_label,
            'description' => $new_description
        );

        $all_items[] = $new_item;

        update_option( 'custom_group_health_icons', $all_items );

        self::admin_notice( __( 'Icon created successfully', 'custom-group-health-plugin' ), 'success' );
    }


    private function process_delete_icon() {
        if ( !empty( $_POST['delete_key'] ) ) {
            if ( !isset( $_POST['delete_key_nonce'] ) || !wp_verify_nonce( sanitize_key( $_POST['delete_key_nonce'] ), 'delete_key' ) ) {
                return;
            }

            $delete_key = sanitize_text_field( wp_unslash( $_POST['delete_key'] ) );
            $all_items = get_option( 'custom_group_health_icons', null );

            if ( is_array( $all_items ) ) {
                foreach ( $all_items as $item ) {
                    if ( $item['key'] == $delete_key ) {
                        $delete_index = array_search( $item, $all_items );
                        unset( $all_items[$delete_index] );
                        update_option( 'custom_group_health_icons', $all_items );
                        self::admin_notice( __( 'Icon deleted successfully', 'custom-group-health-plugin' ), 'success' );

                        // If no custom icons remain, add default DT group health template
                        Custom_Group_Health_Plugin_Menu::check_default_template();
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
                $this->box( 'top', __( 'Manage Custom Group Health Plugin', 'custom-group-health-plugin' ) );
                $this->show_tiles();
                $this->box( 'bottom' );

                $this->box( 'top', __( 'Add new Group Health Icons', 'custom-group-health-plugin' ) );
                $this->add_new_church_health_icons();
                $this->box( 'bottom' );

            // Show add tile module
            if ( isset( $_POST['show_add_new_icon'] ) ) {
                if ( isset( $_POST['health_edit_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['health_edit_nonce'] ), 'health_edit' ) ) {
                    $this->box( 'top', __( 'Create new item', 'custom-group-health-plugin' ) );
                    $this->create_new_icon();
                    $this->box( 'bottom' );
                }
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
                    <th><?php esc_html_e( 'Information', 'custom-group-health-plugin' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <?php esc_html_e( "Create new icons to help you track your group's spiritual health or delete existing icons.", 'custom-group-health-plugin' ); ?>
                    
                    <br>
                    <br>
                    <b><?php esc_html_e( 'Note', 'custom-group-health-plugin' ); ?>:</b> 
                    <?php esc_html_e( 'If you re-create a deleted item with the same label, groups that formerly had that item selected will once again display it as active.', 'custom-group-health-plugin' ); ?>
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
 * Class Custom_Group_Health_Plugin_Tab_Templates
 */
class Custom_Group_Health_Plugin_Tab_Templates {

    public function set_template( $template_name ) {
        $plugin_base_url = Custom_Group_Health_Plugin_Menu::get_plugin_base_url();
        $dt_template_health_items = [
            0 => [
                    'key' => 'church_baptism',
                    'label' => __( 'Baptism', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is baptising.', 'custom-group-health-plugin' ),
                    'icon' => 'baptism'
                ],
            1 => [
                    'key' => 'church_bible',
                    'label' => __( 'Bible Study', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is studying the bible.', 'custom-group-health-plugin' ),
                    'icon' => 'bible'
                ],
            2 => [
                    'key' => 'church_communion',
                    'label' => __( 'Communion', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is practicing communion.', 'custom-group-health-plugin' ),
                    'icon' => 'communion'
                ],
            3 => [
                    'key' => 'church_fellowship',
                    'label' => __( 'Fellowship', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is fellowshiping.', 'custom-group-health-plugin' ),
                    'icon' => 'love'
                ],
            4 => [
                    'key' => 'church_giving',
                    'label' => __( 'Giving', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is giving.', 'custom-group-health-plugin' ),
                    'icon' => 'money'
                ],
            5 => [
                    'key' => 'church_prayer',
                    'label' => __( 'Prayer', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is praying.', 'custom-group-health-plugin' ),
                    'icon' => 'prayer'
                ],
            6 => [
                    'key' => 'church_praise',
                    'label' => __( 'Praise', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is praising.', 'custom-group-health-plugin' ),
                    'icon' => 'praise'
                ],
            7 => [
                    'key' => 'church_sharing',
                    'label' => __( 'Sharing the Gospel', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is sharing the gospel.', 'custom-group-health-plugin' ),
                    'icon' => 'gospel'
                ],
            8 => [
                    'key' => 'church_leaders',
                    'label' => __( 'Leaders', 'custom-group-health-plugin' ),
                    'description' => __( 'The group has leaders.', 'custom-group-health-plugin' ),
                    'icon' => 'happy'
                ],
            ];

        $twelve_practices_template_health_items = [
            0 => [
                    'key' => 'church_sharing',
                    'label' => __( 'Sharing the Gospel', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is sharing the gospel.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-gospel'
                ],
            1 => [
                    'key' => 'church_repentance',
                    'label' => __( 'Repentance', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is practicing repentance.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-repent'
                ],
            2 => [
                    'key' => 'church_baptism',
                    'label' => __( 'Baptism', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is baptising.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-baptism'
            ],
            3 => [
                    'key' => 'church_holy_spirit',
                    'label' => __( 'Holy Spirit', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is moving in the Holy Spirit.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-holy-spirit'
                ],
            4 => [
                    'key' => 'church_bible',
                    'label' => __( 'Word', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is studying the bible.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-word'
                ],
            5 => [
                    'key' => 'church_fellowship',
                    'label' => __( 'Fellowship', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is fellowshiping.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-love'
                ],
            6 => [
                    'key' => 'church_communion',
                    'label' => __( 'Communion', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is practicing communion.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-lords-supper'
                ],
            7 => [
                    'key' => 'church_prayer',
                    'label' => __( 'Prayer', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is praying.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-prayer'
            ],
            8 => [
                    'key' => 'church_signs_wonders',
                    'label' => __( 'Signs and Wonders', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is experiencing signs and wonders.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-signs-wonders'
            ],
            9 => [
                    'key' => 'church_giving',
                    'label' => __( 'Giving', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is giving.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-give'
            ],
            10 => [
                    'key' => 'church_worship',
                    'label' => __( 'Worship', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is worshipping.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-worship'
            ],
            11 => [
                    'key' => 'church_making_disciples',
                    'label' => __( 'Making Disciples', 'custom-group-health-plugin' ),
                    'description' => __( 'The group is making disciples.', 'custom-group-health-plugin' ),
                    'icon' => 'twelve-make-disciples'
            ],
        ];

        switch ( $template_name ) {
            case 'dt_default_template':
                update_option( 'custom_group_health_icons', $dt_template_health_items );
                Custom_Group_Health_Plugin_Tab_General::admin_notice( __( 'Template switched successfully.', 'custom-group-health-plugin' ), 'success' );
                break;

            case 'twelve_practices_template':
                update_option( 'custom_group_health_icons', $twelve_practices_template_health_items );
                Custom_Group_Health_Plugin_Tab_General::admin_notice( __( 'Template switched successfully.', 'custom-group-health-plugin' ), 'success' );
                break;
        }
    }

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
                    <th><?php esc_html_e( 'Templates', 'custom-group-health-plugin' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php esc_html_e( 'Select from common group health tracking methods', 'custom-group-health-plugin' ); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <form method="post">
                            <?php wp_nonce_field( 'set_template', 'set_template_nonce' ); ?>
                            <table class="widefat">
                                <thead>
                                    <tr>
                                        <td><?php esc_html_e( 'Template', 'custom-group-health-plugin' ); ?></td>
                                        <td><?php esc_html_e( 'Icon', 'custom-group-health-plugin' ); ?></td>
                                        <td><?php esc_html_e( 'Label', 'custom-group-health-plugin' ); ?></td>
                                        <td><?php esc_html_e( 'Action', 'custom-group-health-plugin' ); ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><b><?php esc_html_e( 'Disciple.Tools Default', 'custom-group-health-plugin' ); ?></b></td>
                                        <td colspan="3">
                                    </tr>
                                            <?php
                                                $plugin_base_url = Custom_Group_Health_Plugin_Menu::get_plugin_base_url();

                                                $health_factors = [
                                                    [ 'label' => __( 'Baptism', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/baptism.svg' ],
                                                    [ 'label' => __( 'Bible Study', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/bible.svg' ],
                                                    [ 'label' => __( 'Communion', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/communion.svg' ],
                                                    [ 'label' => __( 'Fellowship', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/love.svg' ],
                                                    [ 'label' => __( 'Giving', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/money.svg' ],
                                                    [ 'label' => __( 'Prayer', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/prayer.svg' ],
                                                    [ 'label' => __( 'Praise', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/praise.svg' ],
                                                    [ 'label' => __( 'Sharing the Gospel', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/gospel.svg' ],
                                                    [ 'label' => __( 'Leaders', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url ). '/assets/images/happy.svg' ],
                                                ];

                                                foreach ( $health_factors as $health_factor ) {
                                                    echo '<tr><td></td><td><img src="' . esc_attr( $health_factor['icon'] ) . '" width="25" height="25"></td><td>' . esc_html( $health_factor['label'] ) . '</td><td></td></tr>';
                                                }
                                                ?>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td>
                                            <button type="submit" class="button" name="set-template-dt" title="Set 'Disciple.Tools Default' as Group Health tile"><?php esc_html_e( 'Set', 'custom-group-health-plugin' ); ?></button>
                                            <?php
                                            // Check for template updates
                                            if ( isset( $_POST['set-template-dt'] ) ) {
                                                if ( !isset( $_POST['set_template_nonce'] ) || !wp_verify_nonce( sanitize_key( $_POST['set_template_nonce'] ), 'set_template' ) ) {
                                                    return;
                                                }
                                                self::set_template( 'dt_default_template' );
                                            } else if ( isset( $_POST['set-twelve-practices'] ) ) {
                                                self::set_template( 'twelve_practices_template' );
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b><?php esc_html_e( 'Twelve Practices', 'custom-group-health-plugin' ); ?></b></td>
                                        <td colspan="3"></td>
                                    </tr>
                                            <?php
                                            $health_factors = [
                                                [ 'label' => __( 'Sharing the Gospel', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-gospel.svg' ) ],
                                                [ 'label' => __( 'Repentance', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-repent.svg' ) ],
                                                [ 'label' => __( 'Baptism', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-baptism.svg' ) ],
                                                [ 'label' => __( 'Holy Spirit', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-holy-spirit.svg' ) ],
                                                [ 'label' => __( 'Word', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-word.svg' ) ],
                                                [ 'label' => __( 'Fellowship', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-love.svg' ) ],
                                                [ 'label' => __( 'Communion', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-lords-supper.svg' ) ],
                                                [ 'label' => __( 'Prayer', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-prayer.svg' ) ],
                                                [ 'label' => __( 'Signs and Wonders', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-signs-wonders.svg' ) ],
                                                [ 'label' => __( 'Giving', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-give.svg' ) ],
                                                [ 'label' => __( 'Worship', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-worship.svg' ) ],
                                                [ 'label' => __( 'Making Disciples', 'custom-group-health-plugin' ), 'icon' => esc_html( $plugin_base_url . '/assets/images/twelve-make-disciples.svg' ) ],
                                            ];

                                            foreach ( $health_factors as $health_factor ) {
                                                echo '<tr><td></td><td><img src="' . esc_attr( $health_factor['icon'] ) . '" width="25" height="25"></td><td>' . esc_html( $health_factor['label'] ) . '</td><td></td></tr>';
                                            }
                                            ?>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td>
                                            <button type="submit" class="button" name="set-twelve-practices" title="Set 'Twelve Practices' as Group Health tile"><?php esc_html_e( 'Set', 'custom-group-health-plugin' ); ?></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
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
                    <th><?php esc_html_e( 'Information', 'custom-group-health-plugin' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <?php esc_html_e( "Select between Disciple.Tools' default health items or common Twelve Practices template.", 'custom-group-health-plugin' ); ?>
                    <br>
                    <br>
                    <?php esc_html_e( "You can go back and edit them later from the 'General' tab.", 'custom-group-health-plugin' ); ?>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}

/**
 * Class Custom_Group_Health_Plugin_Tab_Help
 */
class Custom_Group_Health_Plugin_Tab_Help {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-3">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
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
                    <th><?php esc_html_e( 'Help', 'custom-group-health-plugin' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="https://github.com/prykon/custom-group-health-plugin#readme" target="_blank"><?php esc_html_e( 'Full documentation available here', 'custom-group-health-plugin' ); ?></a>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}

// @phpcs:enable
