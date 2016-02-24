<?php

if(is_admin()) {
  add_action('admin_menu', 'aic_ssg_plugin_menu');
  add_action('admin_init', 'aic_ssg_register_settings');
} else {
  // do nothing
}

function static_site_preview_url() {
  $site_url = get_site_url();
  $parsed_url = parse_url($site_url);
  $preview_url = plugins_url('../output'.$parsed_url['path'], __FILE__);
  return $preview_url;
}

function aic_ssg_plugin_menu() {
  add_options_page('AIC Static Site Generator Options', 'AIC Static Site Generator', 'manage_options', 'aic-ssg', 'aic_ssg_options');
}

function aic_ssg_register_settings() {
  add_option('deployment_username', '');
  add_option('deployment_password', '');
  add_option('deployment_url', '');
  register_setting('default', 'deployment_username');
  register_setting('default', 'deployment_password');
  register_setting('default', 'deployment_url');
}

function aic_ssg_options() {
  if(!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }

  echo '<div class="wrap">';
    echo '<h2>AIC Static Site Generator</h2>';

    echo '<form method="post" action="admin-post.php">';
      echo '<input type="hidden" name="action" value="generate_static_site" />';
      submit_button('Generate Static Site');
    echo '</form>';

    echo 'Preview: <a href="'.static_site_preview_url().'" target="_blank">'.static_site_preview_url().'</a>';

    echo '<p>&nbsp;</p>';

    echo '<form method="post" action="options.php">';
      settings_fields('default');

      echo '<label for="deployment_username">Deployment Username</label><br />';
      echo '<input type="text" id="deployment_username" name="deployment_username" value="'.get_option('deployment_username').'"/><br />';

      echo '<label for="deployment_password">Deployment Password</label><br />';
      echo '<input type="password" id="deployment_password" name="deployment_password" value="'.get_option('deployment_password').'"/><br />';

      echo '<label for="deployment_url">Deployment URL</label><br />';
      echo '<input type="text" id="deployment_url" name="deployment_url" value="'.get_option('deployment_url').'" style="width:100%;" />';

      submit_button('Save Deployment Settings');
    echo '</form>';

    echo '<form method="post" action="admin-post.php">';
      echo '<input type="hidden" name="action" value="deploy_static_site" />';
      submit_button('Deploy Static Site');
    echo '</form>';

  echo '</div>';
}

?>
