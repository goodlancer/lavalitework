<div class="tab-pane active disabled" id="details">
    <div class="tab-pan-title"> {{ trans('app.view') }} menu [{{$menu->name ?? 'New menu'}}]</div>
    <div class="row">
        <div class="col-md-6 ">
            {!! Form::text('name')
            -> required()
            -> label(trans('menu::menu.label.name'))
            -> placeholder(trans('menu::menu.placeholder.name'))!!}
        </div>
        <div class="col-md-6 ">
            {!! Form::text('url')
            -> required()
            -> label(trans('menu::menu.label.url'))
            -> placeholder(trans('menu::menu.placeholder.url'))!!}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            
    
            <div class="form-group">
                <label for="status" class="control-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="" disabled="disabled">Enter Status</option>
                    <option value="1"   <?php if($menu->status == 1){echo  "selected";} ?> >Publish</option>
                    <option value="0"   <?php if($menu->status == 0){echo  "selected";} ?> >Draft</option>
                </select>
            </div>
          
        </div>
        <div class="col-md-6 ">
            {!! Form::select('target')
            -> options(trans('menu::menu.options.target'))
            -> label(trans('menu::menu.label.target'))
            -> placeholder(trans('menu::menu.placeholder.target'))!!}
             <input type="hidden" name="menuid" value="{{$menu->id}}">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 ">
            {!! Form::hidden('role[]')!!}
            {!! Form::select('role[]')
            -> options(User::roles(), $menu->role)
            -> multiple('multiple')
            -> class('select-remote form-control')
            -> label(trans('menu::menu.label.role'))!!}
        </div>
        <div class="col-md-6 ">
            {!! Form::text('icon')
            -> label(trans('menu::menu.label.icon'))
            -> placeholder(trans('menu::menu.placeholder.icon'))!!}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 description_">
                <label for="description" class="control-label">Class</label>
                {!! Form::text('description')
                -> label(trans('menu::menu.label.description'))
                -> placeholder('Class')!!}
                {!! Form::hidden('parent_id')->id('parent_id') !!}
        </div>
    </div>
</div>
