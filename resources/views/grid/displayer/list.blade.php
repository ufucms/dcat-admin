<style type="text/css">
    .table th {background-color: #ececf1;}
    .table td {height: auto;}
</style>
@if (isset($content) && $content)
<div class="table-content mb-1">{{ $content??'' }}</div>
@endif
<table class="table border table-hover" style="margin-bottom: 0;">
    <thead>
    <tr>
        @foreach($titles as $column => $title)
        <th>{{ $title }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($data as $datum)
    <tr>
        @foreach($titles as $column => $title)
            <td>{{ $datum[$column] }}</td>
        @endforeach
    </tr>
    @endforeach
    </tbody>
</table>