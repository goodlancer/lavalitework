            <div class="tab-pane active disabled" id="details">
            <div class="tab-pan-title"> {{ trans('app.view') }} menu [{{$menu->name ?? 'New menu'}}]</div>
               <div class="row">
                    <div class="col-md-6 ">
                        {!! Form::text('name')
                        -> label(trans('menu::menu.label.name'))
                        -> required()
                        -> placeholder(trans('menu::menu.placeholder.name'))!!}
                    </div>
                    <div class="col-md-6 ">
                        {!! Form::text('key')
                        -> label(trans('menu::menu.label.key'))
                        -> required()
                        -> placeholder(trans('menu::menu.placeholder.key'))!!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 ">
                        {!! Form::text('order')
                        -> label(trans('menu::menu.label.order'))
                        -> placeholder(trans('menu::menu.placeholder.order'))!!}
                    </div>
                    <div class="col-md-6">
                        {!! Form::select('status')
                        -> options(trans('menu::menu.options.status'))
                        -> label(trans('menu::menu.label.status'))
                        -> placeholder(trans('menu::menu.placeholder.status'))!!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 description_">
                        <label for="description" class="control-label">Class</label>
                        {!! Form::text('description')
                        -> label(trans('menu::menu.label.description'))
                        -> placeholder('Class')!!}
                    </div>
                </div>
            </div>
