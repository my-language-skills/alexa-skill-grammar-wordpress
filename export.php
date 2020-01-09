<?php

/**
 * @package alexa_meta_boxes
 */

 
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

class Chapters
{
    public function __construct()
    {
        add_action('export_book',array($this,'export_book_to_JSON'));
    }
    public function export_book_to_JSON()
    {
        //every position is a different chapter with special fields...
        $json_content = array();

        
        $ids=0;
        $chapter_names=array();
        $chapter_names_json;
        $urls = array();
        //$chapters = array();
        $all_args=array();
        $query= new WP_Query(array(
            'post_type' => 'chapter',
            'post_status' => 'publish',
            'post_per_page' => -1,
            'order' => 'ASC',));
        while ($query->have_posts())
        {
            $query->the_post();
            $post_id= get_the_ID();
            $post_title = get_the_title();
            
            if (!(isset($_SESSION['chapter_title'])))
                $_SESSION['chapter_title']=$post_title;

            $chapter_name=strtolower($post_title);
            $json_tmp = str_replace(' ','_',$post_title);
            $chapter_name_json=strtoupper($json_tmp);
            //Getting meta data from wp_db
            $url_meta = get_post_meta( $post_id,'picture_url',true);
            $basic_meta = get_post_meta($post_id,'basic_info',true);
            $more_meta = get_post_meta($post_id,'more_info',true);
            $example_meta = get_post_meta($post_id,'examples',true);
            //if (!(empty($url_meta) || empty($basic_meta) || empty($more_meta)))
            //{
                //create arg array with chapter data for review before exportation
                $args = array(
                    'chapter_title' => $post_title,
                    'basic_info'    => $basic_meta,
                    'more_info'     => $more_meta,
                    'examples'      => $example_meta,
                    'post_id'       =>$post_id
                );
                array_push($all_args,$args);
                array_push($urls,$url_meta);
                
                //$chapters[$ids]=array($chapter_name_json,$basic_meta,$more_meta,$example_meta);
                //adding to the strings for the Json export final file.
               
                //New test method for export file
                $new_entry = ["chapter_id" => $ids,"chapter_name" =>$chapter_name,"chapter_json" =>$chapter_name_json,"url" => $url_meta,"basic_info" =>$basic_meta,"more_info" =>$more_meta,"example"=>$example_meta];
                $new_entry['basic_info'] = str_replace("'","APOSTROPHE",$new_entry['basic_info']);
                $new_entry['more_info'] = str_replace("'","APOSTROPHE",$new_entry['more_info']);
                $new_entry['example'] = str_replace("'","APOSTROPHE",$new_entry['example']);
                array_push($json_content,$new_entry);
               
                $ids++;
            //}
            }
            if (count($all_args)>0)
                {
                    do_action('add_display',$all_args);
                    //do_action('add_display2');
                }
           
            
            $temp = json_encode($json_content);
            ?><input type='hidden' name='export_file' id='export_file' value='<?php echo $temp;?>' /> <?php
            wp_reset_query();
    }
}
$chapters = new Chapters;
?>