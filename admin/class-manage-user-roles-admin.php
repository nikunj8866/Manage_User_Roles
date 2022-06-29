<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.crestinfosystems.com/
 * @since      1.0.0
 *
 * @package    Manage_User_Roles
 * @subpackage Manage_User_Roles/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Manage_User_Roles
 * @subpackage Manage_User_Roles/admin
 * @author     Crest Infosystems Pvt. Ltd.  <nikunj.h@crestinfosystems.net>
 */
class Manage_User_Roles_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Manage_User_Roles_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Manage_User_Roles_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/manage-user-roles-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Manage_User_Roles_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Manage_User_Roles_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/manage-user-roles-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'manage_user_roles', [
			'ajax_url' => admin_url('admin-ajax.php'),
		]);

	}

	/** Add admin menu page for only super users */
	public function register_menus()
    {  
        
        if ( is_super_admin() ) {
            add_submenu_page( 
                'users.php',
                __('Manage User Role',$this->plugin_name),
                __('Manage User Role',$this->plugin_name), 
                'read', 
                'manage-user-roles',
                array( $this, 'manage_user_roles' )
             );   
        }
         
    }

	public function manage_user_roles()
	{
		if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

		include_once( plugin_dir_path( __FILE__ ) . 'partials/manage-user-roles-admin-display.php' );
	}

	public function get_user_roles_data(){
		ob_start();
		$result = array();
		$user = $_POST['user'];
		if($user) {
			if( username_exists( $user ) ) {
				$user_id = username_exists( sanitize_text_field( $_POST['user'] ) );
				$sites = get_sites();
				$user_blogs = get_blogs_of_user( $user_id, true );
				$activeSite = array();
				foreach($user_blogs as $ub) {
					array_push($activeSite, $ub->userblog_id);
				}
				
				if(count($sites)) : ?> 
                    <?php foreach ($sites as $key => $value) : 
						$blogName = $value->blogname;
						if(empty($blogName))
						{
							$blogName = str_replace("/", "", $value->path);
							$blogName = str_replace("-", " ", $blogName);
							$blogName = ucwords( $blogName );
						}
					?>
                        <tr class="form-field form-required">
							<td class="site-name">
                                <input type="checkbox" name="is-user-active[]" value="<?php echo $value->blog_id; ?>" <?php echo (in_array($value->blog_id, $activeSite)) ? 'checked': '';?> aria-required="true" autocapitalize="none" autocorrect="off"   />
                                <input type="text" name="site_name[]" placeholder="<?php _e('Site Name',$this->plugin_name);?>" value="<?php echo $blogName; ?>" readonly aria-required="true" autocapitalize="none" autocorrect="off"   />
                                <input type="hidden" name="site_id[]" value="<?php  echo $value->blog_id; ?>"  />
                            </td>
							<td class="site-url">
                                <input type="text" name="site_name[]" placeholder="<?php _e('Site URL',$this->plugin_name);?>" value="<?php echo $value->domain.$value->path; ?>" readonly aria-required="true" autocapitalize="none" autocorrect="off"   />
                            </td>
							<?php 
							$blogUserData = new WP_User($user_id,'',$value->blog_id);
							$defaultRole = $blogUserData->roles[0];
							if(empty($defaultRole))
							{
								$defaultRole = 'customer';
							}
							?>
                            <td>
                                <select name="user_role_<?php echo $value->blog_id; ?>" <?php echo (!in_array($value->blog_id, $activeSite)) ? 'disabled': '';?>>
                                    <?php wp_dropdown_roles($defaultRole); ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif;
			}
		}
		$result['html'] = ob_get_contents();
		ob_end_clean();
		$result['status'] = 'success';
		wp_send_json($result);
	}

	public function manage_user_roles_save(){
		$result = array();
		$isuseractive = $_REQUEST['is-user-active'];
		$sites = $_REQUEST['site_id'];
		$username = $_REQUEST['manage-user-roles-username'];
		$user_id = '';
		
		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'ajax-nonce' ) ) {
			$user_id = username_exists( sanitize_text_field( $username ) );
			if(empty($user_id))
			{
				$user_id = email_exists($username);
			}
			if($user_id)
			{
				foreach ( $sites as $key => $site ) {
					if(in_array($site, $isuseractive)) {
						add_user_to_blog( $site, $user_id, $_REQUEST['user_role_'.$site]);
					}
					else
					{
						remove_user_from_blog($user_id, $site);
					}
				}
				$result['msg'] = __( 'User role updated successfully.', $this->plugin_name );
			}
			else
			{
				$userdata = array(
					'user_login' => $username,
				);
				$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
				if (preg_match($pattern, $username) === 1) {
					$userdata['user_email'] = $username;
				}	
				$user_id = wp_insert_user( wp_slash( $userdata ) );
				foreach ( $sites as $key => $site ) {
					if(in_array($site, $isuseractive)) {
						add_user_to_blog( $site, $user_id, $_REQUEST['user_role_'.$site]);
					}
				}
				wp_new_user_notification($user_id, $deprecated = null, $notify = 'user');
				$result['msg'] = __( 'User added successfully.', $this->plugin_name );
			}
			$result['status'] = 'success';
		} else {		   
			$result['status'] = 'error';
			$result['msg'] = __( 'Security checking failed.', $this->plugin_name );
		}
		wp_send_json($result);
	}

	public function manage_user_roles_search_user()
	{
		$str = $_REQUEST['terms'];
		$result = array();
		$wp_user_query = new WP_User_Query(
			array(
			'search' => "*{$str}*",
			'search_columns' => array(
			'user_login',
			'user_nicename',
			'user_email',
			),
			'blog_id' => 'all'  
			)
		);
		$users = $wp_user_query->get_results();
		foreach ( $users as $user ) {
			$result[] = array(
				/* translators: 1: User login, 2: User email address. */
				'label' => sprintf( _x( '%1$s (%2$s)', 'user autocomplete result' ), $user->data->user_login, $user->data->user_email ),
				'value' => $user->data->user_login,
			);
		}
	
		wp_die( wp_json_encode( $result ) );

	}
}
