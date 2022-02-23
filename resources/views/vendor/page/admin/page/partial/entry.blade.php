
<style>
.bottom-button
{
    display: flex;
    clear: both;
}
</style>

<style type="text/css">
    
    .custom_library .modal-dialog
    {
        max-width: 1600px;
        width: inherit;
    }
    .custom_library .library_load ul
    {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        margin: 0;
        padding: 0;
        max-height: 500px;
        overflow-y: scroll;
        justify-content: center;
    }
    .custom_library .library_load .thumbnail .centered
    {
        width: 200px;
        height: 200px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .custom_library .library_load .thumbnail img
    {
        position: relative;
        box-shadow: inset 0 0 15px rgb(0 0 0 / 10%), inset 0 0 0 1px rgb(0 0 0 / 5%);
        background: #f0f0f1;
        cursor: pointer;
        object-fit: cover;
        width: 100%;
        height: 100%;
    }
    
    /*No need to add parent class*/
        .upload_media
        {
            border-radius: 2px;
            background-color: #002a3a;
            min-height: 60px;
            line-height: 20px;
            padding: 8px 0;
            display: flex;
            vertical-align: middle;
            align-items: center;
            text-align: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            max-width: 100%;
            margin: auto;
            margin-top: 10px;
        }

    /*No need to add parent class*/


    .custom_library .library_load
    {
        width: 80%;
        border-right: 1px solid #666666;
        padding: 0;
        margin: 0;
    }
    .custom_library .library-details
    {
         width: 20%;
         padding-left: 15px;
    }
    .custom_library #media
    {
       padding: 0;
        height: inherit;
        overflow: hidden;
    }
    .custom_library .media-frame-title h1
    {
        font-size: 24px;
        padding-left: 15px;
        padding-top: 0;
        margin: 0;
        padding-bottom: 30px;
    }
    .custom_library .modal-body {
        position: relative;
        padding: 15px 0 0;    
    }
    .custom_library .modal-footer
    {
        border: none;
        padding: 5px;
        padding-bottom: 12px;
    }
    .custom_library .btn{
        box-shadow: none;
        background: no-repeat;
        border-radius: 0px;
        border: none;
        font-size: 20px;
        color: #000;
        font-weight: normal;
    }
     .custom_library .modal-header
     {
        border: none;
        padding-bottom: 0;
        position: relative;
     }
    .custom_library .modal-header .btn
    {
        position: absolute;
        right: 15px;
        top: 5px;
        cursor: pointer;
        z-index: 9999;
        font-size: 32px;
        color: #000;
        opacity: 1;
        border: none;
        outline: none;
    }
    .custom_library .nav-tabs-custom a{
        color: #000;
        opacity: 1;
        font-size: 16px;
        font-weight: 500;
    }

    .library-details .thumbnail img
    {
        width: 100%;
    }

    .library-details ul
    {
        list-style: none;
    }

    .library-details .thumbnail
    {
        max-width: 250px;
    }

    .library-details ul{
        padding: 0;
        margin: 0;
    }

    .custom_library .nav-tabs-custom
    {
        margin-bottom: 0;
    }

    .custom_library .modal-footer button
    {
        background: #4086f4;
        border-radius: 0px;
        padding: 12px 20px;
        border: none!important;
        font-weight: normal;
        font-size: 15px;
        margin-left: 30px;
        box-shadow: none;
        transition: 0.5s;
        background-color: #002a3a!important;
        color: #fff;
        margin-top: 12px;
    }
    .wrap-uload-media{
        display: flex;
    }
    .image-src
    {
        max-width: 200px;
        margin: auto;
        margin-top: 12px;
    }
    #multi-file-upload-ajax
    {
        text-align: center;
    }

</style>


<div class="tab-pane disabled active row" id="details">

                <div class="col-md-12 col-lg-12">
                    
                {!! Form::text('name')
                -> label(trans(trans('page::page.label.name')))
                -> placeholder(trans(trans('page::page.placeholder.name')))
                !!}

                {!! Form::textarea('content')
                -> label(trans('page::page.label.content'))
                -> value(e($page['content']))
                -> dataUpload(url($page->getUploadURL('content')))
                -> addClass('html-editor')
                -> placeholder(trans('page::page.placeholder.content'))
                !!}
                    
                <?php
                    $sel = $page->status;
                ?>
                <div class="form-group">
                    <label for="status" class="control-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1" <?php if($sel == "1"){ echo "selected";}?>>Published</option>
                        <option value="0" <?php if($sel == "0"){ echo "selected";}?>>Draft</option>
                    </select>
                </div>
                    
                </div>
            </div>
            <div class="tab-pane disabled row" id="metatags">
                <div class="col-md-6 col-lg-6">
                    {!! Form::text('title')
                    -> label(trans('page::page.label.title'))
                    -> placeholder(trans('page::page.placeholder.title'))
                    !!}
                    {!! Form::text('heading')
                    -> label(trans('page::page.label.heading'))
                    -> placeholder(trans('page::page.placeholder.heading'))
                    !!}
                    {!! Form::text('sub_heading')
                    -> label(trans('page::page.label.sub_heading'))
                    -> placeholder(trans('page::page.placeholder.sub_heading'))
                    !!}
                    {!! Form::text('meta_title')
                    -> label(trans('page::page.label.meta_title'))
                    -> placeholder(trans('page::page.placeholder.meta_title'))
                    !!}
                </div>

                <div class="col-md-6 col-lg-6">
                    {!! Form::text('meta_keyword')
                    -> label(trans('page::page.label.meta_keyword'))
                    -> placeholder(trans('page::page.placeholder.meta_keyword'))
                    !!}
                    {!! Form::textarea('meta_description')
                    -> label(trans('page::page.label.meta_description'))
                    -> rows(3)
                    -> placeholder(trans('page::page.placeholder.meta_description'))
                    !!}
                    {!! Form::textarea('abstract')
                    -> label(trans('page::page.label.abstract'))
                    -> rows(3)
                    -> placeholder(trans('page::page.placeholder.abstract'))
                    !!}
                </div>
            </div>
            <div class="tab-pane disabled row" id="settings">
                <div class="col-md-6 ">
                    {!! Form::range('order')
                    -> label(trans('page::page.label.order'))
                    -> placeholder(trans('page::page.placeholder.order'))
                    !!}

                    {!! Form::text('slug')
                    -> label(trans('page::page.label.slug'))
                    -> append('.html')
                    -> placeholder(trans('page::page.placeholder.slug'))
                    !!}

                    {!! Form::select('view')
                    -> options(trans('page::page.options.view'))
                    -> label(trans('page::page.label.view'))
                    -> placeholder(trans('page::page.placeholder.view'))
                    !!}
                </div>
                <div class='col-md-6'>
                    {!! Form::hidden('compile')
                    -> forceValue('0')
                    !!}

                    {!! Form::select('compile')
                    -> options(trans('page::page.options.compile'))
                    -> label(trans('page::page.label.compile'))
                    -> placeholder(trans('page::page.placeholder.compile'))
                    !!}

                    {!! Form::select('category_id')
                    -> options(trans('page::page.options.category'))
                    -> label(trans('page::page.label.category_id'))
                    -> placeholder(trans('page::page.placeholder.category_id'))
                    !!}

 
                </div>
            </div>



    <div class="tab-pane disabled" id="sections">
                <p>(Shortcode - {heading},{subheading},{imageurl} use inside the content)</p>
                <div id="sortable">  
                    
                <input type="hidden" class="pageid" value="{{$page->id}}">

                    @isset($sections)   
                    @foreach($sections as $key=>$section)
                    
                    <div class='col-md-12 col-sm-12 section_divide ui-sortable-handle' title="<?php echo $section->id;?>" data-listing-price="<?php echo $section->order_by;?>">
<!--
                        <p class="move-icons">  <i class="fa fa-arrows" aria-hidden="true"></i> Move section</p>
                        
-->
                           <div class="col-xl-12">
                               
                                <div class="col-md-8">
                                    <p class="move-icons"><i class="fa fa-arrows" aria-hidden="true"></i> Move section</p>
                                </div>
                                <div class="col-md-2">
                                     <div class="form-group">
                                        <label>ID</label>
                                        <input class="form-control" placeholder="Id" id="classid" type="text" name="section_sectionid[{{$section->id}}]" value="{!!$section->sectionid!!}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Class Name</label>
                                        <input class="form-control" placeholder="Class Name" id="classname" type="text" name="section_sectionclassname[{{$section->id}}]" value="{!!$section->sectionclassname!!}">
                                    </div>
                                </div>
                               <div class="crosssections">
                                     <div class="button">
                                         Delete
                                         <input type="hidden" value="{{$section->id}}" class="sectionrowid">
                                     </div>
                               </div>
                            </div>

                            <div class='col-md-4 col-sm-4'>
                                <input type="hidden" name="section[{{$section->id}}]" value="{{$section->id}}">
                                <div class="form-group">
                                    <label for="name" class="control-label">Heading {heading}</label>
                                    <input class="form-control" placeholder="Enter Name" id="name" type="text" name="section_name[{{$section->id}}]" value="{!!$section->name!!}">
                                </div>
                            </div>
                        
                             <div class='col-md-4 col-sm-4'>
                                <div class="form-group">
                                    <label for="heading" class="control-label">Sub Heading {subheading}</label>
                                    <input class="form-control" placeholder="Enter Heading" id="heading" type="text" name="section_heading[{{$section->id}}]" value="{!!$section->heading!!}">
                                </div>
                            </div>
                             <div class='col-md-4 col-sm-4'>
                                 
                                 
<!--
                                <div class="form-group">
                                    <label for="image" class="control-label">Image {imageurl} (Notes -Inside src="" Attr.)</label>
                                    <input class="form-control" placeholder="You can add a image" id="image" type="file" name="section_image[{{$section->id}}]" >
                                </div>
-->
                                @if($section->image)
<!--
                                   <div class="wrap-service-image">  
                                        <div class="wrap-image-page">
                                            <img src="{{ url($section->image) }}" class="img-thumbnail">
                                        </div>
                                         <div class="crosssmap"  data-value="0" style="color:#000;">
                                            <i class="fa fa-times-circle" aria-hidden="true"></i>

                                            <input type="hidden" class="imagepath" value="{{$section->image}}">
                                            <input type="hidden" class="sectionid" value="{{$section->id}}">
                                        </div> 
                                    </div>      
-->
                                @endif
                                 
                                 
                                <div class="form-group">
                                    
                                    <p class="upload_media">
                                       Set featured image
                                       <input type="hidden" id="classid{{$section->id}}" class="section_image_custom" name="section_image[{{$section->id}}]" value="{{$section->image}}" data-id="{{$section->id}}">
                                    </p>
                                    
                                    <div class="image-src"  id="classimgsrc{{$section->id}}">
                                        @if($section->image)
                                        <div class="wrap-service-image">  
                                            <div class="wrap-image-page">
                                                <img src="{{ url($section->image) }}" class="img-thumbnail">
                                            </div>
                                            <div class="crosssmap"  data-value="0" style="color:#000;">
                                                <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                <input type="hidden" class="imagepath" value="{{$section->image}}">
                                                <input type="hidden" class="sectionid" value="{{$section->id}}">
                                            </div> 
                                        </div>      
                                        @endif
                                    </div>
                                    
                                    
                                </div>

                              
                                 
                            </div>
                            <div class='col-md-12 col-sm-12 custom-front'>
                                <div class="form-group">
                                    <label for="body" class="control-label show_content ">Add Content <i class="fa fa-long-arrow-down"></i></label>
                                    <div class="comment_box">
                                        <textarea class="form-control" style="height:30rem" placeholder="Enter Description" id="body" name="section_body[{{$section->id}}]">{!!$section->body!!}</textarea>
                                    </div>
                                </div>
                            </div>
                    </div>

                    @endforeach
                    @endisset
                </div>

           
               


               <div class="col-12 bottom-button">
                    <button type="button" class="add-section">Add section</button>
                    <button type="submit" class="btn btn-save theme-button">Save</button>
                </div>


            </div>







            
            @if ($mode == 'create')
            <div class="tab-pane row" id="images">
                <div class="form-group">
                    <label for="images" class="control-label col-lg-12 col-sm-12 text-left">
                        {{trans('page::page.label.images') }}
                    </label>
                    <div class='col-lg-6 col-sm-12'>
                        {!! $page->files('images')
                        ->url($page->getUploadUrl('images'))
                        ->uploader()!!}
                    </div>                            
                </div>
            </div>
            @elseif ($mode == 'edit')
            <div class="tab-pane row" id="images">
                <div class="form-group">
                    <label for="images" class="control-label col-lg-12 col-sm-12 text-left">
                        {{trans('page::page.label.images') }}
                    </label>
                    <div class='col-lg-6 col-sm-12'>
                        {!! $page->files('images')
                        ->url($page->getUploadUrl('images'))
                        ->uploader()!!}
                    </div>

                </div>
            </div>
            @elseif ($mode == 'show')
            <div class="tab-pane disabled row" id="images">
                <div class='col-md-6'>
                    {!! $page->files('banner') !!}
                </div>
                <div class='col-md-6'>
                    {!! $page->files('images') !!}
                </div>
            </div>
            @endif

<!-- 
<div class='col-md-5 col-sm-5'>
    <div class="form-group image_crop">
            <label for="image" class="control-label">Image</label>
            <input id="file" class="cropfile" type="file"  name="image" />
            <h2>Image cropper (Jcrop)</h2>
            <div id="jcrop" class="jcrop"></div>
            <h2>Real size cropped image preview (canvas)</h2>
            <canvas id="canvas" class="canvas"></canvas>
            <input id="png"  class="png" type="hidden"  />
    </div>
</div>
 -->
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery("#_sections").click(function(){
        jQuery(".sorted").show();
    });
    jQuery("#_meta").click(function(){
        jQuery(".sorted").hide();
    });
    jQuery("#_page").click(function(){
        jQuery(".sorted").hide();
    });
});
    
