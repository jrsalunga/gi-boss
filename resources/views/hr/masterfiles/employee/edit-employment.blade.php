@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Employee Update Employment')

@section('body-class', 'employee-update-employment')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header page-header-wizard">Update Employee Record</h3>
	</div>
	<div class="col-md-12">
		<ul class='nav nav-wizard'>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit" title="General Info"><span class="gly gly-user"></span> <span class="hidden-xs hidden-sm">General</span></a></li>
		  <li class='active'><a href="javascript:void(0)" data-toggle="tab" title="Employment Info"><span class="gly gly-folder-closed"></span> <span class="hidden-xs hidden-sm">Employment</span></a></li>
			<li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/personal" title="Personal Info"><span class="gly gly-nameplate-alt"></span> <span class="hidden-xs hidden-sm">Personal</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/family" title="Family Info"><span class="gly gly-group"></span> <span class="hidden-xs hidden-sm">Family</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/workedu" title="Work & Education Info"><span class="gly gly-certificate"></span> <span class="hidden-xs hidden-sm">Work & Education</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/confirm" title="Confirmation"><span class="gly gly-disk-saved"></span> <span class="hidden-xs hidden-sm">Confirmation</span></a></li>
		</ul>
	</div>
	<div class="col-md-12">
		@include('_partials.alerts')
	</div>
</div>
<h3 class="text-success" style="margin: 15px 0;"> {{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middlename }} 
	<small data-id="{{ $employee->id }}">{{ $employee->code }}</small>
