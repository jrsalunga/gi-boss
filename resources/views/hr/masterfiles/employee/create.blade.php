@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Employee Add')

@section('body-class', 'employee-add')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header page-header-wizard">Create Employee Record</h3>
	</div>
	<div class="col-md-12">
		<ul class='nav nav-wizard'>
		  <li class='active'><a href='#' data-toggle="tab"><span class="gly gly-user"></span> <span class="hidden-xs hidden-sm">General</span></a></li>
		  <li><a href='#'><span class="gly gly-folder-closed"></span> <span class="hidden-xs hidden-sm">Employment</span></a></li>
			<li><a href='#'><span class="gly gly-nameplate-alt"></span> <span class="hidden-xs hidden-sm">Personal</span></a></li>
		  <li><a href='#'><span class="gly gly-group"></span> <span class="hidden-xs hidden-sm">Family</span></a></li>
		  <li><a href='#'><span class="gly gly-certificate"></span> <span class="hidden-xs hidden-sm">Work & Education</span></a></li>
		  <li><a href='#'><span class="gly gly-disk-saved"></span> <span class="hidden-xs hidden-sm">Confirmation</span></a></li>
		</ul>
	</div>
	<div class="col-md-12">
		@include('_partials.alerts')
	</div>
</div>

<form action="/hr/masterfiles/employee" method="POST">
<div class="row">
	<div class="col-md-4">
		{{ csrf_field() }}

		<div class="form-group @include('_partials.input-error', ['field'=>'lastname'])">
	    <label for="lastname" class="control-label">Lastname</label>
	    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname" maxlength="30" value="{{ request()->old('lastname') }}">
	  </div>
	</div>
	<div class="col-md-4">
	  <div class="form-group @include('_partials.input-error', ['field'=>'firstname'])">
	    <label for="firstname" class="control-label">Firstname</label>
	    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Firstname" maxlength="30" value="{{ request()->old('firstname') }}">
	  </div>
	</div>
	<div class="col-md-4">
		<div class="form-group @include('_partials.input-error', ['field'=>'middlename'])">
	    <label for="middlename" class="control-label">Middlename</label>
	    <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middlename" maxlength="30" value="{{ request()->old('middlename') }}">
	  </div>
	</div>  
</div>
<div class="row">
	<div class="col-md-3">
		<div class="form-group @include('_partials.input-error', ['field'=>'code'])">
			<label for="code" class="control-label">Man No</label>
			<input type="text" class="form-control" id="code" name="code" placeholder="Man No" maxlength="6" value="{{ request()->old('code') }}">
		</div>
	</div><!-- end:.col-md-4 -->
	<div class="col-md-3">
		<div class="form-group @include('_partials.input-error', ['field'=>'companyid'])">
			<label for="companyid" class="control-label">Company</label>
			@if(count($companies)>0)
			<select class="selectpicker form-control show-tick" name="companyid" id="companyid" data-live-search="true" data-size="10">
				@if(!isset($employee->company->id))
					<option disabled selected>-- Select Company -- </option>
				@endif
				@foreach($companies as $company)
			  	<option value="{{$company->id}}" <?=isset($employee->company->id)&&($company->id==$employee->company->id)?'selected':'';?> data-tokens="{{ $company->code }} {{ $company->descriptor }}">
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
			<select class="selectpicker form-control show-tick" name="branchid" id="branchid" data-live-search="true" data-size="10">
				@if(!isset($employee->branch->id))
					<option disabled selected>-- Select Branch -- </option>
				@endif
				@foreach($branches as $branch)
			  	<option value="{{$branch->id}}" <?=isset($employee->branch->id)&&($branch->id==$employee->branch->id)?'selected':'';?> data-tokens="{{ $branch->code }} {{ $branch->descriptor }}">
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
			@if(count($branches)>0)
			<select class="selectpicker form-control show-tick" name="positionid" id="positionid" data-live-search="true" data-size="10">
				@if(!isset($employee->position->id))
					<option disabled selected>-- Select Position -- </option>
				@endif
				@foreach($positions as $position)
			  	<option value="{{$position->id}}" <?=isset($employee->position->id)&&($position->id==$employee->position->id)?'selected':'';?> data-tokens="{{ $position->code }} {{ $position->descriptor }}">
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
<hr>
<div class="row">
	<div class="col-md-6">
		<input type="hidden" name="_type" value="quick">
		<button type="submit" name="_submit" value="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
		<button type="submit" name="_submit" value="next" class="btn btn-success" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save & Next</button>
		<a href="/hr/masterfiles/employee" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
	</div>
</div>
</form>

@endsection

