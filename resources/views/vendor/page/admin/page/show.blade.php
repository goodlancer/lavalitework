
    <div class="nav-tabs-custom custom-page-button">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs primary">
            <li ><a href="#details" data-toggle="tab" class="active">{{ trans('page::page.tab.page') }}</a></li>
            <li><a href="#metatags" data-toggle="tab">{{ trans('page::page.tab.meta') }}</a></li>
            <li><a href="#settings" data-toggle="tab">{{ trans('page::page.tab.setting') }}</a></li>
             <li><a href="#sections" data-toggle="tab">Sections</a></li>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" data-action='NEW' data-load-to='#page-page-entry' data-href='{{Trans::to('admin/page/page/create')}}'><i class="fa fa-plus-circle"></i> {{ trans('app.new') }}</button>
                

                
                @if($page->id)
                <button type="button" class="btn btn-edit btn-sm" data-action="EDIT" data-load-to='#page-page-entry' data-href='{{ guard_url('page/page') }}/{{$page->getRouteKey()}}/edit'> {{ trans('app.edit') }}</button>
                <button type="button" class="btn btn-delete btn-sm" data-action="DELETE" data-load-to='#page-page-entry' data-datatable='#page-page-list' data-href='{{ guard_url('page/page') }}/{{$page->getRouteKey()}}' >
                 {{ trans('app.delete') }}
                </button>
                @endif
            </div>
        </ul>
        {!!Form::vertical_open()
        ->id('show-page-show')
        ->method('PUT')
        ->action(guard_url('page/page/'. $page->getRouteKey()))!!}
        {!!Form::token()!!}
        <div class="tab-content clearfix">
            <div class="tab-pan-title">  {{ trans('app.show') }}   [{!!$page->name!!}]</div>             
                @include('page::admin.page.partial.entry', ['mode' => 'show'])             
        </div>
    </div>
