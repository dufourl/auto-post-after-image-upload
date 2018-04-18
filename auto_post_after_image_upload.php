<?php
/**
 * @package Auto Post With Image Upload
 * @version 1.1
 */
/*
Plugin Name: Auto Post With Image Upload
Plugin URI: http://wordpress.org/extend/plugins/auto-post-after-image-upload/
Description: This plugin will provide you the facility to create automated post when you will upload an image to your wordpress media gallery. Each time after uploading one media file upload one post will be created with attached this uploaded image automatically
Author: G. M. Shaharia Azam & Laurent Dufour
Version: 1.1
Author URI: http://www.shahariaazam.com/
*/

define( 'APAIU_VERSION', '1.1');

if (!defined('ABSPATH')) exit; // prevents direct access to the file

//defined('ABSPATH') or exit; //prevents direct access to the file

## =========================================================================
## ### BASIC DECLARATIONS
## =========================================================================

global $wpdb; //variables used in activation/uninstall functions HAVE TO be declared as global in order to work - see http://codex.wordpress.org/Function_Reference/register_activation_hook#A_Note_on_Variable_Scope

$apaiu_settings = get_option('auto_post_after_image_upload');

$apaiu_plugin_url = WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)) . "/";
$apaiu_plugin_dir = WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)) . "/";
$apaiu_plugin_basename = plugin_basename(__FILE__); //auto-post-after-image-upload/auto-post-after-image-upload.php

$apaiu_message_html_prefix_updated = '<div id="message" class="updated"><p>';
$apaiu_message_html_prefix_error = '<div id="message" class="error"><p>';
$apaiu_message_html_prefix_warning = '<div id="message" class="updated warning"><p>';
$apaiu_message_html_prefix_note = '<div id="message" class="updated note"><p>';
$apaiu_message_html_suffix = '</p></div>';
$apaiu_invalid_nonce_message = $apaiu_message_html_prefix_error .'<strong>Error:</strong> Sorry, your nonce did not verify, your request couldn\'t be executed. Please try again.'. $apaiu_message_html_suffix;

//add_filter('wp_read_image_metadata', 'apaiu_add_exif','',3);


## ===================================
## ### GET PLUGIN VERSION
## ===================================

function apaiu_get_plugin_version(){ //return plugin version
	if(!function_exists('get_plugin_data')){
		require_once(ABSPATH .'wp-admin/includes/plugin.php');
	}

	$apaiu_plugin_data = get_plugin_data( __FILE__, false, false);
	$apaiu_plugin_version = $apaiu_plugin_data['Version'];
	return $apaiu_plugin_version;
}

## ===================================
## ### EXIF GET FLOAT
## ===================================

function exif_get_float($value) { 
  $pos = strpos($value, '/'); 
  if ($pos === false) return (float) $value; 
  $a = (float) substr($value, 0, $pos); 
  $b = (float) substr($value, $pos+1); 
  return ($b == 0) ? ($a) : ($a / $b); 
} 


## ===================================
## ### ACTIVATE FUNCTION
## ===================================

function apaiu_install_plugin(){ //runs only after MANUAL activation! (also used for restoring settings)
	if(get_option('auto_post_after_image_upload') == false){ //create the option only if it doesn't exist yet
		$apaiu_default_settings = array(
			'apaiu_plugin_version' => apaiu_get_plugin_version(),
			'apaiu_status' => '1',
			'apaiu_format' => '5',
			'apaiu_use_autodetect_colors_from_exif' => 'Y',
			'apaiu_use_exif_date' => 'Y',
			'apaiu_use_year_from_date' => 'Y',
			'apaiu_use_daynight_from_date' => 'Y',
			'apaiu_use_season_from_date' => 'Y',
			'apaiu_use_long_exposure_from_exif' => 'Y',
			'apaiu_use_exif_camera_in_tags' => 'Y',
			'apaiu_use_exif_focal_in_tags' => 'Y',
			'apaiu_use_exif_width' => 'Y',
			'apaiu_use_exif_height' => 'Y',
			'apaiu_use_exif_gps' => 'Y',
			'apaiu_use_gps_google_maps' => 'Y',
			'$apaiu_default_google_maps_api_key' => '',
			'apaiu_use_image_name_as_title' => 'Y',
			'apaiu_default_title' => '',
			'apaiu_add_title_to_alt' => 'Y',
			'apaiu_default_text_content' => '',
			'apaiu_add_exif_to_default_content' => 'Y',
			'apaiu_default_long_exposure_categories' => '',
			'apaiu_default_colors_categories' => '',
			'apaiu_default_bw_categories' => '',
			'apaiu_default_analog_categories' => '',
			'apaiu_default_analog_colors_categories' => '',
			'apaiu_default_analog_bw_categories' => '',
			'apaiu_default_square_categories' => '',
			'apaiu_default_24x36_categories' => '',
			'apaiu_default_6x5_categories' => '',
			'apaiu_default_6x7_categories' => '',
			'apaiu_default_6x17_categories' => '',
			'apaiu_default_XPAN_categories' => '',
			'apaiu_default_categories' => '',
			'apaiu_default_ultra_wide_angle_tags' => '',
			'apaiu_default_wide_angle_tags' => '',
			'apaiu_default_standard_tags' => '',
			'apaiu_default_telephoto_tags' => '',
			'apaiu_default_super_telephoto_tags' => '',
			'apaiu_default_square_tags' => '',
			'apaiu_default_24x36_tags' => '',
			'apaiu_default_6x5_tags' => '',
			'apaiu_default_6x7_tags' => '',
			'apaiu_default_6x17_tags' => '',
			'apaiu_default_XPAN_tags' => '',
			'apaiu_default_long_exposure_tags' => '',
			'apaiu_default_portrait_tags' => '',
			'apaiu_default_landscape_tags' => '',
			'apaiu_default_spring_tags' => '',
			'apaiu_default_summer_tags' => '',
			'apaiu_default_autumn_tags' => '',
			'apaiu_default_winter_tags' => '',
			'apaiu_default_morning_tags' => '',
			'apaiu_default_afternoon_tags' => '',
			'apaiu_default_night_tags' => '',
			'apaiu_default_colors_tags' => '',
			'apaiu_default_bw_tags' => '',
			'apaiu_default_analog_tags' => '',
			'apaiu_default_analog_bw_tags' => '',
			'apaiu_default_analog_colors_tags' => '',
			'apaiu_default_tags' => ''
		);

		add_option('auto_post_after_image_upload', $apaiu_default_settings, '', 'no'); //single option for storing default settings
	}

}



## ===================================
## ### READ GPS LOCATION
## ===================================
 /**
     * Returns an array of latitude and longitude from the Image file
     * @param image $file
     * @return multitype:number |boolean
     */
//See: http://stackoverflow.com/a/19420991/1288109
//See: http://stackoverflow.com/questions/37167942/how-to-retrive-geolocation-information-from-jpeg-exif-data

function read_gps_location($file){
	if (is_file($file)) {
		$info = @exif_read_data($file,'EXIF', false);

		if (
			(@array_key_exists('GPSLatitude', $info))
			&& (@array_key_exists('GPSLongitude', $info))
 //           && (@array_key_exists('GPSLatitudeRef', $info))
 //           && (@array_key_exists('GPSLongitudeRef', $info))
 //           && in_array($info['GPSLatitudeRef'], array('E','W','N','S'))
 //           && in_array($info['GPSLongitudeRef'], array('E','W','N','S'))
			) {


			$GPSLatitudeRef  = strtolower(trim($info['GPSLatitudeRef']));
            $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));
			
			
			if (
				   in_array($GPSLatitudeRef, array('e','w','n','s'))
				&& in_array($GPSLongitudeRef, array('e','w','n','s')) 
			)
			{
				
				
				//$lat_degrees = exif_get_float($info['GPSLatitude'][0]);
                //$lat_minutes = exif_get_float($info['GPSLatitude'][1]);
                //$lat_seconds = 0.5;
                //$lng_degrees = exif_get_float($info['GPSLongitude'][0]);
                //$lng_minutes = exif_get_float($info['GPSLongitude'][1]);
                //$lng_seconds = 0.5;
				
				$lat_degrees_a = explode('/',$info['GPSLatitude'][0]);
                $lat_minutes_a = explode('/',$info['GPSLatitude'][1]);
                $lat_seconds_a = explode('/',$info['GPSLatitude'][2]);
                $lng_degrees_a = explode('/',$info['GPSLongitude'][0]);
                $lng_minutes_a = explode('/',$info['GPSLongitude'][1]);
                $lng_seconds_a = explode('/',$info['GPSLongitude'][2]);

                $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
                $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
                $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
                $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
                $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
                $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];
				
				
				
				
				$lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
				$lng = (float) $lng_degrees+((($lng_minutes*60)+($lng_seconds))/3600);

				//If the latitude is South, make it negative. 
				//If the longitude is west, make it negative
				$GPSLatitudeRef  == 's' ? $lat *= -1 : '';
				$GPSLongitudeRef == 'w' ? $lng *= -1 : '';

				return array(
					'lat' => $lat,
					'lng' => $lng
				);
			
			}
		}           
	}
	return false;
}






## ===================================
## ### calculateColorSpaceValue - Check if a given image is grayscale or color
## ===================================
//See: https://phpquiz.wordpress.com/2008/12/16/check-if-a-given-image-is-grayscale-or-color/

function calculateColorSpaceValue($imageHandle) {
$RGBVariance = 0;

//Iterating pixels
for($x = 0; $x < imagesx($imageHandle); $x ++) {
for($y = 0; $y < imagesy($imageHandle); $y ++) {

//Getting the color of each next pixel
$color = imagecolorat($imageHandle, $x, $y);

//Separating R, G and B values of it
$r = ($color >> 16) & 0xFF;
$g = ($color >> 8) & 0xFF;
$b = $color & 0xFF;

//Calculating the  sum of differences between R, G and B values
$RGBVariance += (abs($r - $g) + abs($r - $b) + abs($g - $b));
}
}

//Returing variation coefficient
return ($RGBVariance / (imagesx($imageHandle) * imagesx($imageHandle)));
}


## ===================================
## ### CONVERT TO FLOAT
## ===================================

function ConvertToFloat($s)
{
	$p="Unable to convert ".$s." value";
	if(preg_match('/(\d+)(?:\s*)([\+\-\*\/])(?:\s*)(\d+)/', $s, $matches) !== FALSE){
		$operator = $matches[2];

		switch($operator){
			case '+':
				$p = $matches[1] + $matches[3];
				break;
			case '-':
				$p = $matches[1] - $matches[3];
				break;
			case '*':
				$p = $matches[1] * $matches[3];
				break;
			case '/':
				$p = $matches[1] / $matches[3];
				break;
		}

    
	}

return $p;

}



## ===================================
## ### FSTOP NUMBER FUNCTIONS
## ===================================

function exif_get_fstop($aperture_to_convert) { 
  if (!isset($aperture_to_convert)) return false; 
  $apex  = exif_get_float($aperture_to_convert); 
  $fstop = pow(2, $apex/2); 
  if ($fstop == 0) return false; 
  return 'f/' . round($fstop,1); 
} 



## ===================================
## ### SHUTTER SPEED FUNCTIONS
## ===================================

function exif_get_shutter($shutter_speed_to_convert) { 
  if (!isset($shutter_speed_to_convert)) return false; 
  $apex    = exif_get_float($shutter_speed_to_convert); 
  $shutter = pow(2, -$apex); 
  if ($shutter == 0) return false; 
  if ($shutter >= 1) return round($shutter) . 's'; 
  return '1/' . round(1 / $shutter) . 's'; 
} 



## ===================================
## ### GET IMAGE FORMAT RATIO
## ===================================

function get_image_format_ratio($fpwidth, $fpheight)
{ 
 if ( ( $fpwidth > 0 ) && ($fpheight > 0) ) {
	  
	  if ( $fpwidth > $fpheight ) {
		  
		  return round( ($fpwidth / $fpheight) ,2);
	  }
	  else {
		    return round( ($fpheight / $fpwidth) ,2);
	  }		  
  } else {
    return 0;
  }

}


## ===================================
## ### GET LIST OF CATEGORY ID
## ===================================

function get_list_of_catID($list_of_categories)
{ 

$list_of_catID=array('');


if (empty($list_of_categories)) 
	{ return $list_of_catID; }
else 
	{
		$list_of_category_name=explode(',',$list_of_categories);	
		foreach($list_of_category_name as $category_element){
										
										
										$cat_ID = get_cat_ID( $category_element );
										array_push( $list_of_catID, $cat_ID );
		}
	}

return $list_of_catID;
}


