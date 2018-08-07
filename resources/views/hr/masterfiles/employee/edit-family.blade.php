@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Employee Update Family Info')

@section('body-class', 'employee-update-family')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header page-header-wizard">Update Employee Record</h3>
	</div>
	<div class="col-md-12">
		<ul class='nav nav-wizard'>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit" title="General Info"><span class="gly gly-user"></span> <span class="hidden-xs hidden-sm">General</span></a></li>
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/employment" title="Employment Info"><span class="gly gly-folder-closed"></span> <span class="hidden-xs hidden-sm">Employment</span></a></li>
			<li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/personal" title="Personal Info"><span class="gly gly-nameplate-alt"></span> <span class="hidden-xs hidden-sm">Personal</span></a></li>
		  <li class='active'><a href="javascript:void(0)" data-toggle="tab" title="Family Info"><span class="gly gly-group"></span> <span class="hidden-xs hidden-sm">Family</span></a></li>
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
		<div class="panel-heading">Spouse</div>
	  <div class="panel-body">
	  	<div class="row">
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'spouse.lastname'])">
				   	<label for="spouse[lastname]" class="control-label">Lastname</label>
					 	<input type="text" class="form-control" id="spouse[lastname]" name="spouse[lastname]" placeholder="Lastname" maxlength="30" value="{{ is_null(old('spouse.lastname'))?isset($employee->spouse)?$employee->spouse->lastname:'':old('spouse.lastname') }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'spouse.firstname'])">
				   	<label for="spouse[firstname]" class="control-label">Firstname</label>
					 	<input type="text" class="form-control" id="spouse[firstname]" name="spouse[firstname]" placeholder="Firstname" maxlength="30" value="{{ is_null(old('spouse.firstname'))?isset($employee->spouse)?$employee->spouse->firstname:'':old('spouse.firstname') }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'spouse.middlename'])">
				   	<label for="spouse[middlename]" class="control-label">Middlename</label>
					 	<input type="text" class="form-control mname-mother" id="spouse[middlename]" name="spouse[middlename]" placeholder="Middlename" maxlength="30" value="{{ is_null(old('spouse.middlename'))?isset($employee->spouse)?$employee->spouse->middlename:'':old('spouse.middlename') }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'spouse.birthdate'])">
				    <label for="spouse[birthdate]" class="control-label">Birthday</label>
				    <div class="input-group datepicker">
				    	<input type="text" class="form-control" id="spouse[birthdate]" name="spouse[birthdate]" placeholder="YYYY-MM-DD" maxlength="10" value="{{ is_null(old('spouse.birthdate'))?isset($employee->spouse)?$employee->spouse->getBirthdate():'':old('spouse.birthdate') }}" data-mask="0000-00-00">
				    	<div class="input-group-addon">
					        <span class="glyphicon glyphicon-calendar"></span>
					    </div>
					  </div>
				  </div>
				</div>
				<div class="col-md-6">
					<div class="form-group @include('_partials.input-error', ['field'=>'spouse.address'])">
				    <label for="spouse[address]" class="control-label">Address</label>
				    <textarea class="form-control" id="spouse[address]" name="spouse[address]" placeholder="Address" maxlength="120" style="max-width: 100%; min-width: 100%;" rows="5">{{ is_null(old('spouse.address'))?isset($employee->spouse)?$employee->spouse->address:'':old('spouse.address') }}</textarea>
				  </div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'spouse.mobile'])">
				   	<label for="spouse[mobile" class="control-label">Mobile</label>
					 	<input type="text" class="form-control" id="spouse[mobile]" name="spouse[mobile]" placeholder="0000-0000000" data-mask="0000-0000000" maxlength="20" value="{{ is_null(old('spouse.mobile'))?isset($employee->spouse)?$employee->spouse->mobile:'':old('spouse.mobile') }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'spouse.phone'])">
				   	<label for="spouse[phone]" class="control-label">Phone</label>
					 	<input type="text" class="form-control" id="spouse[phone]" name="spouse[phone]" placeholder="(00) 000-0000" data-mask="(00) 000-0000" maxlength="20" value="{{ is_null(old('spouse.phone'))?isset($employee->spouse)?$employee->spouse->phone:'':old('spouse.phone') }}">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group @include('_partials.input-error', ['field'=>'spouse.email'])">
				   	<label for="spouse[email]" class="control-label">Email</label>
					 	<input type="text" class="form-control" id="spouse[email]" name="spouse[email]" placeholder="Email" maxlength="80" value="{{ is_null(old('spouse.email'))?isset($employee->spouse)?$employee->spouse->email:'':old('spouse.email') }}">
					</div>
				</div>
					 	<input type="hidden" id="spouse[id]" name="spouse[id]" value="{{ isset($employee->spouse)?$employee->spouse->id:'' }}">
			</div><!-- end: .row -->
	  </div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-default -->
	
	<div class="panel panel-primary">
		<div class="panel-heading">Children</div>
	  <div class="panel-body">
	  	<div class="input_fields_wrap">
	  		@if(count($employee->childrens)>0)
	  			@foreach($employee->childrens as $key => $c)
	  	<div class="list-group">
					<a class="list-group-item" tabindex="-10">
			    <div class="row">
	  				<div class="col-md-3">
							<div class="form-group @include('_partials.input-error', ['field'=>'children.'.($key+1).'.lastname'])">
						    <label for="children[{{($key+1)}}][lastname]" class="control-label">{{ ($key+1) }}. Lastname</label>
						    <input type="text" class="form-control" id="children[{{($key+1)}}][lastname]" name="children[{{($key+1)}}][lastname]" maxlength="30" value="{{ is_null(old('children.'.($key+1).'.lastname'))?$c->lastname:old('children.'.($key+1).'.lastname') }}">
						  </div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-5">
							<div class="form-group @include('_partials.input-error', ['field'=>'children.'.($key+1).'.firstname'])">
						    <label for="children[{{($key+1)}}][firstname]" class="control-label">Firstname</label>
						    <input type="text" class="form-control" id="children[{{($key+1)}}][firstname]" name="children[{{($key+1)}}][firstname]" maxlength="30" value="{{ is_null(old('children.'.($key+1).'.firstname'))?$c->firstname:old('children.'.($key+1).'.firstname') }}">
						  </div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-3">
							<div class="form-group @include('_partials.input-error', ['field'=>'children.'.($key+1).'.middlename'])">
						    <label for="children[{{($key+1)}}][middlename]" class="control-label">Middlename</label>
						    <input type="text" class="form-control" id="children[{{($key+1)}}][middlename]" name="children[{{($key+1)}}][middlename]" maxlength="30" value="{{ is_null(old('children.'.($key+1).'.middlename'))?$c->middlename:old('children.'.($key+1).'.middlename') }}">
						  </div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-1">
							<label style="height: 20px; width: 40px;">&nbsp;</label>
							<button type="button" class="btn btn-default rmv" tabindex="-10" data-remove="false" data-table="children" data-id="{{ $c->id }}" data-parentid="{{ $employee->id }}" title="Delete from database"><i class="fa fa-trash" aria-hidden="true" style="color: #d44950;"></i></button>
						</div><!-- end: .col-md-1 -->
						<div class="col-md-3">
							<div class="form-group @include('_partials.input-error', ['field'=>'children.'.($key+1).'.birthdate'])">
						    <label for="children[{{($key+1)}}][birthdate]" class="control-label">Birthday</label>
						    <div class="input-group datepicker">
						    	<input type="text" class="form-control" id="children[{{($key+1)}}][birthdate]" name="children[{{($key+1)}}][birthdate]" placeholder="YYYY-MM-DD" maxlength="10" value="{{ is_null(old('children.'.($key+1).'.birthdate'))?$c->getBirthdate():old('children.'.($key+1).'.birthdate') }}" data-mask="0000-00-00">
						    	<div class="input-group-addon">
					        <span class="glyphicon glyphicon-calendar"></span>
					    </div>
					  </div>
						  </div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-3">
							<div class="form-group @include('_partials.input-error', ['field'=>'children.'.($key+1).'.gender'])">
								<label for="children[{{($key+1)}}][gender]" class="control-label">Gender</label>
								<select class="selectpicker form-control show-tick" name="children[{{($key+1)}}][gender]" id="children[{{($key+1)}}][gender]" data-live-search="true" data-size="10" data-gender="{{ $c->gender }}">
									@if($c->gender==0)
										<option disabled selected>Select Gender</option>
									@endif
									@foreach(['MALE', 'FEMALE'] as $kg => $g)
								  	<option value="{{ ($kg+1) }}" <?=(!is_null(old('children.'.($key+1).'.gender'))&&old('children.'.($key+1).'.gender')==($kg+1))?'selected':isset($c->gender)&&(($kg+1)==$c->gender)?'selected':'';?> data-tokens="{{ $g }}">
								  		{{ $g }}
								  	</option>
								  @endforeach
								</select>
							</div>	
						</div><!-- end: .col-md-3 -->
						<div class="col-md-4">
							<div class="form-group @include('_partials.input-error', ['field'=>'children.'.($key+1).'.acadlvlid'])"">
								<label for="children[{{($key+1)}}][acadlvlid]" class="control-label">Education</label>
								@if(count($acadlevels)>0)
								<select class="selectpicker form-control show-tick" name="children[{{($key+1)}}][acadlvlid]" id="children[{{($key+1)}}][acadlvlid]" data-live-search="true" data-size="10"  data-acadlvlid="{{ $c->acadlvlid }}">
									@if(!isset($c->acadlevel->id))
										<option disabled selected>Select Education</option>
									@endif
									@foreach($acadlevels as $acadlvl)
								  	<option value="{{$acadlvl->id}}" <?=(!is_null(old('children.'.($key+1).'.acadlvlid'))&&old('children.'.($key+1).'.acadlvlid')==$acadlvl->id)?'selected':isset($c->acadlevel->id)&&($acadlvl->id==$c->acadlvlid)?'selected':'';?> data-tokens="{{ $acadlvl->code }} {{ $acadlvl->descriptor }}">
								  		{{ $acadlvl->code }} - {{ $acadlvl->descriptor }}
								  	</option>
								  @endforeach
								</select>
								@endif
							</div>
						</div><!-- end:.col-md-3 -->
						<input type="hidden" id="children[{{($key+1)}}][id]" name="children[{{($key+1)}}][id]" value="{{ $c->id }}">
					</div><!-- end: .row -->
			  	</a>
			</div><!-- end: .list-group -->
	  		
					@endforeach
	  		@endif
	  	</div>
			<a class="add_field_button" href="javascript:void(0)" style="font-size: smaller;">Add Child</a>
		</div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-default -->
	
	<div class="panel panel-primary">
		<div class="panel-heading">Emergency Contact</div>
	  <div class="panel-body">
	  	<div class="row">
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecperson.lastname'])">
				   	<label for="ecperson[lastname]" class="control-label">Lastname</label>
					 	<input type="text" class="form-control" id="ecperson[lastname]" name="ecperson[lastname]" placeholder="Lastname" maxlength="30" value="{{  is_null(old('ecperson.lastname'))?isset($employee->ecperson)?$employee->ecperson->lastname:'':old('ecperson.lastname') }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecperson.firstname'])">
				   	<label for="ecperson[firstname]" class="control-label">Firstname</label>
					 	<input type="text" class="form-control" id="ecperson[firstname]" name="ecperson[firstname]" placeholder="Firstname" maxlength="30" value="{{ is_null(old('ecperson.firstname'))?isset($employee->ecperson)?$employee->ecperson->firstname:'':old('ecperson.firstname') }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecperson.middlename'])">
				   	<label for="ecperson[middlename]" class="control-label">Middlename</label>
					 	<input type="text" class="form-control" id="ecperson[middlename]" name="ecperson[middlename]" placeholder="Middlename" maxlength="30" value="{{ is_null(old('ecperson.middlename'))?isset($employee->ecperson)?$employee->ecperson->middlename:'':old('ecperson.middlename') }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecperson.relation'])">
				   	<label for="ecperson[relation]" class="control-label">Relationship</label>
					 	<input type="text" class="form-control" id="ecperson[relation]" name="ecperson[relation]" placeholder="Relation" maxlength="50" value="{{ is_null(old('ecperson.relation'))?isset($employee->ecperson)?$employee->ecperson->relation:'':old('ecperson.relation') }}">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecperson.address'])">
				    <label for="ecperson[address]" class="control-label">Address</label>
				    <textarea class="form-control" id="ecperson[address]" name="ecperson[address]" placeholder="Address" maxlength="120" style="max-width: 100%; min-width: 100%;" rows="5">{{ is_null(old('ecperson.address'))?isset($employee->ecperson)?$employee->ecperson->address:'':old('ecperson.address') }}</textarea>
				  </div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecperson.mobile'])">
				   	<label for="ecperson[mobile" class="control-label">Mobile</label>
					 	<input type="text" class="form-control" id="ecperson[mobile]" name="ecperson[mobile]" placeholder="0000-0000000" data-mask="0000-0000000" maxlength="20" value="{{ is_null(old('ecperson.mobile'))?isset($employee->ecperson)?$employee->ecperson->mobile:'':old('ecperson.mobile') }}">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecperson.phone'])">
				   	<label for="ecperson[phone]" class="control-label">Phone</label>
					 	<input type="text" class="form-control" id="ecperson[phone]" name="ecperson[phone]" placeholder="(00) 000-0000" data-mask="(00) 000-0000" maxlength="20" value="{{ is_null(old('ecperson.phone'))?isset($employee->ecperson)?$employee->ecperson->phone:'':old('ecperson.phone') }}">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group @include('_partials.input-error', ['field'=>'ecperson.email'])">
				   	<label for="ecperson[email]" class="control-label">Email</label>
					 	<input type="text" class="form-control" id="ecperson[email]" name="ecperson[email]" placeholder="Email" maxlength="80" value="{{ is_null(old('ecperson.email'))?isset($employee->ecperson)?$employee->ecperson->email:'':old('ecperson.email') }}">
					</div>
				</div>
					 	<input type="hidden" id="ecperson[id]" name="ecperson[id]" value="{{ isset($employee->ecperson)?$employee->ecperson->id:'' }}">
			</div><!-- end: .row -->
	  </div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-default -->
