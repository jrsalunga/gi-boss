@extends('hr.dash', ['search_url'=>$table])

@section('title', '- '.page_title($table).': '.$model->descriptor)

@section('body-class', $table.'-view')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12" >
		@include('_partials.alerts')
	</div>
</div>

<div class="row" >
	<div class="col-md-12">
		<h3 class="page-header">Edit Position</h3>
		<form action="/hr/masterfiles/{{ $table }}" method="POST">
		<div class="row">
			<div class="col-md-4">
				{{ csrf_field() }}

				<div class="form-group">
			    <label for="code">Code</label>
			    <input type="text" class="form-control text-uppercase" id="code" name="code" placeholder="File Type Code" maxlength="10" value="{{ $model->code }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">Descriptor</label>
			    <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Descriptor" maxlength="120" value="{{ $model->descriptor }}">
			  </div>
			</div>  
		</div>
			  <hr>
		<div class="row">
			<div class="col-md-6">
			  <input type="hidden" name="type" value="update">
			  <input type="hidden" name="id" value="{{ $model->id }}">
			  <button type="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
			  <a href="/hr/masterfiles/{{ $table }}" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
			</div>
		</div>
			</form>
		</div>
	</div>
</div>
@endsection

