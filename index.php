<?php 
/*
Plugin Name: MediaRSS external gallery
Plugin URI: 
Description: Generates a thumbnails gallery from a media rss feed url. 
Version: 0.1
Author: Marco Constâncio
Author URI: http://www.betasix.net
*/


function generate_meg_gallery($meg_param){
	
$all_content ="No Media Rss url set.<br>";

if(isset($meg_param['url'])){
	require_once (ABSPATH . WPINC . '/class-simplepie.php');

	$feed = new SimplePie();
	$feed->set_feed_url($meg_param['url']);
	$feed->set_cache_location('./wp-content/cache');
	$feed->enable_cache(true);
	$feed->init();
	
	$items = $feed->get_items(0,$meg_param["max_items"]);
	
	$all_content ="";
	$classes = array("img"=>"","item"=>"");
	//DEFAUTL VALUES
	if(!isset($meg_param["columns"])){ $meg_param["columns"] = 3; }
	
	if(isset($meg_param["items"])){ 
		$meg_param["items"] = explode(",",$meg_param["items"]); 
	}else{ 
		$meg_param["items"] = array("title_feed_link","image_file_link"); 
	} 

	if(isset($meg_param["image_border"])){ $classes["img"] = " meg_border"; }
	if(isset($meg_param["item_border"])){ $classes["item"] = " meg_border"; }
	
	$meg_param["img_src_param"] = "";
	$meg_param["img_src"] = "";
	 	 
	//IMAGE CACHE RESIZE AND CACHE
	if(isset($meg_param["width"]) || isset($meg_param["height"])){ 
		//ADD RESQUESTED DOMAIN TO THE ALLOWED SITES CONFIG FILE IF NECESSARY 
		$allowedSites = read_config_file();
		$feed_domain = get_domain($meg_param["url"]);
		if(!in_array($feed_domain,$allowedSites)){
			$allowedSites[]=$feed_domain;
			write_config_file($allowedSites);
		}
		
		$meg_param["img_src"].= home_url()."/wp-content/plugins/mediarss-external-gallery/timthumb.php?src=";
		if(isset($meg_param["width"])){ 
			$meg_param["img_src_param"].="&w=".$meg_param["width"];
		}
		if(isset($meg_param["height"])){ 
			$meg_param["img_src_param"].="&h=".$meg_param["height"];
		}	
	}

	$i=0;
	
	$content = array();
	$line = array();
	foreach ($items as $item){
		$item_data ="";
		foreach($meg_param["items"] as $item_type){
				$item_data.= get_feed_data($item->get_enclosure(),$item,$meg_param,$item_type,$classes)."<br>";			
		}
	
		$line[] = "<div class='meg_item ".$classes["item"]."'>".$item_data."</div>";

		$i++;
		if($i==$meg_param["columns"]){
			$content[] = $line;
			unset($line);
			$i=0;
		}
	}
	
	if($i!=0){ $content[] = $line; }

	
	$all_content.="<div class='meg_table'>";
	foreach($content as $line){
		$all_content.= "<div class='meg_line'>";
		$all_content.= "<div class='meg_cell' style='width:".intval(100/($meg_param["columns"]))."%'>".implode("</div><div class='meg_cell' style='width:".intval(100/($meg_param["columns"]))."%'>",$line)."</div>";
		$all_content.= "<div class='clear'></div></div>";
	}
	$all_content.="</div>";
	
	
	$all_content.="<script language='javascript' type='text/javascript' defer>
					<!--
					jQuery('.meg_line').each(function() {							
						var tallest_height = 0;
						var current_height = 0;			
								
						jQuery(this).find('.meg_cell .meg_item').each(function() {	
							current_height = jQuery(this).height();
							if(current_height > tallest_height){
								tallest_height=current_height ;
							}
						});
						
						jQuery(this).find('.meg_cell .meg_item').each(function() {
							jQuery(this).height(tallest_height);
						});
					});

					// -->
					</script>";
	
	return $all_content; 
	}
}

