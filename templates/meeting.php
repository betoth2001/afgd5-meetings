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

?>
    <td><?php
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
        echo( $label );
        ?>
    </td>
		<td><?php echo( date( 'H:i',strtotime( get_field('start_time')) ) ); ?>
		</td>
		<td><?php
		  $link=get_edit_post_link( get_the_ID(),'' );
		  if( $link ){ ?>
		    <a href="<?php echo( $link ); ?>"><?php echo( get_field('wso_number') ); ?></a>
		  <?php 
		  } else {
  		  the_field('wso_number');
  		}?>
		</td>
		<td><?php
		  if( get_field('group_representative') ){
		    echo( "GR  - ".get_field('group_representative') );
		    if( get_field('cma') ){
		      echo( "<br/>CMA- ".get_field('cma') );
		    }
		  } else {
		  	if( get_field('cma') ){
		      echo( "CMA- ".get_field('cma') );
		    }
		  }
		  ?>
		</td>
		<td><?php the_title( '', '' ); ?>
		</td>
		<td><?php $loc=get_field('location');
		  if( $loc ) {
		    $loc=$loc["address"];
  		  echo( explode(',', $loc,2)[0] );
  		}
		  ?>
		</td>
		<td><?php
			  $info = get_field_object('info');
			  $i_val=$info['value'];
			  $info_str = '';
			  if( $i_val ){
			    foreach( $i_val as $item ){
			      if( afgd5me_is_code_displayed($item) ){
			        $info_str = $info_str.$item;
			      }
			    }
    		  echo( $info_str );
			  }
		  ?>
		</td>
		<td><?php the_modified_time( 'Y-m-d' );
        //setup the calendar button(s)
			  $info = get_field_object('info');
			  $i_val=$info['value'];
			  $info_str = '';
			  $desc_str = '';
//			  if( $i_val ){
//			    foreach( $i_val as $item ){
//			      $info_str = $info_str.$item;
//			      if( $desc_str ){ $desc_str = $desc_str.", "; }
//			      $desc_str = $desc_str.$info['choices'][$item];
//			    }
//			    $desc_str.= "\n";
//			    $info_str.= " ";
//			  }
        $addtocal_atts['title'] = $info_str.get_the_title();
        $addtocal_atts['description'] = $desc_str.get_the_content();
        $info = get_field('info');
        if( in_array('O',$info) ) {
          $addtocal_atts['title'] = "OPEN ".$addtocal_atts['title'];
          $addtocal_atts['description'] .= "\n\nOPEN: This meeting welcomes everyone including those who are curious or wish to observe as a student or professional.";
        } else {
          $addtocal_atts['title'] = "CLOSED ".$addtocal_atts['title'];
          $addtocal_atts['description'] .= "\n\nCLOSED: This meeting welcomes those who feel they have been affected by someone else’s drinking. If you are curious or wish to observe as a student or professional, we welcome you to attend one of our meetings designated as ‘open’.";
        }

        if( get_field('location') ) {
          $addtocal_atts['location'] = get_field('location')['address'];
          //var_dump(get_field('location'));
        }
        $addtocal_atts['width'] = '20';

	      $field = get_field_object('day');
		    //var_dump($field);
        $value = $field['value'];
        $label = '';
        if( is_array($value) ){
          foreach ( $value as $val ){
            $startDT = new DateTime( );
    			  $startDT->modify( 'next '.$val );
		    	  $startDT->modify( get_field('start_time') );
            //$addtocal_atts['start'] = date( 'Y-m-d H:i:s',strtotime( get_field('start_time') ) );
            $addtocal_atts['start'] = $startDT->format( 'Y-m-d H:i:s');
            $value = get_field('end_time');
            if( $value ) {
              $endDT = clone($startDT);
              $endDT->modify( $value );
              $addtocal_atts['end'] = $endDT->format( 'Y-m-d H:i:s');

              //$addtocal_atts['end'] = date( 'Y-m-d H:i:s',strtotime($value) );
            }

            echo(afgd5sh_addtocalbutton($addtocal_atts, null, ''));
          }
        } else {
   			  $startDT = new DateTime( );
			    $startDT->modify( 'next '.get_field('day') );
			    $startDT->modify( get_field('start_time') );
          //$addtocal_atts['start'] = date( 'Y-m-d H:i:s',strtotime( get_field('start_time') ) );
          $addtocal_atts['start'] = $startDT->format( 'Y-m-d H:i:s');
          $value = get_field('end_time');
          if( $value ) {
            $endDT = clone($startDT);
            $endDT->modify( $value );
            $addtocal_atts['end'] = $endDT->format( 'Y-m-d H:i:s');

            //$addtocal_atts['end'] = date( 'Y-m-d H:i:s',strtotime($value) );
          }

          echo(afgd5sh_addtocalbutton($addtocal_atts, null, ''));
        }

		  ?>
		</td>
		<!--<td><?php //the_content(); ?>
		</td>-->