jQuery("#sortable").sortable({
        start: function( event, ui ) { 
        jQuery(ui.item).addClass("yellow");
        },
        stop:function( event, ui ) { 
        jQuery(ui.item).removeClass("yellow");
        }
    });
    
        jQuery(document).ready(function(){
            var divList = $(".ui-sortable-handle");
            divList.sort(function(a, b){
            return $(a).data("listing-price")-$(b).data("listing-price")
            });
            
            $("#sortable").html(divList);
            
            jQuery(".sorted").click(function(){
            var theArray = [];
            $('.ui-sortable-handle').each(function() { 
            var theTitle = jQuery(this).attr('title'); 
            theArray.push(theTitle); 
            }); 
                
            var pageid = jQuery('.pageid').val(); 
            var array_data =  {
            "pageid":pageid,     
            "sectionid":theArray    
            }
            jQuery.ajax({
                type: "POST",
                url: '<?php echo url('admin/page/page/sorted');?>',
                data: array_data,
                success: function(response)
                {
                alert("success");
                }        
            });
                
            });  
        });    
    
    
jQuery(document).ready(function(e) {
    jQuery('.comment_box').hide();
    
    jQuery(".show_content").click(function(){
        jQuery(this).toggleClass('iconsrotate');
        jQuery(this).next().toggle();
    });
});


