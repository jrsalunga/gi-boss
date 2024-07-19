@extends('hr-master')

@section('title', ' - Dashboard')

@section('css-internal')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="/css/material-components-web.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
<style type="text/css">
.material-icons {
  font-size: inherit;
}

.form-control.unit {

}

.input-group-addon.area {
  width: 40%;
  margin: 0;
  padding: 0;
  border: 0;
}

.area .area {
  text-align: right;
}

.input-group-addon .form-control {
  border-left: 0;
}

.rmv-area {
 padding: 0 8px;
}
</style>
@endsection

@section('navbar-1')
  @include('menu.main-hr')

  <form class="navbar-form navbar-left navbar-search" method="GET" action="/hr/masterfiles/<?=isset($search_url)?$search_url:'employee'?>">
    <div class="form-group">
    <input type="text" class="form-control searchbar" name="search" id="search" value="{{ request()->input('search') }}">
  </div>
  </form>
@endsection

@section('sidebar')
  @include('menu.sub-hr')
@endsection

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
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-3 col-md-2 sidebar">
    @section('sidebar')
  
    @show
    </div>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    @section('content')

    @show
    </div>
  </div>
</div>
@endsection



@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="/js/dr-picker.js"> </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.13/jquery.mask.min.js"> </script>
  <script src="/js/material-components-web.min.js"></script>
  <script>mdc.autoInit()</script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>

  <script type="text/javascript">
  $(document).ready(function(){
    @if(request()->has('search'))
    $('.searchbar').focus();
    var val = $('.searchbar').val();
    $('.searchbar').val('');
    $('.searchbar').val(val);
    @endif

    $('#rfid').focus().select();
  });
  </script>

@endsection