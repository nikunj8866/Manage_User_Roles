<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.crestinfosystems.com/
 * @since      1.0.0
 *
 * @package    Manage_User_Roles
 * @subpackage Manage_User_Roles/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">   
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form method="post" name="<?php echo $this->plugin_name;?>_user" id="<?php echo $this->plugin_name;?>_user" class="validate">     
        <input type="hidden" name="action" value="manage_user_roles_save">
        <?php wp_nonce_field('ajax-nonce'); ?>   
        <label for="<?php echo $this->plugin_name; ?>-username"><?php _e('Username / Email ID',$this->plugin_name);?>  <span class="description">(<?php _e('required',$this->plugin_name);?>)</span></label>
        <div class="wrap-input-search">
        <input type="text" id="<?php echo $this->plugin_name; ?>-username" name="<?php echo $this->plugin_name; ?>-username" placeholder="<?php _e('Search',$this->plugin_name);?>" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" required  />                            
        </div>
        <table class="user-role-manage-table">
            <thead>
                <tr class="form-field form-required">
                        <th width="35%" class="site-name-th">
                            <input type="checkbox" name="check-uncheck-all-site" id="check-uncheck-all-site" value="1" aria-required="true" autocapitalize="none" autocorrect="off"   />
                            <label>Site Name</label>
                        </th>
                        <th width="55%">
                            <label>Site URL</label>
                        </th>
                        <th width="10%">  
                            <label>Role</label>
                        </th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    <?php submit_button(__('Save',$this->plugin_name), 'primary',$this->plugin_name.'_save_user', TRUE); ?>        
    </form>
</div>