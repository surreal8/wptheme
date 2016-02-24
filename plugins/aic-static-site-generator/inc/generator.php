<?php

add_action('admin_post_generate_static_site', 'generate_static_site');

function get_site_output_dir() {
  $site_url = get_site_url();
  $parsed_url = parse_url($site_url);
  $development_domain = $parsed_url['host'];
  $site_slug = preg_replace('/^\//', '', $parsed_url['path']);
  $site_output_dir = AIC_STATIC_SITE_GENERATOR_ROOT.'/output/'.$site_slug;
  return $site_output_dir;
}

function generate_static_site() {
  $site_url = get_site_url();
  $site_output_dir = get_site_output_dir();

  $wget_status = wget_all_pages($site_url, $site_output_dir);

  $file_paths = find_all_downloaded_files($site_output_dir);
  foreach($file_paths as $f) {
    clean_urls_in_file($site_url, $f);
  }
}

function wget_all_pages($site_url, $site_output_dir) {
  if(file_exists($site_output_dir)) {
    exec('rm -r '.$site_output_dir);
  }
  mkdir($site_output_dir, 0777, true);

  $cmd = '/usr/local/bin/wget -mpck --user-agent="" -e robots=off --no-parent --quiet --directory-prefix='.$site_output_dir.'/ '.$site_url.'/';
  $status = Null;
  $results = Null;
  exec($cmd, $results, $status);

  $parsed_url = parse_url($site_url);
  $development_domain = $parsed_url['host'];
  $wget_full_path = $site_output_dir.'/'.str_replace('http://', '', $site_url);
  exec('mv '.$wget_full_path.'/* '.$site_output_dir.'/');
  exec('rm -r '.$site_output_dir.'/'.$development_domain);

  return $status;
}

function find_all_downloaded_files($site_output_dir) {
  $cmd = 'find '.$site_output_dir.' -type f';
  $status = Null;
  $results = Null;
  exec($cmd, $results, $status);

  return $results;
}

function clean_urls_in_file($site_url, $filepath) {
  $contents = file_get_contents($filepath);
  $clean_contents = clean_urls($site_url, $contents);
  file_put_contents($filepath, $clean_contents);
}

function clean_urls($site_url, $contents) {
  # find and replace query string question mark in all urls within src and href
  $reg = '`((src|href)=("|\')([^(http)]|http://'.$site_url.')[^"\']*)\?([^"\']*("|\'))`';
  $contents = preg_replace($reg, '$1%3F$5', $contents);

  $parsed_url = parse_url($site_url);
  $development_domain = $parsed_url['host'];

  # remove all full path domain references
  $clean_contents = str_replace($site_url, '', $contents);

  # ADD ANY ADDITIONAL FIND / REPLACE FUNCTIONALITY HERE

  # FIX URLS IN SIMPLE LIGHTBOX PLUGIN JAVASCRIPT
  $lightbox_source = "http:\/\/".$development_domain;
  $clean_contents_lightbox = str_replace($lightbox_source, '', $clean_contents);


  return $clean_contents_lightbox;
}

?>
