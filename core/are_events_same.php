<?php
#function afgd5me_get_gcal_start($event){
#  $start = $event->start->dateTime;
#  if (empty($start)) {
#    $start = $event->start->date;
#  }
#  return $start;
#}
#function afgd5me_get_gcal_end($event){
#  $end = $event->end->dateTime;
#  if (empty($end)) {
#    $end = $event->end->date;
#  }
#  return $end;
#}
function afgd5me_get_gcal_dateTime($prop,$event){
  $dt = $event->{$prop}->dateTime;
  if (empty($dt)) {
    $dt = $event->{$prop}->date;
  }
  return $dt;
}
function afgd5me_are_events_same($eventA,$eventB){
  $startA = afgd5me_get_gcal_dateTime('start',$eventA);
  $startB = afgd5me_get_gcal_dateTime('start',$eventB);
  if( $startA !== $startB ){
    echo("\n Start Do not match");
    return false;
  }
  $endA = afgd5me_get_gcal_dateTime('end',$eventA);
  $endB = afgd5me_get_gcal_dateTime('end',$eventB);
  if( $endA !== $endB ){
    echo("\n Ends Do not match");
    return false;
  }
  echo("\n SummaryA=".$eventA->getSummary());
  echo("\n SummaryB=".$eventB->getSummary());
  if( $eventA->getSummary() !== $eventB->getSummary() ){
    echo("\n event summaries do not match");
    return false;
  }
  if( $eventA->getLocation() !== $eventB->getLocation() ){
    echo("\n event Locations do not match");
    return false;
  }
  echo("\n DescA=".$eventA->getDescription());
  echo("\n DescB=".$eventB->getDescription());
  if( $eventA->getDescription() !== $eventB->getDescription() ){
    echo("\n event Descriptions do not match");
    return false;
  }
  
  return true;
}
