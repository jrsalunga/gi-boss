@extends('hr.dash', ['search_url'=>$table])

@section('title', '- '.page_title($table).': '.$model->descriptor.' Edit')

@section('body-class', $table.'-edit')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12" >
		@include('_partials.alerts')
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<h3 class="page-header">{{ $model->descriptor }} <small>{{ $model->code }}</small>
			@if(request()->has('type') && request()->input('type')==='full')
				<a href="/hr/masterfiles/{{ $table }}/{{$model->lid()}}/edit" class="pull-right" title="Toogle few"><i class="material-icons">more_horiz</i></a>
			@else
				<a href="/hr/masterfiles/{{ $table }}/{{$model->lid()}}/edit?type=full" class="pull-right" title="Toogle full"><i class="material-icons">more_vert</i></a>
			@endif
		</h3>

		

		<form action="/masterfiles/lessor" method="POST">
		<div class="row">
			<div class="col-md-4">
				{{ csrf_field() }}

				@if(request()->has('type') && request()->input('type')==='full')
				<div class="form-group">
			    <label for="code">Code</label>
			    <input type="text" class="form-control" id="code" name="code" placeholder="Lessor Code" maxlength="3" value="{{ $model->code }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">Descriptor</label>
			    <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Descriptor" maxlength="50" value="{{ $model->descriptor }}">
			  </div>
			  @endif
			  <div class="form-group">
			    <label for="trade_name">Trade Name</label>
			    <input type="text" class="form-control" id="trade_name" name="trade_name" placeholder="Trade Name" maxlength="50" value="{{ $model->trade_name }}">
			  </div>
			  <div class="form-group">
			    <label for="address">Address</label>
			    <textarea class="form-control" id="address" name="address" placeholder="Address" maxlength="120" style="max-width: 100%;min-width: 100%;" rows="5">{{ $model->address }}</textarea>
			  </div>
			  <div class="form-group">
			    <label for="email">Email</label>
			    <input type="text" class="form-control" id="email" name="email" placeholder="Email" maxlength="50" value="{{ $model->email }}">
			  </div>
			  <div class="form-group">
			    <label for="tin">TIN</label>
			    <input type="text" class="form-control" id="tin" name="tin" placeholder="000-000-000-000" maxlength="16" value="{{ $model->tin }}" data-mask="000-000-000-000">
			  </div>
			</div>
			<div class="col-md-4">
			  <div class="input_fields_wrap">
					<div class="form-group">
				    <label for="tin">Contact No</label>

						@if(count($model->contacts)<=0)
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
							@foreach($model->contacts as $k => $contact)
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
						      <input type="text" class="form-control" id="cn-{{ $k }}" name="contact[{{ $k }}][number]" value="{{ $contact->number }}">
						      @if($k>=1)
										<div class="input-group-addon"><a href="javascript:void(0)" tabindex="-1" title="Remove" class="rmv" data-parent="{{ $k }}"><i class="fa fa-minus-circle" aria-hidden="true" style="color: #d44950;"></i></a></div>
						      @endif
					    	</div><!-- /input-group -->
					    	</div><!-- /input-group -->
							@endforeach
						@endif
				  </div><!-- end:.form-group -->
				
			  </div><!-- end:.input_fields_wrap -->
			  <div class="form-group">
					<a class="add_field_button" href="javascript:void(0)" style="font-size: smaller;">Add Contact</a>
			  </div>
			</div> <!-- end:.col-md-4 -->
			
		</div><!-- end:.row -->
		<hr>
		<div class="row">
			<div class="col-md-6">
				<input type="hidden" name="type" value="{{ request()->has('type') && request()->input('type')==='full' ? 'full' : 'update' }}">
			  <input type="hidden" name="id" value="{{$model->lid()}}">
			  <button type="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved"></span> Update</button>
			  <a href="/hr/masterfiles/{{ $table }}/{{ $model->lid() }}" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
			</div>
		</div><!-- end:.row -->
		</form>

	
	</div>
</div>
@endsection

@section('js-external')
  @parent
  <script type="text/javascript">
 	


  $(document).ready(function() {
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

    var max_fields      = <?=(count($model->contacts)<=0)?4:5-count($model->contacts)?>; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    
    add_button.click(function(e){ //on add input button click
      e.preventDefault();
      if(x <= max_fields){ //max input box allowed
      	var uniqid = Date.now();
        console.log(uniqid);
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
    
    wrapper.on('click', '.rmv', function(e){ //user click on remove text
      e.preventDefault(); 
      console.log('remove');
      console.log(x);

      $('#fg-'+$(this).data('parent')).remove(); 
      x--;

      if (x <= max_fields) {
      	$('.add_field_button').show();
      }
    })

   	wrapper.on('click', '.dropdown-menu li a', function(e){
    	e.preventDefault();
    	console.log('dropdown');
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
   			cn.mask('000 0000');
   		} else {
   			cn.unmask();
   			cn.prop('maxlength', '17')
   		}
    	
    });
	});
  </script>

@endsection