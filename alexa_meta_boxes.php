<?php

/**
 * @package alexa_meta_boxes
 */

/*
Plugin Name: Alexa Meta Boxes
Plugin URI: D:\xampp\htdocs\wordpress\wp-content\plugins\Alexa-Meta-Boxes\alexa_meta_boxes.php
Description: cow feces text
Version: 4.1.2
Author: Se moi
Author URI: https://www.trueRealsite.com
License: GPLv2 or later
Text Domain: alexa_meta_boxes
*/


//standard line to check if path is correct else plugin dies
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

class alexa_meta_boxes
{
    public function __construct()
    {
        add_action('add_meta_boxes',array($this,'add_chapter_meta_boxes'));
        add_action('admin_enqueue_scripts',array($this,'enqueue_admin_scripts_and_styles'));

        add_action('admin_menu',array($this,'add_admin_page'));
        //add_filter("plugin_action_links_".plugin_basename( __FILE__ ),array($this,'settings_link'));
        
        add_action('save_post',array($this,'save_alexa'));
        register_activation_hook( __FILE__, array($this,'plugin_activate'));
        register_deactivation_hook( __FILE__,array($this,'plugin_deactivate'));
    }
    /* //adding a link to a plugin (settings)
    function settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=alexa_meta_boxes">Settings</a>';
        array_push($links,$settings_link);
        return $links;
    } */
    //function that adds admin pages
    function add_admin_page()
    {
        //add_menu_page('Alexa Meta Boxes','Alexa Meta Boxes','manage_options','alexa_meta_boxes',array($this,'admin_index'),'dashicons-store',110);
        require_once plugin_dir_path( __FILE__).'/export.php';
        //require_once plugin_dir_path( __FILE__).'/admin_settings.php';
        require_once plugin_dir_path( __FILE__).'/admin_settings.php';
        /* add_submenu_page(
            'options-general.php',
            'Alexa Characters',
            'Alexa Characters',
            'manage_options',
            'alexa_characters',
            array(new AdminSettings,'page_display'),
            100); */

        add_submenu_page(
            'options-general.php',
            'Alexa Input Fields',
            'Alexa Input Fields',
            'manage_options',
            'alexa_input',
            array(new AdminSettings,'page_display'),
            101);

    }

    public function plugin_activate()
    {
        flush_rewrite_rules();
    }

    /**
     * Triggered on plugin deactivation
     * called only once
     * 
     */
    public function plugin_deactivate()
    {
        flush_rewrite_rules();
    }
    public function add_chapter_meta_boxes()
    {
        add_meta_box(
            'alexa_info_meta_box',
            'Alexa Meta Info',
            array($this,'alexa_meta_box_display'),
            'chapter',
            'normal',
            'default'
        );
    }

