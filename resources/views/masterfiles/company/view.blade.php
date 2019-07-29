@extends('dash')

@section('title', '- Company')

@section('body-class', 'company-view')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		
		
		<h3 class="page-header">{{ $company->descriptor }} <small>{{ $company->code }}</small>
		<a href="/masterfiles/company/{{$company->lid()}}/edit" title="Toogle Edit" data-toggle="loader"><i class="material-icons">edit</i></a>
		<a href="/masterfiles/company" class="pull-right" title="Back to Company List" data-toggle="loader"><i class="material-icons">list</i></a>
		</h3>

		@include('_partials.alerts')

		<div class="tab-wrap">
	  <!-- Nav tabs -->
		  <ul class="nav nav-tabs" role="tablist">
		    <li role="presentation" class="active">
		    	<a href="#home" aria-controls="home" role="tab" data-toggle="tab">
			    	<i class="material-icons">library_books</i> 
						<span class="hidden-xs hidden-sm">General</span>
			    </a>
		    </li>
		    <li role="presentation">
		    	<a href="#gov" aria-controls="gov" role="tab" data-toggle="tab">
		    		<i class="material-icons">account_balance</i>
						<span class="hidden-xs hidden-sm">Gov</span>
		    	</a>
		   	</li>
		   	<li role="presentation">
		    	<a href="#branches" aria-controls="gov" role="tab" data-toggle="tab">
		    		<i class="gly gly-shop"></i>
						<span class="hidden-xs hidden-sm">Branch</span>
		    	</a>
		   	</li>
		  </ul>
		  <!-- Tab panes -->
		  <div class="tab-content" style="margin-top: 10px;">
		    <div role="tabpanel" class="tab-pane active" id="home">
					<ul class="list-unstyled">
						<li><span class="glyphicon glyphicon-map-marker"></span> <a href="#">{{ $company->address }}</a></li>
						<li><span class="gly gly-envelope"></span> <a href="mailto:{{ $company->email }}">{{ $company->email }}</a></li>
					</ul>

					<ul class="list-unstyled">
					@foreach($company->contacts as $c)
							<li>{!! contact_icon($c->type, true) !!} <a href="#">{{ $c->number }}</a></li>
					@endforeach
					</ul>
		    </div><!-- end:#home -->
		    <div role="tabpanel" class="tab-pane" id="gov">
		    	<ul class="list-unstyled">
		    		<li>TIN: {{ $company->tin }}</li>
		    		<li>SSS: {{ $company->sss_no }}</li>
		    		<li>Philhealth: {{ $company->philhealth_no }}</li>
		    		<li>Pag-Ibig: {{ $company->hdmf_no }}</li>
		    	</ul>
		    </div><!-- end:#gov -->
		     <div role="tabpanel" class="tab-pane" id="branches">
		     	
		     	<table class="table table-condensed">
		     		<tbody>
		    		@foreach($company->all_branches as $branch)
		     			<tr data-id="{{ $branch->id }}" data-status="{{ $branch->status}}" >
		     				<td>
		     					<a class="{{ $branch->status=='3'?'text-muted':(in_array($branch->type, ['5', '4'])?'text-primary':($branch->status=='1'?'text-warning2':'text-sucess')) }}" href="/masterfiles/branch/{{ strtolower($branch->code) }}">{{ $branch->code }}</a>
		     				</td>
		     				<td>
		     					<a class="{{ $branch->status=='3'?'text-muted':(in_array($branch->type, ['5', '4'])?'text-primary':($branch->status=='1'?'text-warning2':'text-sucess')) }}" href="/masterfiles/branch/{{ $branch->lid() }}">{{ $branch->descriptor }}</a>
		     					@if($branch->status=='1')
										<span class="label label-warning pull-right">Under Construction</span>
									@endif
									@if($branch->status=='3')
										<span class="label label-default pull-right">Closed</span>
									@endif
									@if($branch->type=='4')
										<span class="label label-primary pull-right" style="margin-right: 3px;">Office</span>
									@endif
									@if($branch->type=='5')
										<span class="label label-info pull-right" style="margin-right: 3px;">Other</span>
									@endif
		     				</td>
		     				<td>{{ $branch->tin }}</td>
		     			</tr>
		    		@endforeach
		     		</tbody>
		     	</table>
		    	</ul>
		    </div><!-- end:#branches -->
		  </div>
		</div><!-- end:.tab-wrap -->
		
		<br>
		

	

		
		@include('_partials.pager', ['field'=>'code', 'model'=>$company])
	</div>
		

</div>
@endsection