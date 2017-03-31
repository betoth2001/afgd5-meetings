<?php
/**
 * These functions and filters are used for generating the meeting-list and meeting-map pages
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
$afgd5me_pages = array('meeting-list', 'meeting-map');

/**
 * This function finds the appropriate custom page template.  It allows theme to override plugin included template
 */
function afgd5_find_page_template( $page ){
  $templateFile = locate_template( array( 'page-'.$page.'.php' ) );
  if ( '' !== $templateFile ) {
    #die("afgd5_find_page_template locate_template had file");
    return $templateFile;
  }
  $templateFile = afgd5me_PATH .'templates/page-'.$page.'.php';
  #die( "tempFile=".$templateFile );
  if ( file_exists($templateFile) ) {
    return $templateFile;
  }
  #die("afgd5_find_page_template last return");
  return '';
}

/**
 * Load the meeting related custom templates when appropriate query arg is present
 */
add_filter( 'template_include', 'afgd5me_fetch_template', 99 );
function afgd5me_fetch_template( $template ) {
  global $wp;
  global $afgd5me_pages;
  //var_dump($wp->query_vars);
  //die("dump");
  foreach( $afgd5me_pages as $page){
    
    if( array_key_exists( 'afgd5me_'.$page, $wp->query_vars ) ){
      #die("Exists");
      if( $new = afgd5_find_page_template( $page ) ){
        #die("Exists");
        return $new;
      }
    }
  }
  return $template;
}

function afgd5me_remove_sidebars($layout){
  return 'no-sidebar';
}

/**
 * Direct the user to virtual pages
 * This could also be achieved with add_rewrite_endpoint
 */

add_action( 'init', 'afgd5me_init_internal' );
function afgd5me_init_internal(){ 
  global $afgd5me_pages;
  foreach( $afgd5me_pages as $page ){
    #echo( $page . '  index.php?afgd5me_'.$page.'=1');
    #die("dump");
    #add_rewrite_rule( 'meeting-map.*', 'index.php?afgd5me_api=1', 'top' );
    add_rewrite_rule( $page.'.*', 'index.php?afgd5me_'.$page.'=1', 'top' );
  }
  #flush_rewrite_rules();
  #die("flush");
}

/**
 * From: https://developer.wordpress.org/reference/hooks/query_vars/
 * Allows (publicly allowed) query vars to be added, removed, or changed prior to executing the query. Needed to allow custom rewrite rules using your own arguments to work, or any other custom query variables you want to be publicly available.
 */
add_filter( 'query_vars', 'afgd5me_query_vars' );
function afgd5me_query_vars( $query_vars ){
  global $afgd5me_pages;
  foreach( $afgd5me_pages as $page ){
    $query_vars[] = 'afgd5me_'.$page;
  }
  return $query_vars;
}

#add_action( 'parse_request', 'afgd5me_parse_request' );
#function afgd5me_parse_request( &$wp )
#{
#    if ( array_key_exists( 'afgd5me_api', $wp->query_vars ) ) {
#        #include afgd5_find_page_template( 'meeting-map' );
#        load_template( afgd5_find_page_template( 'meeting-map' ) );
#        exit();
#    }
#    return;
#}