<hr>
<div class="row" style="margin-bottom: 50px;">
	<div class="col-md-12">
		<input type="hidden" name="_type" value="family">
		<input type="hidden" name="id" value="{{ $employee->id }}">
		@if(request()->has('raw') && request()->input('raw')=='true')
			<input type="hidden" name="_raw" value="true">
		@endif
		<button type="submit" name="_submit" value="submit" class="btn btn-primary" data-toggle="loaderX"><span class="gly gly-floppy-saved" data-toggle="loaderX"></span> Save</button>
		<button type="submit" name="_submit" value="next" class="btn btn-success" data-toggle="loaderX"><span class="gly gly-floppy-saved" data-toggle="loaderX"></span> Save & Next</button>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}?tab=family" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> <span class="hidden-xs hidden-sm">Cancel</span></a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/workedu" class="btn btn-default pull-right" data-toggle="loader"><span class="gly gly-forward"></span> <span class="hidden-xs hidden-sm">Skip</span></a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/personal" class="btn btn-default pull-right" data-toggle="loader" style="margin-right: 5px;">
			<span class="gly gly-rewind"></span> 
			<span class="hidden-xs hidden-sm">Back</span>
		</a>
	</div>
</div>
</form>


<div class="modal fade" id="mdl-delete-child" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete Record</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this record?</p>
      </div>
      <div class="modal-footer">
      	<form action="/hr/masterfiles/employee/child" method="POST">
      		{{ csrf_field() }}
      		<input type="hidden" name="_method" value="DELETE">
      		<input type="hidden" name="id" id="child-id">
      		<input type="hidden" name="table" id="child-table">
      		<input type="hidden" name="employeeid" id="parent-id">
        <button type="submit" class="btn btn-primary">Yes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      	</form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection



