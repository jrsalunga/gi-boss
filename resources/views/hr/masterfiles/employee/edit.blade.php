@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Employee Edit: General Info')

@section('body-class', 'employee-edit-general')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header page-header-wizard">Update Employee Record</h3>
	</div>
	<div class="col-md-12">
		<ul class='nav nav-wizard'>
		  <li class='active'><a href="javascript:void(0)" data-toggle="tab"><span class="gly gly-user"></span> <span class="hidden-xs hidden-sm">General</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/employment"><span class="gly gly-folder-closed"></span> <span class="hidden-xs hidden-sm">Employment</span></a></li>
			<li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/personal"><span class="gly gly-nameplate-alt"></span> <span class="hidden-xs hidden-sm">Personal</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/family"><span class="gly gly-group"></span> <span class="hidden-xs hidden-sm">Family</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/workedu"><span class="gly gly-certificate"></span> <span class="hidden-xs hidden-sm">Work & Education</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/confirm"><span class="gly gly-disk-saved"></span> <span class="hidden-xs hidden-sm">Confirmation</span></a></li>
		</ul>
	</div>
	<div class="col-md-12">
		@include('_partials.alerts')
	</div>
</div>
<form action="/hr/masterfiles/employee" method="POST">
<div class="panel panel-primary">
	<div class="panel-body">
<div class="row">
	<div class="col-md-4">
		{{ csrf_field() }}

		<div class="form-group @include('_partials.input-error', ['field'=>'lastname'])">
	    <label for="lastname" class="control-label">Lastname</label>
	    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname" maxlength="30" value="{{ $employee->lastname }}">
	  </div>
	</div>
	<div class="col-md-4">
	  <div class="form-group @include('_partials.input-error', ['field'=>'firstname'])">
	    <label for="firstname" class="control-label">Firstname</label>
	    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Firstname" maxlength="30" value="{{ $employee->firstname }}">
	  </div>
	</div>
	<div class="col-md-4">
		<div class="form-group @include('_partials.input-error', ['field'=>'middlename'])">
	    <label for="middlename" class="control-label">Middlename</label>
	    <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middlename" maxlength="30" value="{{ $employee->middlename }}">
	  </div>
	</div>  
</div>
<div class="row">
	<div class="col-md-3">
		<div class="form-group @include('_partials.input-error', ['field'=>'code'])">
			<label for="code" class="control-label">Man No</label>
			<input type="text" class="form-control" id="code" placeholder="Man No" maxlength="6" value="{{ $employee->code }}" readonly>
		</div>
	</div><!-- end:.col-md-4 -->
	<div class="col-md-3">
		<div class="form-group @include('_partials.input-error', ['field'=>'companyid'])">
			<label for="companyid" class="control-label">Company</label>
			@if(count($companies)>0)
			<select class="selectpicker form-control show-tick" name="companyid" id="companyid" data-live-search="true" data-size="10" data-companyid="{{ $employee->companyid }}">
				@if(!isset($employee->company->id))
					<option disabled selected>-- Select Company -- </option>
				@endif
				@foreach($companies as $company)
			  	<option value="{{$company->id}}" <?=isset($employee->company->id)&&($company->id==$employee->companyid)?'selected':'';?> data-tokens="{{ $company->code }} {{ $company->descriptor }}">
			  		{{ $company->code }} - {{ $company->descriptor }}
			  	</option>
			  @endforeach
			</select>
			@else
				Add Company
			@endif
		</div>
	</div><!-- end:.col-md-4 -->
	<div class="col-md-3">
		<div class="form-group @include('_partials.input-error', ['field'=>'branchid'])">
			<label for="branchid" class="control-label">Branch</label>
			@if(count($branches)>0)
			<select class="selectpicker form-control show-tick" name="branchid" id="branchid" data-live-search="true" data-size="10" data-branchid="{{ $employee->branchid }}">
				@if(!isset($employee->branch->id))
					<option disabled selected>-- Select Branch -- </option>
				@endif
				@foreach($branches as $branch)
			  	<option value="{{$branch->id}}" <?=isset($employee->branch->id)&&($branch->id==$employee->branchid)?'selected':'';?> data-tokens="{{ $branch->code }} {{ $branch->descriptor }}">
			  		{{ $branch->code }} - {{ $branch->descriptor }}
			  	</option>
			  @endforeach
			</select>
			@else
				Add Branch
			@endif
		</div>
	</div><!-- end:.col-md-4 -->
	<div class="col-md-3">
		<div class="form-group @include('_partials.input-error', ['field'=>'positionid'])">
			<label for="positionid" class="control-label">Position</label>
			@if(count($positions)>0)
			<select class="selectpicker form-control show-tick" name="positionid" id="positionid" data-live-search="true" data-size="10"  data-positionid="{{ $employee->positionid }}">
				@if(!isset($employee->position->id))
					<option disabled selected>-- Select Position -- </option>
				@endif
				@foreach($positions as $position)
			  	<option value="{{$position->id}}" <?=isset($employee->position->id)&&($position->id==$employee->positionid)?'selected':'';?> data-tokens="{{ $position->code }} {{ $position->descriptor }}">
			  		{{ $position->code }} - {{ $position->descriptor }}
			  	</option>
			  @endforeach
			</select>
			@else
				Add Position
			@endif
		</div>
	</div><!-- end:.col-md-4 -->
</div>
	</div>
</div>
<hr>
<div class="row" style="margin-bottom: 50px;">
	<div class="col-md-12">
		<input type="hidden" name="_type" value="update_general">
		<input type="hidden" name="id" value="{{ $employee->id }}">
		@if(request()->has('raw') && request()->input('raw')=='true')
			<input type="hidden" name="_raw" value="true">
		@endif
		<button type="submit" name="_submit" value="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
		<button type="submit" name="_submit" value="next" class="btn btn-success" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save & Next</button>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/employment" class="btn btn-default pull-right" data-toggle="loader"><span class="gly gly-forward"></span> Skip</a>
	</div>
</div>
</form>

@endsection

