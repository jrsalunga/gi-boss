@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', $employee->code .' - '.$employee->lastname.', '.$employee->firstname.' '.$employee->middlename .'- Employee Edit: General Info')

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
	<div class="panel-heading">Employee</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-4">
				{{ csrf_field() }}

				<div class="form-group @include('_partials.input-error', ['field'=>'lastname'])">
			    <label for="lastname" class="control-label">Lastname</label>
			    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname" maxlength="30" value="{{ !is_null(old('lastname'))?old('lastname'):$employee->lastname }}">
			  </div>
			</div>
			<div class="col-md-4">
			  <div class="form-group @include('_partials.input-error', ['field'=>'firstname'])">
			    <label for="firstname" class="control-label">Firstname</label>
			    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Firstname" maxlength="30" value="{{ !is_null(old('firstname'))?old('firstname'):$employee->firstname }}">
			  </div>
			</div>
			<div class="col-md-4">
				<div class="form-group @include('_partials.input-error', ['field'=>'middlename'])">
			    <label for="middlename" class="control-label">Middlename</label>
			    <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middlename" maxlength="30" value="{{ !is_null(old('middlename'))?old('middlename'):$employee->middlename }}">
			  </div>
			</div>  
		</div>
		<div class="row">
			<div class="col-md-3">
				<div class="form-group @include('_partials.input-error', ['field'=>'code'])">
					<label for="code" class="control-label">Man No</label>
					<input type="text" class="form-control" id="code" placeholder="Man No" maxlength="6" value="{{ $employee->code }}" readonly style="cursor: default;">
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

