<?php

/**
 * @package alexa_meta_boxes
 */

 
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

class Chapters
{
    public function __construct()
    {
        add_action('amb_export_book',array($this,'amb_export_book_to_JSON'));
    }
    public function amb_export_book_to_JSON()
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
            
            if (!(isset($_SESSION['amb_chapter_title'])))
                $_SESSION['amb_chapter_title']=$post_title;

            $chapter_name=strtolower($post_title);
            $json_tmp = str_replace(' ','_',$post_title);
            $chapter_name_json=strtoupper($json_tmp);

            //Getting meta data from wp_db
            $url_meta = get_post_meta( $post_id,'amb_picture_url',true);
            $basic_meta = get_post_meta($post_id,'amb_basic_info',true);
            $more_meta = get_post_meta($post_id,'amb_more_info',true);
            $example_meta = get_post_meta($post_id,'amb_examples',true);

            //create arg array with chapter data for review before exportation
            $args = array(
                'amb_chapter_title' => $post_title,
                'amb_basic_info'    => $basic_meta,
                'amb_more_info'     => $more_meta,
                'amb_examples'      => $example_meta,
                'amb_post_id'       =>$post_id
            );
            array_push($all_args,$args);
            array_push($urls,$url_meta);
            
            
            //adding the correct format to the information before the transformation
            $new_entry = ["amb_chapter_id" => $ids,"amb_chapter_name" =>$chapter_name,"amb_chapter_json" =>$chapter_name_json,"amb_url" => $url_meta,"amb_basic_info" =>$basic_meta,"amb_more_info" =>$more_meta,"amb_example"=>$example_meta];
            $new_entry['amb_basic_info'] = str_replace("'","APOSTROPHE",$new_entry['amb_basic_info']);
            $new_entry['amb_more_info'] = str_replace("'","APOSTROPHE",$new_entry['amb_more_info']);
            $new_entry['amb_example'] = str_replace("'","APOSTROPHE",$new_entry['amb_example']);

            if (empty($new_entry['amb_basic_info']) || empty($new_entry['amb_more_info']) || empty($new_entry['amb_example']) )
                echo '<script>console.log("skipped chapter: '.$new_entry['amb_chapter_id'].', not valid information");</script>';
            else
                array_push($json_content,$new_entry);
            $ids++;
        }
            if (count($all_args)>0)
                {
                    do_action('add_display',$all_args);
                }
           
            
            $temp = json_encode($json_content);
            //sending the final string to admin_scripts to create the javascript content file.
            ?><input type='hidden' name='amb_export_file' id='amb_export_file' value='<?php echo $temp;?>' /> <?php
            wp_reset_query();
    }
}
$chapters = new Chapters;
?>