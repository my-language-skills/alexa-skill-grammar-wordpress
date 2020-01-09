jQuery( function ( $ ){
    //flag to see if edit_btn clicked
    var btn_clicked = false;
    //for disabling all input fields when entering first time to edit information
    //testing hidden input
    var user_role = $("input#user_role").val();

    //blocks fields if meta value of alexa-content-editing is blocked
    if($('#block_btn').attr('value')=='Unblock')
    {
        $('.alexa-text').attr('readOnly',true); 
        btn_clicked=true;
        $('input#update').prop('disabled',true);
        $('input#translate').prop('disabled',true);
        $('input#import_one').prop('disabled',true);
        $('input#translate_one').prop('disabled',true);
    }

    //Function when mouse hover over blocked textarea to show "Block Field"
    /* $('.field').hover(function(evt)
    {
        if (btn_clicked==true)
       {     var x = evt.pageX - (4*$(this).offset().left);
            $(this).find($('span')).css("visibility","visible");
            $(this).find($('span')).css({'transform' : 'translate('+x+'px,0px)'});}
    },function()
    {
        $(this).find($('span')).css("visibility","hidden");
    }); */

    //for enabling to input data to input fields
    $('#block_btn').click(function(){
        if (user_role=="administrator")
        {
            var prev_value = $('input#block_btn').val();
            var value;
            var alexa_input = $('.alexa-text');
            if (prev_value=="Block")
            {
                    value="Unblock";
                    alexa_input.attr('readOnly',true);
                    $('input#update').prop('disabled',true);
                    $('input#translate').prop('disabled',true);
                    $('input#import_one').prop('disabled',true);
                    $('input#translate_one').prop('disabled',true);
            }
            else
            {
                value="Block";
                alexa_input.attr('readOnly',false);
                $('input#update').prop('disabled',false);
                $('input#translate').prop('disabled',false);
                $('input#import_one').prop('disabled',false);
                $('input#translate_one').prop('disabled',false);
            }

            $.ajax({
                type:"POST",
                url: "?",
                success: function(data)
                {
                    $('#block_btn').val(value);
                    $('input#block_value').val(value);
                    if (value=="Unblock")
                        alexa_input.attr('readOnly',true);
                    else
                        alexa_input.attr('readOnly',false);
                        
                    console.log("success");
                }
            });
        }
        else
        {
            console.log("bruuh you are not even allowed for that");
            alert("Admin rights required for that action");
        }
    });

    //to show and hide information
    jQuery('input#drop-down').click(function(e)
    {
        var name=$(this).attr('name');
        if ($(this).attr('value')=='▲')
            {
                $(this).attr('value','▼');
                if (name=="conflictions")
                    $('textarea#'+name).hide();
                else
                    $('textarea#'+name+"_conflictions").hide();
            }
        else
        { 
            $(this).attr('value','▲');
            if (name=="conflictions")
                    $('textarea#'+name).show();
            else
                $('textarea#'+name+"_conflictions").show();
        }

    });

    //import one field
    jQuery('input#import_one').click(function(e)
    {
        var name=$(this).attr('name');
        getContents(name);
    });

    //update data from content
    $('#update').click(function(){
        getContents("all");
    });

    //adding the URL to the pictureUrl textarea
    var img_ele=$('.hover_img img').attr('src');
    if (img_ele !=undefined)
        $('#picture_url').attr("value",img_ele);
    
    //triggered when the url textarea changes to set the new values
    $('#picture_url').change(function()
    {
        $('.hover_img img').attr("src",$(this).attr("value"));
    });
    //translate one field
    jQuery('input#translate_one').click(function(e)
    {
        var name=$(this).attr('name');
        var content_textarea=$('.alexa-content#'+name);
        var alexa_textarea = $('.alexa-text#Alexa_'+name);
        var hidden = $("input#char_transl").val();
        var translations = JSON.parse(hidden);

        var conflictions="";
        
        var text_before=content_textarea.attr('value');
        for (var i=0;i<translations.length;i++)
        {
            if (text_before.includes(translations[i].char))
            {
                while (text_before.includes(translations[i].char))
                {
                    text_before = text_before.replace(translations[i].char,translations[i].translation);
                    conflictions = conflictions + '\n"'+translations[i].char+'" replaced with "'+translations[i].translation+'"';
                  
                }
            }
        }
        $('textarea#Alexa_'+name).val(text_before);
        if (conflictions=="")
            conflictions = "No conflictions found";
        $('textarea#'+name+"_conflictions").val(conflictions);
        
    });

    jQuery('#translate').click(function()
    {
        //options for character translations
        var hidden = $("input#char_transl").val();

        var translations = JSON.parse(hidden);
        //textareas to translate..

        var content_textareas = $(".alexa-content");

        var alexa_textareas = $(".alexa-text");

        var conflictions_textarea="";
        var all_conflictions="";
        for (var i=0;i<content_textareas.length;i++)
        {
            var text_before=content_textareas[i].value;
            conflictions_textarea="";
            for(var j=0;j<translations.length;j++)
            {
                if (content_textareas[i].value.includes(translations[j].char))
                {
                    text_before = content_textareas[i].value;
                    while (text_before.includes(translations[j].char))
                    {
                        text_before= text_before.replace(translations[j].char,translations[j].translation);
                        conflictions_textarea = conflictions_textarea+'"'+translations[j].char+'" found in '+content_textareas[i].name +' and translated to "'+translations[j].translation+'".'+'\n';
                        
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
            $('textarea#'+name+"_conflictions").val(conflictions_textarea)
            alexa_textareas[i].value=text_before;
        }
        $('#conflictions').val(all_conflictions);
    });

    
});
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
function getContents(option)
{
    var hiddens = jQuery("input#section_markers").val();
    var markers = JSON.parse(hiddens);
    
    var content = jQuery("textarea").val();
    
    var temp =content;
    var basic_info = temp.substring(temp.indexOf(markers['basic_info']),temp.legth).substring(markers['basic_info'].length,temp.substring(temp.indexOf(markers['basic_info']),temp.legth).indexOf("</div>")-1);
    basic_info = removeTags(basic_info);

    temp =content;
     var more_info = temp.substring(temp.indexOf(markers['more_info']),temp.legth).substring(markers['more_info'].length,temp.substring(temp.indexOf(markers['more_info']),temp.legth).indexOf("</div>")-1);
    more_info = removeTags(more_info);

    temp = content;
    var example = temp.substring(temp.indexOf(markers['example']),temp.legth).substring(markers['example'].length,temp.substring(temp.indexOf(markers['example']),temp.legth).indexOf("</div>")-1);
    example = removeTags(example);
    
    if (option=="all")
        { 
            jQuery("textarea#basic_info").val(basic_info);
            jQuery("textarea#more_info").val(more_info);
            jQuery("textarea#examples").val(example);
        }
        else if (option=="basic_info")
            jQuery("textarea#basic_info").val(basic_info);
        else if (option=="more_info")
            jQuery("textarea#more_info").val(more_info);
        else if (option=="examples")
            jQuery("textarea#examples").val(example);
}
/* function getContents(option)
{
    var markers = JSON.parse($("input#section_markers").val());
    console.log(markers);
    
    var content = $("textarea").val();
    var temp; //used to store the content value after each begin section..
    //first add the Begin lines (easier because exact/unique line is replaced)
    content = content.replace(markers['basic_info']['phrase'],markers['basic_info']['begin']);
    content = content.replace(markers['more_info']['phrase'],markers['more_info']['begin']);
    content = content.replace(markers['example']['phrase'],markers['example']['begin']);

    //change content data and remove unecessary data
    //we have to find the position of "</div>" after every begin. to close the section.
    temp = content.substring(content.indexOf(markers['basic_info']['begin']),content.length);
    temp = temp.replace("</div>",markers['basic_info']['end']);
    var basic_info = temp.substring(temp.indexOf(markers['basic_info']['begin'])+markers['basic_info']['begin'].length,temp.indexOf(markers['basic_info']['end'])-1);
    basic_info = removeTags(basic_info);

    temp = content.substring(content.indexOf(markers['more_info']['begin']),content.length);
    temp = temp.replace("</div>",markers['more_info']['end']);
    var more_info = temp.substring(temp.indexOf(markers['more_info']['begin'])+markers['more_info']['begin'].length,temp.indexOf(markers['more_info']['end'])-1);
    more_info = removeTags(more_info);

    temp = content.substring(content.indexOf(markers['example']['begin']),content.length);
    temp = temp.replace("</div>",markers['example']['end']);
    var example = temp.substring(temp.indexOf(markers['example']['begin'])+markers['example']['begin'].length,temp.indexOf(markers['example']['end'])-1);
    example = removeTags(example);



    basic_info = basic_info.replace(/(?:\r\n|\r|\n|\t)/g, ' ');
    basic_info = basic_info.replace(/\s\s+/g, ' ');
    more_info = more_info.replace(/(?:\r\n|\r|\n|\t)/g, ' ');
    more_info = more_info.replace(/\s\s+/g, ' ');
    example = example.replace(/(?:\r\n|\r|\n|\t)/g, ' ');
    example = example.replace(/\s\s+/g, ' ');

    
    console.log(basic_info);
    console.log(more_info);
    console.log(example);

    if (option=="all")
        { 
            $("textarea#basic_info").val(basic_info);
            $("textarea#more_info").val(more_info);
            $("textarea#examples").val(example);
        }
        else if (option=="basic_info")
            $("textarea#basic_info").val(basic_info);
        else if (option=="more_info")
            $("textarea#more_info").val(more_info);
        else if (option=="examples")
            $("textarea#examples").val(example);
} */

/* 
    function getContents(option)
    {
        

        var content = $("textarea").val();
        content = content.replace('<div id="introduction" class="box" title="Introduction">','BEGININTRO');
        content = content.replace('</div>\n<div id="form" class="box" title="Form">','ENDINTRO\nBEGINFORM');
        content = content.replace('</div>\n<div id="example" class="box" title="Example">','ENDFORM\nBEGINEXAMPLE');
        content = content.replace('</div>\n<div id="use" class="box" title="Use">','ENDEXAMPLE\nBEGINUSE');
        content = content.replace('</div>\n<div id="extension" class="box" title="Extension">','ENDUSE\nBEGINEXTENSION');
        content = content.replace('</div>','ENDEXTENSION');
        
        //get example text
        var char_index=0;
        var startchar = content.indexOf('BEGINEXAMPLE')+"BEGINEXAMPLE".length;
        var endchar = content.indexOf('ENDEXAMPLE');
        
        while (content.charAt(char_index+startchar)=='\n')
            char_index++;
        var example = content.substring(char_index+startchar,endchar);
        while (example.indexOf("<")!=-1)
        {            
            tag_name = example.substring(example.indexOf("<"),example.indexOf(">")+1);
            tag_name = tag_name.substring(1,tag_name.length-1);
            example = example.replace('<'+tag_name+'>','');
            example = example.replace('</'+tag_name+'>','');
        } 

        //get basic info text
        char_index=0;
        startchar = content.indexOf('BEGINEXTENSION')+"BEGINEXTENSION".length;
        endchar = content.indexOf('ENDEXTENSION');

        while (content.charAt(char_index+startchar)=='\n')
            char_index++;
        var basic_info = content.substring(char_index+startchar,endchar);
        while (basic_info.indexOf("<")!=-1)
        {            
            tag_name = basic_info.substring(basic_info.indexOf("<"),basic_info.indexOf(">")+1);
            tag_name = tag_name.substring(1,tag_name.length-1);
            basic_info = basic_info.replace('<'+tag_name+'>','');
            basic_info = basic_info.replace('</'+tag_name+'>','');
        }   
        //get more info text
        char_index=0;
        startchar = content.indexOf('BEGINUSE')+"BEGINUSE".length;
        endchar = content.indexOf('ENDUSE');

        while (content.charAt(char_index+startchar)=='\n')
            char_index++;
        var more_info = content.substring(char_index+startchar,endchar);
        while (more_info.indexOf("<")!=-1)
        {            
            tag_name = more_info.substring(more_info.indexOf("<"),more_info.indexOf(">")+1);
            tag_name = tag_name.substring(1,tag_name.length-1);
            more_info = more_info.replace('<'+tag_name+'>','');
            more_info = more_info.replace('</'+tag_name+'>','');
        }
        //removing new line space and double spacing for alexa content preparation 
        basic_info = basic_info.replace(/(?:\r\n|\r|\n)/g, ' ');
        basic_info = basic_info.replace(/\s\s+/g, ' ');
        more_info = more_info.replace(/(?:\r\n|\r|\n)/g, ' ');
        more_info = more_info.replace(/\s\s+/g, ' ');
        example = example.replace(/(?:\r\n|\r|\n)/g, ' ');
        example = example.replace(/\s\s+/g, ' ');
        
        if (option=="all")
        { 
            $("textarea#basic_info").val(basic_info);
            $("textarea#more_info").val(more_info);
            $("textarea#examples").val(example);
        }
        else if (option=="basic_info")
            $("textarea#basic_info").val(basic_info);
        else if (option=="more_info")
            $("textarea#more_info").val(more_info);
        else if (option=="examples")
            $("textarea#examples").val(example);

    } 
*/