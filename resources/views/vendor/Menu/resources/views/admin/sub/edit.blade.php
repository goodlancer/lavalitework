
    <div class="nav-tabs-custom">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs primary">
            <li class="active"><a href="#details" data-toggle="tab">Menu</a></li>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-sm" data-action='UPDATE' data-form='#edit-menu' data-load-to='#menu-entry' data-href='{!!guard_url('menu/submenu')!!}/{!!$menu->getRouteKey()!!}' id="btn-save"><i class="fa fa-floppy-o"></i> {{ trans('app.save') }}</button>
                <button type="button" class="btn btn-default btn-sm" data-action='CANCEL' data-load-to='#menu-entry' data-href='{!!guard_url('menu/submenu')!!}/{!!$menu->getRouteKey()!!}' id="btn-cancel"><i class="fa fa-times-circle"></i> {{ trans('app.cancel') }}</button>
            </div>
        </ul>
        {!!Form::vertical_open()
        ->id('edit-menu')
        ->method('PUT')
        ->files('true')
        ->enctype('multipart/form-data')
        ->action(guard_url('menu/submenu/'. $menu->getRouteKey()))!!}
        <div class="tab-content">
            @include('menu::admin.partial.submenu')
        </div>
        {!!Form::close()!!}
        <div class="tab-content" style="border-top:1px solid #000;">
        <div class="container">
        <form action="{{guard_url('serviceuser/uploadicons')}}" enctype="multipart/form-data" method="POST" id="icons_upload">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="row">
            <div class="col-xl-12">

                <div class="form-group" style="margin-top:15px;">
                    <p><strong>Note: If You don't have icons class.</strong></p>
                    <label>Please Upload Icons:</label>
                   
                    <?php
                    $images =  $menu->upload_folder;
                    if($images)
                    {
                    echo ' <div class="wrap-imgae"><img src="'.url('public/storage/').'/'.$images.'" class="profile-user-img img-responsive img-circle" alt="User Image" /></div>';  

                    }
                    else
                    {
                    ///echo '<img src="'.url('public/storage/uploads/images/services').'/avtar.svg" class="profile-user-img img-responsive img-circle" alt="User Image" />';  
                    }
                    ?>
                   
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="hidden" name="un_id" value="{{$menu->id}}">
                    <input type="hidden" name="par_id" value="{{$menu->parent_id}}">
                    <button class="btn theme-button" type="submit">Upload Icons 22222</button>
                </div>

        </div>
            </div>
        </form>
        </div>
        </div>
    </div>
