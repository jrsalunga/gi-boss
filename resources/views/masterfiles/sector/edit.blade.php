@extends('dash')

@section('title', '- Sector Edit')

@section('body-class', 'sector-edit')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header">Edit Sector</h3>

		@include('_partials.alerts')
		
		<form action="/masterfiles/sector" method="POST">
		<div class="row">
			<div class="col-md-4">
				{{ csrf_field() }}

				<div class="form-group">
			    <label for="code">Code</label>
			    <input type="text" class="form-control" id="code" name="code" placeholder="Sector Code" maxlength="3" value="{{ $sector->code }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">Descriptor</label>
			    <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Descriptor" maxlength="25" value="{{ $sector->descriptor }}">
			  </div>
			</div> 
			<div class="col-md-4">
				<div class="form-group">
			    <label for="am">RM/AM</label>
			    <input type="text" class="form-control searchfield" data-input="#am_id" id="am" name="am" placeholder="Search RM/AM" maxlength="100" value="{{ is_null($sector->am)?'':$sector->am->lastname.', '.$sector->am->firstname }}">
			    <input type="hidden" name="am_id" id="am_id" value="{{ $sector->am_id }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">RKH/SKH/KH</label>
			    <input type="kh" class="form-control searchfield" data-input="#kh_id" id="kh" name="kh" placeholder="Search RKH/SKH" maxlength="100" value="{{ is_null($sector->kh)?'':$sector->kh->lastname.', '.$sector->kh->firstname }}">
			    <input type="hidden" name="kh_id" id="kh_id" value="{{ $sector->kh_id }}">
			  </div>
			</div> 
			<div class="col-md-4">
				<div class="form-group">
					<label for="parent_id">Has parent sector?</label>
					@if(count($parents)>0)
					<select class="selectpicker form-control" name="parent_id" id="parent_id" data-live-search="true" data-size="10" {{$sector->is_parent()?'disabled':''}}>
						<option selected disabled>-- Select Parent Sector -- </option>
						@foreach($parents as $c)
					  	<option value="{{$c->id}}" data-tokens="{{ $c->code }} {{ $c->descriptor }}" {{ $c->id==$sector->parent_id?'selected':'' }}>
					  		{{ $c->code }} - {{ $c->descriptor }}
					  	</option>
					  @endforeach
					</select>
					@else
						Add Sector
					@endif
				</div>
			</div>
			
		</div><!-- end:.row -->
			  <hr>
		<div class="row">
			<div class="col-md-6">
			  <input type="hidden" name="type" value="update">
			  <input type="hidden" name="id" value="{{ $sector->id }}">
			  <button type="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
			  <a href="/masterfiles/sector" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
			</div>
		</div>
			</form>
		</div>
	</div>
</div>
@endsection


@section('js-external')
  @parent
	
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	 $(".searchfield").autocomplete({      
      source: function( request, response ) {
        $.when(
          $.ajax({
              type: 'GET',
              url: "/api/search/employee",
              dataType: "json",
              data: {
                maxRows: 20,
                q: request.term
              },
              success: function( data ) {
                response( $.map( data, function( item ) {
                  return {
                    label: item.lastname+ ', ' + item.firstname,
                    value: item.lastname+ ', ' + item.firstname,
                    id: item.id,
                    ordinal: item.punching
                  }
                }));
              }
          })
        ).then(function(data){
          console.log(data);
        });
      },
      minLength: 2,
      select: function(e, ui) {     
        var i = $(this).data('input');
        $(i).val(ui.item.id);
      },
      open: function() {
      	$(this).removeClass('ui-corner-all').addClass('ui-corner-top');
      	var i = $(this).data('input');
        $(i).val('')
      },
      close: function() {
        $(this).removeClass('ui-corner-top').addClass('ui-corner-all');
      },
      focus: function (e, ui) {
        $('.ui-helper-hidden-accessible').hide();
      },
      change: function( event, ui ) {

      },
      messages: {
        noResults: '',
        results: function() {}
      }
      
    });

})
</script>

@endsection