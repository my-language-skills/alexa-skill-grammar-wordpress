<?php


function deleteOptionsAndMetaInfo()
{
    global $wpdb;
    $blogs = get_sites();
    $starting_blog= get_current_blog_id();
    foreach ($blogs as $blog_id)
    {
        if (is_multisite())
        {    
            switch_to_blog( $blog_id->blog_id);
        }
        $blog_options = wp_load_alloptions();
        
        $plugin_options = [];
        
        foreach ($blog_options as $name => $value)
        {
            if (stristr($name,'amb_'))
            {
                $plugin_options[$name] = $value;
            }
        }
        foreach ($plugin_options as $key => $value)
        {
                delete_option($key);
        }
        
        //deleting the meta info for the books..
        $wpdb->query( "DELETE FROM `".$wpdb->prefix."postmeta` WHERE `meta_key` LIKE 'amb_%'");
    }
    switch_to_blog($starting_blog->blog_id);
}