    public function alexa_meta_box_display($post)
    {
        $test='sdfs'; //=wp_nonce_field( 'wp_alexa_meta_nonce', 'wp_alexa_nonce_field',true,true);
        $test = wp_nonce_field( 'wp_alexa_meta_nonce', 'wp_alexa_nonce_field');
        
        //option array that stores all character translations
        $char_translations = get_option('char_translations',"false");

        //option array of section markers
        $section_markers = get_option('section_markers',"false");
        $temp = json_encode($section_markers);
        ?><input type='hidden' name='section_markers' id='section_markers' value='<?php echo $temp;?>' /> <?php

        //option for all blocked chapters from admin...
        $blocked = get_option('blocked',"false");

        //option for hidding picture..
        $show_picture = get_option('show_picture',"false");

        $temp = json_encode($char_translations);
        ?><input type='hidden' name='char_transl' id='char_transl' value='<?php echo $temp;?>' /> <?php
        //meta data for each chapter
        $url_meta = get_post_meta( $post->ID,'picture_url',true);
        $basic_meta = get_post_meta($post->ID,'basic_info',true);
        $more_meta = get_post_meta($post->ID,'more_info',true);
        $example_meta = get_post_meta($post->ID,'examples',true);
        $chapter_block = get_post_meta($post->ID,'block_tag',true);


        if ($chapter_block == "")
            $chapter_block = "Block";
        

        ?>
        
        <p><strong>Chapter Information for Alexa skill</strong></p>
        <div class="field-container">
            
            <input id="block_btn" name="block_btn" type="button" value="<?php echo $chapter_block;?>"/>
            <input id="update" name='update' type="button" value="Import All Content"/>
            <input id="translate" name='translate' type="button" value="Translate All  symbols"/>
            <input id="block_value" name = "block_value" type="hidden"/>
            <!-- All Alexa Conflictions -->
            <div class="container">
                <div class="field">
                    <label style="font-size:15px;" for="conflictions">Alexa Conflictions:</label>
                    <input id="drop-down" name="conflictions" type="button" value="▼"/>
                    <textarea readOnly name="conflictions" style="display:none;" class="alexa-conflictions" id="conflictions" placholder="If something is conflicted this Message will dissapear"></textarea>
                </div> 
            </div>
            <!-- Basic Info Fields -->
            <div class="container">
                <div class="field">
                    <label style="font-size:15px;" for="basic_info">Basic Info:</label>
                    <textarea name="Basic Info" class="alexa-content" id="basic_info"  placeholder="Import to see chapter content" readOnly></textarea>
                </div>
                <div class="field">
                    <div class="alexa-label">
                        <label  for="Alexa_basic_info">Αlexa Translation:</label>
                    </div>
                    <textarea name="Alexa_basic_info"  id="Alexa_basic_info"  class="alexa-text" placeholder="Modified text for Alexa Skill"><?php if(!(empty($basic_meta))) echo $basic_meta; ?></textarea>
                </div>
                <div class="field">
                    <div class="alexa-label">
                        <label for="conflictions">Alexa Conflictions:</label>
                    <input id="drop-down" name="basic_info" type="button" value="▼"/>
                    </div>
                    <textarea readOnly class="alexa-conflictions" name="basic_info_conflictions" style="display:none;" id="basic_info_conflictions" placholder="If something is conflicted this Message will dissapear"></textarea>
                </div>
                <div class="row">
                    <input id="import_one" name="basic_info" type="button"  value="Import Basic Info"/>
                    <input id="translate_one" name="basic_info" type="button"  value="Translate symbols"/>
                </div>
            </div>
            <!-- More Info Fields -->
            <div class="container">
                <div class="field">
                    <label style="font-size:15px;" for="more_info">More Info:</label>
                    <textarea name="More Info" class="alexa-content" id="more_info"  placeholder="Import to see chapter content" readOnly></textarea>
                </div>
                <div class="field">
                    <div class="alexa-label">
                        <label  for="Alexa_more_info">Αlexa Translation:</label>
                    </div>
                    <textarea name="Alexa_more_info"  id="Alexa_more_info"  class="alexa-text" placeholder="Modified text for Alexa Skill"><?php if(!(empty($more_meta))) echo $more_meta; ?></textarea>

                     <?php 
                       /*  if ( $blocked == "Unblock All Chapters")
                        {?>
                            <textarea readOnly name="Alexa_examples"  id="Alexa_examples"  class="alexa-text" placeholder="Modified text for Alexa Skill"><?php if(!(empty($example_meta))) echo $example_meta; ?></textarea>
                            
                        <?php
                        }
                        else
                        {?>
                            <textarea name="Alexa_examples"  id="Alexa_examples"  class="alexa-text" placeholder="Modified text for Alexa Skill"><?php if(!(empty($example_meta))) echo $example_meta; ?></textarea>
                        <?php
                        } */?> 
                </div>
                <div class="field">
                    <div class="alexa-label">
                        <label for="conflictions">Alexa Conflictions:</label>
                    <input id="drop-down" name="more_info" type="button" value="▼"/>
                    </div>
                    <textarea readOnly class="alexa-conflictions" name="more_info_conflictions" style="display:none;" id="more_info_conflictions" placholder="If something is conflicted this Message will dissapear"></textarea>
                </div>
                <div class="row">
                    <input id="import_one" name="more_info" type="button"  value="Import More Info"/>
                    <input id="translate_one" name="more_info" type="button"  value="Translate symbols"/>
                </div>
            </div>
            <!-- Example Fields -->
            <div class="container">
                <div class="field">
                    <label style="font-size:15px;" for="examples">Examples:</label>
                    <textarea name="Examples" class="alexa-content" id="examples"  placeholder="Import to see chapter content" readOnly></textarea>
                </div>
                <div class="field">
                    <div class="alexa-label">
                        <label  for="Alexa_examples">Αlexa Translation:</label>
                    </div>
                    <textarea name="Alexa_examples"  id="Alexa_examples"  class="alexa-text" placeholder="Modified text for Alexa Skill"><?php if(!(empty($example_meta))) echo $example_meta; ?></textarea>

                </div>
                <div class="field">
                    <div class="alexa-label">
                        <label for="conflictions">Alexa Conflictions:</label>
                    <input id="drop-down" name="examples" type="button" value="▼"/>
                    </div>
                    <textarea readOnly class="alexa-conflictions" name="examples_conflictions" style="display:none;" id="examples_conflictions" placholder="If something is conflicted this Message will dissapear"></textarea>
                </div>
                <div class="row">
                    <input id="import_one" name="examples" type="button"  value="Import Examples"/>
                    <input id="translate_one" name="examples" type="button"  value="Translate symbols"/>
                </div>
            </div>
            
            <?php if ($show_picture == "Hide Pictures")
            {
            ?>
            <div class="container">
                <div class="field">
                    <label for="picture_url">More Info Picture Url:</label><br>
                    <?php 
                        if ( $blocked == "Unblock All Chapters")
                        {?>
                            <textarea readOnly class= "alexa-text" name="picture_url" id="picture_url" placeholder="Enter Picture Url"></textarea>
                        <?php
                        }
                        else
                        {?>
                            <textarea class= "alexa-text" name="picture_url" id="picture_url" placeholder="Enter Picture Url"></textarea>
                        <?php
                        }?>
                    <!-- <span class="hovermsg">Blocked Field</span> -->
                </div>
                <span><div class="hover_img">
                    <?php if(!(empty($url_meta))) $url=$url_meta;  else $url="https://cdn.pixabay.com/photo/2017/06/08/17/32/not-found-2384304_1280.jpg";?>
                        <a>Picture Preview<span><img width="500%"  src= <?php echo $url;?> alt="image"  name="uploade"/></span></a>
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
        echo '<input type="hidden" name="user_role" id="user_role" value="'.$user_role.'"/>';
        
        do_action('save_post',$post->ID,$post,true);
    }
    public function save_alexa($post_id)
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
        
        $basic_meta = isset($_POST['Alexa_basic_info']) ? sanitize_text_field($_POST['Alexa_basic_info']):'';
        $more_meta = isset($_POST['Alexa_more_info']) ? sanitize_text_field($_POST['Alexa_more_info']):'';
        $example_meta = isset($_POST['Alexa_examples']) ? sanitize_text_field($_POST['Alexa_examples']):'';
        $img_meta = isset($_POST['picture_url']) ? sanitize_text_field($_POST['picture_url']):'';
        $chapter_block = isset($_POST['block_value'])? sanitize_text_field($_POST['block_value']):'';

        update_post_meta($post_id,'block_tag',$chapter_block);
        update_post_meta( $post_id, 'basic_info', $basic_meta);
        update_post_meta( $post_id, 'more_info', $more_meta);
        update_post_meta( $post_id, 'examples', $example_meta);
        update_post_meta( $post_id, 'picture_url', $img_meta);
        
       
        
        
    }
    
