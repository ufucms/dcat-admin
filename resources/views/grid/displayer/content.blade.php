<style type="text/css">
    .grid-content{min-height: 280px;}
</style>
@if (isset($content) && $content)
<div class="grid-content mb-1">{!! $content??'' !!}</div>
@endif