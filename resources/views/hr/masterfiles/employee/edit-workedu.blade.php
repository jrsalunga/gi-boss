@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Employee Update Work & Education Info')

@section('body-class', 'employee-update-workedu')

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
		  <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/family" title="Family Info"><span class="gly gly-group"></span> <span class="hidden-xs hidden-sm">Family</span></a></li>
		  <li class='active'><a href="javascript:void(0)" data-toggle="tab" title="Work & Education Info"><span class="gly gly-certificate"></span> <span class="hidden-xs hidden-sm">Work & Education</span></a></li>
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
		<div class="panel-heading">Work Experinces</div>
	  <div class="panel-body">
	  	<div class="w_input_fields_wrap">
	  		@if(count($employee->workexps)>0)
	  			@foreach($employee->workexps as $key => $w)
	  	<div class="list-group">
					<a class="list-group-item" tabindex="-10">
			    <div class="row">
	  				
						<div class="col-md-2">
							<div class="form-group @include('_partials.input-error', ['field'=>'workexp.'.($key+1).'.periodfrom'])">
						    <label for="workexp[{{($key+1)}}][periodfrom]" class="control-label">From</label>
						    <div class="input-group monyr">
						    	<input type="text" class="form-control" id="workexp[{{($key+1)}}][periodfrom]" name="workexp[{{($key+1)}}][periodfrom]" placeholder="YYYY-MM" maxlength="7" value="{{ $w->getPeriodFrom()?$w->getPeriodFrom()->format('Y-m'):'' }}" data-mask="0000-00">
						    	<div class="input-group-addon">
						        <span class="glyphicon glyphicon-calendar"></span>
						    	</div>
						  	</div>
						  </div>
						</div><!-- end: .col-md-2 -->
						<div class="col-md-2">
							<div class="form-group @include('_partials.input-error', ['field'=>'workexp.'.($key+1).'.periodto'])">
						    <label for="workexp[{{($key+1)}}][periodto]" class="control-label">To</label>
						    <div class="input-group monyr">
						    	<input type="text" class="form-control monyr" id="workexp[{{($key+1)}}][periodto]" name="workexp[{{($key+1)}}][periodto]" placeholder="YYYY-MM" maxlength="7" value="{{ $w->getPeriodTo()?$w->getPeriodTo()->format('Y-m'):'' }}" data-mask="0000-00">
						    	<div class="input-group-addon">
						        <span class="glyphicon glyphicon-calendar"></span>
						    	</div>
						  	</div>
						  </div>
						</div><!-- end: .col-md-2 -->
						<div class="col-md-5">
							<div class="form-group @include('_partials.input-error', ['field'=>'workexp.'.($key+1).'.company'])">
						   	<label for="workexp[{{($key+1)}}][company]" class="control-label">Company</label>
							 	<input type="text" class="form-control" id="workexp[{{($key+1)}}][company]" name="workexp[{{($key+1)}}][company]" placeholder="Company" maxlength="50" value="{{ $w->company }}">
							</div>
						</div><!-- end: .col-md-4 -->
						<div class="col-md-3">
							<div class="form-group @include('_partials.input-error', ['field'=>'workexp.'.($key+1).'.position'])">
						   	<label for="workexp[{{($key+1)}}][position]" class="control-label">Position</label>
							 	<input type="text" class="form-control" id="workexp[{{($key+1)}}][position]" name="workexp[{{($key+1)}}][position]" placeholder="Position" maxlength="50" value="{{ $w->position }}">
							</div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-11">
							<div class="form-group @include('_partials.input-error', ['field'=>'workexp.'.($key+1).'.remarks'])">
						    <label for="workexp[{{($key+1)}}][remarks]" class="control-label">Remarks</label>
						    <input type="text" class="form-control" id="workexp[{{($key+1)}}][remarks]" name="workexp[{{($key+1)}}][remarks]" placeholder="Remarks" maxlength="150" value="{{ $w->remarks }}">
						  </div>
						</div><!-- end: .col-md-2 -->
						<div class="col-md-1">
							<label style="height: 20px; width: 40px;">&nbsp;</label>
							<button type="button" class="btn btn-default rmv-w" tabindex="-10" data-remove="false" data-id="{{ $w->id }}" data-table="workexp" data-parentid="{{ $employee->id }}" title="Delete from database"><i class="fa fa-trash" aria-hidden="true" style="color: #d44950;"></i></button>
						</div><!-- end: .col-md-1 -->
						<input type="hidden" id="workexp[{{($key+1)}}][id]" name="workexp[{{($key+1)}}][id]" value="{{ $w->id }}">
					</div><!-- end: .row -->
			  	</a>
			</div><!-- end: .list-group -->
	  		
					@endforeach
	  		@endif
	  	</div>
			<a class="w_add_field_button" href="javascript:void(0)" style="font-size: smaller;">Add Work Experince</a>
		</div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-default -->

	<div class="panel panel-primary">
		<div class="panel-heading">Education</div>
	  <div class="panel-body">
	  	<div class="e_input_fields_wrap">
	  		@if(count($employee->educations)>0)
	  			@foreach($employee->educations as $key => $e)
	  	<div class="list-group">
					<a class="list-group-item" tabindex="-10">
			    <div class="row">
	  				
						<div class="col-md-2">
							<div class="form-group @include('_partials.input-error', ['field'=>'education.'.($key+1).'.periodfrom'])">
						    <label for="education[{{($key+1)}}][periodfrom]" class="control-label">From</label>
						    <input type="text" class="form-control monyr" id="education[{{($key+1)}}][periodfrom]" name="education[{{($key+1)}}][periodfrom]" placeholder="YYYY-MM" maxlength="7" value="{{ $e->getPeriodFrom()?$e->getPeriodFrom()->format('Y-m'):'' }}" readonly>
						  </div>
						</div><!-- end: .col-md-2 -->
						<div class="col-md-2">
							<div class="form-group @include('_partials.input-error', ['field'=>'education.'.($key+1).'.periodto'])">
						    <label for="education[{{($key+1)}}][periodto]" class="control-label">To</label>
						    <input type="text" class="form-control monyr" id="education[{{($key+1)}}][periodto]" name="education[{{($key+1)}}][periodto]" placeholder="YYYY-MM" maxlength="7" value="{{ $e->getPeriodTo()?$e->getPeriodTo()->format('Y-m'):'' }}" readonly>
						  </div>
						</div><!-- end: .col-md-2 -->
						<div class="col-md-3">
							<div class="form-group @include('_partials.input-error', ['field'=>'education.'.($key+1).'.acadlvlid'])"">
								<label for="education[{{($key+1)}}][acadlvlid]" class="control-label">Level</label>
								@if(count($acadlevels)>0)
								<select class="selectpicker form-control show-tick" name="education[{{($key+1)}}][acadlvlid]" id="education[{{($key+1)}}][acadlvlid]" data-live-search="true" data-size="10"  data-acadlvlid="{{ $e->acadlvlid }}">
									@if(!isset($e->acadlevel->id))
										<option disabled selected>Select Level</option>
									@endif
									@foreach($acadlevels as $acadlvl)
								  	<option value="{{$acadlvl->id}}" <?=(!is_null(old('education.'.($key+1).'.acadlvlid'))&&old('education.'.($key+1).'.acadlvlid')==$acadlvl->id)?'selected':isset($e->acadlevel->id)&&($acadlvl->id==$e->acadlvlid)?'selected':'';?> data-tokens="{{ $acadlvl->code }} {{ $acadlvl->descriptor }}">
								  		{{ $acadlvl->code }} - {{ $acadlvl->descriptor }}
								  	</option>
								  @endforeach
								</select>
								@endif
							</div>
						</div><!-- end:.col-md-3 -->
						<div class="col-md-5">
							<div class="form-group @include('_partials.input-error', ['field'=>'education.'.($key+1).'.school'])">
						   	<label for="education[{{($key+1)}}][school]" class="control-label">School</label>
							 	<input type="text" class="form-control" id="education[{{($key+1)}}][school]" name="education[{{($key+1)}}][school]" placeholder="School" maxlength="50" value="{{ $e->school }}">
							</div>
						</div><!-- end: .col-md-4 -->
						<div class="col-md-5">
							<div class="form-group @include('_partials.input-error', ['field'=>'education.'.($key+1).'.course'])">
						   	<label for="education[{{($key+1)}}][course]" class="control-label">Course</label>
							 	<input type="text" class="form-control" id="education[{{($key+1)}}][course]" name="education[{{($key+1)}}][course]" placeholder="Course" maxlength="50" value="{{ $e->course }}">
							</div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-6">
							<div class="form-group @include('_partials.input-error', ['field'=>'education.'.($key+1).'.remarks'])">
						    <label for="education[{{($key+1)}}][remarks]" class="control-label">Remarks</label>
						    <input type="text" class="form-control" id="education[{{($key+1)}}][remarks]" name="education[{{($key+1)}}][remarks]" placeholder="Remarks" maxlength="150" value="{{ $e->remarks }}">
						  </div>
						</div><!-- end: .col-md-2 -->
						<div class="col-md-1">
							<label style="height: 20px; width: 40px;">&nbsp;</label>
							<button type="button" class="btn btn-default rmv-e" tabindex="-10" data-remove="false" data-id="{{ $e->id }}" data-table="education" data-parentid="{{ $employee->id }}" title="Delete from database"><i class="fa fa-trash" aria-hidden="true" style="color: #d44950;"></i></button>
						</div><!-- end: .col-md-1 -->
						<input type="hidden" id="education[{{($key+1)}}][id]" name="education[{{($key+1)}}][id]" value="{{ $e->id }}">
					</div><!-- end: .row -->
			  	</a>
			</div><!-- end: .list-group -->
	  		
					@endforeach
	  		@endif
	  	</div><!-- end: .e_input_fields_wrap -->
			<a class="e_add_field_button" href="javascript:void(0)" style="font-size: smaller;">Add Education</a>
		</div><!-- end: .panel-body -->
	</div><!-- end: .panel.panel-default -->
	
	
