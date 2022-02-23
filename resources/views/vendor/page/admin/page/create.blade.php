<div class="nav-tabs-custom custom-page-button">
    
    <ul class="nav nav-tabs primary">
        <li><a href="#details"  data-toggle="tab" class="active">{{ trans('page::page.tab.page') }}</a></li>
        <li><a href="#metatags" data-toggle="tab">{{ trans('page::page.tab.meta') }}</a></li>

        <div class="box-tools pull-right">
             <button type="button" class="btn btn-close btn-sm" data-action='CLOSE' data-load-to='#page-page-entry' data-href='{{guard_url('page/page/0')}}'>{{ trans('app.close') }}</button>
             <button type="button" class="btn btn-save btn-sm" data-action='CREATE' data-form='#page-page-create'  data-load-to='#page-page-entry' data-datatable='#page-page-list'>Save</button>
        </div>
    </ul>
    {!!Form::vertical_open()
    ->id('page-page-create')
    ->method('POST')
    ->files('true')
    ->action(guard_url('page/page'))!!}

    <div class="tab-content clearfix">   
        <div class="tab-pan-title ">  {{ trans('app.create') }}   {{ trans('page::page.name') }}</div>
        @include('page::admin.page.partial.entry', ['mode' => 'create'])
    </div>

    {!! Form::close() !!}
    
    
</div>