</h3>
<form action="/hr/masterfiles/employee" method="POST">
	{{ csrf_field() }}
	<div class="panel panel-primary">
	  <div class="panel-body">
	  	<div class="row">
				<div class="col-md-4">
					<div class="form-group @include('_partials.input-error', ['field'=>'deptid'])">
						<label for="deptid" class="control-label">Department</label>
						@if(count($departments)>0)
						<select class="selectpicker form-control show-tick" name="deptid" id="deptid" data-live-search="true" data-size="10" data-deptid="{{ $employee->deptid }}">
							@if(!isset($employee->department->id))
								<option disabled selected>-- Select Company -- </option>
							@endif
							@foreach($departments as $department)
						  	<option value="{{$department->id}}" <?=isset($employee->department->id)&&($department->id==$employee->deptid)?'selected':'';?> data-tokens="{{ $department->code }} {{ $department->descriptor }}">
						  		{{ $department->code }} - {{ $department->descriptor }}
						  	</option>
						  @endforeach
						</select>
					@else
						Add Department
					@endif
					</div>	
				</div>
				<div class="col-md-4">
					<div class="form-group @include('_partials.input-error', ['field'=>'empstatus'])">
						<label for="empstatus" class="control-label">Employment Status</label>
						
						<select class="selectpicker form-control show-tick" name="empstatus" id="empstatus" data-live-search="true" data-size="10" data-empstatus="{{ $employee->empstatus }}">
							@if($employee->empstatus==0)
								<option disabled selected>-- Select Emp Status -- </option>
							@endif
							@foreach(['TRAINEE', 'CONTRACTUAL', 'REGULAR', 'RESIGNED', 'TERMNINATED'] as $key => $status)
						  	<option value="{{ ($key+1) }}" <?=isset($employee->empstatus)&&(($key+1)==$employee->empstatus)?'selected':'';?> data-tokens="{{ $status }}">
						  		{{ $status }}
						  	</option>
						  @endforeach
						</select>
					</div>	
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'datestart'])">
				    <label for="datestart" class="control-label">Date Start</label>
				    <input type="text" class="form-control datepicker" id="datestart" name="datestart" placeholder="YYYY-MM-DD" maxlength="10" value="{{ $employee->getDatestart() }}">
				  </div>
				</div><!-- end: .col-md-3 -->
			</div>
	  				  
	  </div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-primary -->
	<div class="panel panel-primary">
	  <div class="panel-body">
	  	<div class="row">
	  		<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'paytype'])">
						<label for="paytype" class="control-label">Pay Type</label>
						<select class="selectpicker form-control show-tick" name="paytype" id="paytype" data-live-search="true" data-size="10" data-paytype="{{ $employee->paytype }}">
							@if($employee->paytype==0)
								<option disabled selected>-- Select Pay Type -- </option>
							@endif
							@foreach(['WEEKLY', 'SEMI-MONTHLY', 'MONTHLY'] as $key => $pt)
						  	<option value="{{ ($key+1) }}" <?=isset($employee->paytype)&&(($key+1)==$employee->paytype)?'selected':'';?> data-tokens="{{ $pt }}">
						  		{{ $pt }}
						  	</option>
						  @endforeach
						</select>
					</div>	
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ratetype'])">
						<label for="ratetype" class="control-label">Rate Type</label>
						<select class="selectpicker form-control show-tick" name="ratetype" id="ratetype" data-live-search="true" data-size="10" data-ratetype="{{ $employee->paytype }}">
							@if($employee->ratetype==0)
								<option disabled selected>-- Select Rate Type -- </option>
							@endif
							@foreach(['DAILY', 'MONTHLY'] as $key => $rt)
						  	<option value="{{ ($key+1) }}" <?=isset($employee->ratetype)&&(($key+1)==$employee->ratetype)?'selected':'';?> data-tokens="{{ $rt }}">
						  		{{ $rt }}
						  	</option>
						  @endforeach
						</select>
					</div>	
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
				  <div class="form-group @include('_partials.input-error', ['field'=>'rate'])">
				    <label for="rate" class="control-label">Basic Rate</label>
				    <input type="text" class="form-control" id="rate" name="rate" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ $employee->rate }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecola'])">
				    <label for="ecola" class="control-label">ECOLA</label>
				    <input type="text" class="form-control" id="ecola" name="ecola" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ $employee->ecola }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'allowance1'])">
				    <label for="allowance1" class="control-label">Allowance 1</label>
				    <input type="text" class="form-control" id="allowance1" name="allowance1" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ $employee->allowance1 }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'allowance2'])">
				    <label for="allowance2" class="control-label">Allowance 2</label>
				    <input type="text" class="form-control" id="allowance2" name="allowance2" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ $employee->allowance2 }}">
				  </div>
				</div><!-- end: .col-md-3 -->
			</div><!-- end: .row -->
		</div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-primary -->
	<div class="panel panel-primary">
	  <div class="panel-body">
	  	<div class="row">
	  		<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'sssno'])">
				    <label for="sssno" class="control-label">SSS #</label>
				    <input type="text" class="form-control" id="sssno" name="sssno" placeholder="00-0000000-0" data-mask="00-0000000-0" maxlength="12" value="{{ $employee->sssno }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ee_sss'])">
				    <label for="ee_sss" class="control-label">SSS EE Share</label>
				    <input type="text" class="form-control" id="ee_sss" name="ee_sss" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ isset($employee->statutory)?$employee->statutory->ee_sss:'' }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'er_sss'])">
				    <label for="er_sss" class="control-label">SSS ER Share</label>
				    <input type="text" class="form-control" id="er_sss" name="er_sss" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ isset($employee->statutory)?$employee->statutory->er_sss:'' }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-2">
					<div class="form-group @include('_partials.input-error', ['field'=>'sss_tag'])">
						<label for="sss_tag" class="control-label">Tag</label>
						<select class="form-control" name="sss_tag" id="sss_tag">
							@foreach(['No', 'Yes'] as $key => $t)
						  	<option value="{{ $key }}" <?=isset($employee->statutory)&&($key==$employee->statutory->sss_tag)?'selected':'';?> data-tokens="{{ $t }}">
						  		{{ $t }}
						  	</option>
						  @endforeach
						</select>
					</div>
				</div>
				<div class="col-md-3 col-md-offset-1 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'phicno'])">
				    <label for="phicno" class="control-label">PhilHealth #</label>
				    <input type="text" class="form-control" id="phicno" name="phicno" placeholder="000000000000" data-mask="000000000000" maxlength="12" value="{{ $employee->phicno }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'ee_phic'])">
				    <label for="ee_phic" class="control-label">PhilHealth EE Share</label>
				    <input type="text" class="form-control" id="ee_phic" name="ee_phic" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ isset($employee->statutory)?$employee->statutory->ee_phic:'' }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'er_phic'])">
				    <label for="er_phic" class="control-label">PhilHealth ER Share</label>
				    <input type="text" class="form-control" id="er_phic" name="er_phic" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ isset($employee->statutory)?$employee->statutory->er_phic:'' }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-2 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'phic_tag'])">
						<label for="phic_tag" class="control-label">Tag</label>
						<select class="form-control" name="phic_tag" id="phic_tag">
							@foreach(['No', 'Yes'] as $key => $t)
						  	<option value="{{ $key }}" <?=isset($employee->statutory)&&($key==$employee->statutory->phic_tag)?'selected':'';?> data-tokens="{{ $t }}">
						  		{{ $t }}
						  	</option>
						  @endforeach
						</select>
					</div>
				</div>
				<div class="col-md-3 col-md-offset-1 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'hdmfno'])">
				    <label for="hdmfno" class="control-label">Pag Ibig #</label>
				    <input type="text" class="form-control" id="hdmfno" name="hdmfno" placeholder="0000-0000-0000" data-mask="0000-0000-0000" maxlength="14" value="{{ $employee->hdmfno }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'ee_hdmf'])">
				    <label for="ee_hdmf" class="control-label">Pag Ibig EE Share</label>
				    <input type="text" class="form-control" id="ee_hdmf" name="ee_hdmf" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ isset($employee->statutory)?$employee->statutory->ee_hdmf:'' }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'er_hdmf'])">
				    <label for="er_hdmf" class="control-label">Pag Ibig ER Share</label>
				    <input type="text" class="form-control" id="er_hdmf" name="er_hdmf" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ isset($employee->statutory)?$employee->statutory->er_hdmf:'' }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-2 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'hdmf_tag'])">
						<label for="hdmf_tag" class="control-label">Tag</label>
						<select class="form-control" name="hdmf_tag" id="hdmf_tag">
							@foreach(['No', 'Yes'] as $key => $t)
						  	<option value="{{ $key }}" <?=isset($employee->statutory)&&($key==$employee->statutory->hdmf_tag)?'selected':'';?> data-tokens="{{ $t }}">
						  		{{ $t }}
						  	</option>
						  @endforeach
						</select>
					</div>
				</div>
				<div class="col-md-3 col-md-offset-1 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'tin'])">
				    <label for="tin" class="control-label">TIN #</label>
				    <input type="text" class="form-control" id="tin" name="tin" placeholder="000-000-000-000" data-mask="000-000-000-000" maxlength="15" value="{{ $employee->tin }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3 col-md-pull-1">
					<div class="form-group @include('_partials.input-error', ['field'=>'wtax'])">
				    <label for="wtax" class="control-label">W/Tax</label>
				    <input type="text" class="form-control" id="wtax" name="wtax" placeholder="0.00" data-mask="000,000.00" data-mask-reverse="true" maxlength="10" value="{{ isset($employee->statutory)?$employee->statutory->wtax:'' }}">
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-2 col-md-pull-1 col-md-push-2">
					<div class="form-group @include('_partials.input-error', ['field'=>'wtax_tag'])">
						<label for="wtax_tag" class="control-label">Tag</label>
						<select class="form-control" name="wtax_tag" id="wtax_tag">
							@foreach(['No', 'Yes'] as $key => $t)
						  	<option value="{{ $key }}" <?=isset($employee->statutory)&&($key==$employee->statutory->wtax_tag)?'selected':'';?> data-tokens="{{ $t }}">
						  		{{ $t }}
						  	</option>
						  @endforeach
						</select>
					</div>
				</div>
			</div><!-- end: .row -->
	</div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-primary -->
<hr>
<div class="row" style="margin-bottom: 50px;">
	<div class="col-md-12">
		<input type="hidden" name="_type" value="employment">
		<input type="hidden" name="id" value="{{ $employee->id }}">
		@if(request()->has('raw') && request()->input('raw')=='true')
			<input type="hidden" name="_raw" value="true">
		@endif
		<button type="submit" name="_submit" value="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
		<button type="submit" name="_submit" value="next" class="btn btn-success" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save & Next</button>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> <span class="hidden-xs hidden-sm">Cancel</span></a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/personal" class="btn btn-default pull-right" data-toggle="loader"><span class="gly gly-forward"></span> <span class="hidden-xs hidden-sm">Skip</span></a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit" class="btn btn-default pull-right" data-toggle="loader" style="margin-right: 5px;">
			<span class="gly gly-rewind"></span> 
			<span class="hidden-xs hidden-sm">Back</span>
		</a>
	</div>
</div>
</form>

@endsection

