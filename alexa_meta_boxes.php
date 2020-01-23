<?php

/**
 * @package alexa_meta_boxes
 * 
 * Plugin Name: Alexa Meta Boxes
 * Plugin URI: D:\xampp\htdocs\wordpress\wp-content\plugins\Alexa-Meta-Boxes\alexa_meta_boxes.php
 * Description: Content creator for amazon alexa skill "English Grammar"
 * Version: 1.5
 * Author:            My Language Skills team
 * Author URI:        https://github.com/my-language-skills/
 * License:           GPL 3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: alexa_meta_boxes
 * 
 * 
 * 

*/


//standard line to check if path is correct else plugin dies
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

class alexa_meta_boxes
{
    public function __construct()
    {
        add_action('add_meta_boxes',array($this,'amb_add_chapter_meta_boxes'));
        add_action('admin_enqueue_scripts',array($this,'amb_enqueue_admin_scripts_and_styles'));

        add_action('admin_menu',array($this,'amb_add_admin_page'));
        
        
        add_action('save_post',array($this,'amb_save_alexa'));
        register_activation_hook( __FILE__, array($this,'amb_plugin_activate'));
        register_deactivation_hook( __FILE__,array($this,'amb_plugin_deactivate'));
    }
    //function that adds admin pages
    function amb_add_admin_page()
    {
        require_once plugin_dir_path( __FILE__).'/export.php';
        
        require_once plugin_dir_path( __FILE__).'/admin_settings.php';

        add_submenu_page(
            'options-general.php',
            'Alexa Input Fields',
            'Alexa Input Fields',
            'manage_options',
            'alexa_input',
            array(new AdminSettings,'amb_page_display'),
            101);

    }

    public function amb_plugin_activate()
    {
        flush_rewrite_rules();
    }

    /**
     * Triggered on plugin deactivation
     * called only once
     * 
     */
    public function amb_plugin_deactivate()
    {
        flush_rewrite_rules();
    }
    public function amb_add_chapter_meta_boxes()
    {
        add_meta_box(
            'alexa_info_meta_box',
            'Alexa Meta Info',
            array($this,'amb_alexa_meta_box_display'),
            'chapter',
            'normal',
            'default'
        );
    }

