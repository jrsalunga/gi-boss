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

    
	
  
    <a href="/dailysales?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default">
      {{ $dr->date->copy()->subDay()->format('Y-m-d') }}
    </a>
    <button class="btn btn-success">{{ $dr->date->format('D, M j, Y') }}</button>
    <a href="/dailysales?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default">
      {{ $dr->date->copy()->addDay()->format('Y-m-d') }}
    </a>

    <table class="table">
      <thead>
        <tr>
          <th>Branch</th>
          <th>Sales</th>
          <th>Customer</th>
          <th>Head Spend</th>
          <th>Tips</th>
          <th>Tips %</th>
          <th>Emp Count</th>
          <th>Manpower %</th>
          <th>Cost of Food</th>
          <th>Cost of Food %</th>
        </tr>
      </thead>
      <tbody>
        @foreach($dailysales as $key => $ds) 
  			<tr>
  				<td>{{ $key }} - {{ $ds['br']->descriptor }}</td>
          @if(is_null($ds['ds']))
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
          @else
            <td class="text-right">{{ number_format($ds['ds']->sales,2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->custcount,0) }}</td>
            <td class="text-right">{{ $ds['ds']->custcount==0 ? 0:number_format($ds['ds']->sales/$ds['ds']->custcount, 2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->tips,2) }}</td>
            <td class="text-right">{{ $ds['ds']->custcount==0 || $ds['ds']->tips=='0.00' ? 0:number_format(($ds['ds']->sales/$ds['ds']->custcount)/$ds['ds']->tips, 3) }}</td>
            <td class="text-right">{{ $ds['ds']->empcount }}</td>
            <td class="text-right">{{ $ds['ds']->sales=='0.00' ? 0:number_format(($ds['br']->mancost*$ds['ds']->empcount)/$ds['ds']->sales,2) }}</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
          @endif
          
  			</tr>
  			@endforeach
  		</tbody>
		</table>

    {{ $dr->fr }} <br>
    {{ $dr->to }}
  
    



</div>
@endsection














@section('js-external')
  
<script src="/js/vendors-common.min.js"></script>

<script src="//d3js.org/d3.v3.min.js"></script>

<script>


</script>

  
@endsection