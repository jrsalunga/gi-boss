@extends('master')

@section('title', ' - All Branch Sales by Date Range')

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
    <li>All Branch Sales</li>
    <li class="active">{{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }}</li>
  </ol>

    
  
  
  <nav id="nav-action" class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-form">
        <div class="btn-group" role="group">
          <!-- <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
            <span class="gly gly-unshare"></span>
            <span class="hidden-xs hidden-sm">Back</span>
          </a>  -->
          <a href="/dailysales" class="btn btn-default" title="All Branch">
            <span class="glyphicon glyphicon-star"></span>
            <span class="hidden-xs hidden-sm">Starred</span>
          </a>
          <button type="button" class="btn btn-default active" title="Strarred Branch">
            <span class="glyphicon glyphicon-list-alt"></span>
            <span class="hidden-xs hidden-sm">All</span>
          </button>
        </div>

        <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/dailysales/dr-all', 'method' => 'get', 'id'=>'dp-form']) !!}
            <button type="submit" class="btn btn-success btn-go" title="Go">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->
        
        <div class="btn-group pull-right clearfix dp-container" role="group">
            
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
        
          </div><!-- end btn-grp -->

          <div class="btn-group pull-right clearfix" role="group">
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span id="date-type-name">Date Range</span>
                  <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="date-type">
                  <li><a href="/dailysales/all" data-date-type="daily">Daily</a></li>
                  <li><a href="#" data-date-type="weekly">Date Range</a></li>
                </ul>
              </div>
            </div>
          </div>

      </div>
    </div>
  </nav>

  <div class="table-responsive">
    <table class="table table-hover table-striped table-sort-all">
      <thead>
        <tr>
          <th>Branch</th>
          <th class="text-right">Sales</th>
          <!-- <th class="text-right">Food Cost</th> -->
          <th class="text-right"></th>
          <th class="text-right">Delivery</th>
          <th class="text-right">Purchased</th>
          <th class="text-right">Customer</th>
          <th class="text-right">Head Spend</th>
          <th class="text-right">Trans</th>
          <th class="text-right">Sales/Receipt</th>
          <th class="text-right">Emp Count</th>
          <th class="text-right">Sales/Emp</th>
          <th class="text-right">Mancost</th>
          <th class="text-right">Mancost %</th>
          <th class="text-right">Tips</th>
          <th class="text-right">Tips %</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $tot_sales = 0;
          $tot_deliver = 0;
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
          $tot_cos = 0;
          $tot_trans = 0;
        ?>
        @foreach($dailysales as $key => $ds) 
        
        <tr>
          <td>
            <a target="_blank" href="/status/branch?branchid={{ $ds['br']->lid() }}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}">
            <span data-toggle="tooltip" title="{{ $ds['br']->descriptor }}" class="help">
            {{ $key }} 
            </span>
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
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
          @else
            <?php 
              $tot_sales      += $ds['ds']->sales;
              $tot_deliver    += $ds['ds']->totdeliver;
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
              $tot_cos        += $ds['ds']->cos;
              $tot_trans      += $ds['ds']->trans_cnt;
            ?>
            <td class="text-right">
            @if(number_format($ds['ds']->sales,2)=='0.00')
              {{ number_format($ds['ds']->sales,2)  }}
            @else
              <a href="/product/sales?branchid={{ $ds['br']->lid() }}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}" target="_blank">
              {{ number_format($ds['ds']->sales,2) }}
              </a>
            @endif
            </td>
            <td class="text-right">
              @if($ds['ds']->sales>0)
                <small><em class="text-muted"><small>{{ number_format(($ds['ds']->totdeliver/$ds['ds']->sales)*100,2) }}%</small></em></small>
              @endif
            </td>
            <td class="text-right">
              @if(number_format($ds['ds']->totdeliver,2)=='0.00')
                {{ number_format($ds['ds']->totdeliver,2) }}
              @else
                <!--
                <a href="/component/purchases?table=expscat&item=Food+Cost&itemid=7208aa3f5cf111e5adbc00ff59fbb323&branchid={{$ds['br']->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}" target="_blank">
                </a>-->
               
                {{ number_format($ds['ds']->totdeliver,2) }}
              @endif
              @if(number_format($ds['ds']->cos,2)=='0.00')
                <!-- {{ number_format($ds['ds']->cos,2) }} -->
              @else
                <!--
                <a href="/component/purchases?table=expscat&item=Food+Cost&itemid=7208aa3f5cf111e5adbc00ff59fbb323&branchid={{$ds['br']->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}" target="_blank">
                </a>-->
                <!--  {{ number_format($ds['ds']->cos,2) }} -->
              @endif
            </td>
            <td class="text-right">
            @if($ds['ds']->purchcost>0)
              <a href="/component/purchases?branchid={{ $ds['br']->lid() }}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}" target="_blank">
              {{ number_format($ds['ds']->purchcost,2) }}
              </a>
            @else
              {{ number_format($ds['ds']->purchcost,2) }}
            @endif
            </td>
            <td class="text-right">{{ number_format($ds['ds']->custcount,0) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->headspend,2) }}</td>
            <td class="text-right">{{ $ds['ds']->trans_cnt }}</td>
            <td class="text-right">{{ $ds['ds']->get_receipt_ave() }}</td>
            <td class="text-right">{{ $ds['ds']->empcount }}</td>
            <td class="text-right">{{ $ds['ds']->empcount=='0' ? '0.00':number_format(($ds['ds']->sales/$ds['ds']->empcount),2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->mancost,2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->mancostpct,2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->tips,2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->tipspct,2) }}</td>
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
              {{ number_format($tot_sales,2) }}
            </strong>
          </td>
          <td class="text-right">
              @if($tot_sales>0)
                <small><em class="text-muted">{{ number_format(($tot_deliver/$tot_sales)*100,2) }}%</em></small>
              @endif
            
          </td>
          <td class="text-right">
              
            <strong>
              <!-- {{ number_format($tot_cos,2) }} -->
              {{ number_format($tot_deliver,2) }}
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
              {{ number_format($tot_trans,0) }}
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
<script src="/js/dr-picker.js"></script>

<script>
   moment.locale('en', { week : {
      dow : 1 // Monday is the first day of the week.
    }});  

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
    
    $('#dp-date-fr').datetimepicker({
        //defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        //calendarWeeks: true,
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-date-to').datetimepicker({
       // defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        //calendarWeeks: true,
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


  });

</script>

  
@endsection