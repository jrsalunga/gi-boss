@extends('dash')

@section('title', '- Lessor')

@section('body-class', 'lessor-view')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		
		
		<h3 class="page-header">{{ $lessor->descriptor }} <small>{{ $lessor->code }}</small>
		<a href="/masterfiles/lessor/{{$lessor->lid()}}/edit" title="Toogle Edit" data-toggle="loader"><i class="material-icons">edit</i></a>
		<a href="/masterfiles/lessor" class="pull-right" title="Back to Lessor List" data-toggle="loader"><i class="material-icons">list</i></a>
		</h3>

		@include('_partials.alerts')

	</div>
	<div class="col-md-8 col-sm-6">

		<div>{{ $lessor->trade_name }}</div>
		<div>{{ $lessor->address }}</div>
		<div>{{ $lessor->tin }}</div>		
		<div>{{ $lessor->email }}</div>
		<br>
		@foreach($lessor->contacts as $c)
			<ul class="list-unstyled">
				<li>{!! contact_icon($c->type, true) !!} <a href="#">{{ $c->number }}</a></li>
			</ul>
		@endforeach

	
	</div>
	<div class="col-md-4 col-sm-6">
		<div class="panel panel-default">
		  <div class="panel-body">
		    <h5>Branches</h5>
		    <ul>
		    	@foreach($lessor->branches as $branch)
						<li>
							<a href="/masterfiles/branch/{{$branch->lcode()}}">{{ $branch->code }}</a> - 
							<a href="/masterfiles/branch/{{$branch->lid()}}">{{ $branch->descriptor }}</a>
						</li>
		    	@endforeach
		    </ul>
		  </div>
		</div>
	</div>
	
	

</div>
<div class="row">
	<div class="col-md-12">
		@include('_partials.pager', ['field'=>'code', 'model'=>$lessor])
	</div>
</div>
@endsection