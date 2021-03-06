@extends('dash')

@section('title', '- Branch Add')

@section('body-class', 'branch-add')

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header">Create Branch</h3>

		@include('_partials.alerts')

		<form action="/masterfiles/branch" method="POST">
		{{ csrf_field() }}
		@if(session()->has('branch.import'))

	  <div class="alert alert-warning alert-important">
	    <b>Warning: </b> {{ session('branch.import')->code }} found on HRIS.
	  </div>

		<h5>Do you want to import this record?</h5>
		<h3>{{ session('branch.import')->code }} - {{ session('branch.import')->descriptor }}</h3>
		<h3><i class="fa fa-database" aria-hidden="true"></i> HRIS <span class="gly gly-chevron-right"></span> <i class="fa fa-database" aria-hidden="true"></i> Boss Module</h3>
		<hr>
		<div class="row">
			<div class="col-md-6">
			  <input type="hidden" name="_type" value="import">
			  <input type="hidden" name="id" value="{{ session('branch.import')->lid() }}">
			  <button type="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Import</button>
			  <a href="/masterfiles/branch" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
			</div>
		</div>
		@else
		<div class="row">
			<div class="col-md-4">

				<div class="form-group">
			    <label for="code">Code</label>
			    <input type="text" class="form-control" id="code" name="code" placeholder="Code" maxlength="3" value="{{ request()->old('code') }}">
			  </div>
			  <div class="form-group">
			    <label for="descriptor">Descriptor</label>
			    <input type="text" class="form-control" id="descriptor" name="descriptor" placeholder="Descriptor" maxlength="25" value="{{ request()->old('descriptor') }}">
			  </div>
			</div>  
			<div class="col-md-4 col-md-push-2">
				<div class="form-group">
						<div>
							<p>&nbsp;</p>
						</div>
    				<input type="checkbox" name="user"> Create users?
  			
  			</div>
  		</div>
		</div>
			  <hr>
		<div class="row">
			<div class="col-md-6">
			  <input type="hidden" name="_type" value="quick">
			  <button type="submit" class="btn btn-primary" data-toggle="loader"><span class="gly gly-floppy-saved" data-toggle="loader"></span> Save</button>
			  <a href="/masterfiles/branch" class="btn btn-default" data-toggle="loader"><span class="gly gly-remove"></span> Cancel</a>
			</div>
		</div>
		
		@endif
		</form>

		</div><!-- end:.col-md-12 -->
	</div>
</div>
@endsection

@section('js-external')
  @parent

 <script type="text/javascript">
  $(document).ready(function() {
  	$('#code').focus();
  });
</script>
@endsection

