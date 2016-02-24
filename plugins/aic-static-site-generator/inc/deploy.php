<?php

add_action('admin_post_deploy_static_site', 'deploy_static_site');

function deploy_static_site() {
  $user = get_option('deployment_username');
  $password = get_option('deployment_password');
  $target = get_option('deployment_url');
  $static_output_dir = get_site_output_dir();

  if($target) {
    $cmd = 'rsync -avO --delete-after --chmod=ug=rwx '.$static_output_dir.' '.$target;
    exec($cmd, $cmd_output);
    // print $cmd;
    // print_r($cmd_output);
  }
}

?>
