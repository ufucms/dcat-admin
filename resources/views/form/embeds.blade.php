@if($label!=false)
<div class="row">
    <label class="{{$viewClass['label']}}"><h4 class="pull-right">{!! $label !!}</h4></label>
    <div class="{{$viewClass['field']}}"></div>
</div>
<hr style="margin-top: 0px;">
@endif

<div id="embed-{{$column}}" class="embed-{{$column}}">

    <div class="embed-{{$column}}-forms">

        <div class="embed-{{$column}}-form fields-group">

            @foreach($form->fields() as $field)
                {!! $field->render() !!}
            @endforeach

        </div>
    </div>
</div>

@if($label!=false)
<hr style="margin-top: 0px;">
@endif
