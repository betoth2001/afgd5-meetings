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
add_filter( 'generate_sidebar_layout','afgd5me_remove_sidebars'); #take off sidebar before calling header
get_header();

if ( ! wp_script_is( 'atc-script', 'enqueued' ) ){
  wp_enqueue_script( 'atc-script' );
}
if ( ! wp_script_is( 'jquery-ui-tooltip', 'enqueued' ) ){
  wp_enqueue_script( 'jquery-ui-tooltip' );
}
?>
<style type="text/css">

.acf-map {
	width: 100%;
	height: 400px;
	border: #ccc solid 1px;
	margin: 20px 0;
}

/* fixes potential theme css conflict */
.acf-map img {
   max-width: inherit !important;
}

</style>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo(my_google_map_key()); ?>"></script>
<script type="text/javascript">
(function($) {

/*
*  new_map
*
*  This function will render a Google Map onto the selected jQuery element
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$el (jQuery element)
*  @return	n/a
*/

function new_map( $el ) {

	// var
	var $markers = $el.find('.marker');


	// vars
	var args = {
		zoom		: 16,
		center		: new google.maps.LatLng(0, 0),
		mapTypeId	: google.maps.MapTypeId.ROADMAP
	};


	// create map
	var map = new google.maps.Map( $el[0], args);


	// add a markers reference
	map.markers = [];


	// add markers
	$markers.each(function(){

    	add_marker( $(this), map );

	});


	// center map
	center_map( map );


	// return
	return map;

}

/*
*  add_marker
*
*  This function will add a marker to the selected Google Map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$marker (jQuery element)
*  @param	map (Google Map object)
*  @return	n/a
*/

function add_marker( $marker, map ) {

	// var
	var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

	// create marker
	var marker = new google.maps.Marker({
		position	: latlng,
		map			: map
	});

	// add to array
	map.markers.push( marker );

	// if marker contains HTML, add it to an infoWindow
	if( $marker.html() )
	{
		// create info window
		var infowindow = new google.maps.InfoWindow({
			content		: $marker.html()
		});

		// show info window when marker is clicked
		google.maps.event.addListener(marker, 'click', function() {

			infowindow.open( map, marker );

		});
	}

}

/*
*  center_map
*
*  This function will center the map, showing all markers attached to this map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	map (Google Map object)
*  @return	n/a
*/

function center_map( map ) {

	// vars
	var bounds = new google.maps.LatLngBounds();

	// loop through all markers and create bounds
	$.each( map.markers, function( i, marker ){

		var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

		bounds.extend( latlng );

	});

	// only 1 marker?
	if( map.markers.length == 1 )
	{
		// set center of map
	    map.setCenter( bounds.getCenter() );
	    map.setZoom( 16 );
	}
	else
	{
		// fit to bounds
		map.fitBounds( bounds );
	}

}

/*
*  document ready
*
*  This function will render each map when the document is ready (page has loaded)
*
*  @type	function
*  @date	8/11/2013
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/
// global var
var map = null;

$(document).ready(function(){

	$('.acf-map').each(function(){

		// create map
		map = new_map( $(this) );

	});

});

})(jQuery);
</script>


<?php
$args = array(
	'posts_per_page'   => 9999,
	'offset'           => 0,
	'category'         => '',
	'category_name'    => 'meeting',
	'orderby'          => 'meta_value',
	'order'            => 'ASC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => 'location',
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
				<h1 class="entry-title" itemprop="headline">Meeting Map</h1>
			</header><!-- .entry-header -->
		<?php endif; ?>
    <?php do_action( 'generate_after_entry_header'); ?>
		<div class="entry-content" itemprop="text">
      <a href="<?php admin_url( 'admin-post.php?action=print.csv' ); ?>"> save csv </a>
<?php
    function afgd5_meeting_marker_html(){
      //uses global $post setup
      $field = get_field_object('day');
      //var_dump($field);
      $value = $field['value'];
      $label = '';
      if( is_array($value) ){
        foreach ( $value as $val ){
          if( $label != '' ) $label.="<br/>" ;
          $label .= $field['choices'][ (string)$val ];
        }
      } else {
        $label = $field['choices'][ (string)$value ];
      }
      $label .= ' '.date( 'H:i',strtotime( get_field('start_time')) );
      $label .= ' '.get_the_title();
      return $label;
    }
    if( $posts_array ){
      $locs = [];
      $descriptions = [];
      global $post;
    //$post = get_post( 50686 ); //for debugging
      foreach( $posts_array as $post ){
        setup_postdata( $post );
        $location = get_field('location',$post->ID);
        $key = array_search($location,$locs);
        if( false !== $key ){
          //$descriptions[$key][] = "<br/>".afgd5_meeting_marker_html();
          $descriptions[$key][] = afgd5_meeting_marker_html();
        } else {
          $locs[] = $location;
          $descriptions[] = [afgd5_meeting_marker_html()];
        }
      }
      foreach($descriptions as $key => $desc ){
        
      }
      //print_r($locs);
      //print_r($descriptions);


  $num=0; ?>
  <div><em>Information Codes Legend</em>   
  <?php //do_action( 'generate_archive_title' ); 
    afgd5me_render_info_codes();
  ?>
  </div>
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

<!-- stripped from google calendar inserted locations
<div style="display: inline-block; overflow: auto; max-height: 309px; max-width: 654px;">
  <div dir="ltr" jstcache="0">
    <div jstcache="33" class="poi-info-window gm-style">
      <div jstcache="2">
        <div jstcache="3" class="title full-width" jsan="7.title,7.full-width">Washtenaw County Corrections
        </div>
        <div class="address"> <div jstcache="4" jsinstance="0" class="address-line full-width" jsan="7.address-line,7.full-width">4101 Washtenaw Ave
        </div>
        <div jstcache="4" jsinstance="*1" class="address-line full-width" jsan="7.address-line,7.full-width">Ann Arbor, MI 48108
        </div>
      </div> 
    </div> 
    <div jstcache="5" style="display:none">
    </div>
    <div class="view-link"> <a target="_blank" jstcache="6" href="https://maps.google.com/maps?ll=42.254308,-83.677485&amp;z=18&amp;t=m&amp;hl=en-US&amp;gl=US&amp;mapclient=apiv3&amp;cid=10177715760379839244"> <span> View on Google Maps </span> </a> 
    </div> 
  </div>
</div>
</div>
-->

  <div class="acf-map">
    <?php
    for( $key=0; $key < sizeof($locs); $key++ ){
      $location = $locs[$key];
      ?>
      <div class="marker" data-lat="<?php echo( $location['lat'] ); ?>" data-lng="<?php echo( $location['lng']); ?>">
<!--
<div style="display: inline-block; overflow: auto; max-height: 309px; max-width: 654px;">
  <div dir="ltr" jstcache="0">
    <div jstcache="33" class="poi-info-window gm-style"> 
      <div jstcache="2"> 
        <div jstcache="3" class="title full-width" jsan="7.title,7.full-width"><?php $locname=explode(',', $location['address'],2)[0]; echo($locname); ?>
        </div> 
      <div class="address"> 
        <div jstcache="4" jsinstance="0" class="address-line full-width" jsan="7.address-line,7.full-width"><?php echo(explode(',', $location['address'],3)[1]); ?>
        </div>
        <div jstcache="4" jsinstance="*1" class="address-line full-width" jsan="7.address-line,7.full-width"><?php echo(explode(',', $location['address'],3)[2]); ?>
        </div> 
      </div> 
    </div> 
  </div>
</div>
-->



<!--<div style="top: 9px; position: absolute; left: 15px; width: 203px;" class="gm-style-iw">-->
  <div style="display: inline-block; overflow: auto; max-height: 308px; max-width: 654px;">
    <div dir="ltr" style="" jstcache="0">
      <div jstcache="33" class="poi-info-window gm-style">
        <div jstcache="2">
          <div jstcache="3" class="title full-width" jsan="7.title,7.full-width">United Memorial Gardens
          </div>
          <div class="address">
            <div jstcache="4" jsinstance="0" class="address-line full-width" jsan="7.address-line,7.full-width">4800 Curtis Rd
            </div>
            <div jstcache="4" jsinstance="*1" class="address-line full-width" jsan="7.address-line,7.full-width">Plymouth, MI 48170
            </div>
          </div>
        </div>
        <div jstcache="5" style="display:none">
        </div>
        <div class="view-link"> <a target="_blank" jstcache="6" href="https://maps.google.com/maps?ll=42.343724,-83.616391&amp;z=14&amp;t=m&amp;hl=en-US&amp;gl=US&amp;mapclient=apiv3&amp;cid=8072377034713671238"> <span> View on Google Maps </span> </a>
        </div>
      </div>
    </div>
  </div>
  <div style="border-top: 1px solid rgb(204, 204, 204); margin-top: 9px; padding: 6px; visibility: hidden; font-size: 13px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; font-family: Roboto,Arial; display: none;"><a href="" target="_blank" style="cursor: pointer; color: rgb(66, 127, 237); text-decoration: none;">View on Google Maps</a>
  </div>
<!--</div>-->




<!--
        <h4><?php $locname=explode(',', $location['address'],2)[0]; echo($locname); ?></h4>
				<p class="address"><?php echo( preg_replace( '/'.$locname.'\w*,\w*/', '', $location['address']) ); ?></p>
				<h5>Meetings<h5>
        <p><?php echo( implode('<br/>',$descriptions[$key]) ); ?></p>
        -->
			</div>
	  <?php } ?>
  </div>

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
