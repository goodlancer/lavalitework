
<style type="text/css">
    .profile-user-img
    {
        margin-top: 10px;
        margin-left: 0;
    }
</style>

    <div class="nav-tabs-custom custom-page-button">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs primary">
            <li ><a href="#user" data-toggle="tab" id="usernameid" class="active">
                {!! trans('user::user.tab.name') !!}</a>
            </li>
            <li> <a href="#details" id="detailsuser" data-toggle="tab">Details</a></li>
              <li><a href="#Upload" id="fileshow" data-toggle="tab">Upload</a></li>
            <div class="box-tools pull-right">
               
                <button type="button" class="btn btn-close btn-sm" data-action='CANCEL' data-load-to='#user-user-entry' data-href='{{trans_url('admin/user/user')}}/{{$user->getRouteKey()}}'>{{ trans('app.cancel') }}</button>
                 <button type="button" class="btn btn-save btn-sm" data-action='UPDATE' data-form='#user-user-edit'  data-load-to='#user-user-entry' data-datatable='#user-user-list'>Save</button>
            </div>
        </ul>
        {!!Form::vertical_open()
        ->id('user-user-edit')
        ->method('PUT')
        ->enctype('multipart/form-data')
        ->action(trans_url('admin/user/user/'. $user->getRouteKey()))!!}
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="user">
                <div class="tab-pan-title"> 
                    {!! trans('app.edit') !!}  {!! trans('user::user.name') !!} [ {!!$user->name!!} ] 
                </div>
                @include('user::admin.user.partial.entry')
            </div>
            <div class="tab-pane" id="details">
                <div class="row">
                    <div class='col-md-3 col-sm-4'>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::radios('sex')
                            -> radios(trans('user::user.options.sex'))
                            ->style('margin-left:-15px')
                            -> label(trans('user::user.label.sex'))
                            -> inline() !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::select('reporting_to')
                            -> options(trans('user::user.options.reporting_to'))
                            -> label(trans('user::user.label.reporting_to'))
                            -> placeholder(trans('user::user.placeholder.reporting_to')) !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::select('department')
                            -> options(trans('user::user.options.department'))
                            -> label(trans('user::user.label.department'))
                            -> placeholder(trans('user::user.placeholder.department')) !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::text('dob')
                            -> label(trans('user::user.label.dob'))
                            -> placeholder(trans('user::user.placeholder.dob')) !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::tel('phone')
                            -> label(trans('user::user.label.phone'))
                            -> placeholder(trans('user::user.placeholder.phone')) !!}
                        </div>
                         <div class='col-md-12 col-sm-12'>
                            {!! Form::text('address')
                            -> label(trans('user::user.label.address'))
                            -> placeholder(trans('user::user.placeholder.address')) !!}
                        </div>
       
                    </div>

                    <div class='col-md-3 col-sm-4'>
                         <div class='col-md-12 col-sm-12'>
                            {!! Form::text('street')
                            -> label(trans('user::user.label.street'))
                            -> placeholder(trans('user::user.placeholder.street')) !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::text('city')
                            -> label(trans('user::user.label.city'))
                            -> placeholder(trans('user::user.placeholder.city')) !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::text('district')
                            -> label(trans('user::user.label.district'))
                            -> placeholder(trans('user::user.placeholder.district')) !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::text('state')
                            -> label(trans('user::user.label.state'))
                            -> placeholder(trans('user::user.placeholder.state')) !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::text('country')
                            -> label(trans('user::user.label.country'))
                            -> placeholder(trans('user::user.placeholder.country')) !!}
                        </div>
                        <div class='col-md-12 col-sm-12'>
                            {!! Form::url('web')
                            -> label(trans('user::user.label.web'))
                            -> placeholder(trans('user::user.placeholder.web')) !!}
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
        {!!Form::close()!!}
        <div class="tab-content clearfix">
              <div class="tab-pane" id="Upload" style="display: none;">
             <div class="row">
                <div class='col-md-3 col-sm-4'>
                    <div class='col-md-12 col-sm-12'>

   


                             <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    
                        <?php
                                         if($user->profile)
                                         {
                                            ?>
                                                <label for="address" class="control-label"> Profile</label>
                                                <img class="profile-user-img img-responsive img-circle" src="{{url('public/storage/uploads/')}}/{!!$user->profile!!}" alt="User profile picture">

                                            <?php
                                         }
                                            else
                                            {
                                                    ?>
                                                        <img src="{{url('public/themes/client/assets/img/avatar/male.png')}}" class="profile-user-img img-responsive img-circle">
                                                    <?php

                                            }
                                    ?>
                                    <label for="name" class="control-label">Upload Profile</label>
                                    <input type="file" name="image" class="image">
                                    <input type="hidden" name="userid" class="userid" value="{{$user->id}}">
                                </div>
                            </div>









                    </div>
                </div>

            </div>
        </div>
        </div>
    </div>