jQuery(".crosssections .button").click(function(){
    
    let cnf = confirm("Are you sure want to delete?");
  
    let sectionid =  jQuery(this).find(".sectionrowid").val();
    if(cnf == true){
        jQuery(this).parent().parent().parent().hide();
        if(sectionid)
        {
            var array_data =  {
                "sectionid":sectionid    
            }
            jQuery.ajax({
                type: "POST",
                url: '<?php echo url('admin/page/page/sectiondelete');?>',
                data: array_data,
                success: function(response)
                {
                    alert("successfully delete");

                }        
            });
        }
    }
    
}); 
 
    
jQuery(".crosssmap").click(function(){
      
        var sectionid = jQuery(this).find(".sectionid").val(); 
        var imagepath = jQuery(this).find(".imagepath").val(); 
        var pageid = jQuery('.pageid').val(); 
        let tr = confirm('Are you sure want to delete?');
    
        if(tr == true)
        { 
            jQuery(this).parent().hide();

            var array_data =  {
                "pageid":pageid,     
                "sectionid":sectionid,
                "imagepath":imagepath
            }
            jQuery.ajax({
                type: "POST",
                url: '<?php echo url('admin/page/page/deleteimage');?>',
                data: array_data,
                success: function(response)
                {
                   //window.location.href = "{{ url('admin/page/page') }}";
                }        
            });
         }
    
    
});   

    
jQuery(".upload_media").click(function(){

    
        jQuery("#medialibray").modal('toggle');
    
        jQuery(".selected_section").val(0);
        jQuery(".selected_section_image_src").val(0);
    
        let parentsectionid =  jQuery(this).find('.section_image_custom').attr('id');
        let parentsectionimageid =  jQuery(this).next('.image-src').attr('id');
        console.log(parentsectionid);
        console.log(parentsectionimageid);
    
        jQuery(".selected_section").val(parentsectionid);
        jQuery(".selected_section_image_src").val(parentsectionimageid);

         var formdata =  
         {

            "alldata": {
                'page_id':5
            },
            "_token": "{{ csrf_token() }}",
         } 
         
         jQuery.ajax({
         type: "POST",
         url: '<?php echo url('admin/medialibrary');?>',
         data: formdata,
         success: function(response)
         {
            jQuery(".library_load").html(response);
            loadmore();
            getimagedetails();
         }

    }); 
});
var page = 5; 
function loadmore(){
   
jQuery(".loadmore").click(function(){
        page = page + 5;
        var formdata =  
        {
            "alldata": {
            'page_id':page
            },
            "_token": "{{ csrf_token() }}",
        } 
         jQuery.ajax({
         type: "POST",
         url: '<?php echo url('admin/medialibrary');?>',
         data: formdata,
         success: function(response)
         {
            jQuery(".library_load").html(response);
            loadmore();
            getimagedetails();
         }

        }); 

   });
}
 