@section('js-external')
  @parent

<script type="text/javascript">
var add_child = function(x, idx) {
	var html = '<div class="list-group" id="'+x+'">';
			html += '<a class="list-group-item" tabindex="-10">';
			    html += '<div class="row">';
	  				html += '<div class="col-md-3">';
							html += '<div class="form-group">';
						    html += '<label for="children['+idx+'][lastname]" class="control-label lbl-lname">'+idx+'. Lastname</label>';
						    html += '<input type="text" class="form-control" id="children['+idx+'][lastname]" name="children['+idx+'][lastname]" value="{{ $employee->lastname }}" maxlength="30" required>';
						  html += '</div>';
						html += '</div><!-- end: .col-md-3 -->';
						html += '<div class="col-md-5">';
							html += '<div class="form-group">';
						    html += '<label for="children['+idx+'][firstname]" class="control-label">Firstname</label>';
						    html += '<input type="text" class="form-control fname-'+x+'" id="children.'+idx+'.firstname" name="children['+idx+'][firstname]" maxlength="30" required>';
						  html += '</div>';
						html += '</div><!-- end: .col-md-3 -->';
						html += '<div class="col-md-3">';
							html += '<div class="form-group">';
						    html += '<label for="children['+idx+'][middlename]" class="control-label">Middlename</label>';
						    html += '<input type="text" class="form-control mname-'+x+'" id="children['+idx+'][middlename]" name="children['+idx+'][middlename]" maxlength="30" >';
						  html += '</div>';
						html += '</div><!-- end: .col-md-3 -->';
						html += '<div class="col-md-1">';
							html += '<label style="height: 20px; width: 40px;">&nbsp;</label>';
							html += '<button type="button" class="btn btn-default rmv" tabindex="-10" data-remove="true" data-id="'+x+'" data-idx="'+idx+'" title="Quick delete"><i class="fa fa-minus-circle" aria-hidden="true" style="color: #d44950;"></i></button>';
						html += '</div><!-- end: .col-md-1 -->';
						html += '<div class="col-md-3">';
							html += '<div class="form-group">';
						    html += '<label for="children['+idx+'][birthdate]" class="control-label">Birthday</label>';
						    html += '<div class="input-group datepicker">';
						    html += '<input type="text" class="form-control" id="children['+idx+'][birthdate]" name="children['+idx+'][birthdate]" placeholder="YYYY-MM-DD" maxlength="10" data-mask="0000-00-00">';
						    html += '<div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div></div>';
						  html += '</div>';
						html += '</div><!-- end: .col-md-3 -->';
						html += '<div class="col-md-3">';
							html += '<div class="form-group">';
								html += '<label for="children['+idx+'][gender]" class="control-label">Gender</label>';
								html += '<select class="selectpickerx form-control show-tick" name="children['+idx+'][gender]" id="children['+idx+'][gender]" data-live-search="true" data-size="10">';
										html += '<option disabled selected>Select Gender</option>';
									@foreach(['MALE', 'FEMALE'] as $kg => $g)
								  	html += '<option value="{{ ($kg+1) }}" data-tokens="{{ $g }}">';
								  		html += '{{ $g }}';
								  	html += '</option>';';';
								  @endforeach
								html += '</select>';
							html += '</div>	';
						html += '</div><!-- end: .col-md-3 -->';
						html += '<div class="col-md-4">';
							html += '<div class="form-group">';
								html += '<label for="children['+idx+'][acadlvlid]" class="control-label">Education</label>';
								@if(count($acadlevels)>0)
								html += '<select class="selectpickerx form-control show-tick" name="children['+idx+'][acadlvlid]" id="children['+idx+'][acadlvlid]" data-live-search="true" data-size="10">';
								
										html += '<option disabled selected>Select Education</option>';
									
									@foreach($acadlevels as $acadlvl)
								  	html += '<option value="{{$acadlvl->id}}" data-tokens="{{ $acadlvl->code }} {{ $acadlvl->descriptor }}">';
								  		html += '{{ $acadlvl->code }} - {{ $acadlvl->descriptor }}';
								  	html += '</option>';
								  @endforeach
								html += '</select>';
								@endif
							html += '</div>';
						html += '</div><!-- end:.col-md-3 -->';
						/*
						html += '<input type="hidden" id="children['+idx+'][id]" name="children['+idx+'][id]">';
						*/
					html += '</div><!-- end: .row -->';
			  	html += '</a>';
			html += '</div><!-- end: .list-group -->';
	return html;
}


