jQuery( function ( jQuery ){
    //user role to check later if allowed for certain actions.
    var user_role = jQuery("input#amb_user_role").val();

    //blocks fields if meta value of alexa-content-editing is blocked
    if(jQuery('#amb_block_btn').attr('value')=='Unblock')
    {
        jQuery('.amb_alexa-text').attr('readOnly',true); 
        jQuery('input#amb_update').prop('disabled',true);
        jQuery('input#amb_translate').prop('disabled',true);
        jQuery('input#amb_import_one_basic_info').prop('disabled',true);
        jQuery('input#amb_translate_one_basic_info').prop('disabled',true);
        jQuery('input#amb_import_one_more_info').prop('disabled',true);
        jQuery('input#amb_translate_one_more_info').prop('disabled',true);
        jQuery('input#amb_import_one_examples').prop('disabled',true);
        jQuery('input#amb_translate_one_examples').prop('disabled',true);
    }


    //for enabling to input data to input fields
    jQuery('#amb_block_btn').on('click',function(){
        if (user_role=="administrator")
        {
            var prev_value = jQuery('input#amb_block_btn').val();
            var value;
            var alexa_input = jQuery('.amb_alexa-text');
            if (prev_value=="Block")
            {
                    value="Unblock";
                    alexa_input.attr('readOnly',true);
                    jQuery('input#amb_update').prop('disabled',true);
                    jQuery('input#amb_translate').prop('disabled',true);
                    jQuery('input#amb_import_one_basic_info').prop('disabled',true);
                    jQuery('input#amb_translate_one_basic_info').prop('disabled',true);
                    jQuery('input#amb_import_one_more_info').prop('disabled',true);
                    jQuery('input#amb_translate_one_more_info').prop('disabled',true);
                    jQuery('input#amb_import_one_examples').prop('disabled',true);
                    jQuery('input#amb_translate_one_examples').prop('disabled',true);
            }
            else
            {
                value="Block";
                alexa_input.attr('readOnly',false);
                jQuery('input#amb_update').prop('disabled',false);
                jQuery('input#amb_translate').prop('disabled',false);
                jQuery('input#amb_import_one_basic_info').prop('disabled',false);
                jQuery('input#amb_translate_one_basic_info').prop('disabled',false);
                jQuery('input#amb_import_one_more_info').prop('disabled',false);
                jQuery('input#amb_translate_one_more_info').prop('disabled',false);
                jQuery('input#amb_import_one_examples').prop('disabled',false);
                jQuery('input#amb_translate_one_examples').prop('disabled',false);
            }

            jQuery.ajax({
                type:"POST",
                url: "?",
                success: function(data)
                {
                    jQuery('#amb_block_btn').val(value);
                    jQuery('input#amb_block_value').val(value);
                    if (value=="Unblock")
                        alexa_input.attr('readOnly',true);
                    else
                        alexa_input.attr('readOnly',false);
                        
                    console.log("success blocked");
                }
            });
        }
        else
        {
            console.log("bruuh you are not even allowed for that");
            alert("Admin rights required for that action");
        }
    });

    
    //to show and hide all conflictions when clicked
    jQuery('input#amb_drop-down_conflictions').on('click',function(e)
    {
        hideOrShow("amb_conflictions",jQuery(this).attr('name'));
    });
    //basic info conflictions hide or show information when clicked
    jQuery('input#amb_drop-down_basic_info').on('click',function(e)
    {
        hideOrShow("amb_basic_info_conflictions",jQuery(this).attr('name'));
    });
    
    //more info conflictions hide or show information when clicked
    jQuery('input#amb_drop-down_more_info').on('click',function(e)
    {
        hideOrShow("amb_more_info_conflictions",jQuery(this).attr('name'));
    });
    
    //examples conflictions hide or show information when clicked
    jQuery('input#amb_drop-down_examples').on('click',function(e)
    {
        hideOrShow("amb_examples_conflictions",jQuery(this).attr('name'));
    });

    //imports basic info content when clicked
    jQuery('input#amb_import_one_basic_info').on('click',function(e)
    {
        getContents("basic_info");
    });
    
    //imports more info content when clicked
    jQuery('input#amb_import_one_more_info').on('click',function(e)
    {
        getContents("more_info");
    });

    //imports examples content when clicked
    jQuery('input#amb_import_one_examples').on('click',function(e)
    {
        getContents("examples");
    });

    //imports all data content when clicked
    jQuery('#amb_update').on('click',function(){
        getContents("all");
    });

    //adding the URL to the pictureUrl textarea
    var img_ele=jQuery('.amb_hover_img img').attr('src');
    if (img_ele !=undefined)
        jQuery('#amb_picture_url').attr("value",img_ele);
    
    //triggered when the url textarea changes to set the new values
    jQuery('#amb_picture_url').on('change',function()
    {
        jQuery('.amb_hover_img img').attr("src",jQuery(this).attr("value"));
    });

    
    //translate one field
    jQuery('input#amb_translate_one_basic_info').on('click',function(e)
    {
        translateOne('basic_info');
    });

    jQuery('input#amb_translate_one_more_info').on('click',function(e)
    {
        translateOne('more_info');
    });

    jQuery('input#amb_translate_one_examples').on('click',function(e)
    {
        translateOne('examples');
    });


    //translate all fields
    jQuery('#amb_translate').click(function()
    {
        //options for character translations
        var hidden = jQuery("input#amb_char_transl").val(); 
        /* if (hidden == "false")
        {     
                alert("Cannot translate before configuring the sections in Alexa Input Fields settings page..");
                return;
        } */ 
        var translations = JSON.parse(hidden);
        //textareas to translate..

        var content_textareas = jQuery(".amb_alexa-content");

        var alexa_textareas = jQuery(".amb_alexa-text");

        var conflictions_textarea="";
        var all_conflictions="";
        for (var i=0;i<content_textareas.length;i++)
        {
            var text_before=content_textareas[i].value;
            conflictions_textarea="";
            for(var j=0;j<translations.length;j++)
            {
                if (content_textareas[i].value.includes(translations[j].amb_char))
                {
                    text_before = content_textareas[i].value;
                    while (text_before.includes(translations[j].amb_char))
                    {
                        text_before= text_before.replace(translations[j].amb_char,translations[j].amb_translation);
                        conflictions_textarea = conflictions_textarea+'"'+translations[j].amb_char+'" found in '+content_textareas[i].name +' and translated to "'+translations[j].amb_translation+'".'+'\n';
                        
                    }
                }
            }
            var name =content_textareas[i].id;
            if (conflictions_textarea=="")
            {    
                conflictions_textarea = "No conflictions found";
                all_conflictions= all_conflictions+conflictions_textarea+" in "+content_textareas[i].name+".\n";
            }
            else
                all_conflictions= all_conflictions+conflictions_textarea+"\n";
            jQuery('textarea#'+name+"_conflictions").val(conflictions_textarea)
            alexa_textareas[i].value=text_before;
        }
        jQuery('#amb_conflictions').val(all_conflictions);
    });

    
});
/**
 * - Translates the text value in the textarea content.
 * - Uses the stored option "amb_char_transl" to find the string to translate and replace it.
 * @param {} name : textarea name.
 */
