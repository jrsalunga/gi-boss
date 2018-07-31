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
			
			<div class="col-md-4 col-md-push-2">
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

@endsection