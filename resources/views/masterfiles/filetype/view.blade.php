@extends('dash')

@section('title', '- File Type')

@section('body-class', 'filetype-view')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		
		
		<h3 class="page-header">{{ $filetype->descriptor }} <small>{{ $filetype->code }}</small>
		<a href="/masterfiles/filetype/{{$filetype->lid()}}/edit" title="Toogle Edit" data-toggle="loader"><i class="material-icons">edit</i></a>
		<a href="/masterfiles/filetype" class="pull-right" title="Back to Lessor List" data-toggle="loader"><i class="material-icons">list</i></a>
		</h3>

		@include('_partials.alerts')
		
		@include('_partials.pager', ['field'=>'code', 'model'=>$filetype])
	</div>
		

</div>
@endsection