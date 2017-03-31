<?php
/*
* Create an event in google calendar service
*/
function afgd5me_event_insert($service,$calendarId, $inEvent){
  $count=1;
  echo("\n Inside afgd5me_event_insert inEvent=");
  var_dump($inEvent);
  $event = afgd5me_event_insert_repeater($service,$calendarId, $inEvent);
  while( ($event === 1 )&&( $count < 5 )) {
    usleep(pow(2,$count));
    $count++;
    $event = afgd5me_event_insert_repeater($service,$calendarId, $inEvent);
  }
//   die("pre finish event_get");
  if( $event === 1 ) return false;
  return $event;
}

/*
* This function makes a request to Google server to create an event.
* It returns the event upon success or false if the error is recoverable.
* For a recoverable error, you may call this function again with some hope of sucess.
* If there is something wrong with request then an error is thrown.
*/
function afgd5me_event_insert_repeater($service,$calendarId, $inEvent){
  try {
    $event = $service->events->insert($calendarId, $inEvent);
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
    } elseif( 409 === $code ){ //This error is thrown on event creation if event with specified id already exists
      die( "You have tried to create an event in Google Calendar with an Id that already exists.");
    } elseif( $code === 500 ){ //An unexpected error occurred while processing the request.
      //Suggested action: Use exponential backoff.
      return 1;
    }
    //uncaught
    throw $e;
  }
  return $event;
}
