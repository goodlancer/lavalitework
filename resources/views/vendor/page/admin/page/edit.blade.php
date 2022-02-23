<style>
    .sorted
    {
        display: none;
    }
    .nav-tabs .box-tools
    {
        display: flex;
    }
    img{
        width: 100%;
    }
</style>
    <div class="nav-tabs-custom custom-page-button">
        <!-- Nav tabs -->
        {!!Form::vertical_open()
        ->id('page-page-edit')
        ->method('PUT')
        ->enctype('multipart/form-data')
        ->action(guard_url('page/page/'. $page->id))!!}
        <ul class="nav nav-tabs primary">
            <li><a href="#details" data-toggle="tab" class="active" id="_page">{{ trans('page::page.tab.page') }}</a></li>
            <li><a href="#metatags" data-toggle="tab" id="_meta">{{ trans('page::page.tab.meta') }}</a></li>
            <li><a href="#sections" data-toggle="tab" id="_sections">Sections</a></li>
            <div class="box-tools pull-right">
                <p  class="btn sorted">Confirm Section Order</p>
                <button type="button" class="btn btn-close btn-sm" data-action='CANCEL' data-load-to='#page-page-entry' data-href='{{guard_url('page/page')}}/{{$page->getRouteKey()}}'> {{ trans('app.cancel') }}</button>
                <button type="submit" class="btn btn-save btn-sm">Save</button>
            </div>
        </ul>

        <div class="tab-content  clearfix"> 
           <input type="hidden" name="_token" value="{{ csrf_token() }}" />    
           <input type="hidden" name="page_id" value="{{ $page->id }}">   
           <div class="tab-pan-title">  {{ trans('app.edit') }}   [{!!$page->name!!}]</div>
             @include('page::admin.page.partial.entry', ['mode' => 'edit'])
        </div>
        {!!Form::close()!!}
    </div>


<script>
    
//    $(document).ready(function(){
//        $('.add-section').on('click', function(e) {
//            console.log("adding new section");
//            var sections = $('.tab-content .tab-pane#sections');
//            
//            
//            var html = "<input type='hidden' name='section[0]' value='0'><div class='col-md-8'></div><div class='col-md-2'><div class='form-group'><label>ID</label><input class='form-control' placeholder='Class Id' id='classid' type='text' name='section_sectionid[0]'></div></div><div class='col-md-2'><div class='form-group'><label> Class Name </label><input class='form-control' placeholder='Class Name' id='classname' type='text' name='section_sectionclassname[0]'></div></div><div class='col-md-4 col-sm-4'> <div class='form-group'><label for='name' class='control-label'>Heading {heading}</label> <input class='form-control' placeholder='Enter Name' id='name' type='text' name='section_name[0]' value=''> </div> </div> <div class='col-md-4 col-sm-4'> <div class='form-group'> <label for='heading' class='control-label'>Sub Heading {subheading}</label> <input class='form-control' placeholder='Enter Heading' id='heading' type='text' name='section_heading[0]' value=''> </div> </div> <div class='col-md-4 col-sm-4'> <div class='form-group'> <label for='image' class='control-label'>Image {imageurl} (Notes -Inside src='' Attr.)</label> <input class='form-control' placeholder='You can add a image' id='image' type='file' name='section_image[0]'> </div> </div>  <div class='col-md-12 col-sm-12'> <div class='form-group'><label for='body' class='control-label'>Content </label> <textarea class='form-control' style='height:30rem' placeholder='Enter Content' id='body' name='section_body[0]'></textarea></div></div>";
//
//
//            sections.append(html);
//            
//            $('.add-section').attr("disabled", true);
//        
//        });
//        
//    });
//    
    
    
    $(document).ready(function(){
        $('.add-section').on('click', function(e) {

            console.log("adding new section");
            var sections = $('.tab-content .tab-pane#sections');
            
            
            var html = "<input type='hidden' name='section[0]' value='0'><div class='col-md-8'></div><div class='col-md-2'><div class='form-group'><label>ID</label><input class='form-control' placeholder='Class Id' id='classid' type='text' name='section_sectionid[0]'></div></div><div class='col-md-2'><div class='form-group'><label> Class Name </label><input class='form-control' placeholder='Class Name' id='classname' type='text' name='section_sectionclassname[0]'></div></div><div class='col-md-4 col-sm-4'> <div class='form-group'><label for='name' class='control-label'>Heading {heading}</label> <input class='form-control' placeholder='Enter Name' id='name' type='text' name='section_name[0]' value=''> </div> </div> <div class='col-md-4 col-sm-4'> <div class='form-group'> <label for='heading' class='control-label'>Sub Heading {subheading}</label> <input class='form-control' placeholder='Enter Heading' id='heading' type='text' name='section_heading[0]' value=''> </div> </div> <div class='col-md-4 col-sm-4'> <div class='form-group'> <label for='image' class='control-label'>Image {imageurl} (Notes -Inside src='' Attr.)</label></div><div class='form-group upload_media_parent'><p class='upload_media upload_media_new_section'>Set featured image <input type='hidden' id='section_image_custom_new' name='section_image_custom[0]' class='section_image_custom'> </p> <div class='' id='image_src_id_new'></div></div></div><div class='col-md-12 col-sm-12'> <div class='form-group'><label for='body' class='control-label'>Content </label> <textarea class='form-control' style='height:30rem' placeholder='Enter Content' id='body' name='section_body[0]'></textarea></div></div>";


            sections.append(html);
            activeuploadmedia();


            $('.add-section').attr("disabled", true);
         
        
        });
        
    });
    
    
    
    
    
    </script>   