<hr>
<div class="row" style="margin-bottom: 50px;">
	<div class="col-md-12">
		<input type="hidden" name="_type" value="workedu">
		<input type="hidden" name="id" value="{{ $employee->id }}">
		@if(request()->has('raw') && request()->input('raw')=='true')
			<input type="hidden" name="_raw" value="true">
		@endif
		<button type="submit" name="_submit" value="submit" class="btn btn-primary" data-toggle="loaderX"><span class="gly gly-floppy-saved" data-toggle="loaderX"></span> Save</button>
		<button type="submit" name="_submit" value="next" class="btn btn-success" data-toggle="loaderX"><span class="gly gly-floppy-saved" data-toggle="loaderX"></span> Save & Next</button>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}?tab=workedu" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> <span class="hidden-xs hidden-sm">Cancel</span></a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/confirm" class="btn btn-default pull-right" data-toggle="loader"><span class="gly gly-forward"></span> <span class="hidden-xs hidden-sm">Skip</span></a>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/family" class="btn btn-default pull-right" data-toggle="loader" style="margin-right: 5px;">
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
var add_work = function(x, idx) {
	var html = '<div class="list-group" id="'+x+'">';
			html += '<a class="list-group-item" tabindex="-10">';
			     html += '<div class="row">';
						 html += '<div class="col-md-2">';
						 html += '<div class="form-group">';
						    html += '<label for="workexp['+idx+'][periodfrom]" class="control-label">From</label>';
						    html += '<input type="text" class="form-control monyr" id="workexp['+idx+'][periodfrom]" name="workexp['+idx+'][periodfrom]" placeholder="YYYY-MM" maxlength="7" readonly>';
						  html += '</div>';
						html += '</div><!-- end: .col-md-2 -->';
						html += '<div class="col-md-2">';
							html += '<div class="form-group">';
						    html += '<label for="workexp['+idx+'][periodto]" class="control-label">To</label>';
						    html += '<input type="text" class="form-control monyr" id="workexp['+idx+'][periodto]" name="workexp['+idx+'][periodto]" placeholder="YYYY-MM" maxlength="7" readonly>';
						  html += '</div>';
						html += '</div><!-- end: .col-md-2 -->';
						html += '<div class="col-md-4">';
							html += '<div class="form-group">';
						   	html += '<label for="workexp['+idx+'][company]" class="control-label">Company</label>';
							 	html += '<input type="text" class="form-control" id="workexp['+idx+'][company]" name="workexp['+idx+'][company]" placeholder="Company" maxlength="50" required>';
							html += '</div>';
						html += '</div><!-- end: .col-md-4 -->';
						html += '<div class="col-md-3">';
							html += '<div class="form-group">';
						   	html += '<label for="workexp['+idx+'][position]" class="control-label">Position</label>';
							 	html += '<input type="text" class="form-control" id="workexp['+idx+'][position]" name="workexp['+idx+'][position]" placeholder="Position" maxlength="50" required>';
							html += '</div>';
						html += '</div><!-- end: .col-md-3 -->';
						html += '<div class="col-md-11">';
							html += '<div class="form-group">';
						    html += '<label for="workexp['+idx+'][remarks]" class="control-label">Remarks</label>';
						    html += '<input type="text" class="form-control" id="workexp['+idx+'][remarks]" name="workexp['+idx+'][remarks]" placeholder="Remarks" maxlength="150">';
						  html += '</div>';
						html += '</div><!-- end: .col-md-2 -->';
						html += '<div class="col-md-1">';
							html += '<label style="height: 20px; width: 40px;">&nbsp;</label>';
							html += '<button type="button" class="btn btn-default rmv-w" tabindex="-10" data-remove="true" data-id="'+x+'" data-table="workexp" data-parentid="{{ $employee->id }}" title="Quick delete"><i class="fa fa-minus-circle" aria-hidden="true" style="color: #d44950;"></i></button>';
						html += '</div><!-- end: .col-md-1 -->';
					html += '</div><!-- end: .row -->';
			  	 html += '</a>';
			 html += '</div><!-- end: .list-group -->';
			
	return html;
}


