@extends('master')

@section('title', ' - Daily Sales')

@section('css-internal')

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
<div class="container-fluid">
	
  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li class="active">Daily Sales</li>
  </ol>

  <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
            </a> 
            <!--
            <a href="/backups/history" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
            </a>
            -->
            <button type="button" class="btn btn-default active" title="Strarred Branches">
              <span class="glyphicon glyphicon-star"></span>
            </button>
            <a href="/dailysales/all" class="btn btn-default" title="All Branches">
              <span class="glyphicon glyphicon-list-alt"></span>
            </a>
          </div>
          <div class="btn-group pull-right" role="group">
            <a href="/dailysales?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <button class="btn btn-default">{{ $dr->date->format('D, M j, Y') }}</button>
            <a href="/dailysales?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>
        </div>
      </div>
    </nav>
	
  
    

    <table class="table">
      <thead>
        <tr>
          <th>Branch</th>
          <th>Sales</th>
          <th>Customer</th>
          <th class="hidden-xs hidden-sm">Head Spend</th>
          <th class="hidden-xs hidden-sm hidden-md">Tips</th>
          <th class="hidden-xs hidden-sm hidden-md">Tips %</th>
          <th class="hidden-xs hidden-sm hidden-md">Emp Count</th>
          <th class="hidden-xs hidden-sm hidden-md">Manpower %</th>
          <th class="hidden-xs hidden-sm">Cost of Food</th>
          <th class="hidden-xs hidden-sm">Cost of Food %</th>
        </tr>
      </thead>
      <tbody>
        @foreach($dailysales as $key => $ds) 
  			<tr>
  				<td>{{ $key }} <span class="hidden-xs hidden-sm">- {{ $ds['br']->descriptor }}</span></td>
          @if(is_null($ds['ds']))
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right hidden-xs hidden-sm">-</td>
            <td class="text-right hidden-xs hidden-sm hidden-md">-</td>
            <td class="text-right hidden-xs hidden-sm hidden-md">-</td>
            <td class="text-right hidden-xs hidden-sm hidden-md">-</td>
            <td class="text-right hidden-xs hidden-sm hidden-md">-</td>
            <td class="text-right hidden-xs hidden-sm">-</td>
            <td class="text-right hidden-xs hidden-sm">-</td>
          @else
            <td class="text-right">{{ number_format($ds['ds']->sales,2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->custcount,0) }}</td>
            <td class="text-right">{{ $ds['ds']->custcount==0 ? 0:number_format($ds['ds']->sales/$ds['ds']->custcount, 2) }}</td>
            <td class="text-right hidden-xs hidden-sm hidden-md">{{ number_format($ds['ds']->tips,2) }}</td>
            <td class="text-right hidden-xs hidden-sm hidden-md">{{ $ds['ds']->custcount==0 || $ds['ds']->tips=='0.00' ? 0:number_format(($ds['ds']->sales/$ds['ds']->custcount)/$ds['ds']->tips, 3) }}</td>
            <td class="text-right hidden-xs hidden-sm hidden-md">{{ $ds['ds']->empcount }}</td>
            <td class="text-right hidden-xs hidden-sm hidden-md">{{ $ds['ds']->sales=='0.00' ? 0:number_format(($ds['br']->mancost*$ds['ds']->empcount)/$ds['ds']->sales,2) }}</td>
            <td class="text-right hidden-xs hidden-sm">-</td>
            <td class="text-right hidden-xs hidden-sm">-</td>
          @endif
          
  			</tr>
  			@endforeach
  		</tbody>
		</table>

    
  
    



</div>
@endsection














@section('js-external')
  
<script src="/js/vendors-common.min.js"></script>

<script src="//d3js.org/d3.v3.min.js"></script>

<script>


</script>

  
@endsection