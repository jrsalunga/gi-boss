@extends('dash')

@section('title', '- Sector')

@section('body-class', 'sector-view')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		
		
		<h3 class="page-header">
			@if($sector->is_parent())
				<span class="gly gly-parents"></span> 
			@endif
			{{ $sector->descriptor }} <small>{{ $sector->code }}</small>
		<a href="/masterfiles/sector/{{$sector->lid()}}/edit" title="Toogle Edit" data-toggle="loader"><i class="material-icons">edit</i></a>
		<a href="/masterfiles/sector" class="pull-right" title="Back to sector List" data-toggle="loader"><i class="material-icons">list</i></a>
		</h3>

		@include('_partials.alerts')

		@if(!$sector->is_parent())
			<span class="gly gly-parents"></span> <a href="/masterfiles/sector/{{$sector->parent->lcode()}}"> {{ $sector->parent->code or '*' }}</a>
		@endif
	</div>
	<div class="col-md-8 col-sm-6">
		
	</div>			
	<div class="col-md-4 col-sm-6">
	@if($sector->is_parent())
		<div class="panel panel-default">
		  <div class="panel-body">
		    <h5>Areas</h5>
		    @if(count($sector->children)>0)
					<ul class="list-unstyleds">
					@foreach($sector->children as $child)
						<li>
							<a href="/masterfiles/sector/{{ $child->lcode() }}">{{ $child->code }}</a> -
							<a href="/masterfiles/sector/{{ $child->lid() }}">{{ $child->descriptor }}</a>
						</li>
					@endforeach
					</ul>
				@endif
		  </div>
		</div>
		@endif
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		@include('_partials.pager', ['field'=>'code', 'model'=>$sector])
	</div>
</div>
@endsection