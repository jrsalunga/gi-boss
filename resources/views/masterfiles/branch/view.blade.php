@extends('dash')

@section('title', '- Branch List')

@section('body-class', 'branch-list')

<?php
 $branch->load('company');
?>
@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">

		
		<h3 class="page-header text-success">
			<span class="gly gly-shop"></span> {{ $branch->descriptor }} <small>{{ $branch->code }}</small>

		<a href="/masterfiles/branch/{{$branch->lid()}}/edit" data-toggle="loader"><i class="material-icons">edit</i></a>
		<a href="/masterfiles/branch" class="pull-right" data-toggle="loader"><i class="material-icons">list</i></a>
		</h3>

		@include('_partials.alerts')
		
		<div>{{ $branch->address }}</div>
		<div>{{ $branch->tin or '-' }}</div>
		<div>{{ $branch->email }}</div>
		@if(isset($branch->company))
		<div>
			<a href="/masterfiles/company/{{ $branch->company->lid() }}">
				{{ $branch->company->descriptor }}
			</a>
		</div>
		@endif
		@if(isset($branch->lessor))
		<div>
			<a href="/masterfiles/lessor/{{ $branch->lessor->lid() }}">
				{{ $branch->lessor->descriptor }}
			</a>
		</div>
		@endif
		@if(isset($branch->sector))
		<div>
			<a href="/masterfiles/sector/{{ $branch->sector->lid() }}">
				{{ $branch->sector->descriptor }}
			</a>
		</div>
		@endif
		<ul class="list-unstyled">
		@foreach($branch->contacts as $c)
				<li>{!! contact_icon($c->type, true) !!} <a href="#">{{ $c->number }}</a></li>
		@endforeach
		</ul>
		<br>
		@foreach($branch->boss as $u)
			@if($u->user->admin=='3')
			{{ $u->user->name }} <small>
			<div>
				<em>{{ $u->user->email }}</em></small>
			</div>
			@endif
		@endforeach
	</div>
	<br>
	@include('_partials.pager', ['field'=>'code', 'model'=>$branch])
</div>
@endsection