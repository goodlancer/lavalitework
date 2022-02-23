<style>
  .canvas
  {
    width:400px!important;
  }
</style>
<div class='row disabled'>
 {!! Form::hidden('upload_folder')!!}
  <div class='col-md-6 col-sm-6'>
         {!! Form::text('name')
         -> required()
         -> label(trans('block::block.label.name'))
         -> placeholder(trans('block::block.placeholder.name'))!!}
  </div>
  <div class='col-md-6 col-sm-6'>
         {!! Form::select('category_id')
         ->required()
         ->options(Block::selectCategories())
         -> label(trans('block::block.label.category_id'))
         -> placeholder(trans('block::block.placeholder.category_id'))!!}
  </div>

  <div class='col-md-6 col-sm-6'>
         {!! Form::select('status')
           -> options(trans('block::block.options.status'))
         -> label(trans('block::block.label.status'))
         -> placeholder(trans('block::block.placeholder.status'))!!}
  </div>
  <div class='col-md-6 col-sm-6'>
         {!! Form::text('url')
         -> label(trans('block::block.label.url'))
         -> placeholder(trans('block::block.placeholder.url'))!!}
  </div>
  <div class='col-md-6 col-sm-6'>
         {!! Form::number('order')
         -> label(trans('block::block.label.order'))
         -> placeholder(trans('block::block.placeholder.order'))!!}
  </div>
  <div class='col-md-6 col-sm-6'>
         {!! Form::text('icon')
         -> label(trans('block::block.label.icon'))
         -> placeholder(trans('block::block.placeholder.icon'))!!}
  </div>
  <div class='col-md-3 col-sm-3'>
        <input id="block" class="block" value="{{$block->id}}" name="block_id" type="hidden" /> 
    <div class="form-group">
        <label for="image" class="control-label">Image</label>
        <strong style="color:#b91010;display: block;">Click on image to crop 12.&#42;</strong>
        <input id="file" class="cropfile" type="file"  name="featured_image" /> 
       <?php
     //  echo '<pre>',var_dump($block->featured_image ); echo '</pre>';

         if($block->featured_image)
         {
            echo '<div class="wrap-service-image">  
                  <div class="wrap-image-page">
                    <img src="'.url($block->featured_image).'" class="img-thumbnail">
                  </div>
                  <div class="crosssmap"  data-value="0" style="color:#000;">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                    <input type="hidden" class="pageid" value="{{$page->id}}">
                    <input type="hidden" class="imagepath" value="{{$section->featured_image}}">
                    <input type="hidden" class="sectionid" value="{{$section->id}}">
                  </div> 
            </div>';
         }
       ?>
        <div id="jcrop" class="jcrop" style="margin-top:10px;"></div> 
    </div>
  </div>
  <div class='col-md-1 col-sm-1'>
  </div>
  <div class='col-md-8 col-sm-8'>
    <div class="form-group">
      <h4 style="color: #000;">Real size cropped image preview (canvas)</h4>
      <canvas id="canvas" class="canvas"></canvas>
      <input id="png"  class="png" name="crop_image" type="hidden"/>
  </div>
  </div>
  <div class='col-md-12 col-sm-12'>
         {!! Form::textarea('description')
         -> addClass('html-editor')
         -> label(trans('block::block.label.description'))
         -> placeholder(trans('block::block.placeholder.description'))!!}
  </div>

</div>
