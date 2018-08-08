@extends('hr.dash', ['search_url'=>$table])

@section('title', '- '.page_title($table).': '.$model->descriptor)

@section('body-class', 'filetype-view')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12" >
		@include('_partials.alerts')
	</div>
</div>

<div class="row" style="border-bottom: 1px solid #eee;">
	<div class="col-md-8">
		<h3>{{ $model->descriptor }} <small>{{ $model->code }}</small></h3>
	</div><!-- end: .col-md-8 -->
	<div class="col-md-4">
		<div class="dropdown pull-right">
		  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    <span class="glyphicon glyphicon-cog"></span>
		    <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
		    <li>
		    	<a href="/hr/masterfiles/{{$table}}/{{$model->lid()}}/edit" data-toggle="loader">
						<span class="glyphicon glyphicon-pencil"></span> Edit
		    	</a>
		    </li>
		  </ul>
		</div>
	</div><!-- end: .col-md-4 -->
</div><!-- end: .row -->


@endsection