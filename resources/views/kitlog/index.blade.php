@extends('master')

@section('title', '- Kitchen Logs')

@section('body-class', 'kitlog')

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
  <ol class="breadcrumb">
    <li><a href="/dashboard"><span class="gly gly-shop"></span> </a></li>
    <li><a href="/kitlog">Kitchen Log</a></li>
  </ol>

  <div class="row">
    <div class="col-md-3">
      <ul class="nav nav-pills nav-stacked">
        <li role="presentation" a><a href="/kitlog/month">Monthly Summary</a></li>
        <li role="presentation"><a href="/kitlog/checklist">Monthly Checklist</a></li>
        <li role="presentation"><a href="/kitlog/logs">Daily Raw Logs</a></li>
      </ul>
    </div>
  </div>
</div>
@endsection






@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="/js/dr-picker.js"> </script>

<script>

  moment.locale('en', { week : {
    dow : 1 // Monday is the first day of the week.
  }});

  Highcharts.setOptions({
    lang: {
      thousandsSep: ','
  }});
</script>

@endsection