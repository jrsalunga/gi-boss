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
		  	<table style="width: 100%;">
		  		<tbody>
		  			<tr>
		  				<td style="width: 33%;">
		  					<span>Main Area: </span>
								<span class="gly gly-parents"></span> <a href="/masterfiles/sector/{{$sector->parent->lcode()}}"> {{ $sector->parent->code or '*' }} - {{ $sector->parent->descriptor or '*' }} </a>
		  				</td>
		  				<td style="width: 33%;">
								@if(isset($sector->parent->am))
		  						@if(isset($sector->parent->am->position))
		  							<span class="label label-default help" title="{{$sector->parent->am->position->descriptor}}" data-toggle="tooltip">{{$sector->parent->am->position->code}}</span>
		  						@endif
		  						<span>
		  							<a href="/hr/masterfiles/employee/{{$sector->parent->am->lid() }}" target="_blank">
		  							{{ $sector->parent->am->lastname }}, {{ $sector->parent->am->firstname }} 
		  							</a>
		  						</span>
		  					@endif
		  				</td>
		  				<td>
		  					@if(isset($sector->parent->kh))
		  						@if(isset($sector->parent->kh->position))
		  							<span class="label label-default help" title="{{$sector->parent->am->position->descriptor}}" data-toggle="tooltip">{{$sector->parent->kh->position->code}}</span>
		  						@endif
		  						<span>
		  							<a href="/hr/masterfiles/employee/{{$sector->parent->kh->lid() }}" target="_blank">
		  							{{ $sector->parent->kh->lastname }}, {{ $sector->parent->kh->firstname }} 
										</a>
		  						</span>
		  					@endif
		  				</td>
		  			</tr>
		  		</tbody>
		  	</table>
		  	
			</div>
		</div>
	</div>
	@endif
	<div class="col-md-12">
		<div class="panel panel-default">
		  <div class="panel-body">
		  	<table style="width: 100%;">
		  		<tbody>
		  			<tr>
		  				<td style="width: 33%;">
		  					&nbsp;
		  				</td>
		  				<td style="width: 33%;">
								@if(isset($sector->am))
		  						@if(isset($sector->am->position))
		  							<span class="label label-default help" title="{{$sector->am->position->descriptor}}" data-toggle="tooltip">{{$sector->am->position->code}}</span>
		  						@endif
		  						<span>
		  							<a href="/hr/masterfiles/employee/{{$sector->am->lid() }}" target="_blank">
		  							{{ $sector->am->lastname }}, {{ $sector->am->firstname }} 
		  							</a>
		  						</span>
		  					@endif
		  				</td>
		  				<td>
		  					@if(isset($sector->kh))
		  						@if(isset($sector->kh->position))
		  							<span class="label label-default help" title="{{$sector->am->position->descriptor}}" data-toggle="tooltip">{{$sector->kh->position->code}}</span>
		  						@endif
		  						<span>
		  							<a href="/hr/masterfiles/employee/{{$sector->kh->lid() }}" target="_blank">
		  							{{ $sector->kh->lastname }}, {{ $sector->kh->firstname }} 
										</a>
		  						</span>
		  					@endif
		  				</td>
		  			</tr>
		  		</tbody>
		  	</table>
			</div>
		</div>
	</div>
	<div class="col-md-8 col-sm-6">
		<div class="panel panel-default">
		  <div class="panel-body">
		    @if(count($sector->children)>0)
		    <h4>{{ count($sector->children)>1?str_plural('Branch'):'Branch' }}</h4>
					<ul class="list-unstyled">
					@foreach($sector->children as $child)
						<li><i class="material-icons">pin_drop</i>
							<em><b><a href="/masterfiles/sector/{{ $child->lid() }}">{{ $child->code }}</a></b>
							</em>
								<small style="margin-left: 15px; ">
									@if(isset($child->am))
									{{ $child->am->position->code }}: 
		  							<a href="/hr/masterfiles/employee/{{$child->am->lid() }}" target="_blank">
										{{ $child->am->lastname }}, {{ $child->am->firstname }} 
										</a>
									@endif 
								<small style="margin-left: 15px; ">
								</small>
									@if(isset($child->kh))
									{{ $child->kh->position->code }}: 
										{{ $child->kh->lastname }}, {{ $child->kh->firstname }}
		  							<a href="/hr/masterfiles/employee/{{$child->kh->lid() }}" target="_blank">
		  							</a>
									@endif 
								</small>
							<?php $branches = $child->branch ?>
							@if(count($branches)>0)
								<ul class="list-unstyled" style="margin-left: 15px;">
								@foreach($branches as $k => $branch)
									<li>{{ ($k+1) }}. 
										<a class="{{$branch->status=='1'?'text-warning2':'text-sucess'}}" href="/masterfiles/branch/{{ $branch->lid() }}" target="_blank">{{ $branch->code }}</a> -
										<a class="{{$branch->status=='1'?'text-warning2':'text-sucess'}}" href="/hr/masterfiles/employee/branch/{{ $branch->lid() }}" target="_blank">{{ $branch->descriptor }}</a>
									</li>
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
								<li>{{ ($k+1) }}. 
									<a href="/masterfiles/branch/{{ $branch->lid() }}" target="_blank">{{ $branch->code }}</a> -
									<a href="/hr/masterfiles/employee/branch/{{ $branch->lid() }}" target="_blank">{{ $branch->descriptor }}</a>
								</li>
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