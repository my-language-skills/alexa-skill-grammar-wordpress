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
        
        add_action('add_display2',array($this,'page_display'));
        add_action('save_changes',array($this,'save_meta_changes'));
        //hidden input field to get the path to js.
        //$path = plugin_dir_path( __FILE__).'admin_settings.php';
        //echo '<input id="file_url_path" name="file_url_path" type="hidden" value="'.$path.'"/>';
    }

    public function page_display()
    {
        
        do_action('export_book');
       //section markers option.
        $section_markers = get_option('section_markers',"false");
        if ($section_markers == "false")
        {
            $section_markers= array();
            $section_markers['basic_info'] = '<div id="extension" class="box" title="Extension">';
            $section_markers['example'] = '<div id="example" class="box" title="Example">';
            $section_markers['more_info'] = '<div id="use" class="box" title="Use">';
            /* $section_markers['basic_info']['phrase'] = '<div id="extension" class="box" title="Extension">';
            $section_markers['basic_info']['begin'] = 'BEGININTRO';
            $section_markers['basic_info']['end'] = 'ENDINTRO';
            $section_markers['example']['phrase'] = '<div id="example" class="box" title="Example">';
            $section_markers['example']['begin'] = 'BEGINEXAMPLE';
            $section_markers['example']['end'] = 'ENDEXAMPLE';
            $section_markers['more_info']['phrase'] = '<div id="use" class="box" title="Use">';
            $section_markers['more_info']['begin'] = 'BEGINUSE';
            $section_markers['more_info']['end'] = 'ENDUSE'; */
            update_option('section_markers',$section_markers);
        }

        if (isset($_POST['markers_update']) && !empty($_POST['markers_update']))
        {
            $markers = explode(",",$_POST['markers_update']);
            $section_markers['basic_info'] = str_replace('\"','"',$markers[0]);
            $section_markers['more_info'] = str_replace('\"','"',$markers[1]);
            $section_markers['example']= str_replace('\"','"',$markers[2]);
            /* 
            $section_markers['basic_info']['phrase'] = str_replace('\"','"',$markers[0]);
            $section_markers['basic_info']['begin'] = $markers[1];
            $section_markers['basic_info']['end'] = $markers[2];
            $section_markers['more_info']['phrase'] = str_replace('\"','"',$markers[3]);
            $section_markers['more_info']['begin'] = $markers[4];
            $section_markers['more_info']['end'] = $markers[5];
            $section_markers['example']['phrase'] = str_replace('\"','"',$markers[6]);
            $section_markers['example']['begin'] = $markers[7];
            $section_markers['example']['end'] = $markers[8]; */
            update_option('section_markers',$section_markers);
        }
        //hidden option
        $url_hidden = get_option('show_picture',"false");
        if ($url_hidden == "false")
        {
            update_option('show_picture',"Hide Pictures");
        }
        if (isset($_POST['show_picture']) && !empty($_POST['show_picture']))
        {
            update_option('show_picture',$_POST['show_picture']);
        }

        //Char translations options
        $char_translations = get_option('char_translations',"false");
        if ($char_translations == "false")
        {
            $char_translations = array();
        }
        if (isset($_POST['new_char_hidden']) && !empty($_POST['new_char_hidden']) )
        {
            $new_slot = array();
            $new_slot['char'] = $_POST['new_char_hidden'];
            array_push($char_translations,$new_slot);
            update_option('char_translations',$char_translations);
        }
        else if(isset($_POST['new_translation_hidden']) && !empty($_POST['new_translation_hidden']))
        {/* 
            echo '<script>console.log("'.$_POST['new_translation_hidden'].'");</script>';
            for ($i=0;$i<count($char_translations);$i++)
                echo '<script>console.log("'.$_POST['new_translation_value']; */
            if (isset($_POST['new_transaltion_value']))
            {
                $translations= explode(",",$_POST['new_transaltion_value']);
                for ($i=0;$i<count($char_translations);$i++)
                {
                    $char_translations[$i]['translation']=$translations[$i];   
                }
                update_option('char_translations',$char_translations);
            }
            
        }
        else if(isset($_POST['delete_translation']) && !empty($_POST['delete_translation']))
        {
            $new_translations = array();
            
            for ($i=0; $i<count($char_translations);$i++)
            { 
                $temp =$i." ";
                $type= gettype($_POST['delete_translation']);
                if($temp!==$_POST['delete_translation'])
                {
                    array_push($new_translations,$char_translations[$i]);
                }
            }
            update_option('char_translations',$new_translations);

        }
        //check if blocked or not
        if (isset($_POST['blocked']) && !empty($_POST['blocked']))
        {
            update_option('blocked',$_POST['blocked']);
            
            $query= new WP_Query(array(
                'post_type' => 'chapter',
                'post_status' => 'publish',
                'post_per_page' => -1,
                'order' => 'ASC',));
            
            if ($_POST['blocked']=="Unblock All Chapters")
            {
                while ($query->have_posts())
                {
                    $query->the_post();
                    $post_id= get_the_ID();
                    $post_title = get_the_title();
                    update_post_meta($post_id,'block_tag',"Unblock");
                }
            }
            else
            {
                while ($query->have_posts())
                {
                    $query->the_post();
                    $post_id= get_the_ID();
                    $post_title = get_the_title();
                    update_post_meta($post_id,'block_tag',"Block");
                }
                wp_reset_query();
            }
        }
        $option_block = get_option('blocked',"false");
        $char_translations = get_option('char_translations',"false");
        $url_hidden = get_option('show_picture',"false");
        echo '<div class="container-admin">';
            echo '<h1 id="title">Alexa Character Translation</h1>';
                echo '<div class="container">';
                    echo '<form name="input-fields" id="input-fields" action="?page=alexa_input" method="post">';
                        echo '<div class = "Field-container">';
                            echo '<div class="row">';
                                echo '<div class="column-header-buttons">';
                                        echo '<input id="export" name="export" type="button" value="Export Content"/>';
                                        $button_name;
                                        if ($option_block == "false")
                                            $button_name = "Block All Chapters";
                                        else
                                            $button_name =$option_block;
                                        echo '<input id="block_all" name="block_all" type="button" value="'.$button_name.'"/>';
                                        echo '<input id="show_url" name="show_url" type="button" value="'.$url_hidden.'"/>';
                                echo '</div>';
                                echo '<div class="column-char">';
                                    echo '<h2 id="special">Special Characters</h2>';
                                echo '</div>';
                                echo '<div class="column-translate">';
                                    echo '<h2 id="special">Translations</h2>';
                                echo '</div>';
                                
                                /* 
                            echo '</div>';
                            echo '<div class="row">'; */
                            echo '</div>';
                    
                        for ($i=0;$i<count($char_translations);$i++)      
                        {   $current_char = $char_translations[$i];
                            $translation="";
                            echo '<div class="container-fields">';
                                echo '<div class = "row">';
                                    echo '<div class="column-char">';
                                        echo '<div class="field">';
                                            echo '<textarea class="input-field" readOnly>'.$current_char['char'].'</textarea>';
                                        echo '</div>';
                                    echo '</div>';
                                    echo '<div class="column-translate">';
                                        echo '<div class="field">';
                                        if (!empty($current_char['translation']))
                                            $translation=$current_char['translation'];
                                            if ($option_block!="false" && $option_block=="Unblock All Chapters")
                                                echo '<textarea class="input-field2" readOnly>'.$translation.'</textarea>';
                                            else
                                                echo '<textarea class="input-field2" >'.$translation.'</textarea>';
                                        echo '</div>';
                                    echo '</div>';
                                    echo '<div class="column-delete">';
                                        $name=$i;
                                        if ($option_block!="false" && $option_block=="Unblock All Chapters")
                                            echo '<input type="button" id="delete_row" name="'.$name.'" value="Delete" disabled>';
                                        else
                                            echo '<input type="button" id="delete_row" name="'.$name.'" value="Delete">';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '<div class="row" >';
                            echo '<div class="column-translate-buttons">';
                                echo '<input id="new_char" name="new_char" type="text" value=""/>';
                                echo '<input id="add_new_char" name="add_new_char" type="button" value="Add New"/>';
                                echo '<input id="transl_update" name="transl_update" type="button" value="Update Translations"/>';
                                
                            echo '</div>';
                        echo '</div>';
                        echo '<input type="hidden" id="new_char_hidden" name="new_char_hidden"/>';
                        echo '<input type="hidden" id ="new_transaltion_value" name="new_transaltion_value" />';
                        echo '<input type="hidden" id="new_translation_hidden" name="new_translation_hidden"/>';
                        echo '<input type="hidden" id="delete_translation" name="delete_translation"/>';
                        echo '<input type="hidden" id="show_picture" name="show_picture" value="'.$url_hidden.'"/>';
                        ?><input type='hidden' id='blocked' name='blocked' value='<?php echo $option_block;?>'/><?php
                    echo '</form>';
                echo '</div>';
            //Next box for selecting sections for each information part..
            
                            //<label style="font-size:15px;" for="basic_info">Basic Info:</label>
            echo '<h1 id="title2">Alexa Section Selection</h1>';
                echo '<div class="container">';
                    echo '<form name="section-fields" id="section-fields" action="?page=alexa_input" method="post">';
                        echo '<div class = "Field-container" >';
                            //title row with column titles
                            /* echo '<div class="row">';
                                echo '<div class="column-sections">';
                                    echo '<h2 id="special">Beggining of section to replace</h2>';
                                echo '</div>';
                                 echo '<div class="column-section2">';
                                    echo '<h2 id="special">Begin and End</h2>';
                                echo '</div>'; 
                            echo '</div>'; */
                            //textarea rows..
                            //BASIC INFO
                           echo '<div class="container-fields">';
                                echo '<div class = "row">';
                                    echo '<div class="field">';
                                        echo '<label style="font-size:15px;" for="basic_info_section">Basic Info:</label>';
                                        echo '<textarea class="input-section" id="basic_info_section" name="basic_info_section" placeholder="Enter characters for Basic Info Begin Section">'.$section_markers['basic_info'].'</textarea>';
                                    echo '</div>';
                                    /* echo '<div class="column-section2">';
                                        echo '<div class="field">';
                                            echo '<textarea class="input-marker" placeholder="END">'.$section_markers['more_info']['end'].'</textarea>';
                                        echo '</div>';
                                        echo '<div class="field">';
                                            echo '<textarea class="input-marker" placeholder="BEGIN">'.$section_markers['more_info']['begin'].'</textarea>';
                                        echo '</div>';
                                    echo '</div>'; */
                                echo '</div>';
                            echo '</div';
                            //MORE INFO
                            echo '<div class="container-fields">';
                                echo '<div class = "row">';
                                    echo '<div class="field">';
                                        echo '<label style="font-size:15px;" for="more_info_section">More Info:</label>';
                                        echo '<textarea class="input-section" id="more_info_section" name="more_info_section" placeholder="Enter characters for More Info Begin Section">'.$section_markers['more_info'].'</textarea>';
                                    echo '</div>';
                                    /* echo '<div class="column-section2">';
                                        echo '<div class="field">';
                                            echo '<textarea class="input-marker" placeholder="END">'.$section_markers['more_info']['end'].'</textarea>';
                                        echo '</div>';
                                        echo '<div class="field">';
                                            echo '<textarea class="input-marker" placeholder="BEGIN">'.$section_markers['more_info']['begin'].'</textarea>';
                                        echo '</div>';
                                    echo '</div>'; */
                                echo '</div>';
                            echo '</div';
                            //EXAMPLES
                            echo '<div class="container-fields">';
                                echo '<div class = "row">';
                                    echo '<div class="field">';
                                        echo '<label style="font-size:15px;" for="example_section">Example:</label>';
                                        echo '<textarea class="input-section" id="example" name="example" placeholder="Enter characters for Examples Begin Section">'.$section_markers['example'].'</textarea>';
                                    echo '</div>';
                                    /* echo '<div class="column-section2">';
                                        echo '<div class="field">';
                                            echo '<textarea class="input-marker" placeholder="END">'.$section_markers['example']['end'].'</textarea>';
                                        echo '</div>';
                                        echo '<div class="field">';
                                            echo '<textarea class="input-marker" placeholder="BEGIN">'.$section_markers['example']['begin'].'</textarea>';
                                        echo '</div>';
                                    echo '</div>'; */
                                echo '</div>';
                            echo '</div';
                        echo '</div>';
                        //BUTTONS
                        echo '<div class="row">';
                            echo '<div class="column-translate-buttons">';
                                echo '<input id="save_sections" name="save_sections" type="button" value="Save"/>';
                            echo '</div>';
                        echo '</div>';
                        //HiDDENS
                        echo '<input type="hidden" id="markers_update" name="markers_update"/>';
                    echo '</form>';                 
                echo '</div>';
        echo '</div>';

        
                
    } 

   

}

$admin = new AdminSettings;


 ?>