#GETS A SPECIFIC FEED DATA
function get_feed_data($enclosure,$item,$meg_param,$option,$classes){	
	switch ($option) {
		case "image":
			return "<img src='".$meg_param["img_src"].$enclosure->get_thumbnail().$meg_param["img_src_param"]."' alt='".$enclosure->get_title()."' class='wp-post-img meg-img ".$classes["img"]."' />&nbsp;";
		case "image_thickbox":
			return "<a href='".$enclosure->get_link()."' class='thickbox'><img src='".$meg_param["img_src"].$enclosure->get_thumbnail().$meg_param["img_src_param"]."' alt='".$meg_param["img_src"].$enclosure->get_thumbnail()."' class='wp-post-img meg-img ".$classes["img"]."' /></a>&nbsp;";
		case "image_file_link":
			return "<a href='".$enclosure->get_link()."'><img src='".$meg_param["img_src"].$enclosure->get_thumbnail().$meg_param["img_src_param"]."' alt='".$meg_param["img_src"].$enclosure->get_thumbnail()."' class='wp-post-img ".$classes["img"]."' /></a>&nbsp;";
		case "image_feed_link":
			return  "<a href='".$item->get_permalink()."'><img src='".$meg_param["img_src"].$enclosure->get_thumbnail().$meg_param["img_src_param"]."' alt='".$enclosure->get_title()."' class='wp-post-img meg-img ".$classes["img"]."' /></a>&nbsp;";

		case "title":
			return SimplePie_Misc::htmlspecialchars_decode($enclosure->get_title(),ENT_COMPAT)."&nbsp;";
		case "title_file_link":
			return "<a href='".$enclosure->get_link()."'>".SimplePie_Misc::htmlspecialchars_decode($enclosure->get_title(),ENT_COMPAT)."</a>&nbsp;";
		case "title_feed_link":
			return "<a href='".$item->get_permalink()."'>".SimplePie_Misc::htmlspecialchars_decode($enclosure->get_title(),ENT_COMPAT)."</a>&nbsp;";
							
		case "description":
			return strip_tags(SimplePie_Misc::htmlspecialchars_decode($enclosure->get_description(),ENT_COMPAT))."&nbsp;";	
		case "description_file_link":
			return "<a href='".$enclosure->get_link()."'>".strip_tags(SimplePie_Misc::htmlspecialchars_decode($enclosure->get_description(),ENT_COMPAT))."</a>&nbsp;";
		case "description_feed_link":
			return "<a href='".$item->get_permalink()."'>".strip_tags(SimplePie_Misc::htmlspecialchars_decode($enclosure->get_description(),ENT_COMPAT))."</a>&nbsp;";	      
	}
}

#EXTRACT DOMAIN FROM A FULL URL
function get_domain($full_url=""){
	preg_match("/^(http:\/\/)?([^\/]+)/i", $full_url, $domain_only);
	return $domain_only[2]; 
}

#READS THE LIST OF ALLOWED SITES CONFIG FILE
function read_config_file(){
	$as_file = ABSPATH . 'wp-content/plugins/mediarss-external-gallery/allowedsites.json';
	$handle = fopen($as_file, "rb");
	$allowedsites = json_decode(fread($handle,filesize($as_file)));
	fclose($handle);
	return $allowedsites;
}

#WRITES A LIST OF ALLOWED SITES TO A CONFIG FILE
function write_config_file($content){
	$as_file = ABSPATH . 'wp-content/plugins/mediarss-external-gallery/allowedsites.json';
	$fp = fopen($as_file ,'w');
	fwrite($fp,json_encode($content));
	fclose($fp);
}

wp_register_style( 'meg_style', WP_PLUGIN_URL . '/mediarss-external-gallery/meg_style.css' );
wp_enqueue_style('meg_style');

wp_enqueue_style('thickbox');
wp_enqueue_script('thickbox');

add_shortcode('meg_gallery ', 'generate_meg_gallery');
