@extends('dash')

@section('title', '- Branch Update')

@section('body-class', 'branch-update')


@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header text-success">
			<span class="gly gly-shop"></span> {{ $branch->descriptor }} <small>{{ $branch->code }}</small>
			@if(request()->has('type') && request()->input('type')==='full')
				<a href="/masterfiles/branch/{{$branch->lid()}}/edit" class="pull-right" title="Toogle few"><i class="material-icons">more_horiz</i></a>
			@else
				<a href="/masterfiles/branch/{{$branch->lid()}}/edit?type=full" class="pull-right" title="Toogle full"><i class="material-icons">more_vert</i></a>
			@endif
		</h3>

		@include('_partials.alerts')

		<form action="/masterfiles/branch" method="POST">

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
		    	<a href="#sec" aria-controls="sec" role="tab" data-toggle="tab">
		    		<i class="material-icons">map</i>
						<span class="hidden-xs hidden-sm">Sector</span>
		    	</a>
		   	</li>
		   	<li role="presentation">
		    	<a href="#oth" aria-controls="oth" role="tab" data-toggle="tab">
		    		<i class="material-icons">domain</i>
						<span class="hidden-xs hidden-sm">Other</span>
		    	</a>
		   	</li>
		  </ul>
		  <!-- Tab panes -->
		  <div class="tab-content" style="margin-top: 10px;">
		    <div role="tabpanel" class="tab-pane active" id="home">
					<div class="row">
						<div class="col-md-5">
							{{ csrf_field() }}

							@if(request()->has('type') && request()->input('type')==='full')
							<div class="form-group">
						    <label for="code">Branch Code</label>
						    <input type="text" class="form-control" id="code" name="code" placeholder="Branch Code" maxlength="3" value="{{ $branch->code }}">
						  </div>
						  <div class="form-group">
						    <label for="descriptor">Branch Name</label>
						    <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Branch Name" maxlength="50" value="{{ $branch->descriptor }}">
						  </div>
						  @endif
						  
						  <div class="form-group">
						    <label for="address">Address</label>
						    <textarea class="form-control" id="address" name="address" placeholder="Address" maxlength="120" style="max-width: 100%; min-width: 100%;" rows="5">{{ $branch->address }}</textarea>
						  </div>
	
							<div class="input_space_wrap">
							  <div class="form-group">
								  <label for="units">Unit<?=count($branch->spaces)>1?'s':''?></label>
								  @if(count($branch->spaces)<=0)
								  <div class="input-group">
							      <input type="text" class="form-control unit" id="su-0" name="space[0][unit]" maxlength="32">
								   	<div class="input-group-addon area">
								    	<input type="text" name="space[0][area]" id="sa-0" class="form-control area" data-mask="00,000.00" maxlength="9" value="0.00" data-mask-reverse="true">
								    </div>
								    <span class="input-group-addon">sqm</span>
							   	</div><!-- end:.input-group -->
							   	@else
										@foreach($branch->spaces as $k => $space)
										<div class="form-group" id="ug-{{$k}}">
											<div class="input-group">
									      <input type="text" class="form-control unit" id="su-{{$k}}" name="space[{{$k}}][unit]" maxlength="32" value="{{$space->unit}}">
										   	<div class="input-group-addon area">
										    	<input type="text" name="space[{{$k}}][area]" id="sa-{{$k}}" class="form-control area" data-mask="00,000.00" maxlength="9" value="{{number_format($space->area,2)}}" data-mask-reverse="true">
										    </div>
										    @if($k==0)
										    	<span class="input-group-addon">sqm</span>
										    @else
													<div class="input-group-addon"><a href="javascript:void(0)" tabindex="-1" title="Remove" class="rmv-area" data-parent="{{$k}}"><i class="fa fa-minus-circle" aria-hidden="true" style="color: #d44950;"></i></a></div>
										    @endif
									   	</div><!-- end:.input-group -->
									  </div>
										@endforeach
							   	@endif
							  </div><!-- end:.form-group -->
							  
							</div><!-- end:.input_space_wrap -->
						  <div class="form-group">
								<a class="add_unit" href="javascript:void(0)" style="font-size: smaller;">Add Unit</a>
							</div>
						  
						</div>
						<div class="col-md-4">
						  <div class="form-group">
						    <label for="email">Email</label>
						    <input type="text" class="form-control" id="email" name="email" placeholder="Email" maxlength="50" value="{{ $branch->email }}">
						  </div>
						  <div class="input_fields_wrap">
								<div class="form-group">
							    <label for="tin">Contact No<?=count($branch->contacts)>1?'s':''?></label>

									@if(count($branch->contacts)<=0)
							    <div class="input-group">
							      <div class="input-group-btn">
							        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							        <span id="contact-icon-0" class="gly gly-iphone"></span> <span class="caret"></span></button>
							        <ul class="dropdown-menu">
							          <li><a href="javascript:void(0)" data-contact-type="1" data-parent="0"><span class="gly gly-iphone"></span> Mobile</a></li>
							          <li><a href="javascript:void(0)" data-contact-type="2" data-parent="0"><span class="gly gly-phone-alt"></span> Telephone</a></li>
							          <li><a href="javascript:void(0)" data-contact-type="3" data-parent="0"><span class="gly gly-fax"></span> Fax</a></li>
							        </ul>
							      </div><!-- /btn-group -->
							      <input type="hidden" name="contact[0][type]" id="ct-0" value="1">
							      <input type="text" class="form-control" id="cn-0" name="contact[0][number]" data-mask="0000 000 0000" maxlength="15">
						    	</div><!-- /input-group -->
						    	@else 
										@foreach($branch->contacts as $k => $contact)
										<div class="form-group" id="fg-{{ $k }}">
											<div class="input-group">
									      <div class="input-group-btn">
									        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									        <span id="contact-icon-{{ $k }}" class="gly {{ contact_icon($contact->type) }}"></span> <span class="caret"></span></button>
									        <ul class="dropdown-menu">
									          <li><a href="javascript:void(0)" data-contact-type="1" data-parent="{{ $k }}"><span class="gly gly-iphone"></span> Mobile</a></li>
									          <li><a href="javascript:void(0)" data-contact-type="2" data-parent="{{ $k }}"><span class="gly gly-phone-alt"></span> Telephone</a></li>
									          <li><a href="javascript:void(0)" data-contact-type="3" data-parent="{{ $k }}"><span class="gly gly-fax"></span> Fax</a></li>
									        </ul>
									      </div><!-- /btn-group -->
									      <input type="hidden" name="contact[{{ $k }}][type]" id="ct-{{ $k }}" value="{{ $contact->type }}">
									      <input type="text" class="form-control" id="cn-{{ $k }}" name="contact[{{ $k }}][number]" value="{{ $contact->number }}" {!! jquery_mask($contact->type) !!} >
									      @if($k>=1)
													<div class="input-group-addon"><a href="javascript:void(0)" tabindex="-1" title="Remove" class="rmv" data-parent="{{ $k }}"><i class="fa fa-minus-circle" aria-hidden="true" style="color: #d44950;"></i></a></div>
									      @endif
								    	</div><!-- end:.input-group -->
								    	</div><!-- end:.form-group -->
										@endforeach
									@endif
							  </div><!-- end:.form-group -->
							
						  </div><!-- end:.input_fields_wrap -->
						  <div class="form-group">
								<a class="add_field_button" href="javascript:void(0)" style="font-size: smaller;">Add Contact</a>
							</div>	
						</div> <!-- end:.col-md-4 -->
					</div><!-- end:.row -->
				</div><!-- end:.tabpanel -->
				<div role="tabpanel" class="tab-pane" id="gov">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
						    <label for="name">Name</label>
						    <input type="text" class="form-control" id="name" name="name" placeholder="Name" maxlength="50" value="{{ $branch->name }}">
						  </div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
						    <label for="trade_name">Trade Name</label>
						    <input type="text" class="form-control" id="trade_name" name="trade_name" placeholder="Trade Name" maxlength="50" value="{{ $branch->trade_name }}">
						  </div>
						</div>
						<div class="col-md-4 col-md-offset-4 col-md-pull-4">
							<div class="form-group">
						    <label for="date_reg">Registration Date</label>
						    <div class="input-group datepicker">
						    	
						    <input type="text" class="form-control" id="date_reg" name="date_reg" placeholder="YYYY-MM-DD" value="{{ $branch->date_reg }}" data-mask="0000-00-00" maxlength="10">
						  	<span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>	
						    </div>
						  </div>
						</div>
						<div class="col-md-4 col-md-pull-4">
							<div class="form-group">
						    <label for="tin">TIN No.</label>
						    <input type="text" class="form-control" id="tin" name="tin" placeholder="000-000-000-000" value="{{ $branch->tin }}" data-mask="000-000-000-000" maxlength="15">
						  </div>
						</div><!-- end:.col-md-4 -->
					</div><!-- end:.row #gov -->
				</div><!-- end:.tabpanel -->
				<div role="tabpanel" class="tab-pane" id="sec">
					<div class="row">
						<div class="col-md-4">
						<div class="form-group">
							<label for="sector_id">Sector</label>
							@if(count($sectors)>0)
							<select class="selectpicker form-control show-tick" name="sector_id" id="sector_id" data-live-search="true" data-size="10">
								@if(!isset($branch->sector->id))
								<option disabled selected>-- Select Sector -- </option>
								@endif
								@foreach($sectors as $sector)
									@if(count($sector->children)>0)
									<optgroup label="{{ $sector->code }} - {{ $sector->descriptor }}">
											@foreach($sector->children as $child)
										  	<option value="{{$child->id}}" <?=isset($branch->sector->id)&&($child->id==$branch->sector->id)?'selected':'';?> data-tokens="{{ $child->code }} {{ $child->descriptor }}" data-subtext="{{ $child->code }}">
										  		{{ $child->descriptor }} 
										  	</option>
										   @endforeach
									</optgroup>
									@else
									<option value="{{$sector->id}}" <?=isset($branch->sector->id)&&($sector->id==$branch->sector->id)?'selected':'';?> data-tokens="{{ $sector->code }} {{ $sector->descriptor }}" data-subtext="{{ $sector->code }}">
										  		{{ $sector->descriptor }} 
									</option>
								  @endif
							  @endforeach
							</select>
							@else
								Add Lessor
							@endif
						</div>
						</div><!-- end:.col-md-4 -->
					</div>
					<div class="row">
						<div class="col-md-4">
						<div class="form-group">
							<label for="lessor_id">Lessor</label>
							@if(count($lessors)>0)
							<select class="selectpicker form-control show-tick" name="lessor_id" id="lessor_id" data-live-search="true" data-size="10">
								@if(!isset($branch->lessor->id))
									<option disabled selected>-- Select Lessor -- </option>
								@endif
								@foreach($lessors as $lessor)
							  	<option value="{{$lessor->id}}" <?=isset($branch->lessor->id)&&($lessor->id==$branch->lessor->id)?'selected':'';?> data-tokens="{{ $lessor->code }} {{ $lessor->descriptor }}">
							  		{{ $lessor->code }} - {{ $lessor->descriptor }}
							  	</option>
							  @endforeach
							</select>
							@else
								Add Lessor
							@endif
						</div>
						</div><!-- end:.col-md-4 -->
					</div>
					<div class="row">
						<div class="col-md-4">
						<div class="form-group">
							<label for="company_id">Company</label>
							@if(count($companies)>0)
							<select class="selectpicker form-control show-tick" name="company_id" id="company_id" data-live-search="true" data-size="10">
								@if(!isset($branch->company->id))
								<option disabled selected>-- Select Company -- </option>
								@endif
								@foreach($companies as $company)
							  	<option value="{{$company->id}}" <?=isset($branch->company->id)&&($company->id==$branch->company->id)?'selected':'';?> data-tokens="{{ $company->code }} {{ $company->descriptor }}">
							  		{{ $company->code }} - {{ $company->descriptor }}
							  	</option>
							  @endforeach
							</select>
							@else
								<div>
									<a href="/masterfiles/company">Add Company</a>
								</div>
							@endif
						</div>
						</div><!-- end:.col-md-4 -->
					</div><!-- end:.row -->
				</div><!-- end:.tabpanel -->
				<div role="tabpanel" class="tab-pane" id="oth">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="type">Type</label>
								<?php $_type = !is_null(old('type'))?old('type'):$branch->type; ?>
								<select class="selectpicker form-control show-tick" name="type" id="type" data-live-search="true" data-size="10" data-paytype="{{ $_type }}">
									@if($_type==0)
										<option disabled selected>-- Select Branch Type -- </option>
									@endif
									@foreach(['BRANCH - STANDALONE', 'BRANCH - IN MALL', 'BRANCH - SIDE MALL', 'OFFICE', 'OTHERS'] as $key => $pt)
								  	<option value="{{ ($key+1) }}" <?=(($key+1)==$_type)?'selected':'';?> data-tokens="{{ $pt }}">
								  		{{ $pt }}
								  	</option>
								  @endforeach
								</select>
							</div>	
						</div><!-- end: .col-md-3 -->
						<div class="col-md-3">
							<div class="form-group">
								<label for="status">Status</label>
								<?php $_status = !is_null(old('status'))?old('status'):$branch->status; ?>
								<select class="selectpicker form-control show-tick" name="status" id="status" data-live-search="true" data-size="10" data-paytype="{{ $_status }}">
									@if($_status==0)
										<option disabled selected>-- Select Status -- </option>
									@endif
									@foreach(['UNDER CONSTRUCTION', 'OPENED', 'CLOSED'] as $key => $pt)
								  	<option value="{{ ($key+1) }}" <?=(($key+1)==$_status)?'selected':'';?> data-tokens="{{ $pt }}">
								  		{{ $pt }}
								  	</option>
								  @endforeach
								</select>
							</div>	
						</div><!-- end: .col-md-3 -->
						<div class="col-md-3">
							<div class="form-group">
						    <label for="date_start">Date Opened</label>
						    <div class="input-group datepicker">
							    <input type="text" class="form-control" id="date_start" name="date_start" placeholder="YYYY-MM-DD" value="{{ $branch->get_date('date_start') }}" data-mask="0000-00-00" maxlength="10">
							  	<span class="input-group-addon">
	                  <span class="glyphicon glyphicon-calendar"></span>
	                </span>	
						    </div>
						  </div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-3">
						  <div class="form-group">
						    <label for="mancost">Man Cost</label>
						    <input type="text" class="form-control" id="mancost" name="mancost" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ !is_null(old('mancost'))?old('rate')>0?old('mancost'):'':$branch->mancost }}">
						  </div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-3">
						  <div class="form-group">
						    <label for="seating">Seating Capacity</label>
						    <input type="text" class="form-control" id="seating" name="seating" placeholder="0.00" data-mask="0,000.00" data-mask-reverse="true" maxlength="8" value="{{ !is_null(old('seating'))?old('rate')>0?old('seating'):'':$branch->seating }}">
						  </div>
						</div><!-- end: .col-md-3 -->
						<div class="col-md-3">
							<div class="form-group">
						    <label for="date_end">Date Closed</label>
						    <div class="input-group datepicker">
							    <input type="text" class="form-control" id="date_end" name="date_end" placeholder="YYYY-MM-DD" value="{{ $branch->get_date('date_end') }}" data-mask="0000-00-00" maxlength="10">
							  	<span class="input-group-addon">
	                  <span class="glyphicon glyphicon-calendar"></span>
	                </span>	
						    </div>
						  </div>
						</div><!-- end: .col-md-3 -->
					</div>
				</div><!-- end:.tabpanel -->
			</div><!-- end:.tab-content -->
		<hr>


		<div class="row">
			<div class="col-md-6">
				<input type="hidden" name="_type" value="{{ request()->has('type') && request()->input('type')==='full' ? 'full' : 'update' }}">
			  <input type="hidden" name="id" value="{{ $branch->lid() }}">
			  <button type="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved"></span> Update</button>
			  <a href="/masterfiles/branch/{{ $branch->lid() }}" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
			</div>
		</div><!-- end:.row -->
		</form>

	
	</div>
