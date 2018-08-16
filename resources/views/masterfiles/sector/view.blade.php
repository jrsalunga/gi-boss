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

	</div>
	@if(!$sector->is_parent())
	<div class="col-md-12">
		<div class="panel panel-default">
		  <div class="panel-body">
		  	<span>Main Area: </span>
				<span class="gly gly-parents"></span> <a href="/masterfiles/sector/{{$sector->parent->lcode()}}"> {{ $sector->parent->code or '*' }}</a>
			</div>
		</div>
	</div>
	@endif
	<div class="col-md-8 col-sm-6">
		<div class="panel panel-default">
		  <div class="panel-body">
		    @if(count($sector->children)>0)
		    <h4>{{ count($sector->children)>1?str_plural('Branch'):'Branch' }}</h4>
					<ul class="list-unstyled">
					@foreach($sector->children as $child)
						<li><i class="material-icons">pin_drop</i>
							<em><b><a href="/masterfiles/sector/{{ $child->lid() }}">{{ $child->code }}</a></b></em>
							<?php $branches = $child->branch ?>
							@if(count($branches)>0)
								<ul class="list-unstyled" style="margin-left: 15px;">
								@foreach($branches as $k => $branch)
									<li>{{ ($k+1) }}. <a href="/masterfiles/branch/{{ $branch->lid() }}" target="_blank">{{ $branch->code }} - {{ $branch->descriptor }}</a></li>
								@endforeach
								</ul>
							@endif
						</li>
					@endforeach
					</ul>
				@else
					<?php $branches = $sector->branch ?>
					<h4>{{ count($branches)>1?str_plural('Branch'):'Branch' }}</h4>
						@if(count($branches)>0)
							<ul class="list-unstyled">
							@foreach($branches as $k => $branch)
								<li>{{ ($k+1) }}. <a href="/masterfiles/branch/{{ $branch->lid() }}" target="_blank">{{ $branch->code }} - {{ $branch->descriptor }}</a></li>
							@endforeach
							</ul>
						@endif
				@endif
		  </div>
		</div>
	</div>			
	<div class="col-md-4 col-sm-6">
	@if($sector->is_parent())
		<div class="panel panel-default">
		  <div class="panel-body">
		    <h4>Sub Areas</h4>
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