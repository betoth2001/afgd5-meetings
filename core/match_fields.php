<?php
function afgd5me_set_event_title_and_summary($post_id,&$event){
  //Create the content that fills the event description/summary box
  $post_fetch = get_post($post_id);
  $post_content = $post_fetch->post_content;
  $post_title = get_the_title($post_id);

  $info_str = $post_title;
  $desc_str = $post_content;
/*  $info = get_field_object('info',$post_id);
//  $i_val=$info['value'];
//			  if( $i_val ){
//			    foreach( $i_val as $item ){
//			      $info_str = $info_str.$item;
//			      if( $desc_str ){ $desc_str = $desc_str.", "; }
//			      $desc_str = $desc_str.$info['choices'][$item];
//			    }
//			    $desc_str.= "\n";
//			    $info_str.= " ";
//			  }
*/
  $info = get_field('info',$post_id);
  
  if( in_array('O',$info) ) {
    $info_str = "(OPEN) ".$info_str;
    $desc_str .= "\n\n".afgd5sh_open_meeting_footer_text();
  } else {
    //$info_str = "CLOSED ".$info_str;
    $desc_str .= "\n\n";
    if( is_alateen($post_id) ){
      $desc_str .= afgd5sh_closed_meeting_footer_text(true);
    }else {
      $desc_str .= afgd5sh_closed_meeting_footer_text(false);
    }
    //$desc_str .= "\n\nCLOSED: This meeting welcomes those who feel they have been affected by someone else’s drinking. If you are curious or wish to observe as a student or professional, we welcome you to attend one of our meetings designated as ‘open’.";
  }
  echo("getSummry".$event->getSummary());
  echo("info_str=".$info_str);
  //$event->setSummary(utf8_encode($info_str)); //Did not fix encoding of apostrophe
  $event->setSummary($info_str);
  //$event->setSummary(urlencode($info_str)); //Did not fix encoding of apostrophe
  echo("after set getSummry".$event->getSummary());
  print_r($event);
  $event->setDescription($desc_str);
  
}

///////////////////////////////////////////////////////////////////////////
function afgd5me_match_fields_Google_cal_event_obj($post_id,&$event) {
//setup the calendar button(s)
echo("<div>Start match_fields_Google_cal_event_obj</div>");
//var_dump($event);
  
  afgd5me_set_event_title_and_summary($post_id,$event);
  
  if( get_field('location',$post_id) ) {
    $event->setLocation( get_field('location',$post_id)['address'] );
    //var_dump(get_field('location'));
  }

  $event->setRecurrence(array(
        'RRULE:FREQ=WEEKLY;'
      )
  );
  $field = get_field_object('day',$post_id);
  //var_dump($field);
  $value = $field['value'];
  $label = '';
  if( is_array($value) ){
    if( sizeof($value) > 1 ) {
      die('Auto-update does not support multiple days per meeting');
    } elseif( sizeof($value) == 1 ){
      $value = array_pop($value);
    }
  }
  $currentDT = new DateTime( );
  $currentDT->setTimezone( new DateTimeZone( 'America/Detroit' ) );
  $startDT = new DateTime( );
  $startDT->setTimezone( new DateTimeZone( 'America/Detroit' ) );
  $startDT->modify( 'this '.$value.' '. get_field('start_time',$post_id));
  echo("\nafter DT using this".$startDT->format( 'Y-m-d H:i:s'));
#  $startDT->modify( get_field('start_time',$post_id) );
#  echo("\nafter set time start=".$startDT->format( 'Y-m-d H:i:s'));
  if( $startDT < $currentDT ) {
    $startDT->modify( 'next '.$value.' '.get_field('start_time',$post_id) );
    echo("\ninside start < current start=".$startDT->format( 'Y-m-d H:i:s'));
  }

  $google_start=new Google_Service_Calendar_EventDateTime();
  $google_start->setDateTime($startDT->format( 'c'));
  $google_start->setTimeZone('America/Detroit');
  //var_dump($google_start);
  $event->setStart($google_start);

  $value = get_field('end_time',$post_id);
  $endDT = clone($startDT);
  if( $value ) {
    $endDT->modify( $value );
    echo("\nfound end value set end=".$endDT->format( 'Y-m-d H:i:s'));
  } else {
    $endDT->modify( '+ 1 hour' );
    echo("\nadded hour end=".$endDT->format( 'Y-m-d H:i:s'));
  }
  if( $endDT < $startDT ) {
    $endDT->modify( 'next day');  //add a day when meeting end time goes past midnight
    echo("\nend < start end=".$startDT->format( 'Y-m-d H:i:s'));
  }
  $google_end = new Google_Service_Calendar_EventDateTime();
  $google_end->setDateTime($endDT->format( 'c'));
  $google_end->setTimeZone('America/Detroit');
  //var_dump($google_end);
  //die("tmp dead");
  $event->setEnd($google_end);
//var_dump($event);
  return;
}//end function afgd5me_match_fields_Google_cal_event_obj
///////////////////////////////////////////////////////////////////////////////

