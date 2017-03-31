<?php
/**
 * The template for displaying Meeting Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * created by: Bryan T bet6556@gmail.com
 */

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;
$delim = ',';

the_title( '', '' );
echo $delim;
$loc=get_field('location');
if( $loc ) {
  $loc=$loc["address"];
  echo( implode(' ',explode(',', $loc,2)) . $delim );
}
echo("\n");