    public function amb_alexa_meta_box_display($post)
    {
        $test='sdfs'; 
        $test = wp_nonce_field( 'wp_alexa_meta_nonce', 'wp_alexa_nonce_field');
        
        //option array that stores all character translations
        $char_translations = get_option('amb_char_translations',false);

        //option array of section markers
        $section_markers = get_option('amb_section_markers',false);

        //need to reenter the "<",">" tags after receiving it from the database.//
        $section_markers['amb_basic_info']= '<'.$section_markers['amb_basic_info'].'>';
        $section_markers['amb_more_info']= '<'.$section_markers['amb_more_info'].'>';
        $section_markers['amb_example']= '<'.$section_markers['amb_example'].'>';
        $temp = json_encode($section_markers);
        ?><input type='hidden' name='amb_section_markers' id='amb_section_markers' value='<?php echo $temp;?>' /> <?php

        //option for all blocked chapters from admin...
        $blocked = get_option('amb_blocked',false);

        //option for hidding picture..
        $show_picture = get_option('amb_show_picture',"Hide Pictures");

        $temp = json_encode($char_translations);
        ?><input type='hidden' name='amb_char_transl' id='amb_char_transl' value='<?php echo $temp;?>' /> <?php
        //meta data for each chapter
        $url_meta = get_post_meta( $post->ID,'amb_picture_url',true);
        $basic_meta = get_post_meta($post->ID,'amb_basic_info',true);
        $more_meta = get_post_meta($post->ID,'amb_more_info',true);
        $example_meta = get_post_meta($post->ID,'amb_examples',true);
        $chapter_block = get_post_meta($post->ID,'amb_block_tag',true);


        if ($chapter_block == "")
            $chapter_block = "Block";
        

        ?>
        
        <p><strong>Chapter Information for Alexa skill</strong></p>
        <div class="field-container">
            
            <input id="amb_block_btn" name="amb_block_btn" type="button" value="<?php echo $chapter_block;?>"/>
            <input id="amb_update" name='amb_update' type="button" value="Import All Content"/>
            <input id="amb_translate" name='amb_translate' type="button" value="Translate All  symbols"/>
            <input id="amb_block_value" name = "amb_block_value" type="hidden"/>
            <!-- All Alexa Conflictions -->
            <div class="amb_container">
                <div class="field">
                    <label  class="amb_label" style="font-size:15px;" for="amb_conflictions">Alexa Conflictions:</label>
                    <input id="amb_drop-down_conflictions" name="amb_drop-down_conflictions" type="button" value="▼"/>
                    <textarea readOnly name="amb_conflictions" style="display:none;" class="amb_alexa-conflictions" id="amb_conflictions" placholder="If something is conflicted this Message will dissapear"></textarea>
                </div> 
            </div>
            <!-- Basic Info Fields -->
            <div class="amb_container">
                <div class="field">
                    <label  class="amb_label" style="font-size:15px;" for="amb_basic_info">Basic Info:</label>
                    <textarea name="amb_Basic Info" class="amb_alexa-content" id="amb_basic_info"  placeholder="Import to see chapter content" readOnly></textarea>
                </div>
                <div class="field">
                    <div class="amb_alexa-label">
                        <label  class="amb_label" for="amb_Alexa_basic_info">Αlexa Translation:</label>
                    </div>
                    <textarea name="amb_Alexa_basic_info"  id="amb_Alexa_basic_info"  class="amb_alexa-text" placeholder="Modified text for Alexa Skill"><?php if(!(empty($basic_meta))) echo $basic_meta; ?></textarea>
                </div>
                <div class="field">
                    <div class="amb_alexa-label">
                        <label  class="amb_label" for="amb_basic_info_conflictions">Alexa Conflictions:</label>
                    <input id="amb_drop-down_basic_info" name="amb_drop-down_basic_info" type="button" value="▼"/>
                    </div>
                    <textarea readOnly class="amb_alexa-conflictions" name="amb_basic_info_conflictions" style="display:none;" id="amb_basic_info_conflictions" placholder="If something is conflicted this Message will dissapear"></textarea>
                </div>
                <div class="amb_row">
                    <input id="amb_import_one_basic_info" name="amb_import_one_basic_info" type="button"  value="Import Basic Info"/>
                    <input id="amb_translate_one_basic_info" name="amb_translate_one_basic_info" type="button"  value="Translate symbols"/>
                </div>
            </div>
            <!-- More Info Fields -->
            <div class="amb_container">
                <div class="field">
                    <label  class="amb_label" style="font-size:15px;" for="amb_more_info">More Info:</label>
                    <textarea name="amb_More Info" class="amb_alexa-content" id="amb_more_info"  placeholder="Import to see chapter content" readOnly></textarea>
                </div>
                <div class="field">
                    <div class="amb_alexa-label">
                        <label  class="amb_label" for="amb_Alexa_more_info">Αlexa Translation:</label>
                    </div>
                    <textarea name="amb_Alexa_more_info"  id="amb_Alexa_more_info"  class="amb_alexa-text" placeholder="Modified text for Alexa Skill"><?php if(!(empty($more_meta))) echo $more_meta; ?></textarea>
                </div>
                <div class="field">
                    <div class="amb_alexa-label">
                        <label for="amb_more_infoconflictions" class="amb_label">Alexa Conflictions:</label>
                    <input id="amb_drop-down_more_info" name="amb_drop-down_more_info" type="button" value="▼"/>
                    </div>
                    <textarea readOnly class="amb_alexa-conflictions" name="amb_more_info_conflictions" style="display:none;" id="amb_more_info_conflictions" placholder="If something is conflicted this Message will dissapear"></textarea>
                </div>
                <div class="amb_row">
                    <input id="amb_import_one_more_info" name="amb_import_one_more_info" type="button"  value="Import More Info"/>
                    <input id="amb_translate_one_more_info" name="amb_translate_one_more_info" type="button"  value="Translate symbols"/>
                </div>
            </div>
            <!-- Example Fields -->
            <div class="amb_container">
                <div class="field">
                    <label style="font-size:15px;"  class="amb_label" for="amb_examples">Examples:</label>
                    <textarea name="amb_Examples" class="amb_alexa-content" id="amb_examples"  placeholder="Import to see chapter content" readOnly></textarea>
                </div>
                <div class="field">
                    <div class="amb_alexa-label">
                        <label  class="amb_label" for="amb_Alexa_examples">Αlexa Translation:</label>
                    </div>
                    <textarea name="amb_Alexa_examples"  id="amb_Alexa_examples"  class="amb_alexa-text" placeholder="Modified text for Alexa Skill"><?php if(!(empty($example_meta))) echo $example_meta; ?></textarea>

                </div>
                <div class="field">
                    <div class="amb_alexa-label">
                        <label  class="amb_label" for="amb_examples_conflictions">Alexa Conflictions:</label>
                    <input id="amb_drop-down_examples" name="amb_drop-down_examples" type="button" value="▼"/>
                    </div>
                    <textarea readOnly class="amb_alexa-conflictions" name="amb_examples_conflictions" style="display:none;" id="amb_examples_conflictions" placholder="If something is conflicted this Message will dissapear"></textarea>
                </div>
                <div class="amb_row">
                    <input id="amb_import_one_examples" name="amb_import_one_examples" type="button"  value="Import Examples"/>
                    <input id="amb_translate_one_examples" name="amb_translate_one_examples" type="button"  value="Translate symbols"/>
                </div>
            </div>
            
            <?php if ($show_picture == "Hide Pictures")
            {
            ?>
            <div class="amb_container">
                <div class="field">
                    <label  class="amb_label" for="amb_picture_url">More Info Picture Url:</label><br>
                    <?php 
                        if ( $blocked == "Unblock All Chapters")
                        {?>
                            <textarea readOnly class= "amb_alexa-text" name="amb_picture_url" id="amb_picture_url" placeholder="Enter Picture Url"></textarea>
                        <?php
                        }
                        else
                        {?>
                            <textarea class= "amb_alexa-text" name="amb_picture_url" id="amb_picture_url" placeholder="Enter Picture Url"></textarea>
                        <?php
                        }?>
                </div>
                <span><div class="amb_hover_img">
                    <?php if(!(empty($url_meta))) $url=$url_meta;  else $url="https://cdn.pixabay.com/photo/2017/06/08/17/32/not-found-2384304_1280.jpg";?>
                        <a>Picture Preview<span><img width="500%"  src= <?php echo $url;?> alt="image"  name="amb_uploade"/></span></a>
                    </div></span>
            </div>
            <?php
            }
            ?>
            
        </div>
        <?php
        $user_id = get_current_user_id();
        $user_meta=get_userdata($user_id);

        $user_roles=$user_meta->roles;
        $user_role = $user_roles[0];
        echo '<input type="hidden" name="amb_user_role" id="amb_user_role" value="'.$user_role.'"/>';
        
        do_action('save_post',$post->ID,$post,true);
    }
    public function amb_save_alexa($post_id)
    {
        if (!isset($_POST['wp_alexa_nonce_field']))
        {
            return $post_id;
        }

        if (!wp_verify_nonce( $_POST['wp_alexa_nonce_field'],'wp_alexa_meta_nonce'))
        {
            return $post_id;
        }
        
        if (defined('DOING_AUTOSAVE')&& DOING_AUTOSAVE)
        {
            return $post_id;
        }
        
        $basic_meta = isset($_POST['amb_Alexa_basic_info']) ? sanitize_text_field($_POST['amb_Alexa_basic_info']):'';
        $more_meta = isset($_POST['amb_Alexa_more_info']) ? sanitize_text_field($_POST['amb_Alexa_more_info']):'';
        $example_meta = isset($_POST['amb_Alexa_examples']) ? sanitize_text_field($_POST['amb_Alexa_examples']):'';
        $img_meta = isset($_POST['amb_picture_url']) ? sanitize_text_field($_POST['amb_picture_url']):'';
        $chapter_block = isset($_POST['amb_block_value'])? sanitize_text_field($_POST['amb_block_value']):'';

        update_post_meta($post_id,'amb_block_tag',$chapter_block);
        update_post_meta( $post_id, 'amb_basic_info', $basic_meta);
        update_post_meta( $post_id, 'amb_more_info', $more_meta);
        update_post_meta( $post_id, 'amb_examples', $example_meta);
        update_post_meta( $post_id, 'amb_picture_url', $img_meta);
        
       
        
        
    }
    
     /**
     * Function to enqueue scripts and styles on the back end
     */
    public function amb_enqueue_admin_scripts_and_styles()
    {
        wp_enqueue_style('wp_admin_styles',plugin_dir_url( __FILE__). '/css/styles.css');
        wp_enqueue_script( 'wp_admin_scripts',plugin_dir_url( __FILE__). '/js/scripts.js'); 
        
        wp_enqueue_script('jquery');
        wp_register_script( "ajax_script", plugin_dir_url(__FILE__).'/js/admin_scripts.js', array('jquery') );
   
        // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
        wp_localize_script( 'ajax_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
        
        wp_enqueue_script( 'ajax_script' );
        
    }

}
$metaboxes = new alexa_meta_boxes;