<style>
.custom_library .upload-btn-wrapper {
  position: relative;
  overflow: hidden;
  display: inline-block;
}

.custom_library .btn {
    border: 1px solid gray;
    color: #676767;
    background-color: white;
    padding: 8px 20px;
    border-radius: 5px;
    font-size: 16px;
    font-weight: normal;
}

.custom_library .upload-btn-wrapper input[type=file] {
  font-size: 100px;
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
}

</style>
<div class="modal fade custom_library reload" id="medialibray" role="dialog">
    
    <div class="loading" style="display:none;"></div>  
    
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close btn" data-dismiss="modal">&times;</button>
        </div>

            <div class="modal-body">
                <div class="header">
                    <div class="media-frame-title" id="media-frame-title"><h1>Featured image</h1></div>
                 </div>
                <div class="nav-tabs-custom">
                    
                    <ul class="nav nav-tabs primary">
                        <li><a href="#uploads" id="li_uploads" data-toggle="tab">Upload files</a></li>
                        <li><a href="#media"   id="li_media"   data-toggle="tab" class="active">Media Library</a></li>  
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane" id="uploads">
                            <div class="media-frame-content" data-columns="10" role="tabpanel" aria-labelledby="menu-item-upload" tabindex="0">
                                <div class="uploader-inline">
                                    <div class="uploader-inline-content no-upload-message">
                                        <div class="upload-ui">
                                            <form id="multi-file-upload-ajax" method="POST"  action="javascript:void(0)" accept-charset="utf-8" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="upload-btn-wrapper">
                                                                <label class="uploadfile btn">Upload file</label>
                                                                <input type="file" name="files[]" id="files" placeholder="Choose files" multiple>
                                                            </div>
                                                        </div>
                                                    </div>           
                                                        <div class="col-md-12">
                                                        <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                                                    </div>
                                                </div>  
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane active" id="media">
                            
                            <input type="hidden" name="selected_section" class="selected_section">
                            <input type="hidden" name="selected_section_image_src" class="selected_section_image_src">
                            <div class="wrap-uload-media">
                                <div class="library_load"></div>
                                <div class="library-details"></div>
                            </div>
                        </div>
                        
                    </div>
               </div>
            </div>


            <div class="modal-footer">
                <button class="btn btn-default set_featured_image">Set fearured image</button>
            </div>

      </div>
      
    </div>
</div>

<script>
    

function activeuploadmedia(){ 
    jQuery(".upload_media_parent .upload_media").click(function(){
   
         jQuery("#medialibray").modal('toggle');
            let  parentsectionid  = '';
            let  parentsectionimageid  = '';
                jQuery(".selected_section").val(0);
                jQuery(".selected_section_image_src").val(0);
             parentsectionid      =  jQuery(this).find('.section_image_custom').attr('id');
             parentsectionimageid =  jQuery(this).find('.image-src').attr('id');
        
            console.log(parentsectionid);
            console.log(parentsectionimageid);
        
            jQuery(".selected_section").val(parentsectionid);
            jQuery(".selected_section_image_src").val(parentsectionimageid);
        
        
             var formdata =  
             {

                "alldata": {
                    'page_id':10
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

function getimagedetails(){

    jQuery(".attachment").click(function(){

        let dataid = jQuery(this).attr('data-id');
        var formdata =  
        {
            "alldata": {
            'rowid':dataid
            },
            "_token": "{{ csrf_token() }}",
        } 
         jQuery.ajax({
         type: "POST",
         url: '<?php echo url('admin/getselectimagedetails');?>',
         data: formdata,
         success: function(response)
         {
            jQuery(".library-details").html(response);
           // regetimagedetails();
         }
        }); 

 

    });
} 


//function regetimagedetails(){
    jQuery(".set_featured_image").click(function(){

        let section_pathimage  = jQuery(".selectfeatured_image").val();
        let section_image_path = "public/" + section_pathimage;
        let sectionid = jQuery(".selected_section").val();
        let selected_section_image_srcid = jQuery(".selected_section_image_src").val();

        
        
       
        console.log(sectionid);

        jQuery("#"+sectionid).val(section_image_path);


        let imagesrc = jQuery(".custom_library .library-details .centered").html();

        
        
        if(sectionid == "section_image_custom_new"){
              jQuery("#image_src_id_new").html(imagesrc);
        }        
      
        
        
        
        
        
        jQuery("#"+selected_section_image_srcid).html(imagesrc);

        jQuery("#medialibray").modal('toggle');


    });
    
    
     jQuery('#files').bind('change', function(){


         let total_length =  jQuery(this).get(0).files.length;


         var path = jQuery(this).val();


         var fileName = path.replace(/^.*\\/, "");

             if(total_length > 1)
             {
                 total_length = total_length - 1;
                 fileName = fileName + " +" + total_length;
             }

         jQuery('.uploadfile').html(fileName);



    });
//}
</script>