$(document).ready(function() {

    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    var x = <?=(count($employee->childrens)+1)?>; //initlal text box count
    //var x = 1; //initlal text box count
    
    add_button.click(function(e){ //on add input button click
      e.preventDefault();
      if(x <= max_fields){ //max input box allowed
      	var uniqid = Date.now();
      	//console.log(uniqid);
        wrapper.append(add_child(uniqid, x)); //add input box
        $('.datepicker').datetimepicker({format: 'YYYY-MM-DD', ignoreReadonly: true});
        $('.fname-'+uniqid).focus();
        $('.mname-'+uniqid).val($('.mname-mother').val());
        x++; //text box increment
      } 
      if (x <= max_fields) {

      }	else {
      	$('.add_field_button').hide();
      }
    });

    @if(count($employee->childrens)==10)
    	$('.add_field_button').hide();
    @endif

    wrapper.on('click', '.rmv', function(e){
      e.preventDefault();
      var remove = $(this).data('remove');
    
      if (remove) {
      	$('#'+$(this).data('id')).remove();
      	x--;
      	if (x <= max_fields) {
      		$('.add_field_button').show();
	      }

	      wrapper.children('.list-group').each(function(idx){
	      	if ($('.control-label', this).hasClass('lbl-lname')) {
      			var x = (idx+1);
      			$('.lbl-lname', this).text(x+' Lastname');
	      	}
	      });
      }  

      if (remove==false) {
      	$('#child-id').val($(this).data('id'));
      	$('#child-table').val($(this).data('table'));
      	$('#parent-id').val($(this).data('parentid'));
      	$('#mdl-delete-child').modal('show');
      }

    });
});




</script>
@endsection