var add_edu = function(x, idx) {
	var html = '<div class="list-group" id="'+x+'">';
		html += '<a class="list-group-item" tabindex="-10">';
			    html += '<div class="row">';
						html += '<div class="col-md-2">';
							html += '<div class="form-group">';
						    html += '<label for="education['+idx+'][periodfrom]" class="control-label">From</label>';
						    html += '<input type="text" class="form-control monyr" id="education['+idx+'][periodfrom]" name="education['+idx+'][periodfrom]" placeholder="YYYY-MM" maxlength="7" readonly>';
						  html += '</div>';
						html += '</div><!-- end: .col-md-2 -->';
						html += '<div class="col-md-2">';
							html += '<div class="form-group">';
						    html += '<label for="education['+idx+'][periodto]" class="control-label">To</label>';
						    html += '<input type="text" class="form-control monyr" id="education['+idx+'][periodto]" name="education['+idx+'][periodto]" placeholder="YYYY-MM" maxlength="7" readonly>';
						  html += '</div>';
						html += '</div><!-- end: .col-md-2 -->';
						html += '<div class="col-md-3">';
							html += '<div class="form-group">';
								html += '<label for="education['+idx+'][acadlvlid]" class="control-label">Level</label>';
								@if(count($acadlevels)>0)
								html += '<select class="selectpickerx form-control show-tick" name="education['+idx+'][acadlvlid]" id="education['+idx+'][acadlvlid]" data-live-search="true" data-size="10">';
										html += '<option disabled selected>Select Level</option>';									
									@foreach($acadlevels as $acadlvl)
								  	html += '<option value="{{$acadlvl->id}}" data-tokens="{{ $acadlvl->code }} {{ $acadlvl->descriptor }}">';
								  		html += '{{ $acadlvl->code }} - {{ $acadlvl->descriptor }}';
								  	html += '</option>';
								  @endforeach
								html += '</select>';
								@endif
							html += '</div>';
						html += '</div><!-- end:.col-md-3 -->';
						html += '<div class="col-md-5">';
							html += '<div class="form-group">';
						   	html += '<label for="education['+idx+'][school]" class="control-label">School</label>';
							 	html += '<input type="text" class="form-control" id="education['+idx+'][school]" name="education['+idx+'][school]" placeholder="School" maxlength="50">';
							html += '</div>';
						html += '</div><!-- end: .col-md-4 -->';
						html += '<div class="col-md-5">';
							html += '<div class="form-group">';
						   	html += '<label for="education['+idx+'][course]" class="control-label">Course</label>';
							 	html += '<input type="text" class="form-control" id="education['+idx+'][course]" name="education['+idx+'][course]" placeholder="Course" maxlength="50">';
							html += '</div>';
						html += '</div><!-- end: .col-md-3 -->';
						html += '<div class="col-md-6">';
							html += '<div class="form-group">';
						    html += '<label for="education['+idx+'][remarks]" class="control-label">Remarks</label>';
						    html += '<input type="text" class="form-control" id="education['+idx+'][remarks]" name="education['+idx+'][remarks]" placeholder="Remarks" maxlength="150">';
						  html += '</div>';
						html += '</div><!-- end: .col-md-2 -->';
						html += '<div class="col-md-1">';
							html += '<label style="height: 20px; width: 40px;">&nbsp;</label>';
							html += '<button type="button" class="btn btn-default rmv-e" tabindex="-10" data-remove="true" data-id="'+x+'" data-table="education" data-parentid="{{ $employee->id }}" title="Quick delete"><i class="fa fa-minus-circle" aria-hidden="true" style="color: #d44950;"></i></button>';
						html += '</div><!-- end: .col-md-1 -->';
					html += '</div><!-- end: .row -->';
			  	html += '</a>';
			html += '</div><!-- end: .list-group -->';

	return html;
}



