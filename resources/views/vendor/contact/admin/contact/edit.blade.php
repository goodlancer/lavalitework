    <div class="nav-tabs-custom custom-page-button">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs primary">
            <li class="active"><a href="#contact" data-toggle="tab">{!! trans('contact::contact.tab.name') !!}</a></li>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-close btn-sm" data-action='CANCEL' data-load-to='#contact-contact-entry' data-href='{{guard_url('contact/contact')}}/{{$contact->getRouteKey()}}'>{{ trans('app.cancel') }}</button>
                <button type="button" class="btn btn-save btn-sm" data-action='UPDATE' data-form='#contact-contact-edit'  data-load-to='#contact-contact-entry' data-datatable='#contact-contact-list'>{{ trans('app.save') }}</button>
              

            </div>
        </ul>
        {!!Form::vertical_open()
        ->id('contact-contact-edit')
        ->method('PUT')
        ->enctype('multipart/form-data')
        ->action(guard_url('contact/contact/'. $contact->getRouteKey()))!!}
        <div class="tab-content clearfix">
            <div class="tab-pane active" id="contact">
                <div class="tab-pan-title">  {{ trans('app.edit') }}  {!! trans('contact::contact.name') !!} [{!!$contact->name!!}] </div>
                @include('contact::admin.contact.partial.entry')
            </div>
        </div>
        {!!Form::close()!!}
    </div>