<?php
function afgd5me_create_event($post_id){
  echo("<div>in create event</div>");
  //die("DEBUG:Premature exit afgd5me_create_event");
  update_post_meta( $post_id, 'google_event_id', 'lock');
  //update_field('google_event_id', 'lock', $post_id);
  $client = afgd5me_getClient();
  echo("<div>got client</div>");
  $service = new Google_Service_Calendar($client);
  echo("<div>set service </div>");
  $calendarId = afgd5me_GOOGLE_CALENDAR_ID;

//// create event
echo("<div>Creating event</div>");
  $event = new Google_Service_Calendar_Event(
    array(
      'summary' => 'Ignore Test Event',
      'location' => 'Ann Arbor, MI',
      'description' => 'Test event. Please ignore.',
      'start' => array(
        'dateTime' => '2017-05-28T09:00:00-07:00',
        'timeZone' => 'America/Los_Angeles',
      ),
      'end' => array(
        'dateTime' => '2017-05-28T10:00:00-07:00',
        'timeZone' => 'America/Los_Angeles',
      ),
      'recurrence' => array(
        'RRULE:FREQ=WEEKLY;'
      ),
      'reminders' => array(
        'useDefault' => TRUE,
      ),
    )
  );
  afgd5me_match_fields_Google_cal_event_obj($post_id,$event);
  $upevent = $service->events->insert($calendarId, $event);
  var_dump($upevent->id);
  update_post_meta( $post_id, 'google_event_id', $upevent->id);
  //update_field('google_event_id', $upevent->id, $post_id);
  echo("<div>called update_post_meta</div>");
  the_field('google_event_id',$post_id);
echo(" </pre></div>");
}//end function afgd5me_create_event