$(document).ready(function() {

	$('.monyr').datetimepicker({
	  format: 'YYYY-MM',
	  ignoreReadonly: true
	});

  var max_fields      = 10; //maximum input boxes allowed
  var w_wrapper         = $(".w_input_fields_wrap"); //Fields wrapper
  var e_wrapper         = $(".e_input_fields_wrap"); //Fields wrapper
  var w_add_button      = $(".w_add_field_button"); //Add button ID
  var e_add_button      = $(".e_add_field_button"); //Add button ID
  var x = <?=(count($employee->workexps)+1)?>; //initlal text box count
  var y = <?=(count($employee->educations)+1)?>; //initlal text box count
  //var x = 1; //initlal text box count
  
  w_add_button.click(function(e){ //on add input button click
    e.preventDefault();
    if(x <= max_fields){ //max input box allowed
    	var uniqid = Date.now();
    	console.log(uniqid);
      w_wrapper.append(add_work(uniqid, x)); //add input box
      $('.monyr').datetimepicker({format: 'YYYY-MM', ignoreReadonly: true});
      x++; //text box increment
    } 
    	console.log(x);
    if (x <= max_fields) {

    }	else {
    	w_add_button.hide();
    }
  });

  @if(count($employee->workexps)==10)
  	w_add_button.hide();
  @endif

  w_wrapper.on('click', '.rmv-w', function(e){
    e.preventDefault();
    var remove = $(this).data('remove');
  
    if (remove) {
    	$('#'+$(this).data('id')).remove();
    	x--;
    	if (x <= max_fields) {
    		w_add_button.show();
      }
    }  

    if (remove==false) {
    	$('#child-id').val($(this).data('id'));
    	$('#parent-id').val($(this).data('parentid'));
    	$('#child-table').val($(this).data('table'));
    	$('#mdl-delete-child').modal('show');
    }
  });

  e_add_button.click(function(e){ //on add input button click
    e.preventDefault();
    if(y <= max_fields){ //max input box allowed
    	var uniqid = Date.now();
    	console.log(uniqid);
      e_wrapper.append(add_edu(uniqid, y)); //add input box
      $('.monyr').datetimepicker({format: 'YYYY-MM', ignoreReadonly: true});
      y++; //text box increment
    } 
    	console.log(y);
    if (y <= max_fields) {

    }	else {
    	e_add_button.hide();
    }
  });

  @if(count($employee->educations)==10)
  	e_add_button.hide();
  @endif

  e_wrapper.on('click', '.rmv-e', function(e){
    e.preventDefault();
    var remove = $(this).data('remove');
    console.log(e);
  
    if (remove) {
    	$('#'+$(this).data('id')).remove();
    	y--;
    	if (y <= max_fields) {
    		e_add_button.show();
      }
    }  

    if (remove==false) {
    	$('#child-id').val($(this).data('id'));
    	$('#parent-id').val($(this).data('parentid'));
    	$('#child-table').val($(this).data('table'));
    	$('#mdl-delete-child').modal('show');
    }
  });

});




</script>
@endsection

