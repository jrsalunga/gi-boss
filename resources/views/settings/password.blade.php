@extends('master')

@section('title', ' - Password')

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
			  <li role="presentation" class="active"><a href="/settings/password">Change Password</a></li>
        <li role="presentation"><a href="/settings/bossbranch">Branch</a></li>
        <li role="presentation"><a href="/settings/emp-import">Import Employee</a></li>
			</ul>
  	</div>
  	<div class="col-sm-9">

  		<h4>Account Change Password</h4>
      <hr>

      @include('_partials.alerts')
      
      {!! Form::open() !!}
        <div class="form-group">
          <label for="passwordo">Old Password</label>
          <input type="password" class="form-control" id="passwordo" name="passwordo" placeholder="Old Password" maxlength="50">
        </div>
        <div class="form-group">
          <label for="password1">New Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="New Password"  maxlength="50">
        </div>
        <div class="form-group">
          <label for="username">Confirm New Password</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password"  maxlength="50">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      {!! Form::close()  !!} 
  	</div>

  </div>



</div>
@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  
  <script>
  
    
 
  </script>
@endsection











