@extends('master')

@section('title', ' - All Daily Cash Flow')

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
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
  
  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li>All Branches Cash Flow</li>
    <li class="active">{{ $dr->date->format('D, M j, Y') }}</li>
  </ol>

    
  
  
  <nav id="nav-action" class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-form">
        <!-- <div class="btn-group" role="group">
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
        </div> -->
        <div class="btn-group pull-right clearfix" role="group">
          <a href="/report/daily-cash-flow?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}" data-toggle="loader">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </a>
          <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
          <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
          <a href="/report/daily-cash-flow?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}" data-toggle="loader">
            <span class="glyphicon glyphicon-chevron-right"></span>
          </a>
        </div>

        <!-- <div class="btn-group pull-right clearfix" role="group">
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span id="date-type-name">Daily</span>
                  <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="date-type">
                  <li><a href="#" data-date-type="daily">Daily</a></li>
                  <li><a href="/dailysales/dr-all" data-date-type="weekly">Date Range</a></li>
                </ul>
              </div>
            </div>
          </div> -->
      </div>
    </div>
  </nav>

  <div class="table-responsive">
    <table class="table table-hover table-striped table-sort-data">
      <thead>
        <tr>
          <th>Branch</th>
          <th class="text-right">Beginning Cash</th>
          <th class="text-right">Cash Deposit</th>
          <th class="text-right"><span data-toggle="tooltip" title="percentage of cash deposit from beginning cash">%</span></th>
          <th class="text-right">Change Fund</th>
          <th class="text-right"><span data-toggle="tooltip" title="percentage of change fund  from beginning cash">%</span></th>
          <th class="text-right">Cash Sale</th>
          <th class="text-right"><span data-toggle="tooltip" title="Cash In/Out and Refund">Cash In/Out/Ref</span></th>
          <th class="text-right">Cash Total</th>
          <th class="text-right">Cash Disbursement</th>
          <th class="text-right">Ending Balance</th>
          <th class="text-right">Actual Cash</th>
          <th class="text-right">(Short)/Over</th>
          <th class="text-right">Cummulative (-/+)</th>
          <th class="text-right">Charge Sale</th>
          <th class="text-right help"><span data-toggle="tooltip" title="Cash Sale + Charge Sale">POS Sales</span></th>
        </tr>
      </thead>
      <tbody>
        <?php
          $tot_csh_fwdd = 0;
          $tot_deposit = 0;
          $tot_change_fund = 0;
          $tot_csh_sale = 0;
          $tot_cash_total = 0;
          $tot_csh_disb = 0;
          $tot_csh_bal = 0;
          $tot_csh_cnt = 0;
          $tot_shrt_ovr = 0;
          $tot_shrt_cumm = 0;
          $tot_chg_sale = 0;
          $tot_pos_sales = 0;
          $tot_csh_in_out = 0;
          $ctr = 0;

          // ['csh_fwdd', 'deposit', 'csh_sale', 'chg_sale', 'csh_disb', 'csh_bal', 'csh_cnt', 'shrt_ovr', 'shrt_cumm']
        ?>
        @foreach($datas as $key => $data) 
        
        <tr>
          <td>
            <a target="_blank" href="/report/cash-audit?branchid={{ stl($data['branch_id']) }}&date={{$dr->date->format('Y-m-d')}}">
            <span data-toggle="tooltip" title="{{ $data['branch'] }}">
            {{ $key }} 
            </span>
            </a>
          </td>
          @if(is_null($data['cash_audit']))
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
            <td class="text-right">-</td>
          @else
            <?php 
              // $tot_sales      += $data['ds']->sales;
              // $tot_deliver    += $data['ds']->totdeliver;
              // $tot_gross      += $data['ds']->slsmtd_totgrs;
              // $tot_purchcost  += $data['ds']->purchcost;
              // $tot_custcount  += $data['ds']->custcount;
              // $tot_headspend  += $data['ds']->headspend;
              // $tot_empcount   += $data['ds']->empcount;
              // //$tot_sales_emp = 0;
              // $tot_mancost    += $data['ds']->mancost;
              // $tot_mancostpct += $data['ds']->mancostpct;
              // $tot_tips       += $data['ds']->tips;
              // $tot_tipspct    += $data['ds']->tipspct;
              // $tot_cos        += $data['ds']->cos;
              // $tot_trans      += $data['ds']->trans_cnt;
            // ['csh_fwdd', 'deposit', 'csh_sale', 'chg_sale', 'csh_disb', 'csh_bal', 'csh_cnt', 'shrt_ovr', 'shrt_cumm']


              $tot_csh_fwdd     += $data['cash_audit']['csh_fwdd'];
              $tot_deposit      += $data['cash_audit']['deposit'];
              $tot_change_fund  += $data['cash_audit']['change_fund'];
              $tot_csh_sale     += $data['cash_audit']['csh_sale'];
              $tot_cash_total   += $data['cash_audit']['cash_total'];
              $tot_csh_disb     += $data['cash_audit']['csh_disb'];
              $tot_csh_bal      += $data['cash_audit']['csh_bal'];
              $tot_csh_cnt      += $data['cash_audit']['csh_cnt'];
              $tot_shrt_ovr     += $data['cash_audit']['shrt_ovr'];
              $tot_shrt_cumm    += $data['cash_audit']['shrt_cumm'];
              $tot_chg_sale     += $data['cash_audit']['chg_sale'];
              $tot_pos_sales    += $data['cash_audit']['pos_sales'];
              $tot_csh_in_out   += $data['cash_audit']['csh_in_out'];

              $ctr++;

            ?>
            <td class="text-right" data-sort="{{ $data['cash_audit']['csh_fwdd']>0?nf($data['cash_audit']['csh_fwdd'],0):'' }}">
              {{ nf($data['cash_audit']['csh_fwdd']) }}
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['deposit']>0?nf($data['cash_audit']['deposit'],0):'' }}">
              @if($data['cash_audit']['deposit']>0)
              <a target="_blank" href="/depslp/log?search=branch.code:{{$key}};date:{{$dr->date->format('Y-m-d')}}&searchJoin=and" data-toggle="tooltip" title="view deposit slip upload log page">
                {{ nf($data['cash_audit']['deposit']) }}
              </a>
              @endif
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['csh_fwdd_pct']>0?nf($data['cash_audit']['csh_fwdd_pct'],0):'' }}">
              @if($data['cash_audit']['csh_fwdd_pct']>0)
              <small class="text-muted help" data-toggle="tooltip" title="percentage of cash deposit from beginning cash">
                <em>{{  nf($data['cash_audit']['csh_fwdd_pct'])+0 }}%</em>
              </small>
              @endif
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['change_fund']>0?nf($data['cash_audit']['change_fund'],0):'' }}">
              {{ nf($data['cash_audit']['change_fund']) }}
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['change_fund_pct']>0?nf($data['cash_audit']['change_fund_pct'],0):'' }}">
              @if($data['cash_audit']['change_fund_pct']>0)
              <small class="text-muted help" data-toggle="tooltip" title="percentage of change fund  from beginning cash">
                <em>{{ nf($data['cash_audit']['change_fund_pct'])+0 }}%</em>
              </small>
              @endif
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['csh_sale']>0?nf($data['cash_audit']['csh_sale'],0):'' }}">
              {{ nf($data['cash_audit']['csh_sale']) }}
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['csh_in_out']>0?nf($data['cash_audit']['csh_in_out'],0):'' }}">
              <span class="help" title="{{ empty($data['cash_audit']['col_cas'])?'':'IN:'.$data['cash_audit']['col_cas'] }} {{ empty($data['cash_audit']['csh_out'])?'':'OUT:'.$data['cash_audit']['csh_out'] }}" data-toggle="tooltip">
                {{ nf($data['cash_audit']['csh_in_out']) }}
              </span>
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['cash_total']>0?nf($data['cash_audit']['cash_total'],0):'' }}">
              {{ nf($data['cash_audit']['cash_total']) }}
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['csh_disb']>0?nf($data['cash_audit']['csh_disb'],0):'' }}">
              <a href="/component/purchases?table=payment&item=Cash&itemid=c&branchid={{ stl($data['branch_id']) }}&fr={{ $dr->date->format('Y-m-d') }}&to={{ $dr->date->format('Y-m-d') }}" target="_blank"  data-toggle="tooltip" title="view purchase log page"> 
                {{ nf($data['cash_audit']['csh_disb']) }}
              </a>
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['csh_bal']>0?nf($data['cash_audit']['csh_bal'],0):'' }}">
              {{ nf($data['cash_audit']['csh_bal']) }}
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['csh_cnt']>0?nf($data['cash_audit']['csh_cnt'],0):'' }}">
              <strong>
              {{ nf($data['cash_audit']['csh_cnt']) }}
              </strong>
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['shrt_ovr']>0?nf($data['cash_audit']['shrt_ovr'],0):'' }}">
              {{ nf($data['cash_audit']['shrt_ovr']) }}
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['shrt_cumm']>0?nf($data['cash_audit']['shrt_cumm'],0):'' }}">
              {{ nf($data['cash_audit']['shrt_cumm']) }}
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['chg_sale']>0?nf($data['cash_audit']['chg_sale'],0):'' }}">
              {{ nf($data['cash_audit']['chg_sale']) }}
            </td>
            <td class="text-right" data-sort="{{ $data['cash_audit']['pos_sales']>0?nf($data['cash_audit']['pos_sales'],0):'' }}">
              {{ nf($data['cash_audit']['pos_sales']) }}
            </td>
          @endif
          
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td>
            <strong>
              {{ $ctr }}/{{ count($datas) }}
              {{ count($datas) > 1 ? 'branches':'branch' }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_csh_fwdd,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_deposit,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              <small>
                @if($tot_csh_fwdd>0)
                {{ nf(($tot_deposit/$tot_csh_fwdd)*100) }}%
                @endif
              </small>
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_change_fund,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              <small>
                @if($tot_csh_fwdd>0)
                {{ nf(($tot_change_fund/$tot_csh_fwdd)*100) }}%
                @endif
              </small>
            </strong>
          </td>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_csh_sale,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_csh_in_out,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_cash_total,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_csh_disb,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_csh_bal,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_csh_cnt,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_shrt_ovr,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_shrt_cumm,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_chg_sale,2) }}
            </strong>
          </td>
          <td class="text-right">
            <strong>
              {{ number_format($tot_pos_sales,2) }}
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
      loader();
      document.location.href = '/report/daily-cash-flow?date='+e.date.year()+'-'+e.date.format("MM")+'-'+e.date.format('DD');
    });


  });

</script>

  
@endsection