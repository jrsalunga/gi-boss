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
          <a href="/dailysales" class="btn btn-default" title="All Branches">
            <span class="glyphicon glyphicon-star"></span>
            <span class="hidden-xs hidden-sm">Starred</span>
          </a>
          <button type="button" class="btn btn-default active" title="Strarred Branches">
            <span class="glyphicon glyphicon-list-alt"></span>
            <span class="hidden-xs hidden-sm">All</span>
          </button>
        </div>
        <div class="btn-group pull-right clearfix" role="group">
          <a href="/dailysales/all?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </a>
          <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
          <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
          <a href="/dailysales/all?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-right"></span>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <div class="table-responsive">
    <table class="table table-hover table-striped table-sort-all">
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
        <?php
          $tot_sales = 0;
          $tot_gross = 0;
          $tot_purchcost = 0;
          $tot_custcount = 0;
          $tot_headspend = 0;
          $tot_empcount = 0;
          $tot_sales_emp = 0;
          $tot_mancost = 0;
          $tot_mancostpct = 0;
          $tot_tips = 0;
          $tot_tipspct = 0;
        ?>
        @foreach($dailysales as $key => $ds) 
        
        <tr>
          <td>
            <a target="_blank" href="/status/branch?branchid={{ $ds['br']->lid() }}&fr={{$dr->date->format('Y-m-d')}}&to={{$dr->date->format('Y-m-d')}}">
            {{ $key }} <span class="hidden-xs hidden-sm">- {{ $ds['br']->descriptor }}</span>
            </a>

            @if(!is_null($ds['ds']) && ($ds['ds']->slsmtd_totgrs+0)!=0 && $ds['ds']->sales > $ds['ds']->slsmtd_totgrs)
              <span class="pull-right glyphicon glyphicon-exclamation-sign"></span>
              {{ ($ds['ds']->sales+0) }}
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
            <?php 
              $tot_sales      += $ds['ds']->sales;
              $tot_gross      += $ds['ds']->slsmtd_totgrs;
              $tot_purchcost  += $ds['ds']->purchcost;
              $tot_custcount  += $ds['ds']->custcount;
              $tot_headspend  += $ds['ds']->headspend;
              $tot_empcount   += $ds['ds']->empcount;
              //$tot_sales_emp = 0;
              $tot_mancost    += $ds['ds']->mancost;
              $tot_mancostpct += $ds['ds']->mancostpct;
              $tot_tips       += $ds['ds']->tips;
              $tot_tipspct    += $ds['ds']->tipspct;
            ?>
            <td class="text-right">{{ number_format($ds['ds']->slsmtd_totgrs,2) }}</td>
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
      <tfoot>
        <tr>
          <td>
            <strong>
              {{ count($dailysales) }}
              {{ count($dailysales) > 1 ? 'branches':'branch' }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_gross,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_sales,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_purchcost,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_custcount,0) }}
            </strong>
          </td>
          <td class="text-right"></td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_empcount,0) }}
            </strong>
          </td>
          <td class="text-right"></td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_mancost,2) }}
            </strong>
          </td>
          <td class="text-right"></td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_tips,2) }}
            </strong>
          </td>
          <td class="text-right"></td>

        </tr>
      </tfoot>
    </table>
  </div>
    
</div>
@endsection














@section('js-external')
  
<script src="/js/vendors-common.min.js"></script>


<script>
    
  $(document).ready(function(){

    $('.table-sort-all').tablesorter({
      stringTo: 'min',
      sortList: [[1,1]],
      headers: {
        1: { sorter: "digit", string: "min" }, // non-numeric content is treated as a MAX value
        //2: { sorter: "digit", empty : "top" }, // sort empty cells to the top
        //3: { sorter: "digit", string: "min" }  // non-numeric content is treated as a MIN value
      } 
    });
    
    $('#dp-date').datetimepicker({
      defaultDate: "{{ $dr->date->format('Y-m-d') }}",
      format: 'MM/DD/YYYY',
      showTodayButton: true,
      ignoreReadonly: true
    }).on('dp.change', function(e){
      document.location.href = '/dailysales/all?date='+e.date.year()+'-'+e.date.format("MM")+'-'+e.date.format('DD');
    });


  });

</script>

  
@endsection