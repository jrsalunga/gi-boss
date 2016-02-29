@extends('master')

@section('title', ' - Dashboard')

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
    <li class="active">Dashboard</li>
  </ol>



  <div style="margin-top:50px;" class="hidden-xs"></div>
  <div style="margin-top:10px;" class="visible-xs-block"></div>
  <div class="row">
    <div class="col-md-7">
      <div id="panel-top-sales" class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="fa fa-line-chart"></span> Today's Branch Top Sales</h3>
        </div>
        <div class="panel-body">
          <table class="table">
            <thead>
              <tr>
                <th>Branch</th>
                <th class="text-center">Today
                  <div style="font-weight: normal; font-size: 11px;">
                    {{ $dr->now->format('D, M j') }}
                  </div>
                </th>
                <th class="text-center">Yesterday
                  <div style="font-weight: normal; font-size: 11px;">
                    {{ $dr->now->copy()->subDay()->format('D, M j') }}
                  </div>
                </th>
                <th class="text-center">+/-</th>
              </tr>
            </thead>
            <tbody>
              @foreach($dailysales as $ds)
                <tr>
                  <td>{{ $ds->branch->code }}</td>
                  <td class="text-right">{{ number_format($ds->today->sales,2) }}</td>
                  <td class="text-right">{{ number_format($ds->yesterday->sales,2) }}</td>
                  <td class="text-right">{{ number_format($ds->diff->sales,2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <p>View
            <a href="/dailysales?date={{$dr->now->format('Y-m-d')}}" class="btn btn-default">
              <span class="glyphicon glyphicon-star"></span> 
              <span class="hidden-xs hidden-sm">Starred</span>
            </a> 
            <a href="/dailysales/all?date={{$dr->now->format('Y-m-d')}}" class="btn btn-default">
              <span class="glyphicon glyphicon-list-alt"></span> 
              <span class="hidden-xs hidden-sm">All</span>
            </a>
          </p> 
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div id="panel-top-sales" class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="gly gly-disk-remove"></span> Last Backup</h3>
        </div>
        <div class="panel-body">
        
        </div>
      </div>
    </div>
  </div>  
  



</div>
@endsection














@section('js-external')
  
 	<script src="/js/vendors-common.min.js"></script>

  
@endsection