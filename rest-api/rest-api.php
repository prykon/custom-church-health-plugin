<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Custom_Church_Health_Tile_Endpoints
{
    /**
     * @todo Set the permissions your endpoint needs
     * @link https://github.com/DiscipleTools/Documentation/blob/master/theme-core/capabilities.md
     * @var string[]
     */
    public $permissions = [ 'access_contacts', 'dt_all_access_contacts', 'view_project_metrics' ];


    /**
     * @todo define the name of the $namespace
     * @todo define the name of the rest route
     * @todo defne method (CREATABLE, READABLE)
     * @todo apply permission strategy. '__return_true' essentially skips the permission check.
     */
    //See https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
    public function add_api_routes() {
        $namespace = 'custom_church_health_tile/v1';

        register_rest_route(
            $namespace, '/endpoint', [
                'methods'  => WP_REST_Server::CREATABLE,
                'callback' => [ $this, 'private_endpoint' ],
                'permission_callback' => function( WP_REST_Request $request ) {
                    return $this->has_permission();
                },
            ]
        );
        register_rest_route(
            $namespace, '/update_practice/(?P<group_id>\d+)/(?P<practice>\w+)', [
                'methods'  => WP_REST_Server::CREATABLE,
                'callback' => [ $this, 'update_practice' ],
                'permission_callback' => function( WP_REST_Request $request ) {
                    return $this->has_permission();
                },
            ]
        );
    }

    public function update_practice( WP_REST_Request $request ) {
        $params = $request->get_params();
        $practice = esc_sql( $params['practice'] );
        $group_id = esc_sql( $params['group_id'] );

        //check if practice exists for that group_id and set it if it doesn't
        $args = array(
            'post_type'   => 'group',
            'post_id' => $group_id,
            'meta_key'     => 'health_metrics',
            'meta_value'   => $practice,
        );

        $curr_health_metrics = get_post_meta( $group_id, 'health_metrics' );

        if ( in_array( $practice, $curr_health_metrics ) ) {
            delete_post_meta( $group_id, 'health_metrics', $practice );
            return "success: $practice deleted from group $group_id";
        } else {
            add_post_meta( $group_id, 'health_metrics', $practice );
            return "success: $practice added to group $group_id";
        }
    }

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
    }
    public function has_permission(){
        $pass = false;
        foreach ( $this->permissions as $permission ){
            if ( current_user_can( $permission ) ){
                $pass = true;
            }
        }
        return $pass;
    }
}
Custom_Church_Health_Tile_Endpoints::instance();
