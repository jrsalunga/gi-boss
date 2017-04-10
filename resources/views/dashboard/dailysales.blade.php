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
            <span class="hidden-xs hidden-sm">Back</span>
          </a> 
          <button type="button" class="btn btn-default active" title="Strarred Branches">
            <span class="glyphicon glyphicon-star"></span>
            <span class="hidden-xs hidden-sm">Starred</span>
          </button>
          <a href="/dailysales/all" class="btn btn-default" title="All Branches">
            <span class="glyphicon glyphicon-list-alt"></span>
            <span class="hidden-xs hidden-sm">All</span>
          </a>
        </div>
        
        <div class="btn-group pull-right clearfix" role="group">
          <a href="/dailysales?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </a>
          <!--
          <button class="btn btn-default" id="dp-dates">{{ $dr->date->format('D, M j, Y') }}</button>
          -->
          <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
          <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
          <a href="/dailysales?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-right"></span>
          </a>
        </div>
        
        
      
      </div> <!-- end: .navbar-form -->
    </div>
  </nav>
	
  
    
  <div class="table-responsive">
    <table class="table table-hover table-striped table-sort">
      <thead>
        <tr>
          <th>Branch</th>
          <th class="text-right">Gross</th>
          <th class="text-right">Net</th>
          <th class="text-right">Purchased</th>
          <th class="text-right">Customer</th>
          <th class="text-right">Head Spend</th>
          <th class="text-right">Emp Count</th>
          <th class="text-right">Sales/Emp</th>
          <th class="text-right">Man Cost</th>
          <th class="text-right">Man Cost %</th>
          <th class="text-right">Tips</th>
          <th class="text-right">Tips %</th>
        </tr>
      </thead>
      <tbody>
        @foreach($dailysales as $key => $ds) 
  			<tr>
  				<td>
            <a target="_blank" href="/status/branch?branchid={{ $ds['br']->lid() }}&fr={{$dr->date->format('Y-m-d')}}&to={{$dr->date->format('Y-m-d')}}">
            {{ $key }} <span class="hidden-xs hidden-sm">- {{ $ds['br']->descriptor }}</span>
            </a>

            @if(!is_null($ds['ds']) && ($ds['ds']->slsmtd_totgrs+0)!=0 && $ds['ds']->sales > $ds['ds']->slsmtd_totgrs)
              <span class="pull-right glyphicon glyphicon-exclamation-sign text-warning" title="Warning: Net Sales is greater than Gross Sales" data-toggle="tooltip"></span>
            @endif
          </td>
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
            <td class="text-right">-</td>
            <td class="text-right">-</td>
          @else
            <td class="text-right">
              @if($ds['ds']->slsmtd_totgrs>0)
              <a href="/product/sales?branchid={{ $ds['br']->lid() }}&fr={{$dr->date->format('Y-m-d')}}&to={{$dr->date->format('Y-m-d')}}" target="_blank">
              {{ number_format($ds['ds']->slsmtd_totgrs,2) }}
              </a>
            @else
              {{ number_format($ds['ds']->slsmtd_totgrs,2) }}
            @endif
            </td>
            <td class="text-right">{{ number_format($ds['ds']->sales,2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->purchcost,2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->custcount,0) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->headspend,2) }}</td>
            <td class="text-right">{{ $ds['ds']->empcount }}</td>
            <td class="text-right">{{ $ds['ds']->empcount=='0' ? '0.00':number_format(($ds['ds']->sales/$ds['ds']->empcount),2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->mancost,2) }}</td>
            <td class="text-right">{{ $ds['ds']->mancostpct }}</td>
            <td class="text-right">{{ number_format($ds['ds']->tips,2) }}</td>
            <td class="text-right">{{ $ds['ds']->tipspct }}</td>
          @endif
          
  			</tr>
  			@endforeach
  		</tbody>
		</table>
  </div>
    
  
    



</div>
@endsection














@section('js-external')
  
<script src="/js/vendors-common.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="//d3js.org/d3.v3.min.js"></script>
<script src="/js/dr-picker.js"></script>

<script>
  $(document).ready(function(){
    

    $('#dp-date').datetimepicker({
      defaultDate: "{{ $dr->date->format('Y-m-d') }}",
      format: 'MM/DD/YYYY',
      showTodayButton: true,
      ignoreReadonly: true
    }).on('dp.change', function(e){
      document.location.href = '/dailysales?date='+e.date.year()+'-'+e.date.format("MM")+'-'+e.date.format('DD');
    }).on('dp.show', function(e){
      console.log(e.date.year()+'-'+e.date.format("MM")+'-'+e.date.format('DD'));
    }).on('dp.update', function(e){
      console.log('update');
    });


  });

</script>

  
@endsection