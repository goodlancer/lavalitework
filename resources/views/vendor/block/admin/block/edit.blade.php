
    <div class="nav-tabs-custom">
        {!!Form::vertical_open()
        ->id('block-block-edit')
        ->method('PUT')
        ->enctype('multipart/form-data')
        ->action(guard_url('block/block/'. $block->id))!!}
        <ul class="nav nav-tabs primary">
            <li class="active"><a href="#block" data-toggle="tab">{!! trans('block::block.tab.name') !!}</a></li>
            <div class="box-tools pull-right">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Save</button>
                 <button type="button" class="btn btn-default btn-sm" data-action='CANCEL' data-load-to='#block-block-entry' data-href='{{guard_url('block/block')}}/{{$block->getRouteKey()}}'><i class="fa fa-times-circle"></i> {{ trans('app.cancel') }}</button>
            </div>
        </ul>
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="details">
                <div class="tab-pan-title">  {!! trans('app.edit') !!}  {!! trans('block::block.name') !!} [ {!!$block->name!!} ]</div>
                @include('block::admin.block.partial.entry', ['mode' => 'edit'])
            </div>
            <div class="tab-pane" id="images" style="display:none;">
                <div class="row">
                    <div class="form-group">
                        <label for="images" class="control-label col-lg-12 col-sm-12 text-left">
                            {{trans('block::block.label.images') }}
                        </label>
                        <div class='col-lg-6 col-sm-12'>
                            {!! $block->files('images')->url($block->getUploadUrl('images'))->dropzone()!!}
                        </div>
                        <div class='col-lg-12 col-sm-12'>
                            {!! $block->files('images')->editor()!!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>

<script type="text/javascript">
$(".cropfile").change(function(){
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
    var png = $(".canvas")[0].toDataURL();
    $(".png").val(png);
}
</script>