     /**
     * Function to enqueue scripts and styles on the back end
     */
    public function enqueue_admin_scripts_and_styles()
    {
        wp_enqueue_style('wp_admin_styles',plugin_dir_url( __FILE__). '/css/styles.css');
        wp_enqueue_script( 'wp_admin_scripts',plugin_dir_url( __FILE__). '/js/scripts.js'); 
        
        wp_enqueue_script('jquery');
        wp_register_script( "ajax_script", plugin_dir_url(__FILE__).'/js/admin_scripts.js', array('jquery') );
   
        // localize the script to your domain name, so that you can reference the url to admin-ajax.php file easily
        wp_localize_script( 'ajax_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
        
        wp_enqueue_script( 'ajax_script' );
        
    }


   /*  public function x_init_custom_fields()
    {
       /*  x_add_metadata_group('x_metaBox1','chapter',array(
            'label' => 'Alexa Multiple Fields'
        ));

        x_add_metadata_field('x_fieldName1','chapter',array(
            'group' => 'x_metaBox1',
            'description' => 'Basic Info',
            'field_type' => 'textarea',
            'label' =>  'Text Field',
            'display_column' => true
        ));

        x_add_metadata_field('x_fieldName2','chapter',array(
            'group' => 'x_metaBox1',
            'description' => 'More Info',
            'field_type' => 'textarea',
            'label' =>  'Text Field',
            'display_column' => true
        ));

        x_add_metadata_field('x_fieldName3','chapter',array(
            'group' => 'x_metaBox1',
            'description' => 'Examples',
            'field_type' => 'textarea',
            'label' =>  'Text Field',
            'display_column' => true
        ));
    } */
}
$metaboxes = new alexa_meta_boxes;