jQuery(function($)
{
    jQuery('#export').click(function()
    {
        var hidden = jQuery("input#export_file").val();
        
        var file_output = "module.exports = \n{\n\tEN_US:\n\t{";
        var ids="\n\t\tIDS:\n\t\t[";
        var chapter_names="\n\t\tCHAPTERS_NAMES:\n\t\t[";
        var chapter_json ="\n\t\tCHAPTERS_NAMES_JSON:\n\t\t[";
        var urls="\n\t\tURLS:\n\t\t[";
        var chapters_info="";
        
        var file = JSON.parse(hidden);
        for (var i=0;i<file.length;i++)
        {
            while(file[i].basic_info.includes("APOSTROPHE"))
            {   
                file[i].basic_info = file[i].basic_info.replace("APOSTROPHE","\\'");
            }
            while(file[i].more_info.includes("APOSTROPHE"))
            {   
                file[i].more_info = file[i].more_info.replace("APOSTROPHE","\\'");
            }
            while(file[i].example.includes("APOSTROPHE"))
            {   
                file[i].example = file[i].example.replace("APOSTROPHE","\\'");
            }
        }
        for (var i=0;i<file.length;i++)
        {
            ids = ids+"\n\t\t\t{\n\t\t\t\tgrammatical_rule: '"+file[i].chapter_name+"',\n\t\t\t\tid: '"+file[i].chapter_id+"',\n\t\t\t},";
            chapter_names = chapter_names+"\n\t\t\t'"+file[i].chapter_name+"',";
            chapter_json = chapter_json+"\n\t\t\t'"+file[i].chapter_json+"',";
            urls = urls + "\n\t\t\t{\n\t\t\t\tgrammatical_rule: '"+file[i].chapter_name+"',\n\t\t\t\turl: '"+file[i].url+"',\n\t\t\t},";
            chapters_info = chapters_info + "\n\t\t"+file[i].chapter_json+":\n\t\t[\n\t\t\t{\n\t\t\t\ttitle: 'basic info',\n\t\t\t\tsub_text: '"+file[i].basic_info+"',\n\t\t\t},\n\t\t\t{\n\t\t\t\ttitle: 'more info',\n\t\t\t\tsub_text: '"+file[i].more_info+"',\n\t\t\t},\n\t\t\t{\n\t\t\t\ttitle: 'examples',\n\t\t\t\tsub_text: '"+file[i].example+"',\n\t\t\t}\n\t\t],";
        }
        ids = ids.substring(0,ids.length-1)+"\n\t\t],";
        chapter_names = chapter_names.substring(0,chapter_names.length-1)+"\n\t\t],";
        chapter_json = chapter_json.substring(0,chapter_json.length-1)+"\n\t\t],";
        urls = urls.substring(0,urls.length-1) +"\n\t\t],";
        chapters_info = chapters_info.substring(0,chapters_info.length-1);
        file_output = file_output+ids+chapter_names+chapter_json+urls+chapters_info+"\n\t},\n}";
        

        var dataStr = "data:text/js;charset=utf-8," +encodeURIComponent(file_output);
        
        var downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download","content.json");
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();       
    });

    jQuery('input#show_url').click(function()
    {
        var hidden_show = jQuery("input#show_picture");
        var name;
        if (hidden_show.val() == "" || hidden_show.val()=="Hide Pictures")
            name = "Unhide Pictures";
        else
            name = "Hide Pictures";
        $.ajax({
            type:"POST",
            url:"?",
            success: function()
            {
                jQuery("input#show_picture").val(name);
                console.log("success");
                document.getElementById("input-fields").submit();
            },
        });
    });
    
    jQuery('input#add_new_char').click(function(e)
    {
        var input = $("input#new_char").val();
        if (!input=="")
        {
            $.ajax({
                type: "POST",
                url: "?",
                success: function()
                {
                    $("input#new_char_hidden").val(input);
                    console.log("success");
                    document.getElementById('input-fields').submit();
                }
            });
        }
        else
            alert("Empty Field");
    });
    
    jQuery('input#block_all').click(function(e)
    {
        var hidden_blocked = $("input#blocked");
        var name;
        if (hidden_blocked.val() =="" || hidden_blocked.val()=="Block All Chapters")
         {   
            name = "Unblock All Chapters";
            $("textarea.input-field2").attr('disabled',true);
            $("delete_row").attr('disabled',true);
        }
        else
        {
            name= "Block All Chapters";
            $("textarea.input-field2").attr('disabled',false);
            $("delete_row").attr('disabled',false);
        }
        $.ajax({
            type:"POST",
            url: "?",
            success: function()
            {
                $("input#blocked").val(name);
                console.log("success");
                $('input#block_all').val(name);
                document.getElementById("input-fields").submit();
            }
        });
    });

    jQuery('input#transl_update').click(function(e)
    {
        var new_input_fields = $("textarea.input-field2");
        $.ajax({
            type:"POST",
            url:"?",
            success: function()
            {
                var new_values =[]; 
                for (var i=0;i<new_input_fields.length;i++)
                    {
                        new_values.push(new_input_fields[i].value);
                    }
                $('#new_transaltion_value').val(new_values);
                $("input#new_translation_hidden").val("ready");
                console.log("done success"); 
                document.getElementById('input-fields').submit();
            },
            error: function()
            {
                console.log("error");
            }

        });
    });

    //save button on begin and end sections
    jQuery('input#save_sections').click(function()
    {
        //even markers are end and odd are begin..
        var markers = $("textarea.input-marker");
        var sections = $("textarea.input-section");
        var final_values = [];
        for (var i=0;i<sections.length;i++)
            final_values.push(sections[i].value);//,markers[i*2+1].value,markers[i*2].value)
        $.ajax({
            type: "POST",
            url: "?",
            success: function()
            {
                $("input#markers_update").val(final_values);
                console.log("success");
                document.getElementById('section-fields').submit();
            },
            error: function()
            {
                console.log("error");
            }

        });
    });
    jQuery('input#delete_row').click(function()
    {
        var name=$(this).attr('name')+" ";
        $.ajax({
            type:"POST",
            url:"?",
            success: function(){
                $('#delete_translation').val(name);          
                console.log("done success"); 
                document.getElementById('input-fields').submit();
            }
        });
    });
});


