<?php
/*
Plugin Name: AFG D5 Meetings
Description: Adds direct manipulation of Google calendar from within wordpress posts with category meeting.
Version:     0.0.1
Author:      Bryan T
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Function Prefix: afgd5me_
*/

defined( 'ABSPATH' ) or die( 'AFGD5me No script please!' );
define('afgd5me_APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('afgd5me_CREDENTIALS_PATH', __DIR__.'/.credentials/calendar-php-quickstart.json');
define('afgd5me_CLIENT_SECRET_PATH', __DIR__.'/.credentials/client_secret.json');
define('afgd5me_GOOGLE_CALENDAR_ID','aeh0tpvg5mh2oe8v2o5a7ljvhg@group.calendar.google.com'); //in iframes
//define('afgd5me_GOOGLE_CALENDAR_ID','denqdgnfumoufkbaoamrk96bvg@group.calendar.google.com'); //AFGD5 Meetings
//define('afgd5me_GOOGLE_CALENDAR_ID','810sjuoae1njk1mstds0ivo408@group.calendar.google.com'); //test Meetings
require_once __DIR__.'/vendor/autoload.php';
#include_once('core/event_get.php');
#include_once('core/match_fields.php');
#include_once('core/are_events_same.php');
foreach (glob("core/*.php") as $filename) {
    require_once( $filename );
}



function afgd5me_render_info_codes(){
  $code_descriptions = [
    "4"=>"4th Step",
    "A" => "Alateen",
    "B" => "Babysitting",
    "C" => "Closed Meeting",
    "G" => "LGBTQ",
    "H" => "Handicap Accessible",
    "K" => "Al-Anon Adult Children",
    "L" => "Literature Study",
    "M" => "Men's Group",
    "O" => "Open Meeting",
    "P" => "Parent Group",
    "S" => "Step Study",
    "T" => "Tradition Study",
    "W" => "Women's Group",
    "Z" => "Al-Anon",
  ];
  echo('<div class="afgd5me_info_code_container">');
  foreach ( $code_descriptions as $key => $value){
    if( afgd5me_is_code_displayed($key) ){
      ?><div class="afgd5me_info_code" style="display:inline-block;width:210px;margin-left:5px;margin-right:5px;height:100%;vertical-align:top">
        <?php echo('<span style="width:12px;display:inline-block;text-align:left">'.$key.'</span><span style="display:inline-block;width:15px;text-align:center;">=</span>'.$value); ?>
      </div><?php
    }
  }
  echo("</div>");
}
function afgd5me_is_code_displayed($code){
  $allowed_codes=[
    'A','B','C','H','K','O','Z'
  ];
  if( in_array($code, $allowed_codes) ){
    return true;
  }
  return false;
}


function afgd5me_ltrim( $excerpt ) {
    return preg_replace( '~^(\s*(?:&nbsp;)?)*~i', '', $excerpt );
}
function afgd5me_rtrim( $excerpt ) {
    return preg_replace( '~(\s*(?:&nbsp;)?)*$~i', '', $excerpt );
}



function afgd5me_update_event($post_id = '', $event_id = ''){
  if( '' === $post_id ) die("in afgd5me_update_event $post_id missing");
  if( '' === $event_id ) die("in afgd5me_update_event $event_id missing");
  echo("<div>in afgd5me_update_event</div>");

  $client = afgd5me_getClient();
  echo("<div>got client</div>");
  $service = new Google_Service_Calendar($client);
  echo("<div>set service </div>");
  $calendarId = afgd5me_GOOGLE_CALENDAR_ID;
  echo("<div>calID=".$calendarId."</div>");
  echo("<div>yoevent_id=".$event_id."</div>");
  $event = afgd5me_event_get($service,$calendarId, $event_id);
  echo("<div><pre>dumpEvent:");
  var_dump($event);
  echo("</pre></div>");
  if( (! $event) || ($event->status == 'cancelled') ) {
    echo("<div>Inside update. Event does not exist in google</div>");
    //update_field('google_event_id','',$post_id);
    afgd5me_create_event($post_id);
    return;
  }
echo("<div>after results</div>");
echo("<div><pre>");
  print "Upcoming events:\n";
  $start = $event->start->dateTime;
  if (empty($start)) {
    $start = $event->start->date;
  }
  printf("%s (%s)\n", $event->getSummary(), $start);

  $revisedEvent = clone $event;
  afgd5me_match_fields_Google_cal_event_obj($post_id,$revisedEvent);
  echo("<div>Before updating getId()=".$event->getId()." and event->id=".$event->id."</div>");

  //$client->setUseBatch(true); //for debug

  if(afgd5me_are_events_same($revisedEvent,$event)){
    //Do nothing
    die("\nevents are the same");
  } else {

    //create new recurring event for old event data so that people who set notifications for a meeting will keep their setting.
    $start = new DateTime(afgd5me_get_gcal_dateTime('start',$revisedEvent));
    //echo("\n start=".$start);
    echo("\n start=".$start->format('Ymd\THis\Z'));

    $event->setRecurrence(array('RRULE:FREQ=WEEKLY;UNTIL='.$start->format('Ymd\THis\Z') ) );
    $event->setId('');
    $event->setICalUID('');
    //var_dump($event->id);
    var_dump($event);
    try {
      $oldevent = $service->events->insert($calendarId, $event);
    } catch (Exception $e) {
      $code = $e->getCode();
  //     var_dump($code);
      $msg_obj = $e->getMessage();

      echo("<div><pre>");
      echo("msg=".$e->getMessage() ."\n");
      echo("code=".$e->getCode() ."\n");
      echo("file=".$e->getFile() ."\n");
      echo("line=".$e->getLine() ."\n");
      echo("</pre></div>");
    }
  
    echo("\n Success");
    var_dump($oldevent->id);
    die("\nupdate event needed");
    
  
    $updatedEvent = $service->events->update($calendarId, $event->getId(), $event);
  }

//debug stuff below
//    var_dump($updatedEvent);
//    die('yup');

  $event= $updatedEvent;
  $start = $event->start->dateTime;
      if (empty($start)) {
        $start = $event->start->date;
      }
      printf("%s (%s)\n", $event->getSummary(), $start);

  var_dump($updatedEvent->id);
  //update_field('google_event_id', $event->id, $post_id);
  //echo("<div>called update_field</div>");
  the_field('google_event_id',$post_id);
echo(" </pre></div>");
}//end function afgd5me_update_event

function is_alateen($post_id){
  //returns true if alateen designated in info options or false otherwise
  $info = get_field('info',$post_id);
  if( in_array( 'A', $info ) ){
    return true;
  }
  return false;
}



function afgd5me_google_cal_update($post_id){
/**
 * Save meeting to google calendar when a post is saved.
 *
 */
  echo("<div>Testinggggggggggggggggggggggggggggggg</div>");
  $post_id = afgd5me_grab_post_id($post_id);
  var_dump($post_id);
  echo("<div>post_id=".$post_id."</div>");
  if( false !== $post_id ){
    if( in_category( 'meeting', $post_id ) ){
      echo("<div>in_category( 'meeting', $post_id ) </div>");
      /*$obj = get_field_object('google_event_id',$post_id);
      echo("<div><pre>");
      var_dump($obj);
      echo("</pre></div>");
      echo("<div>pre get_field post_id=".$post_id."</div>");
      $event_id = get_field('google_event_id',$post_id);
      var_dump($event_id);
      echo("<div>post get_field post_id=".$post_id."</div>");*/
      $event_id = get_post_meta($post_id,'google_event_id',true);
      echo("<div>get_post_meta post_id=".$post_id."</div>");
      var_dump($event_id);
      echo("<div>post get_post_meta post_id=".$post_id."</div>");
      if( $event_id == 'lock' ) {
        echo("<div>locked</div>");
        return;
      }
      if( $event_id ){
        echo("<div>updating google_event </div>");
        afgd5me_update_event($post_id, $event_id);
        //afgd5me_create_event($post_id);
      } else {
        echo("<div>creating 'google_event_id' </div>");
        afgd5me_create_event($post_id);
      }
    }
  }
}
add_action('save_post', 'afgd5me_google_cal_update', 999, 1);
add_action('untrashed_post', 'afgd5me_google_cal_update', 999, 1);
//add_action('in_admin_footer','afgd5me_google_cal_update', 999, 1);

//add_action('admin_footer','afgd5me_google_cal_update', 999, 3);

function afgd5me_grab_post_id($post_id){

  $post_id=trim($post_id);
  if( '' === $post_id ){
    $post_id = trim($_GET["post"]);
  }
  if( '' === $post_id ) {
    echo("<div>if grab_post_id post_id=".$post_id."</div>");
    die( "Could not locate post_id" ); // for debug
    return false;
  }
  return $post_id;
}

add_action( 'before_delete_post', 'afgd5me_google_cal_delete' );
add_action( 'trashed_post', 'afgd5me_google_cal_delete' );

function afgd5me_google_cal_delete( $post_id ){
  $post_id = afgd5me_grab_post_id($post_id);
  var_dump($post_id);
  echo("<div>post_id=".$post_id."</div>");
  if( false === $post_id ) return;
  if( ! in_category( 'meeting', $post_id ) ) return;
  //$event_id = get_field('google_event_id',$post_id);
  $event_id = get_post_meta($post_id,'google_event_id',true);
  if( ! $event_id ) return;
  //echo("<div>in update event</div>");
  $client = afgd5me_getClient();
  //echo("<div>got client</div>");
  $service = new Google_Service_Calendar($client);
  //echo("<div>set service </div>");
  $calendarId = GOOGLE_CALENDAR_ID;
  //echo("<div>calID=".$calendarId."</div>");
  try {
    $event = $service->events->get($calendarId, $event_id);
  } catch (Exception $e) {
    $event=false;
  }
  if( ! $event->id ) return;
  $service->events->delete($calendarId, $event->id);
  update_post_meta( $post_id, 'google_event_id', '');
  //update_field('google_event_id','',$post_id);
  return;
}




function afgd5me_event_list_repeater($service,$calendarId,$optParams){
  try {
    echo( "<pre>calID=".$calendarId."\n</pre>" );
    $results = $service->events->listEvents($calendarId, $optParams);
  } catch (Exception $e) {
    $code = $e->getCode();
    var_dump($code);
    $msg_obj = $e->getMessage();
    var_dump($msg_obj);

    echo("<div><pre>");
    echo("msg=".$e->getMessage() ."\n");
    echo("code=".$e->getCode() ."\n");
    echo("file=".$e->getFile() ."\n");
    echo("line=".$e->getLine() ."\n");
    echo("</pre></div>");

    if( $code === 400 ){ //Bad Request
      //User error. This can mean that a required field or parameter has not been provided, the value supplied is invalid, or the combination of provided fields is invalid.
      die("Something went wrong with your Google Calendar request. Please contact the website administrator.");
    } elseif ( $code === 401 ){ //Invalid Credentials
      //Invalid authorization header. The access token you're using is either expired or invalid.
      die("Please inform the website administrator to reset the credentials file.");
    } elseif ( $code === 403 ){
      $message=$msg_obj->error->message;
      if( "Daily Limit Exceeded" === $message ){ //The Courtesy API limit for your project has been reached.
      } elseif( "User Rate Limit Exceeded" === $message ){ //The per-user limit from the Developer Console has been reached.
      } elseif( "Rate Limit Exceeded" === $message ){//The user has reached Google Calendar API's maximum request rate per calendar or per authenticated user.
      }elseif( "Calendar usage limits exceeded." === $message ){ //The user reached one of the Google Calendar limits in place to protect Google users and infrastructure from abusive behavior.
      }
      die("Google API rate limit exceeded with message:".$message);
    } elseif ( $code === 404 ){
      //The specified resource was not found. This can happen in several cases. Here are some examples:
        //when the requested resource (with the provided ID) has never existed
        //when accessing a calendar that the user can not access
      return false;
    } elseif( $code === 500 ){ //An unexpected error occurred while processing the request.
      //Suggested action: Use exponential backoff.
      return 1;
    }
    //uncaught
    throw $e;
  }
  return $results;
}
function afgd5me_event_list($service,$calendarId, $optParams){
  $count=1;
  $event = afgd5me_event_list_repeater($service,$calendarId, $optParams);
  while( ($event === 1 )&&( $count < 5 )) {
    usleep(pow(2,$count));
    $count++;
    $results = afgd5me_event_list_repeater($service,$calendarId, $optParams);
  }
  //die("pre finish afgd5me_event_list");
  if( $results === 1 ) return false;
  return $event;
}





//add_action('in_admin_footer','afgd5me_clear_calendar');
function afgd5me_clear_calendar(){
  delete_post_meta_by_key( 'google_event_id' );
  $client = afgd5me_getClient();
  //echo("<div>got client</div>");
  $service = new Google_Service_Calendar($client);
  $calendarId = afgd5me_GOOGLE_CALENDAR_ID;
  $optParams = array(
    'maxResults' => 90,
    'orderBy' => 'startTime',
    'singleEvents' => TRUE,
    'timeMin' => date('c',0),
  );

echo("\nbefore results\n");
  $results = afgd5me_event_list($service,$calendarId, $optParams);
//$results = $service->events->listEvents($calendarId, $optParams);
echo("\nafter results\n");
echo("<div><pre>");
echo("numItems=".count($results->getItems()));
  $deletedRecurringIds=[];
  if (count($results->getItems()) == 0) {
    print "No upcoming events found.\n";
  } else {
    print "Upcoming events:\n";
    foreach ($results->getItems() as $event) {
      $start = $event->start->dateTime;
      if (empty($start)) {
        $start = $event->start->date;
      }
      printf("%s (%s)\n", $event->getSummary(), $start);
      echo($event->recurringEventId ."\n");
      //var_dump($event);
      if( $event->recurringEventId ){
        //$service->events->delete($calendarId,$event->recurringEventId);
        echo("in_array=".in_array($event->recurringEventId,$deletedRecurringIds)."\n");
        if( ! in_array($event->recurringEventId,$deletedRecurringIds) ){
          array_push($deletedRecurringIds,$event->recurringEventId);
          afgd5me_event_delete($service,$calendarId, $event->recurringEventId);
        }
      }else{
        //$service->events->delete($calendarId, $event->id);
        afgd5me_event_delete($service,$calendarId, $event->id);
      }

      echo("deleted");
    }
  }
  //clear all post meta
echo("</pre></div>");
}



function afgd5me_event_delete_repeater($service,$calendarId, $event_id){
  try {
    $service->events->delete($calendarId, $event_id);
  } catch (Exception $e) {
    $code = $e->getCode();
//    var_dump($code);
    $msg_obj = $e->getMessage();
    var_dump($msg_obj);

    echo("<div><pre>");
    echo("msg=".$e->getMessage() ."\n");
    echo("code=".$e->getCode() ."\n");
    echo("file=".$e->getFile() ."\n");
    echo("line=".$e->getLine() ."\n");
    echo("</pre></div>");

    if( $code === 400 ){ //Bad Request
      //User error. This can mean that a required field or parameter has not been provided, the value supplied is invalid, or the combination of provided fields is invalid.
      die("Something went wrong with your Google Calendar request. Please contact the website administrator.");
    } elseif ( $code === 401 ){ //Invalid Credentials
      //Invalid authorization header. The access token you're using is either expired or invalid.
      die("Please inform the website administrator to reset the credentials file.");
    } elseif ( $code === 403 ){
      $message=$msg_obj["error"]["message"];
      if( "Daily Limit Exceeded" === $message ){ //The Courtesy API limit for your project has been reached.
      } elseif( "User Rate Limit Exceeded" === $message ){ //The per-user limit from the Developer Console has been reached.
      } elseif( "Rate Limit Exceeded" === $message ){//The user has reached Google Calendar API's maximum request rate per calendar or per authenticated user.
      }elseif( "Calendar usage limits exceeded." === $message ){ //The user reached one of the Google Calendar limits in place to protect Google users and infrastructure from abusive behavior.
      }
      die("Google API rate limit exceeded with message:".$message);
    } elseif ( $code === 404 ){
      //The specified resource was not found. This can happen in several cases. Here are some examples:
        //when the requested resource (with the provided ID) has never existed
        //when accessing a calendar that the user can not access
      return false;
    } elseif( $code === 410 ){ //Gone
      return true;
    } elseif( $code === 500 ){ //An unexpected error occurred while processing the request.
      //Suggested action: Use exponential backoff.
      return 1;
    }
    //uncaught
    throw $e;
  }
  return true;
}
function afgd5me_event_delete($service,$calendarId, $event_id){
  $count=1;
  $event = afgd5me_event_delete_repeater($service,$calendarId, $event_id);
  while( ($event === 1 )&&( $count < 5 )) {
    usleep(pow(2,$count));
    $count++;
    $event = afgd5me_event_delete_repeater($service,$calendarId, $event_id);
  }
//   die("pre finish event_get");
  if( $event === 1 ) return false;
  return $event;
}


function afgd5me_install() {
    // Clear the permalinks
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'afgd5me_install' );

function afgd5me_deactivation() {
    // Clear the permalinks
    delete_post_meta_by_key( 'google_event_id' );
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'afgd5me_deactivation' );

