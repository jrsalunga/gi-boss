@extends('master')

@section('title', '-  Daily Cash Audit')

@section('body-class', 'daily-cash-audit')

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
    <li><a href="/"><span class="gly gly-shop"></span> </a></li>
    <!--<li><a href="/report">Report</a></li>-->
    <li><a href="/report/cash-audit">Cash Audit</a></li>
    <li class="active">Daily @if(!is_null($branch))<small>({{ $dr->date->format('D M j, Y') }})</small>@endif</li>   
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group visible-xs-inline-block pull-left" role="group">
            <div style="padding: 6px 12px;">
              <span class="gly gly-shop"></span>
              @if(is_null(($branch)))
                -
              @else
                <span>{{ $branch->code }}</span>
              @endif

              
            </div>
          </div>
          <div class="btn-group visible-xs-inline-block pull-right" role="group">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#mdl-form">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </button>
          </div>
          <div class="btn-group btn-group pull-right clearfix hidden-xs" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/report/cash-audit', 'method' => 'get', 'id'=>'filter-form']) !!}
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="date" id="date" value="{{ $dr->date->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

          <div class="btn-group hidden-xs">
            <div class="dropdown">
              <button id="dLabel" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-shop"></span>
                @if(is_null(($branch)))
                  <span class="br-code">Select Branch</span>
                  <span class="br-desc hidden-xs hidden-sm"></span>
                @else
                  <span class="br-code">{{ $branch->code }}</span>
                  <span class="br-desc hidden-xs hidden-sm">- {{ $branch->descriptor }}</span>
                @endif
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu br" aria-labelledby="dLabel" style="max-height: 400px; overflow-y: scroll;">
                @foreach($branches as $b)
                <li>
                  <a href="#" data-desc="{{ $b->descriptor }}" data-code="{{ $b->code }}" data-branchid="{{ $b->lid() }}">{{ $b->code }}</a>
                </li>
                @endforeach
              </ul>
            </div> <!-- .dropdown -->
          </div>
      
          <div class="btn-group pull-right clearfix" role="group" style="margin-right: 5px;">
            @if(is_null(($branch)))

            @else
            <a href="/report/cash-audit?branchid={{$branch->lid()}}&amp;date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            @endif
            <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
            <label class="btn btn-default hidden-xs" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
            @if(is_null(($branch)))

            @else
            <a href="/report/cash-audit?branchid={{$branch->lid()}}&amp;date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            @endif
          </div>

         
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <div class="row">
      <div class="col-md-12" style="margin-top: 20px;">
      @if(is_null($cash_audit))
        @if(!is_null($branch))
        No Data
        @endif
      @else
        <div class="panel panel-default">
          <div class="panel-heading">Cash Flow</div>
          <div class="panel-body">

            <div class="col-md-3">
              <div class="table-responsive">
                <table class="table table-condensed table-striped">
                  <tbody>
                    <tr><td>Forwarded Cash</td><td class="text-right">{{ nf($cash_audit->csh_fwdd, 2, true) }}</td></tr>
                    <tr><td>Forwarded Cheque</td><td class="text-right">{{ nf($cash_audit->chk_fwdd, 2, true) }}</td></tr>
                    <tr><td>Forwarded Fx Curr.</td><td class="text-right" class="text-right">{{ nf(0, 2, true) }}</td></tr>
                    <tr><td>Forwarded Total</td><td class="text-right">{{ nf($cash_audit->csh_fwdd+$cash_audit->chk_fwdd, 2, true) }}</td></tr>
                    <tr><td>Deposit Cash</td><td class="text-right">{{ nf($cash_audit->deposit, 2, true) }}</td></tr>
                    <tr><td>Deposit Cheque</td><td class="text-right">{{ nf($cash_audit->depositk, 2, true) }}</td></tr>
                    <?php  $change_fund = ($cash_audit->csh_fwdd+$cash_audit->chk_fwdd)-($cash_audit->deposit+$cash_audit->depositk);  ?>
                    <tr><td>Change Fund</td><td class="text-right">{{ nf($change_fund, 2, true) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-3">
              <div class="table-responsive">
                <table class="table table-condensed table-striped">
                  <tbody>
                    <tr><td>Sales POS</td><td class="text-right">{{ nf($cash_audit->csh_sale+$cash_audit->chg_sale, 2, true) }}</td></tr>
                    <tr><td>Sales Function</td><td class="text-right">{{ nf($cash_audit->col_food+$cash_audit->col_foodk, 2, true) }}</td></tr>
                    <?php $sales_total = $cash_audit->csh_sale+$cash_audit->chg_sale+$cash_audit->col_food+$cash_audit->col_foodk; ?>
                    <tr><td>Sales Total</td><td class="text-right">{{ nf($sales_total, 2, true) }}</td></tr>
                    <?php $total_col = $cash_audit->tot_coll + $cash_audit->tot_collk; ?>
                    <tr><td>Total Collection</td><td class="text-right">{{ nf( $total_col, 2, true) }}</td></tr>
                    <tr><td>Overall Total</td><td class="text-right"><b>{{ nf($change_fund+$sales_total+$total_col, 2, true) }}</b></td></tr>
                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                    <tr><td>Cash Disbursement</td><td class="text-right">
                      <a href="/component/purchases?table=payment&item=Cash&itemid=c&branchid={{ $b->lid() }}&fr={{ $dr->date->format('Y-m-d') }}&to={{ $dr->date->format('Y-m-d') }}" target="_blank">
                      {{ nf($cash_audit->csh_disb, 2, true) }}
                      </a>
                    </td></tr>
                    <tr><td>Sales Card</td><td class="text-right">{{ nf($cash_audit->chg_sale+$cash_audit->col_foodc, 2, true) }}</td></tr>
                    <tr><td>CashOut/Refund</td><td class="text-right">{{ nf($cash_audit->csh_out+$cash_audit->csh_outk, 2, true) }}</td></tr>
                    <tr><td>Total Disbursement</td><td class="text-right"><b>{{ nf($cash_audit->tot_disb, 2, true) }}</b></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-3">
              <div class="table-responsive">
                <table class="table table-condensed table-striped">
                  <tbody>
                    <tr><td>Ending Balance</td><td class="text-right">{{ nf($cash_audit->comp_bal, 2, true) }}</td></tr>
                    <tr><td>Actual Cash </td><td class="text-right">{{ nf($cash_audit->csh_cnt, 2, true) }}</td></tr>
                    <tr><td>Actual Cheques</td><td class="text-right">{{ nf($cash_audit->checks, 2, true) }}</td></tr>
                    <tr><td>Actual Fx Curr.</td><td class="text-right">{{ nf($cash_audit->forex, 2, true) }}</td></tr>
                    <tr><td>Actual Total</td>
                      <td class="text-right">
                        <b title="{{$cash_audit->csh_cnt}} / {{$cash_audit->checks}} / {{$cash_audit->forex}}">
                          {{ nf($cash_audit->csh_cnt+$cash_audit->checks+$cash_audit->forex, 2, true) }}
                        </b>
                      </td>
                    </tr>
                    <tr><td>(Short) / Over</td><td class="text-right">{{ nf($cash_audit->shrt_ovr, 2, true) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-3">
              <div class="table-responsive">
                <table class="table table-condensed table-striped">
                  <tbody>
                    <tr><td>Paid Signed Chit</td><td class="text-right">{{ nf($cash_audit->sig_salep, 2, true) }}</td></tr>
                    <tr><td>Unpaid Signed Chit</td><td class="text-right">{{ nf($cash_audit->sig_saleu, 2, true) }}</td></tr>
                    <tr><td>Total Signed</td><td class="text-right">{{ nf($cash_audit->sig_sale, 2, true) }}</td></tr>
                    <tr><td>Total Discounts</td><td class="text-right">{{ nf($cash_audit->tot_disc, 2, true) }}</td></tr>
                    <tr><td>Total Cancellations</td><td class="text-right">{{ nf($cash_audit->tot_canc, 2, true) }}</td></tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      @endif
      </div> <!-- end: .col-md-12 -->
      
      <div class="col-md-4" style="margin-top: 20px;">
        @if(!is_null($cash_audit))
        <div class="panel panel-default">
          <div class="panel-heading">Actual Cashier Drawer Count</div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-condensed table-striped" style="margin-top: 0;">
                <thead>
                  <tr>
                    <th class="text-right">Denomination</th>
                    <th class="text-right">Pcs.</th>
                    <th class="text-right">Value</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-right">Fx Curr.</td><td class="text-right"></td><td class="text-right">{{ nf($cash_audit->forex, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">Cheques</td><td class="text-right">{{ $cash_audit->checks_pcs }}</td><td class="text-right">{{ nf($cash_audit->checks, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">P1000</td><td class="text-right">{{ $cash_audit->p1000_pcs }}</td><td class="text-right">{{ nf($cash_audit->p1000_amt, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">500</td><td class="text-right">{{ $cash_audit->p500_pcs }}</td><td class="text-right">{{ nf($cash_audit->p500_amt, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">200</td><td class="text-right">{{ $cash_audit->p200_pcs }}</td><td class="text-right">{{ nf($cash_audit->p200_amt, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">100</td><td class="text-right">{{ $cash_audit->p100_pcs }}</td><td class="text-right">{{ nf($cash_audit->p100_amt, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">50</td><td class="text-right">{{ $cash_audit->p50_pcs }}</td><td class="text-right">{{ nf($cash_audit->p50_amt, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">20</td><td class="text-right">{{ $cash_audit->p20_pcs }}</td><td class="text-right">{{ nf($cash_audit->p20_amt, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">10</td><td class="text-right">{{ $cash_audit->p10_pcs }}</td><td class="text-right">{{ nf($cash_audit->p10_amt, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">5</td><td class="text-right">{{ $cash_audit->p5_pcs }}</td><td class="text-right">{{ nf($cash_audit->p5_amt, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">Coins</td><td class="text-right"></td><td class="text-right">{{ nf($cash_audit->coins, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="2">Total Cash</td><td class="text-right"><b>{{ nf($cash_audit->p1000_amt+$cash_audit->p500_amt+$cash_audit->p200_amt+$cash_audit->p100_amt+$cash_audit->p50_amt+$cash_audit->p20_amt+$cash_audit->p10_amt+$cash_audit->p5_amt+$cash_audit->coins, 2, true) }}</b></td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="2">Total CSH/CHQ/FX</td><td class="text-right"><b title="{{$cash_audit->csh_cnt}} / {{$cash_audit->checks}} / {{$cash_audit->forex}}">{{ nf($cash_audit->csh_cnt+$cash_audit->checks+$cash_audit->forex, 2, true) }}</b></td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="2">Unliquidated C/A of {{ $dr->date->format('m/d') }}</td><td class="text-right">{{ nf($cash_audit->ca, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right" colspan="2">Cummulative (Short)/Over</td><td class="text-right">{{ nf($cash_audit->shrt_cumm, 2, true) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div><!-- end: .panel-body  -->
        </div><!--end: .panel.panel-default  -->
        @endif
       </div>

      <div class="col-md-4" style="margin-top: 20px;">
        @if(!is_null($cash_audit))
        <div class="panel panel-default">
          <div class="panel-heading">&nbsp;</div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-condensed table-striped" style="margin-top: 0;">
                <thead>
                  <tr>
                    <th>Collection</th>
                    <th class="text-right">Cash</th>
                    <th class="text-right">Cheque</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Backard C.Cards</td>
                    <td class="text-right">{{ nf($cash_audit->col_card, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_cardk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>BDO C.Cards</td>
                    <td class="text-right">{{ nf($cash_audit->col_bdo, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_bdok, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>Other Cards</td>
                    <td class="text-right">{{ nf($cash_audit->col_din, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_dink, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>{{ $cash_audit->col_cas }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_ca, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_cak, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>{{ $cash_audit->col_oths }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_othr, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_othrk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>{{ $cash_audit->coloth2s }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_oth2, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_oth2k, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>Total Collection</td>
                    <td class="text-right">{{ nf($cash_audit->tot_coll, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->tot_collk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td colspan="2" class="text-right">Grand Total :</td>
                    <td class="text-right">{{ nf($cash_audit->tot_coll+$cash_audit->tot_colk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>Func/F.O. Coll.</td>
                    <td class="text-right">{{ nf($cash_audit->col_food, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->col_foodk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>Func/F.O. Card</td>
                    <td class="text-right">{{ nf($cash_audit->col_foodc, 2, true) }}</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>S.Chit - Paid</td>
                    <td class="text-right">{{ nf($cash_audit->sig_salep, 2, true) }}</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>S.Chit - Unpaid</td>
                    <td class="text-right">{{ nf($cash_audit->sig_saleu, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->sig_salep+$cash_audit->sig_saleu, 2, true) }}</td>
                  </tr>
                  <tr>
                    <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td style="color: #95A5A6; font-weight: bold;">Disbursements</td>
                    <td style="color: #95A5A6; font-weight: bold;" class="text-right">Cash</td>
                    <td style="color: #95A5A6; font-weight: bold;" class="text-right">Cheque</td>
                  </tr>
                  <tr>
                    <td>Deposits</td>
                    <td class="text-right">{{ nf($cash_audit->deposit, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->depositk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>CashOut/Ref</td>
                    <td class="text-right">{{ nf($cash_audit->csh_out, 2, true) }}</td>
                    <td class="text-right">{{ nf($cash_audit->csh_outk, 2, true) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        
        @if(count($depslps)>0 || count($setslps)>0)
        <div class="panel panel-info">
          <div class="panel-heading">File Upload</div>
          <div class="panel-body">
            @if(count($depslps)>0)
            <div class="table-responsive">
              <table class="table table-condensed table-striped" style="margin-top: 0;">
                <thead>
                  <tr>
                    <th>Deposit Slip</th>
                    <th>Type</th>
                    <th class="text-right">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($depslps as $d)
                  <tr>
                    <td>
                      <a href="javascript:void(0)" target="popup" onclick='window.open("/images/depslp/{{ $d->lid() }}.jpg", "_blank", "width=auto,height=auto"); return false'>
                      {{ $d->filename }}
                      </a>
                    </td>
                    <td>
                      @if($d->type=='1')
                        Cash
                      @elseif ($d->type=='2')
                        Cheque
                      @else

                      @endif
                    </td>
                    <td class="text-right">{{ nf($d->amount) }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
        </div>
        @endif
        @endif
      </div>

      <div class="col-md-4" style="margin-top: 20px;">
        @if(!is_null($month_cashaudit) && !is_null($cash_audit))
        <div class="panel panel-primary">
          <div class="panel-heading">Cash Audit Summary ({{ $dr->date->copy()->startOfMonth()->format('M d') }}-{{ $dr->date->format('d Y') }})</div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-condensed table-striped" style="margin-top: 0;">
                <tbody>
                  <tr>
                    <td class="text-right">Sales - Cash</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->csh_sale, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">Charged</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->chg_sale, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">Signed</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->sig_sale, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>Total Sales</td>
                    <td colspan="2" class="text-right"><b>{{ nf($month_cashaudit->csh_sale+$month_cashaudit->chg_sale+$month_cashaudit->sig_sale, 2, true) }}</b></td>
                  </tr>
                  <tr>
                    <td>&nbsp</td><td></td><td></td>
                  </tr>
                  <tr>
                    <td style="color: #95A5A6; font-weight: bold;" class="text-center">Collections</td>
                    <td style="color: #95A5A6; font-weight: bold;" class="text-center">Cash</td>
                    <td style="color: #95A5A6; font-weight: bold;" class="text-center">Check</td>
                  </tr>
                  <tr>
                    <td class="text-right">Credit Card</td>
                    <td class="text-right">{{ nf($month_cashaudit->col_card, 2, true) }}</td>
                    <td class="text-right">{{ nf($month_cashaudit->col_cardk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">FO/Catering</td>
                    <td class="text-right">{{ nf($month_cashaudit->col_food, 2, true) }}</td>
                    <td class="text-right">{{ nf($month_cashaudit->col_foodk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">CA/Signed</td>
                    <td class="text-right">{{ nf($month_cashaudit->col_ca, 2, true) }}</td>
                    <td class="text-right">{{ nf($month_cashaudit->col_cak+$month_cashaudit->col_bdok+$month_cashaudit->col_dink, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">Others</td>
                    <td class="text-right">{{ nf($month_cashaudit->othr, 2, true) }}</td>
                    <td class="text-right">{{ nf($month_cashaudit->othrk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>Sub-total</td>
                    <td class="text-right"><b>{{ nf($month_cashaudit->tot_coll, 2, true) }}</b></td>
                    <td class="text-right"><b>{{ nf($month_cashaudit->tot_collk, 2, true) }}</b></td>
                  </tr>
                  <tr>
                    <td>Total</td>
                    <td colspan="2" class="text-right"><b>{{ nf($month_cashaudit->tot_coll+$month_cashaudit->tot_collk, 2, true) }}</b></td>
                  </tr>
                  <tr>
                    <td>&nbsp</td><td></td><td></td>
                  </tr>
                  <tr>
                    <td style="color: #95A5A6; font-weight: bold;" class="text-center"></td>
                    <td style="color: #95A5A6; font-weight: bold;" class="text-center">Cash</td>
                    <td style="color: #95A5A6; font-weight: bold;" class="text-center">Check</td>
                  </tr>
                  <tr>
                    <td class="text-right">Disbursement</td>
                    <td class="text-right">{{ nf($month_cashaudit->csh_disb, 2, true) }}</td>
                    <td class="text-right">{{ nf($month_cashaudit->chk_disb, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">Deposits</td>
                    <td class="text-right">{{ nf($month_cashaudit->deposit, 2, true) }}</td>
                    <td class="text-right">{{ nf($month_cashaudit->depositk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td class="text-right">CO/Refund</td>
                    <td class="text-right">{{ nf($month_cashaudit->csh_out, 2, true) }}</td>
                    <td class="text-right">{{ nf($month_cashaudit->csh_outk, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>Sub-total</td>
                    <td class="text-right"><b>{{ nf($month_cashaudit->tot_out+$month_cashaudit->csh_disb, 2, true) }}</b></td>
                    <td class="text-right"><b>{{ nf($month_cashaudit->tot_outk, 2, true) }}</b></td>
                  </tr>
                  <tr>
                    <td> Total Excl. Check Disb.</td>
                    <td colspan="2" class="text-right"><b>{{ nf($month_cashaudit->tot_out+$month_cashaudit->tot_outk+$month_cashaudit->csh_disb, 2, true) }}</b></td>
                  </tr>
                  <tr>
                    <td>&nbsp</td><td></td><td></td>
                  </tr>
                  <tr>
                    <td>Charged Sale</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->chg_sale*-1, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>Signed Sale</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->sig_sale*-1, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>&nbsp</td><td></td><td></td>
                  </tr>
                  <tr>
                    <td style="color: #95A5A6; font-weight: bold;">Summary</td><td></td><td></td>
                  </tr>
                  <tr>
                    <td>Ending Balance</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->comp_bal, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>Actual Balance</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->csh_cnt+$month_cashaudit->checks+$month_cashaudit->forex, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>(Short) / Over</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->shrt_ovr, 2, true) }}</td>
                  </tr>
                  <tr>
                    <td>&nbsp</td><td></td><td></td>
                  </tr>
                  <tr>
                    <td>Cummulative  (Short) / Over</td>
                    <td colspan="2" class="text-right">{{ nf($month_cashaudit->shrt_cumm, 2, true) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        @endif
      </div>


    </div>
  </div>
</div><!-- end: .container-fluid  -->
      
<div class="modal fade" id="mdl-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="mdl-formLabel">Filter Parameters</h4>
      </div>
      <div class="modal-body">
        <div>
          <div class="form-group">
            <label>Branch:</label>
            <div class="dropdown">
              <button id="dLabel" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-shop"></span>
                @if(is_null(($branch)))
                  <span class="br-code">Select Branch</span>
                  <span class="br-desc"></span>
                @else
                  <span class="br-code">{{ $branch->code }}</span>
                  <span class="br-desc">- {{ $branch->descriptor }}</span>
                @endif
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu br" aria-labelledby="dLabel" style="max-height: 400px; overflow-y: scroll;">
                @foreach($branches as $b)
                <li>
                  <a href="#" data-desc="{{ $b->descriptor }}" data-code="{{ $b->code }}" data-branchid="{{ $b->lid() }}">{{ $b->code }}</a>
                </li>
                @endforeach
              </ul>
            </div> <!-- .dropdown -->
          </div>
          
          <div class="form-group">
            <label>Month:</label>
            <div>
            <div class="btn-group" role="group">
            <label class="btn btn-default" for="mdl-dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="mdl-dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;">
            </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success pull-right mdl-btn-go" data-dismiss="modal" data-toggle="loader"><span class="gly gly-search"></span> Go </button>
        <button type="button" class="btn btn-link pull-right" data-dismiss="modal">Discard</button>
      </div>
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

  $(document).ready(function(){

  $('[data-toggle="tooltip"]').tooltip();

  $('#dp-date').datetimepicker({
    //defaultDate: "2016-06-01",
    format: 'MM/DD/YYYY',
    showTodayButton: true,
    ignoreReadonly: true,
    // orientation: "auto right",
    // viewMode: 'months'
  }).on('dp.change', function(e){
    var date = e.date.format('YYYY-MM-DD');
    $('#date').val(date);
    @if(!is_null(($branch)))
    loader();

    document.location.href = '/report/cash-audit?branchid='+$('#branchid').val()+'&date='+e.date.format('YYYY-MM-DD');
    @endif
  });

  $('#mdl-dp-date').datetimepicker({
    //defaultDate: "2016-06-01",
    format: 'MM/DD/YYYY',
    showTodayButton: true,
    ignoreReadonly: true,
    // viewMode: 'months'
  }).on('dp.change', function(e){
    var date = e.date.format('YYYY-MM-DD');
    $('#date').val(date);
  });

  initDatePicker();
  branchSelector();
  mdlBranchSelector();

  Highcharts.setOptions({
      chart: {
          style: {
              fontFamily: "Helvetica"
          }
      }
    });

    var arr = [];

    $('#container').highcharts({
      data: {
          table: 'datatable'
      },
      chart: {
        type: 'line',
        height: 300,
        //spacingRight: 20,
        marginTop: 40,
        //marginRight: 20,
        //marginRight: 20,
        zoomType: 'x',
        panning: true,
        panKey: 'shift'
      },
      colors: ['#15C0C2', '#B09ADB', '#5CB1EF', '#F49041', '#f15c80', '#F9CDAD', '#91e8e1','#D36A71', '#8d4653'],
      title: {
          text: ''
      },
      xAxis: [
        {
          gridLineColor: "#CCCCCC",
          type: 'datetime',
          //tickInterval: 24 * 3600 * 1000, // one week
          tickWidth: 0,
          gridLineWidth: 0,
          lineColor: "#C0D0E0", // line on X axis
          labels: {
            align: 'center',
            x: 3,
            y: 15,
            formatter: function () {
              //var date = new Date(this.value);
              //console.log(date.getDay());
              //console.log(date);
              return Highcharts.dateFormat('%b %Y',  this.value-86400000);
            }
          },
          plotLines: arr
        },
        { // slave axis
          type: 'datetime',
          linkedTo: 0,
          opposite: true,
          tickInterval: 7 * 24 * 3600 * 1000,
          tickWidth: 0,
          labels: {
            formatter: function () {
              /*
              arr.push({ // mark the weekend
                color: "#CCCCCC",
                width: 1,
                value: this.value-86400000,
                zIndex: 3
              });
                */
              //return Highcharts.dateFormat('%a', (this.value-86400000));
            }
          }
        }
      ],
      yAxis: [{ // left y axis
        min: 0,
        title: {
          text: null
        },
        labels: {
          align: 'left',
          x: 3,
          y: 16,
          format: '{value:.,0f}'
        },
          showFirstLabel: false
        },
        { // right y axis
        min: 0,
          title: {
            text: null
          },
          labels: {
            align: 'right',
            x: -10,
            y: 15,
            format: '{value:.,0f}'
          },
            showFirstLabel: false,
            opposite: true
          }], 
      legend: {
        align: 'left',
        verticalAlign: 'top',
        y: -10,
        floating: true,
        borderWidth: 0
      },
      tooltip: {
        shared: true,
        crosshairs: true
      },
      plotOptions: {
        series: {
          cursor: 'pointer',
          point: {
            events: {
              click: function (e) {
              console.log(Highcharts.dateFormat('%Y-%m-%d', this.x));
              /*
                hs.htmlExpand(null, {
                    pageOrigin: {
                        x: e.pageX,
                        y: e.pageY
                    },
                    headingText: this.series.name,
                    maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) +':<br/> '+
                        this.y +' visits',
                    width: 200
                });
              */
              }
            }
          },
          marker: {
            symbol: 'circle',
            radius: 3
          },
          lineWidth: 2,
          dataLabels: {
              enabled: false,
              align: 'right',
              crop: false,
              formatter: function () {
                console.log(this.series.index);
                return this.series.name;
              },
              x: 1,
              verticalAlign: 'middle'
          }
        }
      },
      exporting: {
        enabled: false
      },
      series: [
        {
          type: 'line',
          yAxis: 0
        }, {
          type: 'line',
          yAxis: 0
        }, {
          type: 'line',
          yAxis: 0
        }, {
          type: 'line',
          yAxis: 0,
        }, {
          type: 'line',
          yAxis: 0,
          visible: false
        }, {
          type: 'line',
          yAxis: 0,
          visible: false
        }, {
          type: 'line',
          yAxis: 0,
          visible: false
        }, {
          type: 'line',
          dashStyle: 'shortdot',
          yAxis: 1,
          dataLabels: {
            enabled: true,
            crop: false,
            format: '{y}%',
            verticalAlign: "bottom",
            align: "center"
          }
        }
      ]
    });


  
  
  $('.mdl-btn-go').on('click', function(){
    //loader();
    $('#filter-form').submit();
  });
  

 

    $('.show.toggle').on('click', function(){
      var div = $(this).siblings('div.show');
      if(div.hasClass('less')) {
        div.removeClass('less');
        div.addClass('more');
        $(this).text('show less');
      } else if(div.hasClass('more')) {
        div.removeClass('more');
        div.addClass('less');
        $(this).text('show more');
      }
    });

    

    


   	


    var submitForm  = function(){
      console.log('submit Form');
      $('#filter-form').submit();
    }
 
  });

  </script>

@endsection