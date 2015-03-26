<?php
/**
 * @package wp-json-rest-api-meta-query
 * @version 1.0
 */
/*
  Plugin Name: WP JSON REST API Meta Queries
  Plugin URI: TBA
  Description: This plugin adds support for full meta-queries using the WP JSON REST API
  Author: Hugo Leandersson
  Version: 1.0
  Author URI: TBA
*/


//add support for meta data queries when not authenticated
function addMetaQuerySearch() {
  global $wp;

  // public for now...
  array_push($wp->public_query_vars, 'meta_key');
  array_push($wp->public_query_vars, 'meta_value');
  array_push($wp->public_query_vars, 'meta_query');
  array_push($wp->public_query_vars, 'meta_compare');

}

add_action("init", "addMetaQuerySearch");


function addMetaQuerySupport($data){
  $args = array();
  $args['relation'] = 'AND';

  foreach ($data as $key=>$value) {
    $value = urldecode($value);

      if ( 'relation' === $key ) {
          $args['relation'] = $data['relation'];
      }
      if (  substr($key, 0, 3) === 'key' ) {
          $arg_num = substr($key, 3);
          $args[(int)$arg_num]['key'] = $value;
      }
      if (  substr($key, 0, 5) === 'value' ) {
          $arg_num = substr($key, 5);
          $args[(int)$arg_num]['value'] = json_decode($value);
      }
      if (  substr($key, 0, 4) === 'type' ) {
          $arg_num = substr($key, 4);
          $args[(int)$arg_num]['type'] = $value;
      }
      if (  substr($key, 0, 7) === 'compare' ) {
          $arg_num_comp = substr($key, 7);
          $args[(int)$arg_num_comp]['compare'] = $value;
      }
  }
  return $args;
}

add_filter('json_query_var-meta_query', 'addMetaQuerySupport', 10, 1);


