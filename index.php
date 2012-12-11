<?php
/*
Plugin Name: MediaRSS external gallery
Plugin URI:
Description: Generates a thumbnails gallery from a media rss feed url.
Version: 0.4.1
Author: Marco Constâncio
Author URI: http://www.betasix.net
*/

function generate_meg_gallery($meg_param){

$all_content ="No Media Rss url set.<br>";

if(isset($meg_param['url'])){
	require_once (ABSPATH . WPINC . '/class-simplepie.php');

	$feed = new SimplePie();
	$feed->set_feed_url($meg_param['url']);
	$cache_dir = './wp-content/plugins/mediarss-external-gallery/cache'; 
	
	if(!is_dir($cache_dir)){
		@mkdir($cache_dir, 0755);
	}
	
	if(is_writable($cache_dir)){
		$feed->set_cache_location('./wp-content/plugins/mediarss-external-gallery/cache');
		$feed->enable_cache(true);
	}else{
		$feed->enable_cache(false);
	}
	
	$feed->init();
    
    if (isset($meg_param["max_items"])){
        if(isset($meg_param["start_item"])){
            $items = $feed->get_items($meg_param["start_item"],$meg_param["max_items"]);
        }else{
            $items = $feed->get_items(0,$meg_param["max_items"]);
        }
    }else{
        if(isset($meg_param["start_item"])){
            $items = $feed->get_items($meg_param["start_item"]);
        }else{
            $items = $feed->get_items(0);
        }        
    }
    
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
	if(is_writable($cache_dir) && extension_loaded('gd')){
		if(isset($meg_param["width"]) || isset($meg_param["height"])){
			$meg_param["img_src"].= home_url()."/wp-content/plugins/mediarss-external-gallery/timthumb.php?src=";
			if(isset($meg_param["width"])){
				$meg_param["img_src_param"].="&w=".$meg_param["width"];
			}
			if(isset($meg_param["height"])){
				$meg_param["img_src_param"].="&h=".$meg_param["height"];
			}
		}
	}
	

	$i=0;

	$content = array();
	$line = array();
    
    if(isset($meg_param["pagination"])){
		if(!isset($meg_param["max_pag_items"])){
			$meg_param["max_pag_items"]=6;
		}
        $pag_table_item = 0;
        $pag_table = array();
        $pag_tables = array();
    }    
	
    foreach ($items as $item){
		$item_data ="";
		foreach($meg_param["items"] as $item_type){
				$item_data.= get_feed_data($item->get_enclosure(),$item,$meg_param,$item_type,$classes)."<br>";
		}

		$line[] = "<div class='meg_item ".$classes["item"]."'>".$item_data."</div>";
		$i++;

        if(isset($meg_param["pagination"])){
            $pag_table_item++;
            $pag_table[] = "<div class='meg_item ".$classes["item"]."'>".$item_data."</div>";
            
            if($pag_table_item == $meg_param["max_pag_items"]){
                $pag_table_item = 0;
                $pag_tables[] = $pag_table;
                $pag_table = array();
            }
        }
        
 		if($i==$meg_param["columns"]){
			$content[] = $line;
			unset($line);
			$i=0;
		}       
	}

	if($i!=0){ $content[] = $line; }

    if(isset($meg_param["pagination"])){
        $all_content.="<div id='meg_pagination_bar' class='meg_pagination_bar'></div>";

        $all_content.="<ul id='meg_pages' style='list-style: none; margin-left:0px;'>";
        foreach($pag_tables as $pag_table){    
            $all_content.="<li><div class='meg_table'>";
            $i=0;
            foreach($pag_table as $record){
                if($i==0){
                    $all_content.= "<div class='meg_line'>";
                }
               
                $all_content.="<div class='meg_cell' style='width:".intval(100/($meg_param["columns"]))."%'>".$record."</div>";
                $i++;
                if($i==$meg_param["columns"]){
                    $all_content.= "</div>"; $i=0;
                }
                
            }
            $all_content.="<div class='clear'></div></div></li>";
        }
        $all_content.="</ul>";
        
    }else{
        $all_content.="<div class='meg_table'>";
        foreach($content as $line){
            $all_content.= "<div class='meg_line'>";
            $all_content.= "<div class='meg_cell' style='width:".intval(100/($meg_param["columns"]))."%;'>".implode("</div><div class='meg_cell' style='width:".intval(100/($meg_param["columns"]))."%'>",$line)."</div>";
            $all_content.= "<div class='clear'></div></div>";
        }
        $all_content.="</div>";
    }

	$all_content.="<script language='javascript' type='text/javascript'>
					<!--
					jQuery(window).load(function(){
       
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

                        });";
    
    if(isset($meg_param["pagination"])){                 
        $all_content.="
                    jQuery('div.meg_pagination_bar').jPages({
                        containerID: 'meg_pages',
                        perPage      : 1
                    });
                    ";
    }
    
	$all_content.="
					});
					// -->
					</script>";
    
    if(isset($meg_param["pagination"])){
        $all_content.="<style type='text/css'>
                        .meg_pagination_bar { margin:15px 0; }
                        .meg_pagination_bar a { cursor:pointer; margin:0 5px; }
                        .meg_pagination_bar a:hover { background-color:#222; color:#fff; }
                        .meg_pagination_bar a.jp-previous { margin-right:15px; }
                        .meg_pagination_bar a.jp-next { margin-left:15px; }
                        .meg_pagination_bar a.jp-current,a.jp-current:hover { color:#FF4242; font-weight:bold; }
                        .meg_pagination_bar a.jp-disabled,a.jp-disabled:hover { color:#bbb; }
                        .meg_pagination_bar a.jp-current,a.jp-current:hover,.meg_pagination_bar a.jp-disabled,a.jp-disabled:hover { cursor:default; background:none; }
                        .meg_pagination_bar span { margin: 0 5px; }
                       </style>";
    }
    
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
			return meg_htmlspecialchars_decode($enclosure->get_title(),ENT_COMPAT)."&nbsp;";
		case "title_file_link":
			return "<a href='".$enclosure->get_link()."'>".meg_htmlspecialchars_decode($enclosure->get_title(),ENT_COMPAT)."</a>&nbsp;";
		case "title_feed_link":
			return "<a href='".$item->get_permalink()."'>".meg_htmlspecialchars_decode($enclosure->get_title(),ENT_COMPAT)."</a>&nbsp;";

		case "description":
			return strip_tags(meg_htmlspecialchars_decode($enclosure->get_description(),ENT_COMPAT))."&nbsp;";
		case "description_file_link":
			return "<a href='".$enclosure->get_link()."'>".strip_tags(meg_htmlspecialchars_decode($enclosure->get_description(),ENT_COMPAT))."</a>&nbsp;";
		case "description_feed_link":
			return "<a href='".$item->get_permalink()."'>".strip_tags(meg_htmlspecialchars_decode($enclosure->get_description(),ENT_COMPAT))."</a>&nbsp;";
	}
}

function meg_htmlspecialchars_decode($string, $quote_style) {
  if (function_exists('htmlspecialchars_decode')) {
    return htmlspecialchars_decode($string, $quote_style);
  }else{
    return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
  }
}

# Added because version jquery > 1.8 was causing "Syntax error, unrecognized expression: &#8592; previous" errors
function my_init() {
	if (!is_admin()) { 
		wp_deregister_script('jquery'); 
		
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', false, '1.7.2'); 
		wp_enqueue_script('jquery');
	}
}
add_action('init', 'my_init');

# Added remove wordpress warnings
function load_scripts(){
	wp_register_style('meg_style', WP_PLUGIN_URL . '/mediarss-external-gallery/meg_style.css' );
	wp_register_script('jPages', WP_PLUGIN_URL . '/mediarss-external-gallery/jPages.min.js');

	wp_enqueue_style('thickbox');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jPages');
	wp_enqueue_style('meg_style');	
}

add_action('wp_head', 'load_scripts'); 
add_shortcode('meg_gallery', 'generate_meg_gallery');