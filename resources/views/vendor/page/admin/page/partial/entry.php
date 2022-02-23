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

                    {!! Form::select('status')
                    -> options(trans('page::page.options.status'))
                    -> label(trans('page::page.label.status'))
                    -> placeholder(trans('page::page.placeholder.status'))
                    !!}
                </div>
            </div>



    <div class="tab-pane disabled row" id="sections">
                <div id="sortable">  


                    @isset($sections)   
                    @foreach($sections as $key=>$section)
                    
                    <div class='col-md-12 col-sm-12 section_divide ui-sortable-handle' title="<?php echo $section->id;?>" data-listing-price="<?php echo $section->order_by;?>">
                        <p class="move-icons">  <i class="fa fa-arrows" aria-hidden="true"></i> Move section</p>
                            <div class='col-md-4 col-sm-4'>
                                <input type="hidden" name="section[{{$section->id}}]" value="{{$section->id}}">
                                <div class="form-group">
                                    <label for="name" class="control-label">Heading</label>
                                    <input class="form-control" placeholder="Enter Name" id="name" type="text" name="section_name[{{$section->id}}]" value="{!!$section->name!!}">
                                </div>
                            </div>
                             <div class='col-md-4 col-sm-4'>
                                <div class="form-group">
                                    <label for="heading" class="control-label">Sub Heading</label>
                                    <input class="form-control" placeholder="Enter Heading" id="heading" type="text" name="section_heading[{{$section->id}}]" value="{!!$section->heading!!}">
                                </div>
                            </div>
                             <div class='col-md-4 col-sm-4'>
                                <div class="form-group">
                                    <label for="image" class="control-label">Image</label>
                                    <input class="form-control" placeholder="You can add a image" id="image" type="file" name="section_image[{{$section->id}}]" >
                                </div>
                                @if($section->image)
                               <div class="wrap-service-image">  
                                    <div class="wrap-image-page">
                                        <img src="{{ url($section->image) }}" class="img-thumbnail">
                                    </div>
                                     <div class="crosssmap"  data-value="0" style="color:#000;">
                                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                                        <input type="hidden" class="pageid" value="{{$page->id}}">
                                        <input type="hidden" class="imagepath" value="{{$section->image}}">
                                        <input type="hidden" class="sectionid" value="{{$section->id}}">
                                    </div> 
                                </div>      
                                @endif
                            </div>
 
              

                            <div class='col-md-12 col-sm-12 custom-front'>
                            <div class="form-group">
                                <label for="body" class="control-label">Content</label>
                                <textarea class="form-control" style="height:30rem" placeholder="Enter Description" id="body" name="section_body[{{$section->id}}]">{!!$section->body!!}</textarea>
                            </div>
                            </div>
                    </div>

                    @endforeach
                    @endisset
                </div>

           
               


                <button type="button" class="btn btn-primary add-section">Add section</button>


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
                            </div> -->
<script type="text/javascript">

$(".image_crop .cropfile").change(function(){
    picture(this);
});


var picture_width;
var picture_height;
var crop_max_width  = 300;
var crop_max_height = 300;
function picture(input) {
    if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {

            $(".jcrop, #preview").html("").append("<img src=\""+e.target.result+"\" alt=\"\" />");
                picture_width  =  $("#preview img").width();
                picture_height =  $("#preview img").height();

            $(".jcrop  img").Jcrop({
                onChange: canvas,
                onSelect: canvas,
                boxWidth: crop_max_width,
                boxHeight: crop_max_height
            });
        }
        reader.readAsDataURL(input.files[0]);
    }
}
function canvas(coords){
    var imageObj = $(".jcrop img")[0];
    var canvas = $(".canvas")[0];
    canvas.width  = coords.w;
    canvas.height = coords.h;
    var context = canvas.getContext("2d");
    context.drawImage(imageObj, coords.x, coords.y, coords.w, coords.h, 0, 0, canvas.width, canvas.height);
    png();
}
function png() {
    var png = $(".canvas")[0].toDataURL('image/png');
    $(".png").val(png);
}
</script>