function translateOne(name)
{
    var content_textarea = jQuery('.amb_alexa-content#amb_'+name);
    var hidden = jQuery("input#amb_char_transl").val();
    var translations = JSON.parse(hidden);

    var conflictions="";

    var text_before=content_textarea.attr('value');
    for (var i=0;i<translations.length;i++)
    {
        if (text_before.includes(translations[i].amb_char))
        {
            while (text_before.includes(translations[i].amb_char))
            {
                text_before = text_before.replace(translations[i].amb_char,translations[i].amb_translation);
                conflictions = conflictions + '\n"'+translations[i].amb_char+'" replaced with "'+translations[i].amb_translation+'"';
                
            }
        }
    }
    jQuery('textarea#amb_Alexa_'+name).val(text_before);
    if (conflictions=="")
        conflictions = "No conflictions found";
    jQuery('textarea#amb_'+name+"_conflictions").val(conflictions);
}
/**
 * - Hides or Shows the textareas selected
 * @param {*} textarea_name 
 * @param {*} button_name 
 */
function hideOrShow(textarea_name,button_name)
{
    if (jQuery('input#'+button_name).attr('value')=='▲')
    {
        jQuery('input#'+button_name).attr('value','▼');
        jQuery('textarea#'+textarea_name).hide();
    }
    else
    {
        jQuery('input#'+button_name).attr('value','▲');
        jQuery('textarea#'+textarea_name).show(); 
    }
}

/**
 * - Loops through the html content and removes any html tags.
 * - Returns importation text for specific section without tags.
 * @param {*} section 
 */
