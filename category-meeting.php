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

get_header();

if ( ! wp_script_is( 'atc-script', 'enqueued' ) ){
  wp_enqueue_script( 'atc-script' );
}
if ( ! wp_script_is( 'jquery-ui-tooltip', 'enqueued' ) ){
  wp_enqueue_script( 'jquery-ui-tooltip' );
}
 ?>

 <?php $args = array(
	'posts_per_page'   => 9999,
	'offset'           => 0,
	'category'         => '',
	'category_name'    => 'meeting',
	'orderby'          => 'date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'post',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'author'	   => '',
	'author_name'	   => '',
	'post_status'      => 'publish',
	'suppress_filters' => true
);
$posts_array = get_posts( $args );

if( $posts_array ){
  $num=0; ?>
  <?php do_action( 'generate_archive_title' ); ?>
  <p>Codes: 4 : 4th Step
A : Alateen
B : Babysitting
C : Closed Meeting
G : LGBTQ
H : Handicap Accessible
K : Adult Children
L : Literature Study
M : Men's Group
O : Open Meeting
P : Parent Group
S : Step Study
T : Tradition Study
W : Women's Group</p>
  <table class="afgd5_meetings_table">
    <tr>
      <th>#</th>
      <th>Day</th>
      <th>Time</th>
      <th>WSO</th>
      <th>Contact</th>
      <th>Name</th>
      <th>Location</th>
      <th>Code</th>
      <th>Updated</th>
      <!--<th>Description</th>-->
    </tr>
    <? global $post;
    //$post = get_post( 50686 ); //for debugging
    foreach( $posts_array as $post ){
      setup_postdata( $post ); ?>
      <tr><td style="text-align:right"><?php $num=$num+1; echo( $num ); ?></td>
        <? get_template_part( 'meeting', get_post_format() ); ?>
      </tr>
	  <? } ?>
  </table>

<? } else {
  get_template_part( 'no-results', 'archive' );
}?>

<?php
//do_action('generate_sidebars');
get_footer();
?>
<!--<script>
//  ( function() {
//    $( document ).tooltip();
//  } )(jQuery);
</script>-->

<?php
