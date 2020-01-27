jQuery(function(jQuery)
{
    //when export button clicked
    jQuery('#amb_export').on("click",function()
    {
        var hidden = jQuery("input#amb_export_file").val();
        
        var file_output = "module.exports = \n{\n\tEN_US:\n\t{";
        var ids="\n\t\tIDS:\n\t\t[";
        var chapter_names="\n\t\tCHAPTERS_NAMES:\n\t\t[";
        var chapter_json ="\n\t\tCHAPTERS_NAMES_JSON:\n\t\t[";
        var urls="\n\t\tURLS:\n\t\t[";
        var chapters_info="";
        //after initial object attributes set.
        var file = JSON.parse(hidden);
        for (var i=0;i<file.length;i++)
        {//looping through the information for all chapters. restructuring for alexa content.
            while(file[i].amb_basic_info.includes("APOSTROPHE"))
            {   
                file[i].amb_basic_info = file[i].amb_basic_info.replace("APOSTROPHE","\\'");
            }
            while(file[i].amb_more_info.includes("APOSTROPHE"))
            {   
                file[i].amb_more_info = file[i].amb_more_info.replace("APOSTROPHE","\\'");
            }
            while(file[i].amb_example.includes("APOSTROPHE"))
            {   
                file[i].amb_example = file[i].amb_example.replace("APOSTROPHE","\\'");
            }
        }
        for (var i=0;i<file.length;i++)
        {//looping through chapters again. Setting the attributes and values in a text form.
            ids = ids+"\n\t\t\t{\n\t\t\t\tgrammatical_rule: '"+file[i].amb_chapter_name+"',\n\t\t\t\tid: '"+file[i].amb_chapter_id+"',\n\t\t\t},";
            chapter_names = chapter_names+"\n\t\t\t'"+file[i].amb_chapter_name+"',";
            chapter_json = chapter_json+"\n\t\t\t'"+file[i].amb_chapter_json+"',";
            urls = urls + "\n\t\t\t{\n\t\t\t\tgrammatical_rule: '"+file[i].amb_chapter_name+"',\n\t\t\t\turl: '"+file[i].amb_url+"',\n\t\t\t},";
            chapters_info = chapters_info + "\n\t\t"+file[i].amb_chapter_json+":\n\t\t[\n\t\t\t{\n\t\t\t\ttitle: 'basic info',\n\t\t\t\tsub_text: '"+file[i].amb_basic_info+"',\n\t\t\t},\n\t\t\t{\n\t\t\t\ttitle: 'more info',\n\t\t\t\tsub_text: '"+file[i].amb_more_info+"',\n\t\t\t},\n\t\t\t{\n\t\t\t\ttitle: 'examples',\n\t\t\t\tsub_text: '"+file[i].amb_example+"',\n\t\t\t}\n\t\t],";
        }
        //adding the footer of the json file, in text form.
        ids = ids.substring(0,ids.length-1)+"\n\t\t],";
        chapter_names = chapter_names.substring(0,chapter_names.length-1)+"\n\t\t],";
        chapter_json = chapter_json.substring(0,chapter_json.length-1)+"\n\t\t],";
        urls = urls.substring(0,urls.length-1) +"\n\t\t],";
        chapters_info = chapters_info.substring(0,chapters_info.length-1);
        file_output = file_output+ids+chapter_names+chapter_json+urls+chapters_info+"\n\t},\n}";
        
        //custom code for creating the .js file and parsing the text form of information in  a way alexa reads.
        var dataStr = "data:text/js;charset=utf-8," +encodeURIComponent(file_output);
        
        var downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download","content.js");
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();       
    });
    //when hide pictures button clicked
    jQuery('input#amb_show_url').on("click",function()
    {
        var hidden_show = jQuery("input#amb_show_picture");
        var name;
        //setting appropriate name to the option that will store the button feature and name
        if (hidden_show.val() == "" || hidden_show.val()=="Hide Pictures")
            name = "Unhide Pictures";
        else
            name = "Hide Pictures";
        jQuery.ajax({
            type:"POST",
            url:"?",
            success: function()
            {
                jQuery("input#amb_show_picture").val(name);
                console.log("success");
                document.getElementById("amb_input-fields").submit();
            },
        });
    });
    //when add new button clicked
    jQuery('input#amb_add_new_char').on("click",function(e)
    {
        var input = jQuery("input#amb_new_char").val();
        if (!input=="")
        {
            jQuery.ajax({
                type: "POST",
                url: "?",
                success: function()
                {
                    jQuery("input#amb_new_char_hidden").val(input);
                    console.log("success");
                    document.getElementById('amb_input-fields').submit();
                }
            });
        }
        else
            alert("Empty Field");
    });
    //when blocked all button clicked
    jQuery('input#amb_block_all').on("click",function(e)
    {
        var hidden_blocked = jQuery("input#amb_blocked");
        var name;
        //setting appropriate name to the option that will store the button feature and name
        if (hidden_blocked.val() =="" || hidden_blocked.val()=="Block All Chapters")
         {   
            name = "Unblock All Chapters";
            jQuery("textarea.amb_input-field2").attr('disabled',true);
            jQuery("amb_delete_row").attr('disabled',true);
        }
        else
        {
            name= "Block All Chapters";
            jQuery("textarea.amb_input-field2").attr('disabled',false);
            jQuery("amb_delete_row").attr('disabled',false);
        }
        jQuery.ajax({
            type:"POST",
            url: "?",
            success: function()
            {
                jQuery("input#amb_blocked").val(name);
                console.log("success");
                jQuery('input#amb_block_all').val(name);
                document.getElementById("amb_input-fields").submit();
            }
        });
    });
    //when update Translations button clicked
    jQuery('input#amb_transl_update').on("click",function(e)
    {
        var new_input_fields = jQuery("textarea.amb_input-field2");
        jQuery.ajax({
            type:"POST",
            url:"?",
            success: function()
            {
                var new_values =[]; 
                for (var i=0;i<new_input_fields.length;i++)
                    {
                        new_values.push(new_input_fields[i].value);
                    }
                jQuery('#amb_new_transaltion_value').val(new_values);
                jQuery("input#amb_new_translation_hidden").val("ready");
                console.log("done success"); 
                document.getElementById('amb_input-fields').submit();
            },
            error: function()
            {
                console.log("error");
            }

        });
    });

    //save button on begin and end sections
    jQuery('input#amb_save_sections').on("click",function()
    {
        //even markers are end and odd are begin..
        var markers = jQuery("textarea.amb_input-marker");
        var sections = jQuery("textarea.amb_input-section");
        var final_values = [];
        for (var i=0;i<sections.length;i++)
            final_values.push(sections[i].value.substr(1,sections[i].value.length-2));//removes the "<" ">" characters before storing to database.
        jQuery.ajax({
            type: "POST",
            url: "?",
            success: function()
            {
                jQuery("#amb_markers_update").val(final_values);
                console.log("success");
                document.getElementById('amb_section-fields').submit();
            },
            error: function()
            {
                console.log("error");
            }

        });
    });
    //deleted a row
    jQuery('input#amb_delete_row').on("click",function()
    {
        var name=jQuery(this).attr('name')+" ";
        jQuery.ajax({
            type:"POST",
            url:"?",
            success: function(){
                jQuery('#amb_delete_translation').val(name);          
                console.log("done success"); 
                document.getElementById('amb_input-fields').submit();
            }
        });
    });
});