function removeTags(section)
{
    while (section.indexOf("<")!=-1)
        {            
            tag_name = section.substring(section.indexOf("<"),section.indexOf(">")+1);
            tag_name = tag_name.substring(1,tag_name.length-1);
            section = section.replace('<'+tag_name+'>','');
            section = section.replace('</'+tag_name+'>','');
        }
    section = section.replace(/(?:\r\n|\r|\n|\t)/g, ' ');
    section = section.replace(/\s\s+/g, ' ');
    return section.substring(1,section.length);
}
/**
 * - Gets the html code from the chapter informations.
 * - Devides it in sections for importation
 * - Checks if appropriate markers exist (if not then)
 * @param {} option 
 */
function getContents(option)
{
    var hiddens = jQuery("input#amb_section_markers").val();
    if (hiddens == "false")
    {     
            alert("Cannot import before configuring the sections in Alexa Input Fields settings page..");
            return;
    }
    var markers = JSON.parse(hiddens);
    var content = jQuery("textarea").val();
    
    var temp =content;
    //option check here if contains the string to import.
    if (option == "all")
    {   //check to see if it contains the appropriate markers in the text before importing.
        if (content.includes(markers['amb_basic_info']) &&  content.includes(markers['amb_more_info']) && content.includes(markers['amb_example']) )
        {
            var basic_info = temp.substring(temp.indexOf(markers['amb_basic_info']),temp.legth).substring(markers['amb_basic_info'].length,temp.substring(temp.indexOf(markers['amb_basic_info']),temp.legth).indexOf("</div>")-1);
            basic_info = removeTags(basic_info);

            temp =content;
            var more_info = temp.substring(temp.indexOf(markers['amb_more_info']),temp.legth).substring(markers['amb_more_info'].length,temp.substring(temp.indexOf(markers['amb_more_info']),temp.legth).indexOf("</div>")-1);
            more_info = removeTags(more_info);

            temp = content;
            var example = temp.substring(temp.indexOf(markers['amb_example']),temp.legth).substring(markers['amb_example'].length,temp.substring(temp.indexOf(markers['amb_example']),temp.legth).indexOf("</div>")-1);
            example = removeTags(example);
        }
        else
        {
            alert("wrong selection for importation markers. could cause an issue if not corrected");
            return;
        }
    }
    else if (option == "basic_info")
    {   //check to see if it contains the appropriate markers in the text before importing.
        if (content.includes(markers['amb_basic_info']))
        {
            var basic_info = temp.substring(temp.indexOf(markers['amb_basic_info']),temp.legth).substring(markers['amb_basic_info'].length,temp.substring(temp.indexOf(markers['amb_basic_info']),temp.legth).indexOf("</div>")-1);
            basic_info = removeTags(basic_info);
        }
        else
        {
            alert("wrong selection for importation markers. could cause an issue if not corrected");
            return;
        }
    }
    else if (option == "more_info")
    {   //check to see if it contains the appropriate markers in the text before importing.
        if (content.includes(markers['amb_more_info']))
        {
            temp =content;
            var more_info = temp.substring(temp.indexOf(markers['amb_more_info']),temp.legth).substring(markers['amb_more_info'].length,temp.substring(temp.indexOf(markers['amb_more_info']),temp.legth).indexOf("</div>")-1);
            more_info = removeTags(more_info);
        }
        else
        {
            alert("wrong selection for importation markers. could cause an issue if not corrected");
            return;
        }
    }
    else if (option == "examples")
    {   //check to see if it contains the appropriate markers in the text before importing.
        if (content.includes(markers['amb_example']))
        {
            temp = content;
            var example = temp.substring(temp.indexOf(markers['amb_example']),temp.legth).substring(markers['amb_example'].length,temp.substring(temp.indexOf(markers['amb_example']),temp.legth).indexOf("</div>")-1);
            example = removeTags(example);
        }
        else
        {
            alert("wrong selection for importation markers. could cause an issue if not corrected");
            return;
        }
    }
    if (option=="all")
        { 
            jQuery("textarea#amb_basic_info").val(basic_info);
            jQuery("textarea#amb_more_info").val(more_info);
            jQuery("textarea#amb_examples").val(example);
        }
        else if (option=="basic_info")
            jQuery("textarea#amb_basic_info").val(basic_info);
        else if (option=="more_info")
            jQuery("textarea#amb_more_info").val(more_info);
        else if (option=="examples")
            jQuery("textarea#amb_examples").val(example);
}