<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Laravel Crop Image Before Upload using Cropper JS - NiceSnippets.com</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="img-container">
            <div class="row">
                <div class="col-md-8">
                    <img id="image" src="https://avatars0.githubusercontent.com/u/3456749">
                </div>
                <div class="col-md-4">
                    <div class="preview"></div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="crop">Crop</button>
      </div>
    </div>
  </div>
</div>

</div>


                        </div>
                    </div>
                </div>
            </div>

            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<style type="text/css">
    .mh500{
        min-height: 500px;
    }
</style>
<?php
$base_url = URL::to('/');

?>
<script>

var $modal = $('#modal');
var image = document.getElementById('image');
var cropper;
var base_url = "<?php echo $base_url;?>";
$("body").on("change", ".image", function(e){
    var files = e.target.files;
    var done = function (url) {
      image.src = url;
      $modal.modal('show');
    };
    var reader;
    var file;
    var url;

    if (files && files.length > 0) {
      file = files[0];

      if (URL) {
        done(URL.createObjectURL(file));
      } else if (FileReader) {
        reader = new FileReader();
        reader.onload = function (e) {
          done(reader.result);
        };
        reader.readAsDataURL(file);
      }
    }
});

$modal.on('shown.bs.modal', function () {
    cropper = new Cropper(image, {
      aspectRatio: 1,
      viewMode: 3,
      preview: '.preview'
    });
}).on('hidden.bs.modal', function () {
   cropper.destroy();
   cropper = null;
});

$("#crop").click(function(){
    let userid = jQuery(".userid").val();
    var filename =   $('input[type=file]').val().replace(/C:\\fakepath\\/i, '');
    filename =   filename.split('.').slice(0, -1).join('.');
    filename =   filename.replace(/\s/g, '');
    canvas = cropper.getCroppedCanvas({
        width: 160,
        height: 160,
    });
    canvas.toBlob(function(blob) {
         url = URL.createObjectURL(blob);
         var reader = new FileReader();
         reader.readAsDataURL(blob); 
         reader.onloadend = function() {
            var base64data = reader.result; 
            $.ajax({
                type: "POST",
                dataType: "json",
                url: base_url + "/admin/image-cropper/upload",
                data: {'_token': $('meta[name="_token"]').attr('content'), 'image': base64data,'userid':userid,'filename':filename},
                success: function(data){
                    $modal.modal('hide');
                    location.reload();
                }
              });
         }
    });
})

</script>






<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#fileshow").click(function(){
            jQuery("#Upload").show();
        
            jQuery("#details").hide();
            jQuery("#user").hide();
        });
    });

    jQuery(document).ready(function(){
        jQuery("#detailsuser").click(function(){
            jQuery("#details").show();
            jQuery("#user").hide();
            jQuery("#Upload").hide();
        });
    });

    jQuery(document).ready(function(){
        jQuery("#usernameid").click(function(){
            jQuery("#Upload").hide();
            jQuery("#user").show();
            jQuery("#details").hide();


        });
    });
</script>