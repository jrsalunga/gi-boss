@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Employee Confirm')

@section('body-class', 'employee-edit-confirm')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header page-header-wizard">Confirm Employee Record</h3>
	</div>
	<div class="col-md-12">
		<ul class='nav nav-wizard'>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit" title="General Info"><span class="gly gly-user"></span> <span class="hidden-xs hidden-sm">General</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/employment" title="Employment Info"><span class="gly gly-folder-closed"></span> <span class="hidden-xs hidden-sm">Employment</span></a></li>
			<li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/personal" title="Personal Info"><span class="gly gly-nameplate-alt"></span> <span class="hidden-xs hidden-sm">Personal</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/family" title="Family Info"><span class="gly gly-group"></span> <span class="hidden-xs hidden-sm">Family</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/workedu" title="Work & Education Info"><span class="gly gly-certificate"></span> <span class="hidden-xs hidden-sm">Work & Education</span></a></li>
		  <li class='active'><a href="javascript:void(0)" data-toggle="tab"  title="Confirmation"><span class="gly gly-disk-saved"></span> <span class="hidden-xs hidden-sm">Confirmation</span></a></li>
		</ul>
	</div>
	<div class="col-md-12">
		@include('_partials.alerts')
	</div>
</div>
<form action="/hr/masterfiles/employee" method="POST">
	{{ csrf_field() }}
<div class="panel panel-primary">
	<div class="panel-heading">Employee</div>
  <div class="panel-body">
  	<h4>Man #: {{ $employee->code }}</h4>
  	<h3><a href="/hr/masterfiles/employee/{{ $employee->lid() }}">{{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middlename }}</a></h3>
 	</div>
</div>
<?php
	$f = 'field is';
	if(count($invalid_fields)>1) {
		$f = 'fields are';
		$valid = false;
	}

	$c = '';
	$g = '';
	if ($employee->isConfirm() && $employee->hasEmpfile('MAS'))
		$valid = true;
	elseif ($employee->isConfirm() && !$employee->hasEmpfile('MAS')) {
		$valid = true;
		$g = 'text-muted';
	}	elseif (!$employee->isConfirm() && $employee->hasEmpfile('MAS')) {
		$valid = true;
		$c = 'text-muted';
	}
?>

@if(count($invalid_fields)>0)
<div class="panel panel-warning">
	<div class="panel-heading">Required Fields</div>
  <div class="panel-body">
  	<h4 class="text-danger">Unable to confirm and generate .MAS file, unless the following {{$f}} resolved.</h4>
  	<ul>
  	@foreach($invalid_fields as $field => $value)
			<li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/{{ $value['url'] }}">{{ $value['desc'] }}</a></li>
  	@endforeach
  	</ul>
  </div>
</div>
@else

	

	@if($employee->isConfirm() && $employee->hasEmpfile('MAS'))
		<h4 class="text-success">.MAS file already generated.</h4>
	@elseif($employee->isConfirm() && !$employee->hasEmpfile('MAS'))
		<h4 class="text-info">Employee is already confirmed but missing .MAS file</h4>
	@elseif(!$employee->isConfirm() && $employee->hasEmpfile('MAS'))
		<h4 class="text-info">Employee is not confirmed but has .MAS file</h4>
	@else

	@endif
		<div class="panel panel-info">
			<div class="panel-heading">E-mail to Branch</div>
		  <div class="panel-body">
		  	<div class="row">
		  		<div class="col-md-12">
						<div class="checkbox">
					  <label>
					    <input type="checkbox" value="1" name="email" checked>
					    Do you want to generate an email to <b>{{ $employee->branch->code }} - {{ $employee->branch->descriptor }}</b>@if(!empty($employee->branch->email))
					    <em>({{ $employee->branch->email }})</em>
					    @endif? 
					  </label>
						</div>
		  		</div>
		  		<div class="col-md-8">
						<div class="form-group">
					    <textarea class="form-control" id="message" name="message" placeholder="Message" maxlength="1000" style="max-width: 100%; min-width: 100%;" rows="4">Please double check and make sure all info is correct.&#13;&#10;&#10;Download and save to gi_pay folder.</textarea>
					  </div>
					</div>
		  	</div>
		 	</div>
		</div>
@endif
<hr>
<div class="row" style="margin-bottom: 50px;">
	<div class="col-md-12">
		<input type="hidden" name="_type" value="confirm">
		<input type="hidden" name="id" value="{{ $employee->id }}">
		@if(request()->has('raw') && request()->input('raw')=='true')
			<input type="hidden" name="_raw" value="true">
		@endif
		<button type="submit" name="_submit" value="submit" class="btn btn-success" data-toggle="loader" {{ $valid ? '':'disabled' }}>
			<span class="gly gly-disk-saved" data-toggle="loader"></span> 
			<span class="{{ $c }}">Confirm</span> & 
			<span class="{{ $g }}">Generate .MAS File</span>
		</button>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
		
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/workedu" class="btn btn-default pull-right" data-toggle="loader" style="margin-right: 5px;">
			<span class="gly gly-rewind"></span> 
			<span class="hidden-xs hidden-sm">Back</span>
		</a>

		<a href="/hr/masterfiles/employee/create" class="btn btn-primary pull-right" data-toggle="loader" style="margin-right: 5px;">
			<i class="material-icons">note_add</i> 
			<span class="hidden-xs hidden-sm">Create New Employee Record</span>
		</a>
	</div>
</div>
</form>

@endsection