## ===================================
## ### UPLOAD IMAGE FUNCTION
## ===================================


function auto_post_after_image_upload($attachId)
{

	$apaiu_settings = get_option('auto_post_after_image_upload');

	$is_digital='N';
	
	$ratio_24x36='1.46';
	$ratio_XPAN='2.71';
	$ratio_6x5='1.25';
	$ratio_6x7='1.16';
	$ratio_6x17='2.83';
	
	if (empty($apaiu_settings)||!is_array($apaiu_settings)) $apaiu_settings=array();
	$apaiu_status='1';
	$apaiu_format='5';
	$apaiu_use_href='Y';
	$apaiu_use_autodetect_colors_from_exif='Y';
	$apaiu_use_exif_date='Y';
	$apaiu_use_year_from_date='Y';
	$apaiu_use_daynight_from_date='Y';
	$apaiu_use_season_from_date='Y';
	$apaiu_use_long_exposure_from_exif='Y';
	$apaiu_use_exif_camera_in_tags='Y';
	$apaiu_use_exif_focal_in_tags='Y';
	$apaiu_use_exif_width='Y';
	$apaiu_use_exif_height='Y';
	$apaiu_use_exif_gps='Y';
	$apaiu_use_gps_google_maps='Y';
	$apaiu_default_google_maps_api_key='';
	$apaiu_use_image_name_as_title='Y';
	$apaiu_default_title='';
	$apaiu_add_title_to_alt='Y';
	$apaiu_default_text_content='';
	$apaiu_add_exif_to_default_content='Y';
	$apaiu_default_long_exposure_categories='';
	$apaiu_default_colors_categories='';
	$apaiu_default_bw_categories='';
	$apaiu_default_analog_categories='';
	$apaiu_default_analog_colors_categories='';
	$apaiu_default_analog_bw_categories='';
	$apaiu_default_square_categories='';
	$apaiu_default_24x36_categories='';
	$apaiu_default_6x5_categories='';
	$apaiu_default_6x7_categories='';
	$apaiu_default_6x17_categories='';
	$apaiu_default_XPAN_categories='';
	$apaiu_default_categories='';
	$apaiu_default_ultra_wide_angle_tags='';
	$apaiu_default_wide_angle_tags='';
	$apaiu_default_standard_tags='';
	$apaiu_default_telephoto_tags='';
	$apaiu_default_super_telephoto_tags='';
	$apaiu_default_square_tags='';
	$apaiu_default_24x36_tags='';
	$apaiu_default_6x5_tags='';
	$apaiu_default_6x7_tags='';
	$apaiu_default_6x17_tags='';
	$apaiu_default_XPAN_tags='';
	$apaiu_default_long_exposure_tags='';
	$apaiu_default_portrait_tags='';
	$apaiu_default_landscape_tags='';
	$apaiu_default_spring_tags='';
	$apaiu_default_summer_tags='';
	$apaiu_default_autumn_tags='';
	$apaiu_default_winter_tags='';
	$apaiu_default_morning_tags='';
	$apaiu_default_afternoon_tags='';
	$apaiu_default_night_tags='';
	$apaiu_default_colors_tags='';
	$apaiu_default_bw_tags='';
	$apaiu_default_analog_tags='';
	$apaiu_default_analog_bw_tags='';
	$apaiu_default_analog_analog_tags='';
	$apaiu_default_tags='';
	$apaiu_default_href_class='';
	
	extract($apaiu_settings);
	
	if (empty($apaiu_status)) $apaiu_status='1';
	
	if (empty($apaiu_format)) $apaiu_format='5';
	
	if (empty($apaiu_use_href)) $apaiu_use_href='Y';
	if ($apaiu_use_href!='Y') $apaiu_use_href='N';
	

	if (empty($apaiu_use_autodetect_colors_from_exif)) $apaiu_use_autodetect_colors_from_exif='Y';
	if ($apaiu_use_autodetect_colors_from_exif!='Y') $apaiu_use_autodetect_colors_from_exif='N';

	if (empty($apaiu_use_year_from_date)) $apaiu_use_year_from_date='Y';
	if ($apaiu_use_year_from_date!='Y') $apaiu_use_year_from_date='N';

	if (empty($apaiu_use_daynight_from_date)) $apaiu_use_daynight_from_date='Y';
	if ($apaiu_use_daynight_from_date!='Y') $apaiu_use_daynight_from_date='N';

	if (empty($apaiu_use_season_from_date)) $apaiu_use_season_from_date='Y';
	if ($apaiu_use_season_from_date!='Y') $apaiu_use_season_from_date='N';

	if (empty($apaiu_use_long_exposure_from_exif)) $apaiu_use_long_exposure_from_exif='Y';
	if ($apaiu_use_long_exposure_from_exif!='Y') $apaiu_use_long_exposure_from_exif='N';
	
	if (empty($apaiu_use_exif_camera_in_tags)) $apaiu_use_exif_camera_in_tags='Y';
	if ($apaiu_use_exif_camera_in_tags!='Y') $apaiu_use_exif_camera_in_tags='N';
	
	if (empty($apaiu_use_exif_focal_in_tags)) $apaiu_use_exif_focal_in_tags='Y';
	if ($apaiu_use_exif_focal_in_tags!='Y') $apaiu_use_exif_focal_in_tags='N';
	
	
	
	if (empty($apaiu_use_exif_height)) $apaiu_use_exif_height='Y';
	if ($apaiu_use_exif_height!='Y') $apaiu_use_exif_height='N';
	
	if (empty($apaiu_use_exif_width)) $apaiu_use_exif_width='Y';
	if ($apaiu_use_exif_width!='Y') $apaiu_use_exif_width='N';

	if (empty($apaiu_use_exif_gps)) $apaiu_use_exif_gps='Y';
	if ($apaiu_use_exif_gps!='Y') $apaiu_use_exif_gps='N';

	if (empty($apaiu_use_gps_google_maps)) $apaiu_use_gps_google_maps='Y';
	if ($apaiu_use_gps_google_maps!='Y') $apaiu_use_gps_google_maps='N';

	
	if (empty($apaiu_use_image_name_as_title)) $apaiu_use_image_name_as_title='Y';
	if ($apaiu_use_image_name_as_title!='Y') $apaiu_use_image_name_as_title='N';
	
	if (empty($apaiu_add_title_to_alt)) $apaiu_add_title_to_alt='Y';
	if ($apaiu_add_title_to_alt!='Y') $apaiu_add_title_to_alt='N';

	if (empty($apaiu_add_exif_to_default_content)) $apaiu_add_exif_to_default_content='Y';
	if ($apaiu_add_exif_to_default_content!='Y') $apaiu_add_exif_to_default_content='N';
	
	
	
	$pic_date = date('Y-m-d H:i:s',time());
	$pic_year = date('Y',time());
	$pic_hour = date('H',time());
	$pic_day = date('d',time());
	$pic_month = date('m',time());
		

	
    $attachment = get_post($attachId);
	
	$image = array();
    $image = wp_get_attachment_image_src( $attachId, 'full');
	
    if( is_array($image) ) {

	
	$exif_result = "";
	$pic_width = $image[1];
	$pic_height= $image[2];
	
	
	$imagemeta = array();
	$image_data = array ();
	
	//$image_data = wp_get_attachment_metadata( $attachId);
	//$imagemeta = $image_data['image_meta'];
	
	
	$pics_path_and_filename=get_attached_file( $attachId);
	
	$origimg = imagecreatefromjpeg($pics_path_and_filename);
	$result_bw_or_color=calculateColorSpaceValue($origimg);
	
	
	if ( $result_bw_or_color < 10  )  
			 $photo_is_in_colors = "N";
			else
			 $photo_is_in_colors = "Y";
	
	
	
	$imagemeta=apaiu_read_exif($pics_path_and_filename);
	
	
	// var_export -- nice, one-liner
	$debug_export = var_export($imagemeta, true);
	
	//var_dump($imagemeta); 
	// list values in array

	
	// Is there any exif ?
	
	
		
	
		if ($apaiu_use_exif_width=='Y')	
		 { $pic_width = $imagemeta['width']; }
	

		if ($apaiu_use_exif_height=='Y') 
		 { $pic_height = $imagemeta['height']; }
	
		if( isset($imagemeta['make']) )         $pmake = $imagemeta['make'];
        else                                                $pmake = "";
	
		if( isset($imagemeta['model']) ) 	$pcamera = $imagemeta['model']; 
		else 												$pcamera = "";

		if( isset($imagemeta['shutter_speed']) ) 	
		{
			$pshutter_speed = exif_get_shutter($imagemeta['shutter_speed']);
			$is_digital='Y';
		}	
		else 												$pshutter_speed = "";

		if( isset($imagemeta['aperture']) ) 	
		{
			$paperture = exif_get_fstop($imagemeta['aperture']);
			$is_digital='Y';
		}	
		else 												$paperture = "";
		
        
        if( isset($imagemeta['focal_length']) ) 
		{ 
			 $nfocal_length = exif_get_float($imagemeta['focal_length']);
			 $pfocal_length = $nfocal_length . "mm";
			 $is_digital='Y';
		}
        else                                                $pfocal_length = "";
        
        if( isset($imagemeta['iso']) )          { $piso = "ISO " . $imagemeta['iso']; $is_digital='Y'; }
        else                                                $piso = "";

		if( isset($imagemeta['copyright']) )          $pcopyright = "Copyright : " . $imagemeta['copyright'];
        else                                                $pcopyright = "";

		
        // eliminate long make names like "NIKON CORPORATION"
        if( strlen($pmake)>12 && strcasecmp(substr($pmake, strlen($pmake)-12), " CORPORATION")==0 )
            $pmake = substr($pmake, 0, strlen($pmake)-12);
        
        if( strlen($pmake)==20 && strcasecmp($pmake, "PENTAX RICOH IMAGING")==0 )
            $pmake = "RICOH";
        
        // eliminate duplicate brand names in make and model field, like "Canon Canon EOS 5D"
        if( $pmake!="" &&  strcasecmp( substr($pcamera, 0, strlen($pmake)), $pmake)==0 )
            $pcamera = substr($pcamera, strlen($pmake)+1);
        
		
        if( isset($imagemeta['latitude']) )          { $platitude = "Latitude " . $imagemeta['latitude'][0].' '. $imagemeta['latitude'][1].' '. $imagemeta['latitude'][2]; $is_digital='Y'; }
        else                                                $platitude = "";
		
        if( isset($imagemeta['longitude']) )         { $plongitude = "Longitude " . $imagemeta['longitude'][0] .' '. $imagemeta['longitude'][1].' '. $imagemeta['longitude'][2]; $is_digital='Y'; }
        else                                                $plongitude = "";
		
		
		if( isset($imagemeta['latitude_ref']) )          $platitude_ref = $imagemeta['latitude_ref'];
        else                                                $platitude_ref = "";
		
		if( isset($imagemeta['longitude_ref']) )          $plongitude_ref = $imagemeta['longitude_ref'];
        else                                                $plongitude_ref = "";
		
		
		
        // prevent code injections
        $pmake = htmlspecialchars($pmake, ENT_QUOTES);
        $pcamera = htmlspecialchars($pcamera, ENT_QUOTES);
        $pfocal_length = htmlspecialchars($pfocal_length, ENT_QUOTES);
        $piso = htmlspecialchars($piso, ENT_QUOTES);
		$pshutter_speed = htmlspecialchars($pshutter_speed, ENT_QUOTES);
		$paperture = htmlspecialchars($paperture, ENT_QUOTES);
		
		$platitude = htmlspecialchars($platitude, ENT_QUOTES);
		$plongitude = htmlspecialchars($plongitude, ENT_QUOTES);
		$platitude_ref = htmlspecialchars($platitude_ref, ENT_QUOTES);
		$plongitude_ref = htmlspecialchars($plongitude_ref, ENT_QUOTES);
		
/*		 
		$aperture          = $imagemeta['aperture'];
		$credit            = $imagemeta['credit'];
		$pcamera           = $imagemeta['camera'];
		$caption           = $imagemeta['caption'];
		$created_timestamp = $imagemeta['created_timestamp'];
		$copyright         = $imagemeta['copyright'];
		$focal_length      = $imagemeta['focal_length'];
		$iso               = $imagemeta['iso'];
		$shutter_speed     = $imagemeta['shutter_speed'];
		$title             = $imagemeta['title'];
*/

		if ($apaiu_use_exif_date=='Y')	
		  {
		   $pic_date = date('Y-m-d H:i:s', strtotime($imagemeta['created_timestamp']));
		   $pic_year = date('Y',strtotime($imagemeta['created_timestamp']));
		   $pic_hour = date('H',strtotime($imagemeta['created_timestamp']));
		   $pic_day = date('d',strtotime($imagemeta['created_timestamp']));
		   $pic_month = date('m',strtotime($imagemeta['created_timestamp']));

		  } 
	
	
		$exif_result =  $pmake . ' ' . $pcamera . ' (' . $pfocal_length . ', ' . $piso. ', ' . $pshutter_speed . ', ' . $paperture .') - '.$pcopyright.'';
	
	
	
	 
	
	
	
	
	// prevent code injections
	$apaiu_default_text_content = htmlspecialchars($apaiu_default_text_content, ENT_QUOTES);
	$apaiu_default_title = htmlspecialchars($apaiu_default_title, ENT_QUOTES);
	
	$this_pic_ratio=get_image_format_ratio($pic_width,$pic_height);
	
	
	
	if ($apaiu_use_image_name_as_title=='Y') 
	 $image_post_title=$attachment->post_title;
	else
	 $image_post_title=$apaiu_default_title;
	
	
	if ($apaiu_use_href=='Y')	 
	{
		
		if ($apaiu_add_title_to_alt=='Y')
		 $image_alt=$image_post_title;
		else
		 $image_alt='';	
		
		$image_tag = '<a href="'.$image[0].'" class="'.$apaiu_default_href_class.'"/><img src="'.$image[0].'" alt="'.$image_alt.'" width="'.$pic_width.'" height="'.$pic_height.'" class="alignleft size-full wp-image-'.$attachId.'" /></a>';
		
		if ($apaiu_add_exif_to_default_content=='Y')
		{
			if  ( strlen($exif_result)>6) 
				{
					$image_tag = $image_tag.$exif_result.'</br>';
				}
		
		}
		
		$image_tag = $image_tag.$apaiu_default_text_content.'';
	} 
	else	
      $image_tag = '<p><img src="'.$image[0].'" /></p>'.$apaiu_default_text_content.'';

  
	// Add GPS Location
	
	if ($apaiu_use_exif_gps=='Y')	 
	{
	
		
	
		$gpsLocation = read_gps_location($pics_path_and_filename);            
		if ($gpsLocation !== false) {
			$gpsInfo  = "GPS Lat : ". $gpsLocation['lat']. ' </br>';
			$gpsInfo  = $gpsInfo . "GPS Lon : ". $gpsLocation['lng']. ' </br>';
			$image_tag = $image_tag . $gpsInfo;
		} 
		
		
		if ( ($apaiu_use_gps_google_maps=='Y') && !empty($apaiu_default_google_maps_api_key) ) {
            if($gpsLocation !== false){
                // See: https://www.w3schools.com/graphics/google_maps_basic.asp
                $result = '<script>
                 function apaiu_InitializeGoogleMaps() {
                     var mapProp = {
                         center: new google.maps.LatLng('.$gpsLocation['lat'] . ',' . $gpsLocation['lng'] . '),
                         zoom: 12,
                         mapTypeId: google.maps.MapTypeId.ROADMAP
                     };
                     var map = new google.maps.Map(document.getElementById("apaiu_googleMapDiv"),mapProp);
                     var marker = new google.maps.Marker({
                         position: new google.maps.LatLng('.$gpsLocation['lat'] . ',' . $gpsLocation['lng'] . '),
                     });
                     marker.setMap(map);
                 }
                 function apaiu_LoadGoogleMapsScript() {
                    var alreadyRegistered = false;
                    if (typeof google === "object"){
                        if (typeof google.maps === "object"){
                            alreadyRegistered = true;
                        }
                    }
                    if (alreadyRegistered){
                        apaiu_InitializeGoogleMaps();
                    } else {
                        var script = document.createElement("script");
                        script.src = "http://maps.googleapis.com/maps/api/js?key='.$apaiu_default_google_maps_api_key.'&callback=apaiu_InitializeGoogleMaps";
                        document.body.appendChild(script);
                    }
                 }
                 window.onload = apaiu_LoadGoogleMapsScript;
                 </script>
                 <div id="apaiu_googleMapDiv" style="width:500px;height:380px;"></div>';
				 
				 
				 $image_tag = $image_tag . $result;
				 
            }
        }
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}
	
	
	
	// Add status
	
	switch ($apaiu_status) {
    case '1':
        $post_status='publish';
        break;
    case '2':
        $post_status='pending';
        break;
    case '3':
        $post_status='draft';
        break;
	default :	
		$post_status='publish';	
	}
	
    $postData = array(
        'post_title' => $image_post_title,
		'post_date' => $pic_date, 
        'post_type' => 'post',
        'post_content' => $image_tag,
    //  'post_content' => $image_tag . $attachment->post_title, 
        'post_category' => array('0'),
        'post_status' => $post_status

    );

    $post_id = wp_insert_post($postData);

    // attach media to post
    wp_update_post(array(
        'ID' => $attachId,
        'post_parent' => $post_id,
    ));
	
	if ($apaiu_use_exif_camera_in_tags=='Y') { 
		if ($apaiu_use_exif_focal_in_tags=='Y') {
			$image_post_tags=array($pmake,$pcamera,$pfocal_length);
		}
		else 
		{
			$image_post_tags=array($pmake,$pcamera);
		}			
	}
	else 
	 $image_post_tags=array('');


	

 
	// Add new tags
	
    wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );


	
	
	// add colors or bw tags
	
	
	
	if ($apaiu_use_autodetect_colors_from_exif=='Y') {
		
		if (strcasecmp($photo_is_in_colors, "Y")==0)
		{
			
			if (empty($apaiu_default_colors_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_colors_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
			
			if (empty($apaiu_default_colors_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_colors_categories;
			
			$image_post_categories_id=array('');
			$image_post_categories_id=get_list_of_catID($image_post_categories);
			wp_set_post_terms( $post_id, $image_post_categories_id, 'category',true );

			
		}
		else {
						
			if (empty($apaiu_default_bw_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_bw_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
			
			
			if (empty($apaiu_default_bw_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_bw_categories;
			
			$image_post_categories_id=array('');
			$image_post_categories_id=get_list_of_catID($image_post_categories);
			wp_set_post_terms( $post_id, $image_post_categories_id, 'category',true );
			
			
			
		}
		
		
	}
	
	
	
	if ($is_digital=='N') {
		
			if (empty($apaiu_default_analog_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_analog_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
			
			
			
			if (empty($apaiu_default_analog_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_analog_categories;
			
			$image_post_categories_id=array('');
			$image_post_categories_id=get_list_of_catID($image_post_categories);
			wp_set_post_terms( $post_id, $image_post_categories_id, 'category',true );
			
		

		if ($apaiu_use_autodetect_colors_from_exif=='Y') {
			
			if (strcasecmp($photo_is_in_colors, "Y")==0)
			{
				
				if (empty($apaiu_default_analog_colors_tags)) 
					$image_post_tags=array('');
				else
					$image_post_tags=explode(',',$apaiu_default_analog_colors_tags,0);
				
				wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
				
				if (empty($apaiu_default_analog_colors_categories)) 
					$image_post_categories=array('');
				else
					$image_post_categories=$apaiu_default_analog_colors_categories;
				
				
				$image_post_categories_id=array('');
				$image_post_categories_id=get_list_of_catID($image_post_categories);
				wp_set_post_terms( $post_id, $image_post_categories_id, 'category',true );

				
			}
			else {
							
				if (empty($apaiu_default_analog_bw_tags)) 
					$image_post_tags=array('');
				else
					$image_post_tags=explode(',',$apaiu_default_analog_bw_tags,0);
				
				wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
				
				
				if (empty($apaiu_default_analog_bw_categories)) 
					$image_post_categories=array('');
				else
					$image_post_categories=$apaiu_default_analog_bw_categories;
				
				
				$image_post_categories_id=array('');
				$image_post_categories_id=get_list_of_catID($image_post_categories);
				wp_set_post_terms( $post_id, $image_post_categories_id, 'category',true );
				
				
				
			}
			
			
		}












		
	}
	
	
	
	
	
	
	// Add long exposure tags & categories if autodetect is selected

	if ( ($apaiu_use_long_exposure_from_exif=='Y') && !empty($imagemeta['shutter_speed']) ){
		
		$local_apex    = exif_get_float($imagemeta['shutter_speed']); 
		$local_shutter_value = pow(2, -$local_apex); 
		
		
		if ($local_shutter_value >= 1) {
			if (empty($apaiu_default_long_exposure_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_long_exposure_tags,0);
			
			if (empty($apaiu_default_long_exposure_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_long_exposure_categories;
			
			
		}	
		else 
		{
			$image_post_tags=array('');
			$image_post_categories=array('');
		}
	}	
	else 
	{ $image_post_tags=array(''); $image_post_categories=array(''); }
	
	// Add new tags & categories
	
    wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );

	
	$image_post_categories_id=array('');
	$image_post_categories_id=get_list_of_catID($image_post_categories);
	wp_set_post_terms( $post_id, $image_post_categories_id, 'category',true );

	// Add year of post or photo as a tag

	if ($apaiu_use_year_from_date=='Y') 
	 $image_post_tags=array($pic_year);
	else 
	 $image_post_tags=array('');
	
	// Add new tags
	
    wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );



	// Add default tags & categories
	
	if (empty($apaiu_default_tags)) 
	 $image_post_tags=array('');
	else
	 $image_post_tags=explode(',',$apaiu_default_tags,0);
	
	wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );

	
	
	
	if (empty($apaiu_default_categories)) 
	 $image_post_categories=array('');
	else
	 $image_post_categories=$apaiu_default_categories;
 
	$image_post_categories_id=array('');
	$image_post_categories_id=get_list_of_catID($image_post_categories);
	wp_set_post_terms( $post_id, $image_post_categories_id, 'category',true );

	
	
	
	// Photo ratio
	
	if ( ($pic_width > 0) && ( $pic_height > 0 ) ) 
	{
		
		
		
		
		
		if ( ( $this_pic_ratio >= $ratio_XPAN ) && ( $this_pic_ratio <= ($ratio_XPAN+0.05) ) )
		{
			
			if (empty($apaiu_default_XPAN_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_XPAN_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );

			if (empty($apaiu_default_XPAN_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_XPAN_categories;
			
			$image_post_categories_id=array('');
			$image_post_categories_id=get_list_of_catID($image_post_categories);
			wp_set_post_terms( $post_id,  $image_post_categories_id, 'category',true );
		
			
		}
		elseif ( ( $this_pic_ratio >= $ratio_6x5 ) && ( $this_pic_ratio <= ($ratio_6x5+0.1) ) )
		{
			
			if (empty($apaiu_default_6x5_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_6x5_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );

			if (empty($apaiu_default_6x5_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_6x5_categories;
			
			$image_post_categories_id=array('');
			$image_post_categories_id=get_list_of_catID($image_post_categories);
			wp_set_post_terms( $post_id,  $image_post_categories_id, 'category',true );
			
		}	
		elseif ( ( $this_pic_ratio >= $ratio_6x7 ) && ( $this_pic_ratio <= ($ratio_6x7+0.1) ) )
		{
			if (empty($apaiu_default_6x7_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_6x7_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );

			if (empty($apaiu_default_6x7_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_6x7_categories;
			

			$image_post_categories_id=array('');
			$image_post_categories_id=get_list_of_catID($image_post_categories);
			wp_set_post_terms( $post_id,  $image_post_categories_id, 'category',true );
		}	
		elseif ( ( $this_pic_ratio >= $ratio_6x17 ) && ( $this_pic_ratio <= ($ratio_6x17+0.1) ) )
		{
			if (empty($apaiu_default_6x17_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_6x17_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );

			if (empty($apaiu_default_6x17_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_6x17_categories;
			
			$image_post_categories_id=array('');
			$image_post_categories_id=get_list_of_catID($image_post_categories);
			wp_set_post_terms( $post_id,  $image_post_categories_id, 'category',true );
		}
		elseif ( ( $this_pic_ratio >= $ratio_24x36 ) && ( $this_pic_ratio <= ($ratio_24x36+0.1) ) )
		{
			if (empty($apaiu_default_24x36_tags)) 
				$image_post_tags=array('');
			else
				$image_post_tags=explode(',',$apaiu_default_24x36_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );

			if (empty($apaiu_default_24x36_categories)) 
				$image_post_categories=array('');
			else
				$image_post_categories=$apaiu_default_24x36_categories;
			
			$image_post_categories_id=array('');
			$image_post_categories_id=get_list_of_catID($image_post_categories);
			wp_set_post_terms( $post_id,  $image_post_categories_id, 'category',true );
		}
		else 
		{
			
		}			
		
		
	}
	
	
	
	
	
	// Photo is a square, so add tags related to a square
	if ( ($pic_width == $pic_height) && ($pic_width > 0) && ( $pic_height > 0 ) ) 
	{
		if (empty($apaiu_default_square_tags)) 
		 $image_post_tags=array('');
		else
		 $image_post_tags=explode(',',$apaiu_default_square_tags,0);
		
		wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		
		
		if (empty($apaiu_default_square_categories)) 
			$image_post_categories=array('');
		else
			$image_post_categories=$apaiu_default_square_categories;
		
		$image_post_categories_id=array('');
		$image_post_categories_id=get_list_of_catID($image_post_categories);
		wp_set_post_terms( $post_id, $image_post_categories_id, 'category',true );
		
		
		
	}
	
	// Photo is a portrait, so add tags related to a square
	if ( ($pic_width < $pic_height) && ($pic_width > 0) && ( $pic_height > 0 ) ) 
	{
		if (empty($apaiu_default_portrait_tags)) 
		 $image_post_tags=array('');
		else
		 $image_post_tags=explode(',',$apaiu_default_portrait_tags,0);
		
		wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		
		
		
		
		
		
	}
	
	
	
	
	// Photo is a landscape, so add tags related to a square
	if ( ($pic_width > $pic_height) && ($pic_width > 0) && ( $pic_height > 0 ) ) 
	{
		if (empty($apaiu_default_landscape_tags)) 
		 $image_post_tags=array('');
		else
		 $image_post_tags=explode(',',$apaiu_default_landscape_tags,0);
		
		wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
	}
	
	
	if ($apaiu_use_daynight_from_date == 'Y') {
	
		// Photo is shot in the morning, so add tags related to morning
		if ( ($pic_hour > 6 ) && ($pic_hour < 12 )  ) 
		{
			if (empty($apaiu_default_morning_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_morning_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
		
		// Photo is shot in the afternoon, so add tags related to afternoon
		if ( ($pic_hour >= 12 ) && ($pic_hour < 18 )  ) 
		{
			if (empty($apaiu_default_afternoon_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_afternoon_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
		
		// Photo is shot in the night, so add tags related to night
		if ( ($pic_hour >= 18 ) || ($pic_hour < 6 ) ) 
		{
			if (empty($apaiu_default_night_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_night_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
	
	}
	
	
	if ($apaiu_use_season_from_date == 'Y') {
	
		// Photo is shot in spring, so add tags related to spring
		if ( ( ($pic_month == 3) && ($pic_day > 20) ) || ($pic_month == 4 ) || ($pic_month == 5 ) || ( ( $pic_month == 6 ) && ($pic_day < 21) ) ) 
		{
			if (empty($apaiu_default_spring_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_spring_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
		
		// Photo is shot in summer, so add tags related to summer
		if ( ( ($pic_month == 6) && ($pic_day > 20) ) || ($pic_month == 7 ) || ($pic_month == 8 ) || ( ( $pic_month == 9 ) && ($pic_day < 21) ) ) 
		{
			if (empty($apaiu_default_summer_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_summer_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
		
		// Photo is shot in autumn, so add tags related to autumn
		if ( ( ($pic_month == 9) && ($pic_day > 20) ) || ($pic_month == 10 ) || ($pic_month == 11 ) || ( ( $pic_month == 12 ) && ($pic_day < 21) ) ) 
		{
			if (empty($apaiu_default_autumn_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_autumn_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
		
		// Photo is shot in winter, so add tags related to winter
		if ( ( ($pic_month == 12) && ($pic_day > 20) ) || ($pic_month == 1 ) || ($pic_month == 2 ) || ( ( $pic_month == 3 ) && ($pic_day < 21) ) ) 
		{
			if (empty($apaiu_default_winter_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_winter_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
		
		
	
	
	}
	
	
	// Lense is Ultra Wide Angle ( 1mm to 20mm )
		if ( ($nfocal_length > 0 ) && ($nfocal_length <= 20 ) ) 
		{
			if (empty($apaiu_default_ultra_wide_angle_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_ultra_wide_angle_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
	
	// Lense is Wide Angle ( 21mm to 49mm )
		if ( ($nfocal_length > 20 ) && ($nfocal_length < 50 ) ) 
		{
			if (empty($apaiu_default_wide_angle_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_wide_angle_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}

	// Lense is Standard ( 50 to 100 mm )
		if ( ($nfocal_length >=50 ) && ($nfocal_length <= 100 ) ) 
		{
			if (empty($apaiu_default_standard_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_standard_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
		
	// Lense is Telephoto ( 101 to 300 mm )
		if ( ($nfocal_length > 100 ) && ($nfocal_length <= 300 ) ) 
		{
			if (empty($apaiu_default_telephoto_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_telephoto_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
	
	// Lense is Super Telephoto ( 300 to 1200 mm )
		if ( ($nfocal_length > 300 ) && ($nfocal_length <= 1200 ) ) 
		{
			if (empty($apaiu_default_telephoto_tags)) 
			 $image_post_tags=array('');
			else
			 $image_post_tags=explode(',',$apaiu_default_telephoto_tags,0);
			
			wp_set_post_terms( $post_id, $image_post_tags, 'post_tag',true );
		}
	
	// Add format
	
	switch ($apaiu_format) {
    case '1':
        $post_format='standard';
        break;
    case '2':
        $post_format='aside';
        break;
    case '3':
        $post_format='gallery';
        break;
    case '4':
        $post_format='link';
        break;
    case '5':
        $post_format='image';
        break;
    case '6':
        $post_format='quote';
        break;
    case '7':
        $post_format='status';
        break;
    case '8':
        $post_format='video';
        break;
    case '9':
        $post_format='audio';
        break;
    case '10':
        $post_format='chat';
        break;
	default :	
	 $post_format='image';
 	}
	
	
	set_post_format( $post_id, $post_format );
	
	// Add thumbnail
	
    set_post_thumbnail($post_id, $attachId);

}	

    return $attachId;
}



## ===================================
## ### INIT FUNCTION
## ===================================




function apaiu_control()  {
	$apaiu_settings=get_option('auto_post_after_image_upload');
	if (empty($apaiu_settings)||!is_array($apaiu_settings)) $apaiu_settings=array();
	$apaiu_status='1';
	$apaiu_format='5';
	$apaiu_use_href='Y';
	$apaiu_use_autodetect_colors_from_exif='Y';
	$apaiu_use_exif_date='Y';
	$apaiu_use_year_from_date='Y';
	$apaiu_use_daynight_from_date='Y';
	$apaiu_use_season_date='Y';
	$apaiu_use_long_exposure_from_exif='Y';
	$apaiu_use_exif_camera_in_tags='Y';
	$apaiu_use_exif_focal_in_tags='Y';
	$apaiu_use_exif_width='Y';
	$apaiu_use_exif_height='Y';
	$apaiu_use_exif_gps='Y';
	$apaiu_use_gps_google_maps='Y';
	$apaiu_default_google_maps_api_key='';
	$apaiu_use_image_name_as_title='Y';
	$apaiu_default_title='';
	$apaiu_add_title_to_alt='Y';
	$apaiu_default_text_content='';
	$apaiu_add_exif_to_default_content='Y';
	$apaiu_default_categories='';
	$apaiu_default_long_exposure_categories='';
	$apaiu_default_colors_categories='';
	$apaiu_default_bw_categories='';
	$apaiu_default_analog_categories='';
	$apaiu_default_analog_colors_categories='';
	$apaiu_default_analog_bw_categories='';
	$apaiu_default_square_categories='';
	$apaiu_default_24x36_categories='';
	$apaiu_default_6x5_categories='';
	$apaiu_default_6x7_categories='';
	$apaiu_default_6x17_categories='';
	$apaiu_default_XPAN_categories='';
	$apaiu_default_ultra_wide_angle_tags='';
	$apaiu_default_wide_angle_tags='';
	$apaiu_default_standard_tags='';
	$apaiu_default_telephoto_tags='';
	$apaiu_default_super_telephoto_tags='';
	$apaiu_default_square_tags='';
	$apaiu_default_24x36_tags='';
	$apaiu_default_6x5_tags='';
	$apaiu_default_6x7_tags='';
	$apaiu_default_6x17_tags='';
	$apaiu_default_XPAN_tags='';
	$apaiu_default_long_exposure_tags='';
	$apaiu_default_portrait_tags='';
	$apaiu_default_landscape_tags='';
	$apaiu_default_tags='';
	$apaiu_default_spring_tags='';
	$apaiu_default_summer_tags='';
	$apaiu_default_autumn_tags='';
	$apaiu_default_winter_tags='';
	$apaiu_default_morning_tags='';
	$apaiu_default_afternoon_tags='';
	$apaiu_default_night_tags='';
	$apaiu_default_colors_tags='';
	$apaiu_default_bw_tags='';
	$apaiu_default_analog_tags='';
	$apaiu_default_analog_colors_tags='';
	$apaiu_default_analog_bw_tags='';
	$apaiu_default_href_class='';
	extract($apaiu_settings);
	
	if (empty($apaiu_status)) $apaiu_status='1';
	
	if (empty($apaiu_format)) $apaiu_format='5';
	
	if (empty($apaiu_use_href)) $apaiu_use_href='Y';
	if ($apaiu_use_href!='Y') $apaiu_use_href='N';


	if (empty($apaiu_use_autodetect_colors_from_exif)) $apaiu_use_autodetect_colors_from_exif='Y';
	if ($apaiu_use_autodetect_colors_from_exif!='Y') $apaiu_use_autodetect_colors_from_exif='N';

	
	if (empty($apaiu_use_exif_date)) $apaiu_use_exif_date='Y';
	if ($apaiu_use_exif_date!='Y') $apaiu_use_exif_date='N';

	if (empty($apaiu_use_year_from_date)) $apaiu_use_year_from_date='Y';
	if ($apaiu_use_year_from_date!='Y') $apaiu_use_year_from_date='N';


	if (empty($apaiu_use_daynight_from_date)) $apaiu_use_daynight_from_date='Y';
	if ($apaiu_use_daynight_from_date!='Y') $apaiu_use_daynight_from_date='N';

	if (empty($apaiu_use_season_from_date)) $apaiu_use_season_from_date='Y';
	if ($apaiu_use_season_from_date!='Y') $apaiu_use_season_from_date='N';

	if (empty($apaiu_use_long_exposure_from_exif)) $apaiu_use_long_exposure_from_exif='Y';
	if ($apaiu_use_long_exposure_from_exif!='Y') $apaiu_use_long_exposure_from_exif='N';

	
	if (empty($apaiu_use_exif_camera_in_tags)) $apaiu_use_exif_camera_in_tags='Y';
	if ($apaiu_use_exif_camera_in_tags!='Y') $apaiu_use_exif_camera_in_tags='N';

	if (empty($apaiu_use_exif_focal_in_tags)) $apaiu_use_exif_focal_in_tags='Y';
	if ($apaiu_use_exif_focal_in_tags!='Y') $apaiu_use_exif_focal_in_tags='N';

	
	if (empty($apaiu_use_exif_height)) $apaiu_use_exif_height='Y';
	if ($apaiu_use_exif_height!='Y') $apaiu_use_exif_height='N';
	
	if (empty($apaiu_use_exif_width)) $apaiu_use_exif_width='Y';
	if ($apaiu_use_exif_width!='Y') $apaiu_use_exif_width='N';

	if (empty($apaiu_use_exif_gps)) $apaiu_use_exif_gps='Y';
	if ($apaiu_use_exif_gps!='Y') $apaiu_use_exif_gps='N';

	if (empty($apaiu_use_gps_google_maps)) $apaiu_use_gps_google_maps='Y';
	if ($apaiu_use_gps_google_maps!='Y') $apaiu_use_gps_google_maps='N';

	
	if (empty($apaiu_use_image_name_as_title)) $apaiu_use_image_name_as_title='Y';
	if ($apaiu_use_image_name_as_title!='Y') $apaiu_use_image_name_as_title='N';

	if (empty($apaiu_add_title_to_alt)) $apaiu_add_title_to_alt='Y';
	if ($apaiu_add_title_to_alt!='Y') $apaiu_add_title_to_alt='N';
	
	if (empty($apaiu_add_exif_to_default_content)) $apaiu_add_exif_to_default_content='Y';
	if ($apaiu_add_exif_to_default_content!='Y') $apaiu_add_exif_to_default_content='N';

	
	if (array_key_exists('apaiu_nonce',$_POST)&&wp_verify_nonce($_POST['apaiu_nonce'],'auto_post_after_image_upload')) { 
		// need to update replace
	
		if (array_key_exists('apaiu_status',$_POST)) {
			$apaiu_status=stripslashes($_POST['apaiu_status']);
		} else {
			$apaiu_status='1';
		}

	
		if (array_key_exists('apaiu_format',$_POST)) {
			$apaiu_format=stripslashes($_POST['apaiu_format']);
		} else {
			$apaiu_format='5';
		}

		
		if (array_key_exists('apaiu_use_href',$_POST)) {
			$apaiu_use_href=stripslashes($_POST['apaiu_use_href']);
		} else {
			$apaiu_use_href='N';
		}


		if (array_key_exists('apaiu_use_autodetect_colors_from_exif',$_POST)) {
			$apaiu_use_autodetect_colors_from_exif=stripslashes($_POST['apaiu_use_autodetect_colors_from_exif']);
		} else {
			$apaiu_use_autodetect_colors_from_exif='N';
		}

		if (array_key_exists('apaiu_use_exif_date',$_POST)) {
			$apaiu_use_exif_date=stripslashes($_POST['apaiu_use_exif_date']);
		} else {
			$apaiu_use_exif_date='N';
		}

		if (array_key_exists('apaiu_use_year_from_date',$_POST)) {
			$apaiu_use_year_from_date=stripslashes($_POST['apaiu_use_year_from_date']);
		} else {
			$apaiu_use_year_from_date='N';
		}

		
		
		
		
		if (array_key_exists('apaiu_use_daynight_from_date',$_POST)) {
			$apaiu_use_daynight_from_date=stripslashes($_POST['apaiu_use_daynight_from_date']);
		} else {
			$apaiu_use_daynight_from_date='N';
		}

		if (array_key_exists('apaiu_use_season_from_date',$_POST)) {
			$apaiu_use_season_from_date=stripslashes($_POST['apaiu_use_season_from_date']);
		} else {
			$apaiu_use_season_from_date='N';
		}

		if (array_key_exists('apaiu_use_long_exposure_from_exif',$_POST)) {
			$apaiu_use_long_exposure_from_exif=stripslashes($_POST['apaiu_use_long_exposure_from_exif']);
		} else {
			$apaiu_use_long_exposure_from_exif='N';
		}


		
		if (array_key_exists('apaiu_use_exif_camera_in_tags',$_POST)) {
			$apaiu_use_exif_camera_in_tags=stripslashes($_POST['apaiu_use_exif_camera_in_tags']);
		} else {
			$apaiu_use_exif_camera_in_tags='N';
		}

		if (array_key_exists('apaiu_use_exif_focal_in_tags',$_POST)) {
			$apaiu_use_exif_focal_in_tags=stripslashes($_POST['apaiu_use_exif_focal_in_tags']);
		} else {
			$apaiu_use_exif_focal_in_tags='N';
		}





		
		if (array_key_exists('apaiu_use_exif_width',$_POST)) {
			$apaiu_use_exif_width=stripslashes($_POST['apaiu_use_exif_width']);
		} else {
			$apaiu_use_exif_width='N';
		}
		
		if (array_key_exists('apaiu_use_exif_height',$_POST)) {
			$apaiu_use_exif_height=stripslashes($_POST['apaiu_use_exif_height']);
		} else {
			$apaiu_use_exif_height='N';
		}
		
		
		if (array_key_exists('apaiu_use_exif_gps',$_POST)) {
			$apaiu_use_exif_gps=stripslashes($_POST['apaiu_use_exif_gps']);
		} else {
			$apaiu_use_exif_gps='N';
		}
		
		if (array_key_exists('apaiu_use_gps_google_maps',$_POST)) {
			$apaiu_use_gps_google_maps=stripslashes($_POST['apaiu_use_gps_google_maps']);
		} else {
			$apaiu_use_gps_google_maps='N';
		}
		
		if (array_key_exists('apaiu_default_google_maps_api_key',$_POST)) {
			$apaiu_default_google_maps_api_key=stripslashes($_POST['apaiu_default_google_maps_api_key']);
		} else {
			$apaiu_default_google_maps_api_key='';
		}
		
		
		
		
		
		if (array_key_exists('apaiu_use_image_name_as_title',$_POST)) {
			$apaiu_use_image_name_as_title=stripslashes($_POST['apaiu_use_image_name_as_title']);
		} else {
			$apaiu_use_image_name_as_title='N';
		}
		
		if (array_key_exists('apaiu_default_title',$_POST)) {
			$apaiu_default_title=stripslashes($_POST['apaiu_default_title']);
		} else {
			$apaiu_default_title='';
		}

		if (array_key_exists('apaiu_add_title_to_alt',$_POST)) {
			$apaiu_add_title_to_alt=stripslashes($_POST['apaiu_add_title_to_alt']);
		} else {
			$apaiu_add_title_to_alt='N';
		}
		
		if (array_key_exists('apaiu_default_text_content',$_POST)) {
			$apaiu_default_text_content=stripslashes($_POST['apaiu_default_text_content']);
		} else {
			$apaiu_default_text_content='';
		}
		
		if (array_key_exists('apaiu_add_exif_to_default_content',$_POST)) {
			$apaiu_add_exif_to_default_content=stripslashes($_POST['apaiu_add_exif_to_default_content']);
		} else {
			$apaiu_add_exif_to_default_content='N';
		}

		if (array_key_exists('apaiu_default_long_exposure_categories',$_POST)) {
			$apaiu_default_long_exposure_categories=stripslashes($_POST['apaiu_default_long_exposure_categories']);
		} else {
			$apaiu_default_long_exposure_categories='';
		}
		
		if (array_key_exists('apaiu_default_colors_categories',$_POST)) {
			$apaiu_default_colors_categories=stripslashes($_POST['apaiu_default_colors_categories']);
		} else {
			$apaiu_default_colors_categories='';
		}

		if (array_key_exists('apaiu_default_bw_categories',$_POST)) {
			$apaiu_default_bw_categories=stripslashes($_POST['apaiu_default_bw_categories']);
		} else {
			$apaiu_default_bw_categories='';
		}

		if (array_key_exists('apaiu_default_colors_categories',$_POST)) {
			$apaiu_default_analog_colors_categories=stripslashes($_POST['apaiu_default_analog_colors_categories']);
		} else {
			$apaiu_default_analog_colors_categories='';
		}

		if (array_key_exists('apaiu_default_analog_bw_categories',$_POST)) {
			$apaiu_default_analog_bw_categories=stripslashes($_POST['apaiu_default_analog_bw_categories']);
		} else {
			$apaiu_default_analog_bw_categories='';
		}


		
		if (array_key_exists('apaiu_default_analog_categories',$_POST)) {
			$apaiu_default_analog_categories=stripslashes($_POST['apaiu_default_analog_categories']);
		} else {
			$apaiu_default_analog_categories='';
		}
		
		
		if (array_key_exists('apaiu_default_square_categories',$_POST)) {
			$apaiu_default_square_categories=stripslashes($_POST['apaiu_default_square_categories']);
		} else {
			$apaiu_default_square_categories='';
		}

		if (array_key_exists('apaiu_default_24x36_categories',$_POST)) {
			$apaiu_default_24x36_categories=stripslashes($_POST['apaiu_default_24x36_categories']);
		} else {
			$apaiu_default_24x36_categories='';
		}

		
		
		if (array_key_exists('apaiu_default_6x5_categories',$_POST)) {
			$apaiu_default_6x5_categories=stripslashes($_POST['apaiu_default_6x5_categories']);
		} else {
			$apaiu_default_6x5_categories='';
		}

		if (array_key_exists('apaiu_default_6x7_categories',$_POST)) {
			$apaiu_default_6x7_categories=stripslashes($_POST['apaiu_default_6x7_categories']);
		} else {
			$apaiu_default_6x7_categories='';
		}

		if (array_key_exists('apaiu_default_6x17_categories',$_POST)) {
			$apaiu_default_6x17_categories=stripslashes($_POST['apaiu_default_6x17_categories']);
		} else {
			$apaiu_default_6x17_categories='';
		}

		if (array_key_exists('apaiu_default_XPAN_categories',$_POST)) {
			$apaiu_default_XPAN_categories=stripslashes($_POST['apaiu_default_XPAN_categories']);
		} else {
			$apaiu_default_XPAN_categories='';
		}

		
		
		if (array_key_exists('apaiu_default_categories',$_POST)) {
			$apaiu_default_categories=stripslashes($_POST['apaiu_default_categories']);
		} else {
			$apaiu_default_categories='';
		}
		
		
		if (array_key_exists('apaiu_default_ultra_wide_angle_tags',$_POST)) {
			$apaiu_default_ultra_wide_angle_tags=stripslashes($_POST['apaiu_default_ultra_wide_angle_tags']);
		} else {
			$apaiu_default_ultra_wide_angle_tags='';
		}

		if (array_key_exists('apaiu_default_wide_angle_tags',$_POST)) {
			$apaiu_default_wide_angle_tags=stripslashes($_POST['apaiu_default_wide_angle_tags']);
		} else {
			$apaiu_default_wide_angle_tags='';
		}

		if (array_key_exists('apaiu_default_standard_tags',$_POST)) {
			$apaiu_default_standard_tags=stripslashes($_POST['apaiu_default_standard_tags']);
		} else {
			$apaiu_default_standard_tags='';
		}

		if (array_key_exists('apaiu_default_telephoto_tags',$_POST)) {
			$apaiu_default_telephoto_tags=stripslashes($_POST['apaiu_default_telephoto_tags']);
		} else {
			$apaiu_default_telephoto_tags='';
		}

		if (array_key_exists('apaiu_default_super_telephoto_tags',$_POST)) {
			$apaiu_default_super_telephoto_tags=stripslashes($_POST['apaiu_default_super_telephoto_tags']);
		} else {
			$apaiu_default_super_telephoto_tags='';
		}

		
		if (array_key_exists('apaiu_default_square_tags',$_POST)) {
			$apaiu_default_square_tags=stripslashes($_POST['apaiu_default_square_tags']);
		} else {
			$apaiu_default_square_tags='';
		}

		if (array_key_exists('apaiu_default_6x5_tags',$_POST)) {
			$apaiu_default_6x5_tags=stripslashes($_POST['apaiu_default_6x5_tags']);
		} else {
			$apaiu_default_6x5_tags='';
		}

		if (array_key_exists('apaiu_default_24x36_tags',$_POST)) {
			$apaiu_default_24x36_tags=stripslashes($_POST['apaiu_default_24x36_tags']);
		} else {
			$apaiu_default_24x36_tags='';
		}

		
		
		if (array_key_exists('apaiu_default_6x7_tags',$_POST)) {
			$apaiu_default_6x7_tags=stripslashes($_POST['apaiu_default_6x7_tags']);
		} else {
			$apaiu_default_6x7_tags='';
		}

		if (array_key_exists('apaiu_default_6x17_tags',$_POST)) {
			$apaiu_default_6x17_tags=stripslashes($_POST['apaiu_default_6x17_tags']);
		} else {
			$apaiu_default_6x17_tags='';
		}

		if (array_key_exists('apaiu_default_XPAN_tags',$_POST)) {
			$apaiu_default_XPAN_tags=stripslashes($_POST['apaiu_default_XPAN_tags']);
		} else {
			$apaiu_default_XPAN_tags='';
		}

		
		
		if (array_key_exists('apaiu_default_long_exposure_tags',$_POST)) {
			$apaiu_default_long_exposure_tags=stripslashes($_POST['apaiu_default_long_exposure_tags']);
		} else {
			$apaiu_default_long_exposure_tags='';
		}

		
		
		if (array_key_exists('apaiu_default_portrait_tags',$_POST)) {
			$apaiu_default_portrait_tags=stripslashes($_POST['apaiu_default_portrait_tags']);
		} else {
			$apaiu_default_portrait_tags='';
		}

		if (array_key_exists('apaiu_default_landscape_tags',$_POST)) {
			$apaiu_default_landscape_tags=stripslashes($_POST['apaiu_default_landscape_tags']);
		} else {
			$apaiu_default_landscape_tags='';
		}
		
		
		if (array_key_exists('apaiu_default_tags',$_POST)) {
			$apaiu_default_tags=stripslashes($_POST['apaiu_default_tags']);
		} else {
			$apaiu_default_tags='';
		}

		
		
		if (array_key_exists('apaiu_default_spring_tags',$_POST)) {
			$apaiu_default_spring_tags=stripslashes($_POST['apaiu_default_spring_tags']);
		} else {
			$apaiu_default_spring_tags='';
		}

		if (array_key_exists('apaiu_default_summer_tags',$_POST)) {
			$apaiu_default_summer_tags=stripslashes($_POST['apaiu_default_summer_tags']);
		} else {
			$apaiu_default_summer_tags='';
		}

		if (array_key_exists('apaiu_default_autumn_tags',$_POST)) {
			$apaiu_default_autumn_tags=stripslashes($_POST['apaiu_default_autumn_tags']);
		} else {
			$apaiu_default_autumn_tags='';
		}

		if (array_key_exists('apaiu_default_winter_tags',$_POST)) {
			$apaiu_default_winter_tags=stripslashes($_POST['apaiu_default_winter_tags']);
		} else {
			$apaiu_default_winter_tags='';
		}


		if (array_key_exists('apaiu_default_morning_tags',$_POST)) {
			$apaiu_default_morning_tags=stripslashes($_POST['apaiu_default_morning_tags']);
		} else {
			$apaiu_default_morning_tags='';
		}


		if (array_key_exists('apaiu_default_afternoon_tags',$_POST)) {
			$apaiu_default_afternoon_tags=stripslashes($_POST['apaiu_default_afternoon_tags']);
		} else {
			$apaiu_default_afternoon_tags='';
		}


		if (array_key_exists('apaiu_default_night_tags',$_POST)) {
			$apaiu_default_night_tags=stripslashes($_POST['apaiu_default_night_tags']);
		} else {
			$apaiu_default_night_tags='';
		}
		
		if (array_key_exists('apaiu_default_colors_tags',$_POST)) {
			$apaiu_default_colors_tags=stripslashes($_POST['apaiu_default_colors_tags']);
		} else {
			$apaiu_default_colors_tags='';
		}

		if (array_key_exists('apaiu_default_bw_tags',$_POST)) {
			$apaiu_default_bw_tags=stripslashes($_POST['apaiu_default_bw_tags']);
		} else {
			$apaiu_default_bw_tags='';
		}
		
		if (array_key_exists('apaiu_default_analog_tags',$_POST)) {
			$apaiu_default_analog_tags=stripslashes($_POST['apaiu_default_analog_tags']);
		} else {
			$apaiu_default_analog_tags='';
		}

		if (array_key_exists('apaiu_default_analog_colors_tags',$_POST)) {
			$apaiu_default_analog_colors_tags=stripslashes($_POST['apaiu_default_analog_colors_tags']);
		} else {
			$apaiu_default_analog_colors_tags='';
		}

		if (array_key_exists('apaiu_default_analog_bw_tags',$_POST)) {
			$apaiu_default_analog_bw_tags=stripslashes($_POST['apaiu_default_analog_bw_tags']);
		} else {
			$apaiu_default_analog_bw_tags='';
		}




		
		if (array_key_exists('apaiu_default_href_class',$_POST)) {
			$apaiu_default_href_class=stripslashes($_POST['apaiu_default_href_class']);
		} else {
			$apaiu_default_href_class='';
		}
		
		
		if (empty($apaiu_status)) $apaiu_status='1';
		
		if (empty($apaiu_format)) $apaiu_format='5';
		
		if (empty($apaiu_use_href)) $apaiu_use_href='Y';
		if ($apaiu_use_href!='Y') $apaiu_use_href='N';
		
		if (empty($apaiu_use_autodetect_colors_from_exif)) $apaiu_use_autodetect_colors_from_exif='Y';
	    if ($apaiu_use_autodetect_colors_from_exif!='Y') $apaiu_use_autodetect_colors_from_exif='N';
		
		
		if (empty($apaiu_use_exif_date)) $apaiu_use_exif_date='Y';
		if ($apaiu_use_exif_date!='Y') $apaiu_use_exif_date='N';


		if (empty($apaiu_use_year_from_date)) $apaiu_use_year_from_date='Y';
		if ($apaiu_use_year_from_date!='Y') $apaiu_use_year_from_date='N';

		if (empty($apaiu_use_daynight_from_date)) $apaiu_use_daynight_from_date='Y';
		if ($apaiu_use_daynight_from_date!='Y') $apaiu_use_daynight_from_date='N';

		
		if (empty($apaiu_use_season_from_date)) $apaiu_use_season_from_date='Y';
		if ($apaiu_use_season_from_date!='Y') $apaiu_use_season_from_date='N';

		if (empty($apaiu_use_long_exposure_from_exif)) $apaiu_use_long_exposure_from_exif='Y';
		if ($apaiu_use_long_exposure_from_exif!='Y') $apaiu_use_long_exposure_from_exif='N';

		
		if (empty($apaiu_use_exif_camera_in_tags)) $apaiu_use_exif_camera_in_tags='Y';
		if ($apaiu_use_exif_camera_in_tags!='Y') $apaiu_use_exif_camera_in_tags='N';

		if (empty($apaiu_use_exif_focal_in_tags)) $apaiu_use_exif_focal_in_tags='Y';
		if ($apaiu_use_exif_focal_in_tags!='Y') $apaiu_use_exif_focal_in_tags='N';


		
		if (empty($apaiu_use_exif_height)) $apaiu_use_exif_height='Y';
		if ($apaiu_use_exif_height!='Y') $apaiu_use_exif_height='N';
		
		if (empty($apaiu_use_exif_width)) $apaiu_use_exif_width='Y';
		if ($apaiu_use_exif_width!='Y') $apaiu_use_exif_width='N';

		if (empty($apaiu_use_exif_gps)) $apaiu_use_exif_gps='Y';
		if ($apaiu_use_exif_gps!='Y') $apaiu_use_exif_gps='N';

		if (empty($apaiu_use_gps_google_maps)) $apaiu_use_gps_google_maps='Y';
		if ($apaiu_use_gps_google_maps!='Y') $apaiu_use_gps_google_maps='N';


		
		if (empty($apaiu_use_image_name_as_title)) $apaiu_use_image_name_as_title='Y';
		if ($apaiu_use_image_name_as_title!='Y') $apaiu_use_image_name_as_title='N';
		
		if (empty($apaiu_add_title_to_alt)) $apaiu_add_title_to_alt='Y';
		if ($apaiu_add_title_to_alt!='Y') $apaiu_add_title_to_alt='N';
		
		if (empty($apaiu_add_exif_to_default_content)) $apaiu_add_exif_to_default_content='Y';
		if ($apaiu_add_exif_to_default_content!='Y') $apaiu_add_exif_to_default_content='N';

		
		$apaiu_settings['apaiu_status']=$apaiu_status;
		$apaiu_settings['apaiu_format']=$apaiu_format;
		$apaiu_settings['apaiu_use_href']=$apaiu_use_href;
		$apaiu_settings['apaiu_use_autodetect_colors_from_exif']=$apaiu_use_autodetect_colors_from_exif;
		$apaiu_settings['apaiu_use_exif_date']=$apaiu_use_exif_date;
		$apaiu_settings['apaiu_use_year_from_date']=$apaiu_use_year_from_date;
		$apaiu_settings['apaiu_use_daynight_from_date']=$apaiu_use_daynight_from_date;
		$apaiu_settings['apaiu_use_season_from_date']=$apaiu_use_season_from_date;
		$apaiu_settings['apaiu_use_long_exposure_from_exif']=$apaiu_use_long_exposure_from_exif;
		$apaiu_settings['apaiu_use_exif_camera_in_tags']=$apaiu_use_exif_camera_in_tags;
		$apaiu_settings['apaiu_use_exif_focal_in_tags']=$apaiu_use_exif_focal_in_tags;
		$apaiu_settings['apaiu_use_exif_height']=$apaiu_use_exif_height;
		$apaiu_settings['apaiu_use_exif_width']=$apaiu_use_exif_width;
		$apaiu_settings['apaiu_use_exif_gps']=$apaiu_use_exif_gps;
		$apaiu_settings['apaiu_use_gps_google_maps']=$apaiu_use_gps_google_maps;
		$apaiu_settings['apaiu_default_google_maps_api_key']=$apaiu_default_google_maps_api_key;
		$apaiu_settings['apaiu_use_image_name_as_title']=$apaiu_use_image_name_as_title;
		$apaiu_settings['apaiu_default_title']=$apaiu_default_title;
		$apaiu_settings['apaiu_add_title_to_alt']=$apaiu_add_title_to_alt;
		$apaiu_settings['apaiu_default_text_content']=$apaiu_default_text_content;
		$apaiu_settings['apaiu_add_exif_to_default_content']=$apaiu_add_exif_to_default_content;
		
		$apaiu_settings['apaiu_default_long_exposure_categories']=$apaiu_default_long_exposure_categories;
		$apaiu_settings['apaiu_default_colors_categories']=$apaiu_default_colors_categories;
		$apaiu_settings['apaiu_default_bw_categories']=$apaiu_default_bw_categories;
		$apaiu_settings['apaiu_default_analog_categories']=$apaiu_default_analog_categories;
		$apaiu_settings['apaiu_default_analog_colors_categories']=$apaiu_default_analog_colors_categories;
		$apaiu_settings['apaiu_default_analog_bw_categories']=$apaiu_default_analog_bw_categories;
		$apaiu_settings['apaiu_default_square_categories']=$apaiu_default_square_categories;
		$apaiu_settings['apaiu_default_24x36_categories']=$apaiu_default_24x36_categories;
		$apaiu_settings['apaiu_default_6x5_categories']=$apaiu_default_6x5_categories;
		$apaiu_settings['apaiu_default_6x7_categories']=$apaiu_default_6x7_categories;
		$apaiu_settings['apaiu_default_6x17_categories']=$apaiu_default_6x17_categories;
		$apaiu_settings['apaiu_default_XPAN_categories']=$apaiu_default_XPAN_categories;

		$apaiu_settings['apaiu_default_categories']=$apaiu_default_categories;
		
		
		$apaiu_settings['apaiu_default_ultra_wide_angle_tags']=$apaiu_default_ultra_wide_angle_tags;
		$apaiu_settings['apaiu_default_wide_angle_tags']=$apaiu_default_wide_angle_tags;
		$apaiu_settings['apaiu_default_standard_tags']=$apaiu_default_standard_tags;
		$apaiu_settings['apaiu_default_telephoto_tags']=$apaiu_default_telephoto_tags;
		$apaiu_settings['apaiu_default_super_telephoto_tags']=$apaiu_default_super_telephoto_tags;
		$apaiu_settings['apaiu_default_square_tags']=$apaiu_default_square_tags;
		$apaiu_settings['apaiu_default_24x36_tags']=$apaiu_default_24x36_tags;
		$apaiu_settings['apaiu_default_6x5_tags']=$apaiu_default_6x5_tags;
		$apaiu_settings['apaiu_default_6x7_tags']=$apaiu_default_6x7_tags;
		$apaiu_settings['apaiu_default_6x17_tags']=$apaiu_default_6x17_tags;
		$apaiu_settings['apaiu_default_XPAN_tags']=$apaiu_default_XPAN_tags;
		$apaiu_settings['apaiu_default_long_exposure_tags']=$apaiu_default_long_exposure_tags;
		$apaiu_settings['apaiu_default_portrait_tags']=$apaiu_default_portrait_tags;
		$apaiu_settings['apaiu_default_landscape_tags']=$apaiu_default_landscape_tags;
		$apaiu_settings['apaiu_default_spring_tags']=$apaiu_default_spring_tags;
		$apaiu_settings['apaiu_default_summer_tags']=$apaiu_default_summer_tags;
		$apaiu_settings['apaiu_default_autumn_tags']=$apaiu_default_autumn_tags;
		$apaiu_settings['apaiu_default_winter_tags']=$apaiu_default_winter_tags;
		$apaiu_settings['apaiu_default_morning_tags']=$apaiu_default_morning_tags;
		$apaiu_settings['apaiu_default_afternoon_tags']=$apaiu_default_afternoon_tags;
		$apaiu_settings['apaiu_default_night_tags']=$apaiu_default_night_tags;
		$apaiu_settings['apaiu_default_colors_tags']=$apaiu_default_colors_tags;
		$apaiu_settings['apaiu_default_bw_tags']=$apaiu_default_bw_tags;
		$apaiu_settings['apaiu_default_analog_tags']=$apaiu_default_analog_tags;
		$apaiu_settings['apaiu_default_analog_colors_tags']=$apaiu_default_analog_colors_tags;
		$apaiu_settings['apaiu_default_analog_bw_tags']=$apaiu_default_analog_bw_tags;
		$apaiu_settings['apaiu_default_tags']=$apaiu_default_tags;
		$apaiu_settings['apaiu_default_href_class']=$apaiu_default_href_class;
		
		update_option('auto_post_after_image_upload', $apaiu_settings);

	}
   $nonce=wp_create_nonce('auto_post_after_image_upload');
 

?>

<div class="wrap">
  <h2>Auto Post With Image Upload&nbsp;<?php echo APAIU_VERSION; ?></h2>
 
  
  <h4>The Auto Post With Image Upload is installed and working correctly.</h4>
  <p>This plugin will provide you the facility to create automated post when you will upload an image to your wordpress media gallery. Each time after uploading one media file upload one post will be created with attached this uploaded image automatically. </p>
  <p>This is a major revision in the way the plugin works. <a href="mailto:laurent.dufour@havas.com">Please report all bugs</a> as soon as possible. </p>
  <form method="post" action="">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="apaiu_nonce" value="<?php echo $nonce;?>" />
	<table cellpadding="2">
	<tr>
		<td>Status to post to</td>
		<td>
			<input type="radio" name="apaiu_status" value="1" <?php if($apaiu_settings['apaiu_status'] == '1') echo 'checked="checked"'; ?>> <label>Publish</label>
			<input type="radio" name="apaiu_status" value="2" <?php if($apaiu_settings['apaiu_status'] == '2') echo 'checked="checked"'; ?>> <label>Pending</label>
			<input type="radio" name="apaiu_status" value="3" <?php if($apaiu_settings['apaiu_status'] == '3') echo 'checked="checked"'; ?>> <label>Draft</label>
		</td>
	</tr>
	<tr>
		<td align="top">Use a default content for your article</td>
		<td align="top"><textarea name="apaiu_default_text_content" rows="5" cols="50"><?php echo $apaiu_default_text_content;?></textarea></td>
		<td>If you want to add some text then put it here.<br/>
		</td>
	</tr>
	<tr>
		<td>Add EXIF data before your default content</td>
		<td><input type="checkbox" <?php if ($apaiu_add_exif_to_default_content=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_add_exif_to_default_content"></td>
		<td>Check the box if you want to use it.<br/>It will not be used if <i>Use HREF</i> is not checked</p>
		</td>
	</tr>
	<tr>
		<td>Format to post to</td>
		<td>
			<input type="radio" name="apaiu_format" value="1" <?php if($apaiu_settings['apaiu_format'] == '1') echo 'checked="checked"'; ?>> <label>Standard</label><br />
			<input type="radio" name="apaiu_format" value="2" <?php if($apaiu_settings['apaiu_format'] == '2') echo 'checked="checked"'; ?>> <label>Aside</label><br />
			<input type="radio" name="apaiu_format" value="3" <?php if($apaiu_settings['apaiu_format'] == '3') echo 'checked="checked"'; ?>> <label>Gallery</label><br />
			<input type="radio" name="apaiu_format" value="4" <?php if($apaiu_settings['apaiu_format'] == '4') echo 'checked="checked"'; ?>> <label>Link</label><br />
			<input type="radio" name="apaiu_format" value="5" <?php if($apaiu_settings['apaiu_format'] == '5') echo 'checked="checked"'; ?>> <label>Image</label><br />
			<input type="radio" name="apaiu_format" value="6" <?php if($apaiu_settings['apaiu_format'] == '6') echo 'checked="checked"'; ?>> <label>Quote</label><br />
			<input type="radio" name="apaiu_format" value="7" <?php if($apaiu_settings['apaiu_format'] == '7') echo 'checked="checked"'; ?>> <label>Status</label><br />
			<input type="radio" name="apaiu_format" value="8" <?php if($apaiu_settings['apaiu_format'] == '8') echo 'checked="checked"'; ?>> <label>Video</label><br />
			<input type="radio" name="apaiu_format" value="9" <?php if($apaiu_settings['apaiu_format'] == '9') echo 'checked="checked"'; ?>> <label>Audio</label><br />
			<input type="radio" name="apaiu_format" value="10" <?php if($apaiu_settings['apaiu_format'] == '10') echo 'checked="checked"'; ?>> <label>Chat</label><br />
		</td>
	</tr>
	<tr>
		<td>Use HREF</td>
		<td><input type="checkbox" <?php if ($apaiu_use_href=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_href"></td>
		<td><p>Check the box if you want to use it.<br />If not checked, img src only will be used</p></td>
	</tr>
	
	<tr>
		<td align="top">Class to use if HREF is selected</td>
		<td align="top"><input type="text" name="apaiu_default_href_class" size="50" value="<?php echo $apaiu_default_href_class; ?>"></td>
		<td>If you want to add some alternate class then put it here.<br/>
		</td>
	</tr>

	
	<tr>
		<td>Use image filename as title</td>
		<td><input type="checkbox" <?php if ($apaiu_use_image_name_as_title=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_image_name_as_title"></td>
		<td>Check the box if you want to use it.
		</td>
	</tr>

	<tr>
		<td align="top">Use an alternate default title</td>
		<td align="top"><input type="text" name="apaiu_default_title" size="50" value="<?php echo $apaiu_default_title; ?>"></td>
		<td>If you want to add an alternate default title then put it here.<br/>If <i>Use image filename as title</i> is checked, this option will not be used</p>
		</td>
	</tr>
	
	
	
	<tr>
		<td>Add title to ALT in HREF</td>
		<td><input type="checkbox" <?php if ($apaiu_add_title_to_alt=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_add_title_to_alt"></td>
		<td>Check the box if you want to use it.<br/>It will not be used if <i>Use HREF</i> is not checked</p>
		</td>
	</tr>
	
	<tr>
		<td align="top">Use an alternate tags</td>
		<td align="top"><input type="text" name="apaiu_default_tags" size="50" value="<?php echo $apaiu_default_tags; ?>"></td>
		<td>If you want to add some alternate tags then put them here.<br/>
		</td>
	</tr>

	<tr>
		<td align="top">Use an alternate categories</td>
		<td align="top"><input type="text" name="apaiu_default_categories" size="50" value="<?php echo $apaiu_default_categories; ?>"></td>
		<td>If you want to add some alternate categories then put them here.<br/>
		</td>
	</tr>
	
	
	</table>
	
	<p><br><br></p>
	
	
	<h4>All options below are only available for a photo</h4>
	<p>Informations from the exif of your photo can be used if they are available.</p>

	<p><br><br></p>
	
	<table cellpadding="2" >
	
	<tr>
		<td>Autodetect if photo is in colors or in black and white</td>
		<td><input type="checkbox" <?php if ($apaiu_use_autodetect_colors_from_exif=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_autodetect_colors_from_exif"></td>
		<td><p>Check the box if you want to use it.<br />If not checked, no automatic tagging based on colors or black and white will be used</p>
	    </td>
	</tr>

	<tr>
		<td>Autodetect period of day from date</td>
		<td><input type="checkbox" <?php if ($apaiu_use_daynight_from_date=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_daynight_from_date"></td>
		<td><p>Check the box if you want to use it.<br />If not checked, no period of day tagging will be used</p>
	    </td>
	</tr>

	<tr>
		<td>Autodetect season from date</td>
		<td><input type="checkbox" <?php if ($apaiu_use_season_from_date=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_season_from_date"></td>
		<td><p>Check the box if you want to use it.<br />If not checked, no season tagging will be used</p>
	    </td>
	</tr>

	<tr>
		<td>Autodetect long exposure</td>
		<td><input type="checkbox" <?php if ($apaiu_use_long_exposure_from_exif=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_long_exposure_from_exif"></td>
		<td><p>Check the box if you want to use it.<br />If not checked, no long exposure tagging will be used</p>
	    </td>
	</tr>




	
	<tr>
		<td>Use date</td>
		<td><input type="checkbox" <?php if ($apaiu_use_exif_date=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_exif_date"></td>
		<td><p>Check the box if you want to use it.<br />If not checked, today will be used</p>
	    </td>
	</tr>
	<tr>
		<td>Use your camera brand in tags</td>
		<td><input type="checkbox" <?php if ($apaiu_use_exif_camera_in_tags=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_exif_camera_in_tags"></td>
		<td>Check the box if you want to use it.</td>
	</tr>
	<tr>
		<td>Use your focal length in tags</td>
		<td><input type="checkbox" <?php if ($apaiu_use_exif_focal_in_tags=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_exif_focal_in_tags"></td>
		<td>Check the box if you want to use it.</td>
	</tr>
	<tr>
		<td>Use width</td>
		<td><input type="checkbox" <?php if ($apaiu_use_exif_width=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_exif_width"></td>
		<td>Check the box if you want to use it.
		</td>
	</tr>
	
	<tr>
		<td>Use height</td>
		<td><input type="checkbox" <?php if ($apaiu_use_exif_height=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_exif_height"></td>
		<td>Check the box if you want to use it.
		</td>
	</tr>
	
	<tr>
		<td>Autodetect GPS Location</td>
		<td><input type="checkbox" <?php if ($apaiu_use_exif_gps=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_exif_gps"></td>
		<td>Check the box if you want to use it.
		</td>
	</tr>


	<tr>
		<td>Use Google Maps to show the GPS Location</td>
		<td><input type="checkbox" <?php if ($apaiu_use_gps_google_maps=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_gps_google_maps"></td>
		<td>Check the box if you want to use it.<br/>If <i>Autodetect GPS Location</i> is checked, this option be used</p>
		</td>
	</tr>
	
	<tr>
		<td align="top">Google Maps API Key</td>
		<td align="top"><input type="text" name="apaiu_default_google_maps_api_key" size="50" value="<?php echo $apaiu_default_google_maps_api_key; ?>"></td>
		<td>If you want to use Google Maps, this key is mandatory. <br/><b>Free Google API Key</b> - Google allows a website to call any Google API for free, thousands of times a day.</br>Go to <a href="https://console.developers.google.com" target="_blank">https://console.developers.google.com</a> to get a free API key.</br>
		</td>
	</tr>
	
	<tr>
		<td>If photo, use year as a tag from date</td>
		<td><input type="checkbox" <?php if ($apaiu_use_year_from_date=='Y') {echo 'checked="true"';} ?>value="Y" name="apaiu_use_year_from_date"></td>
		<td><p>Check the box if you want to use it.<br />If not checked, no year tagging will be used</p>
	    </td>
	</tr>
	
	
	<tr>
		<td align="top">If photo use an ultra wide angle lense add those tags</td>
		<td align="top"><input type="text" name="apaiu_default_ultra_wide_angle_tags" size="50" value="<?php echo $apaiu_default_ultra_wide_angle_tags; ?>"></td>
		<td>If you want to add some tags based on ULTRA WIDE ANGLE then put them here. <br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo use a wide angle lense add those tags</td>
		<td align="top"><input type="text" name="apaiu_default_wide_angle_tags" size="50" value="<?php echo $apaiu_default_wide_angle_tags; ?>"></td>
		<td>If you want to add some tags based on WIDE ANGLE then put them here. <br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo use a standard lense add those tags</td>
		<td align="top"><input type="text" name="apaiu_default_standard_tags" size="50" value="<?php echo $apaiu_default_standard_tags; ?>"></td>
		<td>If you want to add some tags based on STANDARD then put them here. <br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo use a telephoto lense add those tags</td>
		<td align="top"><input type="text" name="apaiu_default_telephoto_tags" size="50" value="<?php echo $apaiu_default_telephoto_tags; ?>"></td>
		<td>If you want to add some tags based on TELEPHOTO then put them here. <br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo use a super telephoto lense add those tags</td>
		<td align="top"><input type="text" name="apaiu_default_super_telephoto_tags" size="50" value="<?php echo $apaiu_default_super_telephoto_tags; ?>"></td>
		<td>If you want to add some tags based on SUPER TELEPHOTO then put them here. <br/>
		</td>
	</tr>

	
	
	<tr>
		<td align="top">If photo is a square use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_square_tags" size="50" value="<?php echo $apaiu_default_square_tags; ?>"></td>
		<td>If you want to add some square tags then put them here. Eg : 6x6<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a square use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_square_categories" size="50" value="<?php echo $apaiu_default_square_categories; ?>"></td>
		<td>If you want to add some square categories then put them here. Eg : Square<br/>
		</td>
	</tr>

	
	<tr>
		<td align="top">If photo is a 24x36 format use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_24x36_tags" size="50" value="<?php echo $apaiu_default_24x36_tags; ?>"></td>
		<td>If you want to add some 24x36 tags then put them here. Eg : 24x36<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a 24x36 format use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_24x36_categories" size="50" value="<?php echo $apaiu_default_24x36_categories; ?>"></td>
		<td>If you want to add some 24x36 categories then put them here. Eg : 24x36<br/>
		</td>
	</tr>


	<tr>
		<td align="top">If photo is a 6x5 format use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_6x5_tags" size="50" value="<?php echo $apaiu_default_6x5_tags; ?>"></td>
		<td>If you want to add some 6x5 tags then put them here. Eg : 6x5<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a 6x5 format use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_6x5_categories" size="50" value="<?php echo $apaiu_default_6x5_categories; ?>"></td>
		<td>If you want to add some 6x5 categories then put them here. Eg : 6x5<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a 6x7 format use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_6x7_tags" size="50" value="<?php echo $apaiu_default_6x7_tags; ?>"></td>
		<td>If you want to add some 6x7 tags then put them here. Eg : 6x7<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a 6x7 format use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_6x7_categories" size="50" value="<?php echo $apaiu_default_6x7_categories; ?>"></td>
		<td>If you want to add some 6x7 categories then put them here. Eg : 6x7<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a 6x17 format use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_6x17_tags" size="50" value="<?php echo $apaiu_default_6x17_tags; ?>"></td>
		<td>If you want to add some 6x17 tags then put them here. Eg : 6x17, Panoramic, Medium Format<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a 6x17 format use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_6x17_categories" size="50" value="<?php echo $apaiu_default_6x17_categories; ?>"></td>
		<td>If you want to add some 6x17 categories then put them here. Eg : 6x17<br/>
		</td>
	</tr>
	
		<tr>
		<td align="top">If photo is a XPAN format use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_XPAN_tags" size="50" value="<?php echo $apaiu_default_XPAN_tags; ?>"></td>
		<td>If you want to add some XPAN tags then put them here. Eg : 24x65, XPAN, Panoramic<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a XPAN format use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_XPAN_categories" size="50" value="<?php echo $apaiu_default_XPAN_categories; ?>"></td>
		<td>If you want to add some XPAN categories then put them here. Eg : XPAN<br/>
		</td>
	</tr>

	
	
	<tr>
		<td align="top">If photo is a long exposure use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_long_exposure_tags" size="50" value="<?php echo $apaiu_default_long_exposure_tags; ?>"></td>
		<td>If you want to add some long exposure tags then put them here. Eg : Long Exposure<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a long exposure use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_long_exposure_categories" size="50" value="<?php echo $apaiu_default_long_exposure_categories; ?>"></td>
		<td>If you want to add some long exposure categories then put them here. Eg : Long Exposure<br/>
		</td>
	</tr>
	
	
	<tr>
		<td align="top">If photo is a portrait use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_portrait_tags" size="50" value="<?php echo $apaiu_default_portrait_tags; ?>"></td>
		<td>If you want to add some portrait tags then put them here. Eg : 3x2, Portrait<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is a landscape use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_landscape_tags" size="50" value="<?php echo $apaiu_default_landscape_tags; ?>"></td>
		<td>If you want to add some landscape tags then put them here. Eg : 3x2, Landscape, Urban Landscape<br/>
		</td>
	</tr>


	<tr>
		<td align="top">If photo is shot between 6am and noon use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_morning_tags" size="50" value="<?php echo $apaiu_default_morning_tags; ?>"></td>
		<td>If you want to add some morning tags then put them here. Eg : Morning<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is shot after noon and before 6pm use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_afternoon_tags" size="50" value="<?php echo $apaiu_default_afternoon_tags; ?>"></td>
		<td>If you want to add some afternoon tags then put them here. Eg : Afternoon<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is shot after 6pm and before 6am use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_night_tags" size="50" value="<?php echo $apaiu_default_night_tags; ?>"></td>
		<td>If you want to add some night tags then put them here. Eg : Night<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is during spring use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_spring_tags" size="50" value="<?php echo $apaiu_default_spring_tags; ?>"></td>
		<td>If you want to add some spring tags then put them here. Eg : Spring<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is during summer use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_summer_tags" size="50" value="<?php echo $apaiu_default_summer_tags; ?>"></td>
		<td>If you want to add some summer tags then put them here. Eg : Summer<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is during autumn use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_autumn_tags" size="50" value="<?php echo $apaiu_default_autumn_tags; ?>"></td>
		<td>If you want to add some autumn tags then put them here. Eg : Autumn<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is during winter use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_winter_tags" size="50" value="<?php echo $apaiu_default_winter_tags; ?>"></td>
		<td>If you want to add some winter tags then put them here. Eg : Winter<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is in colors use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_colors_tags" size="50" value="<?php echo $apaiu_default_colors_tags; ?>"></td>
		<td>If you want to add some colors tags then put them here. Eg : Colors<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is in colors use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_colors_categories" size="50" value="<?php echo $apaiu_default_colors_categories; ?>"></td>
		<td>If you want to add some colors categories then put them here. Eg : Colors<br/>
		</td>
	</tr>


	<tr>
		<td align="top">If photo is in analog colors use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_analog_colors_tags" size="50" value="<?php echo $apaiu_default_analog_colors_tags; ?>"></td>
		<td>If you want to add some analog colors tags then put them here. Eg : Color Film<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is in analog colors use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_analog_colors_categories" size="50" value="<?php echo $apaiu_default_analog_colors_categories; ?>"></td>
		<td>If you want to add some analog_colors categories then put them here. Eg : Color Film<br/>
		</td>
	</tr>



	
	
	<tr>
		<td align="top">If photo is in black and white use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_bw_tags" size="50" value="<?php echo $apaiu_default_bw_tags; ?>"></td>
		<td>If you want to add some black and white tags then put them here. Eg : Black and White<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is in black and white use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_bw_categories" size="50" value="<?php echo $apaiu_default_bw_categories; ?>"></td>
		<td>If you want to add some black and white categories then put them here. Eg : Black and White Photography<br/>
		</td>
	</tr>


	<tr>
		<td align="top">If photo is in analog black and white use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_analog_bw_tags" size="50" value="<?php echo $apaiu_default_analog_bw_tags; ?>"></td>
		<td>If you want to add some analog black and white tags then put them here. Eg : Black and White Film<br/>
		</td>
	</tr>

	<tr>
		<td align="top">If photo is in analog black and white use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_analog_bw_categories" size="50" value="<?php echo $apaiu_default_analog_bw_categories; ?>"></td>
		<td>If you want to add some analog black and white categories ID then put them here. Eg : Analog Black and White Photography<br/>
		</td>
	</tr>


	
	
	<tr>
		<td align="top">If photo is analog use those tags</td>
		<td align="top"><input type="text" name="apaiu_default_analog_tags" size="50" value="<?php echo $apaiu_default_analog_tags; ?>"></td>
		<td>If you want to add some analog tags then put them here. Eg : Film Photography, I Shoot Film, etc...<br/>
		</td>
	</tr>
	
	<tr>
		<td align="top">If photo is analog use those categories</td>
		<td align="top"><input type="text" name="apaiu_default_analog_categories" size="50" value="<?php echo $apaiu_default_analog_categories; ?>"></td>
		<td>If you want to add some analog categories then put them here. Eg : Analog Photography<br/>
		</td>
	</tr>
	
	
	</table>
	
	
    <p>
      <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
  </form>
</div>
<?php
}














## ===================================
## ### MENU LINK
## ===================================

function apaiu_menu_link() {
   add_options_page('Auto Post With Image Upload', 'Auto Post With Image Upload', 'manage_options',__FILE__,'apaiu_control');
}

  
## ===================================
## ### INIT FUNCTION
## ===================================

if(is_admin()){  
	add_action('admin_menu', 'apaiu_menu_link');	
	add_action('add_attachment', 'auto_post_after_image_upload'); // Wordpress Hook
	add_action('admin_init', 'apaiu_admin_init_actions');
	}
  // Plugin added to Wordpress plugin architecture


## ===================================
## ### UNINSTALL FUNCTION
## ===================================

function apaiu_uninstall() {
	if(!current_user_can('manage_options')) {
		die('Access Denied');
	}
	delete_option('auto_post_after_image_upload'); 
	return;
}


## ===================================
## ### ACTION + META LINKS
## ===================================

function apaiu_plugin_action_links($apaiu_action_links){
   $apaiu_action_links[] = '<a href="'. admin_url('options-general.php?page=auto-post-after-image-upload/auto_post_after_image_upload') .'">'. __('Settings') .'</a>';
   return $apaiu_action_links;
}

function apaiu_plugin_meta_links($apaiu_meta_links, $apaiu_file){
	global $apaiu_plugin_basename;

	if($apaiu_file == $apaiu_plugin_basename){
		$apaiu_meta_links[] = '<a href="https://wordpress.org/support/plugin/auto-post-after-image-upload">Support forum</a>';
		$apaiu_meta_links[] = '<a href="http://wordpress.org/extend/plugins/auto-post-after-image-upload/faq">FAQ</a>';
	}
	return $apaiu_meta_links;
}

function apaiu_admin_init_actions(){
	global $pagenow,
	$apaiu_plugin_basename;

	if($pagenow == 'plugins.php'){ //page plugins.php is being displayed
		add_filter('plugin_action_links_'. $apaiu_plugin_basename, 'apaiu_plugin_action_links', 10, 1);
		add_filter('plugin_row_meta', 'apaiu_plugin_meta_links', 10, 2);
	}
	
	
}


## =========================================================================
## ### READ EXIF FIELD
## =========================================================================

function apaiu_read_exif($file) {

	$meta=array();

	if ( is_callable('exif_read_data') ) {
		$exif = @exif_read_data( $file, 'ANY_TAG' );
		
		
			if (!empty($exif['GPSLatitude']))
				$meta['latitude'] = $exif['GPSLatitude'] ;
			if (!empty($exif['GPSLatitudeRef']))
				$meta['latitude_ref'] = trim( $exif['GPSLatitudeRef'] );
			if (!empty($exif['GPSLongitude']))
				$meta['longitude'] = $exif['GPSLongitude'] ;
			if (!empty($exif['GPSLongitudeRef']))
				$meta['longitude_ref'] = trim( $exif['GPSLongitudeRef'] );
			if (!empty($exif['ExposureBiasValue']))
				$meta['exposure_bias'] = trim( $exif['ExposureBiasValue'] );
			if (!empty($exif['Flash']))
				$meta['flash'] = trim( $exif['Flash'] );
			if (!empty($exif['Make']))
				$meta['make'] = trim( $exif['Make'] );
			if (!empty($exif['Model']))
				$meta['model'] = trim( $exif['Model'] );
			if (!empty($exif['Camera']))
				$meta['camera'] = trim( $exif['Camera'] );

			if (!empty($exif['FNumber']))
					$meta['aperture'] = trim( $exif['FNumber'] );
			
			if (!empty($exif['ApertureValue']))
					$meta['aperture'] = trim( $exif['ApertureValue'] );
			
			if (!empty($exif['ShutterSpeedValue']))
					$meta['shutter_speed'] = trim( $exif['ShutterSpeedValue'] );

			if ( is_callable('exif_get_float') )
			{
			
			
					
			}
			if (!empty($exif['ISOSpeedRatings']))
				$meta['iso'] = trim( $exif['ISOSpeedRatings'] );
			if (!empty($exif['FocalLength']))
				$meta['focal_length'] = trim( $exif['FocalLength'] );
			if (!empty($exif['DateTimeOriginal']))
				$meta['created_timestamp'] = trim( $exif['DateTimeOriginal'] );
			if (!empty($exif['Copyright']))
				$meta['copyright'] = trim( $exif['Copyright'] );
			
			if (!empty($exif['Credit']))
				$meta['credit'] = trim( $exif['Credit'] );
		
		
			list($w, $h) = getimagesize($file);
	
			if (!empty($h))
				$meta['height'] = trim( $h );
			if (!empty($w))
				$meta['width'] = trim( $w );
	
	return $meta;
	}
}




## =========================================================================
## ### HOOKS
## =========================================================================

if ( function_exists('register_activation_hook') ) {
    register_activation_hook(__FILE__, 'apaiu_install_plugin');
}


if ( function_exists('register_uninstall_hook') ) {
	register_uninstall_hook(__FILE__, 'apaiu_uninstall');
}