</div>
@endsection

@section('js-external')
  @parent

	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>

  <script type="text/javascript">
  	var add_unit = function(x) {
  		var html = '<div class="form-group" id="ug-'+x+'">';
		  	html += '<div class="input-group">';
	      	html += '<input type="text" class="form-control unit" id="su-'+x+'" name="space['+x+'][unit]" maxlength="32">';
		   	 html += '<div class="input-group-addon area">';
		     html += '<input type="text" name="space['+x+'][area]" id="sa-'+x+'" class="form-control area" data-mask="00,000.00" maxlength="9" value="0.00" data-mask-reverse="true">';
		     html += '</div>';
		     html += '<div class="input-group-addon"><a href="javascript:void(0)" tabindex="-1" title="Remove" class="rmv-area" data-parent="'+x+'"><i class="fa fa-minus-circle" aria-hidden="true" style="color: #d44950;"></i></a></div>';
	   		html += '</div><!-- end:.input-group -->';
	  		html += '</div><!-- end:.form-group -->';
	  	return html;
  	}

  	var add_contact = function(x) {
	 		var html = '';
	 		html += '<div class="form-group" id="fg-'+x+'">';
	 		html += '<div class="input-group">';
		      html += '<div class="input-group-btn">';
		        html += '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
		        html += '<span id="contact-icon-'+x+'" class="gly gly-iphone"></span> <span class="caret"></span></button>';
		        html += '<ul class="dropdown-menu dr-'+x+'">';
		          html += '<li><a class="toggle-icon" href="javascript:void(0)" data-contact-type="1" data-parent="'+x+'"><span class="gly gly-iphone"></span> Mobile</a></li>';
		          html += '<li><a class="toggle-icon" href="javascript:void(0)" data-contact-type="2" data-parent="'+x+'"><span class="gly gly-phone-alt"></span> Telephone</a></li>';
		          html += '<li><a class="toggle-icon" href="javascript:void(0)" data-contact-type="3" data-parent="'+x+'"><span class="gly gly-fax"></span> Fax</a></li>';
		        html += '</ul>';
		      html += '</div><!-- /btn-group -->';
		      html += '<input type="hidden" name="contact['+x+'][type]" id="ct-'+x+'" value="1">';
		      html += '<input type="text" class="form-control" name="contact['+x+'][number]" id="cn-'+x+'" data-mask="0000 000 0000" maxlength="15">';
	    	html += '<div class="input-group-addon"><a href="javascript:void(0)" tabindex="-1" title="Remove" class="rmv" data-parent="'+x+'"><i class="fa fa-minus-circle" aria-hidden="true" style="color: #d44950;"></i></a></div>';
	    	html += '</div><!-- /input-group -->';
		  html += '</div><!-- end:.form-group -->';
		  return html;
	 	}
 	


  $(document).ready(function() {

    var max_fields      = <?=(count($branch->contacts)<=0)?4:5-count($branch->contacts)?>; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    var x = 1; //initlal text box count
    
    add_button.click(function(e){ //on add input button click
      e.preventDefault();
      if(x <= max_fields){ //max input box allowed
      	var uniqid = Date.now();

        $(wrapper).append(add_contact(uniqid)); //add input box
        $('#cn-'+uniqid).mask('0000 000 0000');
        $('#cn-'+uniqid).focus();
        $('.dr-'+uniqid).trigger('click');
        x++; //text box increment
      } 
      if (x <= max_fields) {
      }	else {
      	$('.add_field_button').hide();
      }
    });

    @if(count($branch->contacts)==5)
    	$('.add_field_button').hide();
    @endif
    
    wrapper.on('click', '.rmv', function(e){ //user click on remove text
      e.preventDefault(); 

      $('#fg-'+$(this).data('parent')).remove(); 
      x--;

      if (x <= max_fields) {
      	$('.add_field_button').show();
      }
    })

   	wrapper.on('click', '.dropdown-menu li a', function(e){
    	e.preventDefault();

    	var type  = $(this).data('contact-type');
    	var cls  = $(this).children().prop('class');
    	var p = $(this).data('parent');
    	var i  = $('#contact-icon-'+p);
    	var cn  = $('#cn-'+p);
   		
   		if (i.prop('class')==cls) {
    		//console.log(i.prop('class'));
    		//console.log(cls);
   		} else {
    		i.prop('class', '');
    		i.addClass(cls);
    		$('#ct-'+p).val(type);
   		}

   		if (type==1) {
   			cn.mask('0000 000 0000');
   		} else if (type==2) {
   			cn.mask('(00) 0000 0000');
   		} else {
   			cn.unmask();
   			cn.prop('maxlength', '17')
   		}
    	
    });

   	var unit_max = <?=(count($branch->spaces)<=0)?4:5-count($branch->spaces)?>;
    var u = 1;

    $(".add_unit").on('click', function(e){
    	e.preventDefault();

    	if(u <= unit_max){ //max input box allowed
      	var uniqid = Date.now();
        $('.input_space_wrap').append(add_unit(uniqid)); //add input box
    		$('#su-'+uniqid).focus();
    		$('#sa-'+uniqid).mask('00,000.00', {reverse: true});
        u++; //text box increment
      } 

      if (u <= unit_max) {
      }	else {
      	$('.add_unit').hide();
      }
    });

    @if(count($branch->spaces)==5)
    	$('.add_unit').hide();
    @endif

    $(".input_space_wrap").on('click', '.rmv-area', function(e){ //user click on remove text
      e.preventDefault(); 

      $('#ug-'+$(this).data('parent')).remove(); 
      u--;

      if (u <= max_fields)
      	$('.add_unit').show();
      
    })


     $('.datepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        ignoreReadonly: true
    });


	});
  </script>

@endsection