function uploadmedialibrary(){
   page = page + 10;
    var formdata =  
    {
        "alldata": {
        'page_id':page
        },
        "_token": "{{ csrf_token() }}",
    } 
     jQuery.ajax({
     type: "POST",
     url: '<?php echo url('admin/medialibrary');?>',
     data: formdata,
     success: function(response)
     {
        jQuery(".library_load").html(response);
        loadmore();
        getimagedetails();
     }
    });    
}


$(document).ready(function (e) {
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
    
    
    
    
jQuery('#multi-file-upload-ajax').submit(function(e) {
        
    jQuery(".custom_library .loading").show();
        
        
    e.preventDefault();
    var formData = new FormData(this);
    let TotalFiles = $('#files')[0].files.length; //Total files
    let files = $('#files')[0];
    for (let i = 0; i < TotalFiles; i++) {
    formData.append('files' + i, files.files[i]);
    }
    formData.append('TotalFiles', TotalFiles);
    $.ajax({
    type:'POST',
    url: "{{ url('admin/store-multi-file-ajax')}}",
    data: formData,
    cache:false,
    contentType: false,
    processData: false,
    dataType: 'json',
    success: (data) => {
    this.reset();
        alert('Files has been uploaded using jQuery ajax');
        jQuery(".custom_library .nav-tabs ul li").removeClass('active');
        jQuery(".custom_library .nav-tabs #media").addClass('active');
        jQuery(".custom_library .tab-content #uploads").removeClass('active');
        jQuery(".custom_library .tab-content #media").addClass('active');
        
        jQuery(".uploadfile").html('Upload file');
        jQuery(".custom_library .loading").hide();
        uploadmedialibrary();
        
      
    },
    error: function(data){
   // alert(data.responseJSON.errors.files[0]);
        console.log(data.responseJSON.errors);
    }
    });

    });
});
   
 
    
</script>