@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Employee Update Personal Info')

@section('body-class', 'employee-update-personal')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header page-header-wizard">Update Employee Record</h3>
	</div>
	<div class="col-md-12">
		<ul class='nav nav-wizard'>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit" title="General Info"><span class="gly gly-user"></span> <span class="hidden-xs hidden-sm">General</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/employment" title="Employment Info"><span class="gly gly-folder-closed"></span> <span class="hidden-xs hidden-sm">Employment</span></a></li>
			<li class='active'><a href="javascript:void(0)" data-toggle="tab" title="Personal Info"><span class="gly gly-nameplate-alt"></span> <span class="hidden-xs hidden-sm">Personal</span></a></li>
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
		<div class="panel-heading">Address & Contacts</div>
	  <div class="panel-body">
	  	<div class="row">
				<div class="col-md-6">
					<div class="form-group @include('_partials.input-error', ['field'=>'address'])">
				    <label for="address" class="control-label">Address</label>
				    <textarea class="form-control" id="address" name="address" placeholder="Address" maxlength="120" style="max-width: 100%; min-width: 100%;" rows="5">{{ !is_null(old('address'))?old('address'):$employee->address }}</textarea>
				  </div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'mobile'])">
				   	<label for="mobile" class="control-label">Mobile</label>
					 	<input type="text" class="form-control" id="mobile" name="mobile" placeholder="0000-0000000" data-mask="0000-0000000" maxlength="20" value="{{ !is_null(old('mobile'))?old('mobile'):$employee->mobile }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'email'])">
				   	<label for="email" class="control-label">Email</label>
					 	<input type="text" class="form-control" id="email" name="email" placeholder="Email" maxlength="80" value="{{ !is_null(old('email'))?old('email'):$employee->email }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'phone'])">
				   	<label for="phone" class="control-label">Phone</label>
					 	<input type="text" class="form-control" id="phone" name="phone" placeholder="(00) 000-0000" data-mask="(00) 000-0000" maxlength="20" value="{{ !is_null(old('phone'))?old('phone'):$employee->phone }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'fax'])">
				   	<label for="fax" class="control-label">Fax</label>
					 	<input type="text" class="form-control" id="fax" name="fax" placeholder="(00) 000-0000" data-mask="(00) 000-0000" maxlength="20" value="{{ !is_null(old('fax'))?old('fax'):$employee->fax }}">
					</div>
				</div>
			</div><!-- end: .row -->
	  </div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-primary -->
	<div class="panel panel-primary">
		<div class="panel-heading">Personal Details</div>
	  <div class="panel-body">
	  	<div class="row">
	  		<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'birthdate'])">
				    <label for="birthdate" class="control-label">Birthday</label>
				    <div class="input-group datepicker">
				    	<input type="text" class="form-control" id="birthdate" name="birthdate" placeholder="YYYY-MM-DD" data-mask="0000-00-00" maxlength="10" value="{{ !is_null(old('birthdate'))?old('birthdate'):$employee->getBirthdate() }}">
				    	<div class="input-group-addon">
					        <span class="glyphicon glyphicon-calendar"></span>
					    </div>
					  </div>
				  </div>
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
				  <div class="form-group @include('_partials.input-error', ['field'=>'birthplace'])">
				    <label for="birthplace" class="control-label">Birthplace</label>
				    <input type="text" class="form-control" id="birthplace" name="birthplace" placeholder="Birthplace" maxlength="30" value="{{ !is_null(old('birthplace'))?old('birthplace'):$employee->birthplace }}">
				  </div>
				</div><!-- end: .col-md-3 -->
	  		<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'gender'])">
						<label for="gender" class="control-label">Gender</label>
						<?php $_gender = !is_null(old('gender'))?old('gender'):$employee->gender; ?>
						<select class="selectpicker form-control show-tick" name="gender" id="gender" data-live-search="true" data-size="10" data-gender="{{ $_gender }}">
							@if($_gender==0)
								<option disabled selected>-- Select Gender -- </option>
							@endif
							@foreach(['MALE', 'FEMALE'] as $key => $g)
						  	<option value="{{ ($key+1) }}" <?=(($key+1)==$_gender)?'selected':'';?> data-tokens="{{ $g }}">
						  		{{ $g }}
						  	</option>
						  @endforeach
						</select>
					</div>	
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'civstatus'])">
						<label for="civstatus" class="control-label">Civil Status</label>
						<?php $_civstatus = !is_null(old('civstatus'))?old('civstatus'):$employee->civstatus; ?>
						<select class="selectpicker form-control show-tick" name="civstatus" id="civstatus" data-live-search="true" data-size="10" data-civstatus="{{ $_civstatus }}">
							@if($_civstatus==0)
								<option disabled selected>-- Select Civil Status -- </option>
							@endif
							@foreach(['SINGLE', 'MARRIED', 'SEPARATED'] as $key => $c)
						  	<option value="{{ ($key+1) }}" <?=(($key+1)==$_civstatus)?'selected':'';?> data-tokens="{{ $c }}">
						  		{{ $c }}
						  	</option>
						  @endforeach
						</select>
					</div>	
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'religionid'])"">
						<label for="religionid" class="control-label">Religion</label>
						<?php $_religionid = !is_null(old('religionid'))?old('religionid'):$employee->religionid; ?>
						@if(count($religions)>0)
						<select class="selectpicker form-control show-tick" name="religionid" id="religionid" data-live-search="true" data-size="10"  data-religionid="{{ $employee->religionid }}">
							@if(!isset($employee->religion->id))
								<option disabled selected>-- Select Religion -- </option>
							@endif
							@foreach($religions as $religion)
						  	<option value="{{$religion->id}}" <?=($religion->id==$_religionid)?'selected':'';?> data-tokens="{{ $religion->code }} {{ $religion->descriptor }}">
						  		{{ $religion->code }} - {{ $religion->descriptor }}
						  	</option>
						  @endforeach
						</select>
						@else
							Add Religion
						@endif
					</div>
				</div><!-- end:.col-md-3 -->
				<?php
					$hgts = ['121.92', '124.46', '127.00', '129.54', '132.08', '134.62', '137.16', '139.70', '142.24', '144.78', '147.32', '149.86', '152.40', '154.94', '157.48', '160.02', '162.56', '165.10', '167.64', '170.18', '172.72', '175.26', '177.80', '180.34', '182.88', '185.42', '187.96', '190.50', '193.04', '195.58', '198.12', '200.66', '203.20', '205.74', '208.28', '210.82', '213.36', '215.90', '218.44', '220.98', '223.52', '226.06', '228.60', '231.14', '233.68', '236.22', '238.76', '241.30', '243.84'];
					$m_hgts = ['1.2192',	'1.2446',	'1.2700',	'1.2954',	'1.3208',	'1.3462',	'1.3716',	'1.3970',	'1.4224',	'1.4478',	'1.4732',	'1.4986',	'1.5240',	'1.5494',	'1.5748',	'1.6002',	'1.6256',	'1.6510',	'1.6764',	'1.7018',	'1.7272',	'1.7526',	'1.7780',	'1.8034',	'1.8288',	'1.8542',	'1.8796',	'1.9050',	'1.9304',	'1.9558',	'1.9812',	'2.0066',	'2.0320',	'2.0574',	'2.0828',	'2.1082',	'2.1336',	'2.1590',	'2.1844',	'2.2098',	'2.2352',	'2.2606',	'2.286',	'2.3114',	'2.3368',	'2.3622',	'2.3876',	'2.4130',	'2.4384'];
					$ft = 4;
					$in = 0;
				?>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'height'])">
						<label for="height" class="control-label">Height (m)</label>
						<?php $_height = !is_null(old('height'))?old('height'):$employee->height; ?>
						<select class="selectpicker form-control show-tick" name="height" id="height" data-live-search="true" data-size="10" data-height="{{ $_height }}">
							@if($_height<1.21)
								<option disabled selected>-- Select Height -- </option>
							@endif
							@foreach($m_hgts as $key => $c)
						  	<option value="{{ number_format($c,2) }}" <?=(number_format($c,2)==$_height)?'selected':'';?> data-tokens="{{ number_format($c,2) }} {{ $ft }}'{{ $in }}''" data-actual="{{ $c }}">
						  		{{ number_format($c,2) }}m <?=$in>0?"(".$ft."'".$in."'')":"(".$ft."'')";?>
						  	</option>
						  	<?php
						  		$in++;
						  		if ($in==12) {
						  			$in=0;
						  			$ft++;
						  		}
						  	?>
						  @endforeach
						</select>
					</div>	
				</div><!-- end: .col-md-3 -->
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'weight'])">
				    <label for="weight" class="control-label">Weight (kg)</label>
				    <input type="text" class="form-control" id="weight" name="weight" placeholder="0.00" data-mask="000.00" data-mask-reverse="true" maxlength="8" value="{{ !is_null(old('weight'))?old('weight'):$employee->weight }}">
				  </div>
				</div><!-- end: .col-md-3 -->
			</div><!-- end: .row -->
		</div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-primary -->
	<div class="panel panel-primary">
	  <div class="panel-body">
	  	<div class="row">
	  		<div class="col-md-6">
					<div class="form-group @include('_partials.input-error', ['field'=>'notes'])">
				    <label for="notes" class="control-label">Notes</label>
				    <textarea class="form-control" id="notes" name="notes" placeholder="Notes" maxlength="220" style="max-width: 100%; min-width: 100%;" rows="5">{{ !is_null(old('notes'))?old('notes'):$employee->notes }}</textarea>
				  </div>
				</div>
				<div class="col-md-4">
				  <div class="form-group @include('_partials.input-error', ['field'=>'hobby'])">
				    <label for="hobby" class="control-label">Hobbies</label>
				    <input type="text" class="form-control" id="hobby" name="hobby" placeholder="Hobbies" maxlength="50" value="{{ !is_null(old('hobby'))?old('hobby'):$employee->hobby }}">
				  </div>
				</div><!-- end: .col-md-3 -->

				<div class="col-md-4">
				  <div class="form-group @include('_partials.input-error', ['field'=>'uniform'])">
				    <label for="uniform" class="control-label">Uniform</label>
				    <input type="text" class="form-control" id="uniform" name="uniform" placeholder="Uniform" maxlength="50" value="{{ is_null(old('uniform'))?isset($employee->statutory)?$employee->statutory->uniform:'':(old('uniform')>0?old('uniform'):'') }}">
				  </div>
				</div><!-- end: .col-md-3 -->
			</div><!-- end: .row -->
	</div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-primary -->
<hr>
<div class="row" style="margin-bottom: 50px;">
	<div class="col-md-12">
		<input type="hidden" name="_type" value="personal">
		<input type="hidden" name="id" value="{{ $employee->id }}">
		@if(request()->has('raw') && request()->input('raw')=='true')
			<input type="hidden" name="_raw" value="true">
		@endif
		<button type="submit" name="_submit" value="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
		<button type="submit" name="_submit" value="next" class="btn btn-success" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save & Next</button>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}?tab=personal" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> <span class="hidden-xs hidden-sm">Cancel</span></a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/family" class="btn btn-default pull-right" data-toggle="loader"><span class="gly gly-forward"></span> <span class="hidden-xs hidden-sm">Skip</span></a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/employment" class="btn btn-default pull-right" data-toggle="loader" style="margin-right: 5px;">
			<span class="gly gly-rewind"></span> 
			<span class="hidden-xs hidden-sm">Back</span>
		</a>
	</div>
</div>
</form>

@endsection

