<?php
/**
 * The template for displaying Meeting Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * created by: Bryan T bet6556@gmail.com
 */

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
  echo("<div><pre>Inside no direct access</pre></div>");
  die("<div><pre>Inside no direct access</pre></div>");
  exit;
}
add_filter( 'generate_sidebar_layout','afgd5me_remove_sidebars'); #take off sidebar before calling header
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
?>
<div id="primary" <?php generate_content_class();?>>
		<main id="main" <?php generate_main_class(); ?>>
			<?php do_action('generate_before_main_content'); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php generate_article_schema( 'CreativeWork' ); ?>>
	<div class="inside-article">
		<?php do_action( 'generate_before_content'); ?>
  <?php if ( generate_show_title() ) : ?>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title" itemprop="headline">', '</h1>' ); ?>
			</header><!-- .entry-header -->
		<?php endif; ?>
    <?php do_action( 'generate_after_entry_header'); ?>
		<div class="entry-content" itemprop="text">
<?php
if( $posts_array ){
  $num=0; ?>
  <?php //do_action( 'generate_archive_title' ); ?>
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
      <th>Pid</th>
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
    <?php global $post;
    //$post = get_post( 50686 ); //for debugging
    foreach( $posts_array as $post ){
      setup_postdata( $post ); ?>
      <tr><td style="text-align:right"><?php $num=$num+1; echo( $num ); ?></td>
        <td style="text-align:right"><?php echo( $post->ID ); ?></td>
        <?php require(afgd5me_PATH.'templates/meeting.php' ); ?>
      </tr>
	  <?php } ?>
  </table>

<?php } else {
  get_template_part( 'no-results', 'archive' );
}?>
  </div><!-- .entry-content -->
		<?php do_action( 'generate_after_content'); ?>
	</div><!-- .inside-article -->
</article><!-- #post-## -->
<?php do_action('generate_after_main_content'); ?>
		</main><!-- #main -->
	</div><!-- #primary -->
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
