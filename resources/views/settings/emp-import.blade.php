@extends('master')

@section('title', ' - Settings')

@section('navbar-2')
<ul class="nav navbar-nav navbar-right"> 
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="glyphicon glyphicon-menu-hamburger"></span>
    </a>
    <ul class="dropdown-menu">
      <li><a href="/settings"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
      <li><a href="/logout"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>     
    </ul>
  </li>
</ul>
<p class="navbar-text navbar-right">{{ $name }}</p>
@endsection

@section('container-body')
<div class="container-fluid">
	
  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li class="active">Settings</li>
  </ol>
	<hr>
  
  <div class="row">
  	<div class="col-sm-3">
  		<ul class="nav nav-pills nav-stacked">
        <li role="presentation"><a href="/settings">Profile</a></li>
        <li role="presentation"><a href="/settings/password">Change Password</a></li>
        <li role="presentation"><a href="/settings/bossbranch">Branch</a></li>
  			<li role="presentation" class="active"><a href="/settings/emp-import">Import Employee</a></li>
        <li role="presentation"><a href="/masterfiles">Masterfiles</a></li>
        <li role="presentation"><a href="/hr">HRIS</a></li>
			</ul>
  	</div>
  	<div class="col-sm-9">
      
      <h4>Import Employee</h4>
      <hr>
      @include('_partials.alerts')

      
      {!! Form::open(['url'=>'/settings/emp-import', 'method'=>'post', 'files'=>true]) !!}
        
        
        
        <div class="form-group">
          <label for="exampleInputFile">Attach .MAS File</label>
          <input type="file" name="empfile">

          <p class="help-block">&nbsp;</p>
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="fa fa-cloud-upload" aria-hidden="true"></i> 
          Import
        </button>
      </form>
      

  		
  	</div>

  </div>



</div>
@endsection



@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  
  <script>
  
    
 
  </script>
@endsection










