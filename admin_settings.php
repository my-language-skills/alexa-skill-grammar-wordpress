<?php

/**
 * @package alexa_meta_boxes
 */
//gather all meta data to be exported from start..

//standard line to check if path is correct else plugin dies
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );
class AdminSettings
{   

    public function __construct()
    {
        
        add_action('add_display2',array($this,'amb_page_display'));
        add_action('save_changes',array($this,'amb_save_meta_changes'));
       
    }

    public function amb_page_display()
    {
        do_action('amb_export_book');
       //section markers option.
        $section_markers = get_option('amb_section_markers',"false");
        if ($section_markers == "false")
        {
            $section_markers= array();
            $section_markers['amb_basic_info'] = 'div id="extension" class="box" title="Extension"';
            $section_markers['amb_example'] = 'div id="example" class="box" title="Example"';
            $section_markers['amb_more_info'] = 'div id="use" class="box" title="Use"';
            update_option('amb_section_markers',$section_markers);
        }

        if (isset($_POST['amb_markers_update']) && !empty($_POST['amb_markers_update']))
        {
            $markers = explode(",",$_POST['amb_markers_update']);
            $section_markers['amb_basic_info'] = str_replace('\"','"',$markers[0]);
            $section_markers['amb_more_info'] = str_replace('\"','"',$markers[1]);
            $section_markers['amb_example']= str_replace('\"','"',$markers[2]);
            update_option('amb_section_markers',$section_markers);
        }
        //hidden option
        $url_hidden = get_option('amb_show_picture',false);
        if ($url_hidden == false)
        {
            update_option('amb_show_picture',"Hide Pictures");
        }
        if (isset($_POST['amb_show_picture']) && !empty($_POST['amb_show_picture']))
        {
            update_option('amb_show_picture',$_POST['amb_show_picture']);
        }


        //Char translations options
        $char_translations = get_option('amb_char_translations',false);
        
        if ($char_translations == false)
        {
            $char_translations = array();
        }
        
        //checks if requested to add a new string - to be translated
        if (isset($_POST['amb_new_char_hidden']) && !empty($_POST['amb_new_char_hidden']) )
        {
            $new_slot = array();
            $new_slot['amb_char'] = $_POST['amb_new_char_hidden'];
            array_push($char_translations,$new_slot);
            update_option('amb_char_translations',$char_translations);
        }//checks if requested to add a new string for a translation
        else if(isset($_POST['amb_new_translation_hidden']) && !empty($_POST['amb_new_translation_hidden']))
        {
            if (isset($_POST['amb_new_transaltion_value']))
            {
                $translations= explode(",",$_POST['amb_new_transaltion_value']);
                for ($i=0;$i<count($char_translations);$i++)
                {
                    $char_translations[$i]['amb_translation']=$translations[$i];   
                }
                update_option('amb_char_translations',$char_translations);
            }
            
        }//checks if requested to delete a row of translation
        else if(isset($_POST['amb_delete_translation']) && !empty($_POST['amb_delete_translation']))
        {
            $new_translations = array();
            
            for ($i=0; $i<count($char_translations);$i++)
            { 
                $temp ="amb_".$i." ";
                $type= gettype($_POST['amb_delete_translation']);
                if($temp!==$_POST['amb_delete_translation'])
                {
                    array_push($new_translations,$char_translations[$i]);
                }
            }
            update_option('amb_char_translations',$new_translations);

        }

        //check if blocked or not
        if (isset($_POST['amb_blocked']) && !empty($_POST['amb_blocked']))
        {
            update_option('amb_blocked',$_POST['amb_blocked']);
            
            $query= new WP_Query(array(
                'post_type' => 'chapter',
                'post_status' => 'publish',
                'post_per_page' => -1,
                'order' => 'ASC',));
            if ($_POST['amb_blocked']=="Unblock All Chapters")
            {
                while ($query->have_posts())
                {
                    $query->the_post();
                    $post_id= get_the_ID();
                    $post_title = get_the_title();
                    update_post_meta($post_id,'amb_block_tag',"Unblock");
                }
            }
            else
            {
                while ($query->have_posts())
                {
                    $query->the_post();
                    $post_id= get_the_ID();
                    $post_title = get_the_title();
                    update_post_meta($post_id,'amb_block_tag',"Block");
                }
                wp_reset_query();
            }
        }
        $option_block = get_option('amb_blocked',false);
        $char_translations = get_option('amb_char_translations',false);
        $url_hidden = get_option('amb_show_picture',"Hide Pictures");
        echo '<div class="amb_container-admin">';
            echo '<h1 id="amb_title">Alexa Character Translation</h1>';
                echo '<div class="amb_container">';
                    echo '<form name="amb_input-fields" id="amb_input-fields" action="?page=alexa_input" method="post">';
                        echo '<div class = "Field-container">';
                            echo '<div class="amb_row">';
                                echo '<div class="amb_column-header-buttons">';
                                        echo '<input id="amb_export" name="amb_export" type="button" value="Export Content"/>';
                                        $button_name;
                                        if ($option_block == false)
                                            $button_name = "Block All Chapters";
                                        else
                                            $button_name =$option_block;
                                        echo '<input id="amb_block_all" name="amb_block_all" type="button" value="'.$button_name.'"/>';
                                        echo '<input id="amb_show_url" name="amb_show_url" type="button" value="'.$url_hidden.'"/>';
                                echo '</div>';
                                echo '<div class="amb_column-char">';
                                    echo '<h2 id="amb_special">Special Characters</h2>';
                                echo '</div>';
                                echo '<div class="amb_column-translate">';
                                    echo '<h2 id="amb_special">Translations</h2>';
                                echo '</div>';
                            echo '</div>';
                        if ($char_translations!=false)
                        { 
                            for ($i=0;$i<count($char_translations);$i++)      
                            {   $current_char = $char_translations[$i];
                                $translation="";
                                echo '<div class="amb_container-fields">';
                                    echo '<div class = "amb_row">';
                                        echo '<div class="amb_column-char">';
                                            echo '<div class="field">';
                                                echo '<textarea class="amb_input-field" readOnly>'.$current_char['amb_char'].'</textarea>';
                                            echo '</div>';
                                        echo '</div>';
                                        echo '<div class="amb_column-translate">';
                                            echo '<div class="field">';
                                            if (!empty($current_char['amb_translation']))
                                                $translation=$current_char['amb_translation'];
                                                if ($option_block!="false" && $option_block=="Unblock All Chapters")
                                                    echo '<textarea class="amb_input-field2" readOnly>'.$translation.'</textarea>';
                                                else
                                                    echo '<textarea class="amb_input-field2" >'.$translation.'</textarea>';
                                            echo '</div>';
                                        echo '</div>';
                                        echo '<div class="amb_column-delete">';
                                            $name=$i;
                                            if ($option_block!="false" && $option_block=="Unblock All Chapters")
                                                echo '<input type="button" id="amb_delete_row" name="amb_'.$name.'" value="Delete" disabled>';
                                            else
                                                echo '<input type="button" id="amb_delete_row" name="amb_'.$name.'" value="Delete">';
                                        echo '</div>';
                                    echo '</div>';
                                echo '</div>';
                            }
                        }
                        echo '</div>';
                        echo '<div class="amb_row" >';
                            echo '<div class="amb_column-translate-buttons">';
                                echo '<input id="amb_new_char" name="amb_new_char" class="amb_input_text" type="text" value=""/>';
                                echo '<input id="amb_add_new_char" name="amb_add_new_char" type="button" value="Add New"/>';
                                echo '<input id="amb_transl_update" name="amb_transl_update" type="button" value="Update Translations"/>';
                                
                            echo '</div>';
                        echo '</div>';
                        echo '<input type="hidden" id="amb_new_char_hidden" name="amb_new_char_hidden"/>';
                        echo '<input type="hidden" id ="amb_new_transaltion_value" name="amb_new_transaltion_value" />';
                        echo '<input type="hidden" id="amb_new_translation_hidden" name="amb_new_translation_hidden"/>';
                        echo '<input type="hidden" id="amb_delete_translation" name="amb_delete_translation"/>';
                        echo '<input type="hidden" id="amb_show_picture" name="amb_show_picture" value="'.$url_hidden.'"/>';
                        ?><input type='hidden' id='amb_blocked' name='amb_blocked' value='<?php echo $option_block;?>'/><?php
                    echo '</form>';
                echo '</div>';
            //Next box for selecting sections for each information part..
            echo '<h1 id="amb_title2">Alexa Section Selection</h1>';
                echo '<div class="amb_container">';
                    echo '<form name="amb_section-fields" id="amb_section-fields" action="?page=alexa_input" method="post">';
                        echo '<div class = "Field-container" >';
                            //textarea rows..
                            //BASIC INFO
                           echo '<div class="amb_container-fields">';
                                echo '<div class = "amb_row">';
                                    echo '<div class="field">';
                                        echo '<label class="amb_label" style="font-size:15px;" for="amb_basic_info_section">Basic Info:</label>';
                                        echo '<textarea class="amb_input-section" id="amb_basic_info_section" name="amb_basic_info_section" placeholder="Enter characters for Basic Info Begin Section"><'.$section_markers['amb_basic_info'].'></textarea>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div';
                            //MORE INFO
                            echo '<div class="amb_container-fields">';
                                echo '<div class = "amb_row">';
                                    echo '<div class="field">';
                                        echo '<label class="amb_label" style="font-size:15px;" for="amb_more_info_section">More Info:</label>';
                                        echo '<textarea class="amb_input-section" id="amb_more_info_section" name="amb_more_info_section" placeholder="Enter characters for More Info Begin Section"><'.$section_markers['amb_more_info'].'></textarea>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div';
                            //EXAMPLES
                            echo '<div class="amb_container-fields">';
                                echo '<div class = "amb_row">';
                                    echo '<div class="field">';
                                        echo '<label class="amb_label" style="font-size:15px;" for=amb_"example_section">Example:</label>';
                                        echo '<textarea class="amb_input-section" id="amb_example" name="amb_example" placeholder="Enter characters for Examples Begin Section"><'.$section_markers['amb_example'].'></textarea>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div';
                        echo '</div>';
                        //BUTTONS
                        echo '<div class="amb_row">';
                            echo '<div class="amb_column-translate-buttons">';
                                echo '<input id="amb_save_sections" name="amb_save_sections" type="button" value="Save"/>';
                            echo '</div>';
                        echo '</div>';
                        //HiDDENS
                        echo '<input type="hidden" id="amb_markers_update" name="amb_markers_update"/>';
                    echo '</form>';                 
                echo '</div>';
        echo '</div>';

        
                
    } 

   

}

$admin = new AdminSettings;



 ?>