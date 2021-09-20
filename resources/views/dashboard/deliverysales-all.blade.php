@extends('master')

@section('title', ' - All Branches Delivery Sales')

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
    <li><a href="/delivery/all">Delivery Sales</a></li>
    <li class="active">All Branches <small>({{ $dr->date->format('D, M j, Y') }})</small></li>
  </ol>

    
  
  
  <nav id="nav-action" class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-form">
        <div class="btn-group" role="group">
          <!-- <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
            <span class="gly gly-unshare"></span>
            <span class="hidden-xs hidden-sm">Back</span>
          </a>  -->
          <!-- <a href="/delivery" class="btn btn-default" title="All Branches">
            <span class="glyphicon glyphicon-star"></span>
            <span class="hidden-xs hidden-sm">Starred</span>
          </a>
          <button type="button" class="btn btn-default active" title="Strarred Branches">
            <span class="glyphicon glyphicon-list-alt"></span>
            <span class="hidden-xs hidden-sm">All</span>
          </button> -->
        </div>
        <div class="btn-group pull-right clearfix" role="group">
          <a href="/delivery/all?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </a>
          <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
          <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
          <a href="/delivery/all?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-right"></span>
          </a>
        </div>

        <div class="btn-group pull-right clearfix" role="group">
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span id="date-type-name">Daily</span>
                  <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="date-type">
                  <li><a href="#" data-date-type="daily">Daily</a></li>
                  <li><a href="/delivery/dr-all" data-date-type="weekly">Date Range</a></li>
                </ul>
              </div>
            </div>
          </div>
      </div>
    </div>
  </nav>

  <div class="table-responsive">
    <table class="table table-hover table-striped table-sort-data">
      <thead>
        <tr>
          <th>Branch</th>
          <th class="text-right">Cash Sale</th>
          <th class="text-right">Charge Sale</th>
          <th class="text-right">Total Sales</th>
          <th class="text-right">Delivery Sales</th>
          <th class="text-right">%</th>
          <th class="text-right">Grab Food</th>
          <th class="text-right">Grab Conceirge</th>
          <th class="text-right">Food Panda</th>
          <th class="text-right">Zap Delivery</th>
          <th class="text-right">Total Zap Sales</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $tot_sales = 0;
          $tot_deliver = 0;
          $tot_grab = 0;
          $tot_grabc = 0;
          $tot_panda = 0;
          $tot_zap = 0;
          $tot_zapsales = 0;
          $tot_sale_csh = 0;
          $tot_sale_chg = 0;
        ?>
        @foreach($dailysales as $key => $ds) 
        
        <tr>
          <td data-sort="{{ $ds['br']->code }}">
            <a target="_blank" href="/status/branch?branchid={{ $ds['br']->lid() }}&fr={{$dr->date->format('Y-m-d')}}&to={{$dr->date->format('Y-m-d')}}">
            <span data-toggle="tooltip" title="{{ $ds['br']->descriptor }}" class="help">
            {{ $key }} 
            </span>
            </a>
            @if(!is_null($ds['ds']) && ($ds['ds']->slsmtd_totgrs+0)!=0 && $ds['ds']->sales > $ds['ds']->slsmtd_totgrs)
              <span class="pull-right glyphicon glyphicon-exclamation-sign text-warning" title="Warning: " data-toggle="tooltip"></span>
            @endif
          </td>
          @if(is_null($ds['ds']))
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
            <td class="text-right" data-sort="">-</td>
          @else
            <?php 
              $tot_sales  += $ds['ds']->sales;
              $tot_deliver+= $ds['ds']->totdeliver;
              $tot_grab   += $ds['ds']->grab;
              $tot_grabc  += $ds['ds']->grabc;
              $tot_panda  += $ds['ds']->panda;
              $tot_zap    += $ds['ds']->zap;
              $tot_zapsales+= $ds['ds']->zap_sales;
              $tot_sale_csh+= $ds['ds']->sale_csh;
              $tot_sale_chg+= $ds['ds']->sale_chg;
            ?>
            <td class="text-right" data-sort="{{ $ds['ds']->sale_csh>0?number_format($ds['ds']->sale_csh,0):'' }}">
              @if($ds['ds']->sale_csh>0)
                {{ number_format($ds['ds']->sale_csh, 2) }}
              @else 
                -
              @endif
            </td>
            <td class="text-right" data-sort="{{ $ds['ds']->sale_chg>0?number_format($ds['ds']->sale_chg,0):'' }}">
              @if($ds['ds']->sale_chg>0)
                {{ number_format($ds['ds']->sale_chg, 2) }}
              @else 
                -
              @endif
            </td>
            <td class="text-right" data-sort="{{ number_format($ds['ds']->sales,0) }}">
            @if($ds['ds']->sales>0)
              <a href="/product/sales?branchid={{ $ds['br']->lid() }}&fr={{$dr->date->format('Y-m-d')}}&to={{$dr->date->format('Y-m-d')}}" target="_blank">
              <strong>{{ number_format($ds['ds']->sales,2) }}</strong>
              </a>
            @else
              -
            @endif
            </td>
            <td class="text-right" data-sort="{{ $ds['ds']->totdeliver>0?number_format($ds['ds']->totdeliver,0):'' }}">
              @if($ds['ds']->totdeliver>0)
                {{ number_format($ds['ds']->totdeliver,2) }}
              @else
                -
              @endif
            </td>
            <td class="text-right" data-sort="{{ ($ds['ds']->sales>0&&$ds['ds']->totdeliver>0?number_format(($ds['ds']->totdeliver/$ds['ds']->sales)*100,2):'') }}" style="width: 5%;">
              @if($ds['ds']->sales>0 && $ds['ds']->totdeliver>0)
                <small><em class="text-muted">{{ number_format(($ds['ds']->totdeliver/$ds['ds']->sales)*100,2) }}%</em></small>
              @else 
                -
              @endif
            </td>
            <td class="text-right" data-sort="{{ $ds['ds']->grab>0?number_format($ds['ds']->grab,0):'' }}">
              @if($ds['ds']->grab>0)
                {{ number_format($ds['ds']->grab, 2) }}
              @else 
                -
              @endif
            </td>
            <td class="text-right" data-sort="{{ $ds['ds']->grabc>0?number_format($ds['ds']->grabc,0):'' }}">
              @if($ds['ds']->grabc>0)
                {{ number_format($ds['ds']->grabc,2) }}
              @else 
                -
              @endif
            </td>
            <td class="text-right" data-sort="{{ $ds['ds']->panda>0?number_format($ds['ds']->panda,0):'' }}">
              @if($ds['ds']->panda>0)
                {{ number_format($ds['ds']->panda,2) }}
              @else 
                -
              @endif
            </td>
             <td class="text-right" data-sort="{{ $ds['ds']->zap>0?number_format($ds['ds']->zap,0):'' }}">
              @if($ds['ds']->zap>0)
                {{ number_format($ds['ds']->zap,2) }}
              @else 
                -
              @endif
            </td>
            <td class="text-right" data-sort="{{ $ds['ds']->zap_sales>0?number_format($ds['ds']->zap_sales,0):'' }}">
              @if($ds['ds']->zap_sales>0)
                {{ number_format($ds['ds']->zap_sales,2) }}
              @else 
                -
              @endif
            </td>
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
              {{ number_format($tot_sale_csh,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_sale_chg,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_sales,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_deliver,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              @if($tot_sales>0)
                {{ number_format(($tot_deliver/$tot_sales)*100,2) }} %
              @endif
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_grab,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_grabc,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_panda,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_zap,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_zapsales,2) }}
            </strong>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
    
</div>
@endsection














@section('js-external')
  
<script src="/js/vendors-common.min.js"></script>
<script src="/js/dr-picker.js"></script>

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
      document.location.href = '/delivery/all?date='+e.date.year()+'-'+e.date.format("MM")+'-'+e.date.format('DD');
    });


  });

</script>

  
@endsection