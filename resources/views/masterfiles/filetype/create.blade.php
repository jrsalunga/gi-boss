@extends('dash')

@section('title', '- File Type Add')

@section('body-class', 'filetype-add')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header">Create File Type</h3>

		@include('_partials.alerts')
		
		<form action="/masterfiles/filetype" method="POST">
		<div class="row">
			<div class="col-md-4">
				{{ csrf_field() }}

				<div class="form-group">
			    <label for="code">Code</label>
			    <input type="text" class="form-control text-uppercase" id="code" name="code" placeholder="File Type Code" maxlength="10" value="{{ request()->old('code') }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">Descriptor</label>
			    <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Descriptor" maxlength="120" value="{{ request()->old('descriptor') }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">Document Assignment</label>
					<select class="form-control" name="assigned" id="assigned">
						<option disabled selected>-- Select Document Assignment -- </option>
						<option value="1">Branch</option>
						<option value="2">Company</option>
						<option value="3">Branch and Company</option>
					</select>
			  </div>

			</div>  
		</div>
			  <hr>
		<div class="row">
			<div class="col-md-6">
			  <input type="hidden" name="type" value="quick">
			  <button type="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
			  <a href="/masterfiles/filetype" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
			</div>
		</div>
			</form>
		</div>
	</div>
</div>
@endsection

