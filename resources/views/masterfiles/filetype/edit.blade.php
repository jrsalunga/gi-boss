@extends('dash')

@section('title', '- File Type Edit')

@section('body-class', 'filetype-edit')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header">Edit File Type</h3>

		@include('_partials.alerts')
		
		<form action="/masterfiles/filetype" method="POST">
		<div class="row">
			<div class="col-md-4">
				{{ csrf_field() }}

				<div class="form-group">
			    <label for="code">Code</label>
			    <input type="text" class="form-control text-uppercase" id="code" name="code" placeholder="File Type Code" maxlength="10" value="{{ $filetype->code }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">Descriptor</label>
			    <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Descriptor" maxlength="120" value="{{ $filetype->descriptor }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">Document Assignment</label>
					<select class="form-control" name="assigned" id="assigned">
						<option disabled <?=$filetype->assigned==0?'selected':''?> >-- Select Document Assignment -- </option>
						<option value="1" <?=$filetype->assigned==1?'selected':''?> >Branch</option>
						<option value="2" <?=$filetype->assigned==2?'selected':''?> >Company</option>
						<option value="3" <?=$filetype->assigned==3?'selected':''?> >Branch and Company</option>
					</select>
			  </div>
			</div>  
		</div>
			  <hr>
		<div class="row">
			<div class="col-md-6">
			  <input type="hidden" name="type" value="update">
			  <input type="hidden" name="id" value="{{ $filetype->id }}">
			  <button type="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
			  <a href="/masterfiles/filetype" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
			</div>
		</div>
			</form>
		</div>
	</div>
</div>
@endsection

