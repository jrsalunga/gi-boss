@extends('master')

@section('title', '- Daily Purchases')

@section('body-class', 'daily-purchases')

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
    <li><a href="/component">Component</a></li>
    @if($branch)
    <li><a href="/component/purchases">Purchases</a></li>
    <li class="active">{{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }}</li>
    @else
    <li class="active">Purchases</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <!--
          <div class="btn-group pull-left" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs">Back</span>
            </a> 
          </div> end btn-grp -->
          <div class="btn-group visible-xs-inline-block pull-left" role="group">
            <div style="padding: 6px 12px;">
              <span class="gly gly-shop"></span>
              @if(is_null(($branch)))
                -
              @else
                <span>{{ $branch->code }}</span>
              @endif

              @if(empty(($filter->item)))
                
              @else
              <span class="label label-info" data-toggle="loader">
                {{ $filter->item }} 
                <a href="/component/purchases?branchid={{strtolower($branch->id)}}&amp;to={{$dr->to->format('Y-m-d')}}&amp;fr={{$dr->fr->format('Y-m-d')}}" title="Remove filter">
                <span style="color:#ccc; margin-right: 5px; border-radius: .25em;">x</span>
                </a>
              </span>
              @endif

              
            </div>
          </div>
          <div class="btn-group visible-xs-inline-block pull-right" role="group">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#mdl-form">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </button>
          </div>
          <!--
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix hidden-xs" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/component/purchases', 'method' => 'get', 'id'=>'filter-form']) !!}
            <!--
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid="'. $branch->id  }}">
            -->  
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="table" id="table" value="{{ $filter->table }}">
            <input type="hidden" name="item" id="item" value="{{ $filter->item }}">
            <input type="hidden" name="itemid" id="itemid" value="{{ $filter->id }}">
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

          <div class="btn-group hidden-xs" style="margin-left: 5px;">
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
      
          <div class="btn-group pull-right clearfix dp-container hidden-xs" role="group">
            
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

          <div class="btn-group hidden-xs" role="group">
          	<input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search filter">
          	<!--
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="http://example.com" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span id="date-type-name">Daily</span>
                  <span class="caret"></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="date-type">
                  <li><a href="#" data-date-type="daily">Daily</a></li>
                  <li><a href="#" data-date-type="weekly">Weekly</a></li>
                  <li><a href="#" data-date-type="monthly">Monthly</a></li>
                  <li><a href="#" data-date-type="quarterly">Quarterly</a></li>
                  <li><a href="#" data-date-type="yearly">Yearly</a></li>
                </ul>
              </div>
            </div>
          	-->
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')


    <div class="row">
      <div>

  <!-- Nav tabs -->
<div>
  
</div>
<div>
  <!-- Tab panes -->
  
</div>

</div>
      <?php
        $totpurchcost = 0;
        $totqty = 0;
        $totemp=0;
        $totpospurch=0;
      ?>
    	@if(is_null($purchases))

      @else
    
      <div class="col-md-12">
        <ul class="nav nav-pills" role="tablist">
          <li role="presentation" class="active">
            <a href="#items" aria-controls="items" role="tab" data-toggle="tab">
              <span class="gly gly-shopping-cart"></span>
              <span class="hidden-xs">
                Components
              </span>
            </a>
          </li>
          <li role="presentation">
            <a href="#stats" aria-controls="stats" role="tab" data-toggle="tab">
              <span class="gly gly-charts"></span>
              <span class="hidden-xs">
                Stats
              </span> 
            </a>
          </li>
          <!--
          <li role="presentation">
            <a href="#download" aria-controls="download" role="tab" data-toggle="tab">
              <span class="gly gly-disk-save"></span>
              <span class="hidden-xs hidden-sm">
              Download
              </span>
            </a>
          </li>
        -->
          <li role="presentation" style="float: right;">
            <div>
            Total : 
            <h3 id="tot-purch-cost" class="text-right" style="margin:0 0 10px 0;">0.00</h3>
            </div>
          </li>
          <li role="presentation" style="float: right;">
            <div>
            Emp Meal: 
            <h3 id="tot-emp" class="text-right" style="margin:0 100px 10px 0;">0.00</h3>
            </div>
          </li>
          <li role="presentation" style="float: right;">
            <div>
            Purchased Cost: 
            <h3 id="tot-pos-purch" class="text-right" style="margin:0 100px 10px 0;">0.00</h3>
            </div>
          </li>
        </ul>
      </div><!-- end: .col-md-12 -->

    	<div class="col-md-12">
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="items">
            <div class="table-responsive">
              <table class="table table-hover table-striped table-sort-data" style="margin-top: 0;">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Component</th>
                    <th>Qty</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right" class="text-right">Total Cost</th>
                    <th></th>
                    <th>Supp Ref #</th>
                    <th>Supplier</th>
                    <th>Comp. Cat. Code</th>
                    <th>Expense Code</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($purchases as $purchase)
                  <tr data-componentid="{{ $purchase->componentid }}" >
                    <td data-sort="{{ $purchase->date->format('Y-m-d') }}"><span style="cursor: help;" title="{{ $purchase->date->format('D M j, Y') }}" data-toggle="tooltip">
                      {{ $purchase->date->format('Y-m-d') }}</span>
                    </td>
                    <td data-sort="{{ strtolower($purchase->component) }}"><span data-toggle="tooltip" title="{{ $purchase->componentcode }} - {{ $purchase->component }}">{{ $purchase->component }}</span></td>
                    <td data-sort="{{ number_format($purchase->qty, 2,'.','') }}">{{ number_format($purchase->qty, 2,'.','')+0 }} 
                      <small class="text-muted">
                        {{ strtolower($purchase->uom)}}@if($purchase->qty>1 && substr(strtolower($purchase->uom), -1)!='s')s
                        @endif
                      </small>
                    </td>
                    <td class="text-right" data-sort="{{ number_format($purchase->ucost, 2,'.','') }}">{{ number_format($purchase->ucost,2) }}</td>
                    <td class="text-right" data-sort="{{ number_format($purchase->tcost, 2,'.','') }}">{{ number_format($purchase->tcost,2) }}</td>
                    <td class="text-center" data-sort="{{ $purchase->terms }}">
                      <span 
                      @if(strtolower($purchase->terms)=='c')
                        class="label label-success" title="Cash"
                      @elseif(strtolower($purchase->terms)=='k')
                        class="label label-info" title="Check"
                      @else
                        class="label label-default" title=""
                      @endif
                      
                      style="cursor: help;"  data-toggle="tooltip"><small>{{ $purchase->terms }}</small></span>
                    </td>
                    <td class="text-muted" data-sort="{{ $purchase->supprefno }}">
                      <small>
                        <a href="/invoice?supprefno={{ $purchase->supprefno }}&amp;date={{ $purchase->date->format('Y-m-d') }}&amp;branchid={{strtolower($branch->id)}}" target="_blank" {{ $purchase->save>0?'class=text-danger':'' }}>
                          {{ $purchase->supprefno }}
                        </a>
                      </small>
                    </td>
                    <td class="text-muted" data-sort="{{ strtolower($purchase->supplier) }}"><small data-toggle="tooltip" title="{{ $purchase->suppliercode }} - {{ $purchase->supplier }}" style="cursor:help;">{{ $purchase->supplier }}</small></td>
                    <td class="text-muted" data-sort="{{ strtolower($purchase->compcatcode) }}"><smalll data-toggle="tooltip" title="{{ $purchase->compcatcode }} - {{ $purchase->compcat }}" style="cursor:help;">{{ $purchase->compcatcode }}</small></td>
                    <td class="text-muted" data-sort="{{ strtolower($purchase->expensecode) }}"><smalll data-toggle="tooltip" title="{{ $purchase->expensecode }} - {{ $purchase->expense }}" style="cursor:help;">{{ $purchase->expensecode }}</small></td>
                    <td data-sort="{{ $purchase->expscatcode+0 }}">
                      <span class="label 
                    @if($purchase->expscatcode=='05')
                      label-warning
                    @elseif($purchase->expscatcode=='08')
                      label-primary
                    @else
                      label-default
                    @endif
                     pull-right" title="{{ $purchase->expscatcode }} - {{ $purchase->expscat }}" style="cursor: help;" data-toggle="tooltip" >{{ $purchase->expscatcode }}</span>
                    </td>
                  </tr>
                  <?php
                    $totpurchcost += $purchase->tcost;
                    $totqty += $purchase->qty;

                    if ($purchase->componentid=='11E8BB3635ABF63DAEF21C1B0D85A7E0')
                      $totemp += $purchase->tcost;
                  ?>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td title="Total Quantity">
                      @if(isset($_GET['table']) && $_GET['table']==='component' && count($purchases)>0)
                        <strong>{{ number_format($totqty, 2,'.','')+0 }}</strong>
                        <small class="text-muted">
                        {{ strtolower($purchase->uom)}}@if($totqty>1 && substr(strtolower($purchase->uom), -1)!='s')s
                        @endif
                      </small>
                      @endif
                    </td>
                    <td class="text-right" title="Average Unit Cost">
                      @if(isset($_GET['table']) && $_GET['table']==='component' && $totqty>0 && count($purchases)>0)
                        <strong>{{ number_format($totpurchcost/$totqty, 2) }}</strong>
                      @endif
                    </td>
                    <td class="text-right" title="Total Purchased Cost">
                      <strong>{{ number_format($totpurchcost, 2) }}</strong>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                </tfoot>
              </table>
            </div> <!-- end: .table-responsive -->
          </div>
          <div role="tabpanel" class="tab-pane" id="stats">
            <!-- Supplier Panel -->
            <div class="panel panel-default">
              <div class="panel-heading">Supplier</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-md-5 col-md-push-7">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-supplier" data-table="#supplier-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                  <div class="col-xs-12 col-md-7 col-md-pull-5">
                    <div class="row">
                      <div class="table-responsive">
                        <div class="show less">
                          <table class="tb-supplier-data table table-condensed table-hover table-striped table-sort">
                            <thead>
                              <tr>
                                <th>Code</th>
                                <th>Supplier</th>
                                <th></th>
                                <th class="text-right">Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $totsup=0; ?>
                              @foreach($suppliers as $supplier)
                                <tr data-supplierid="{{ $supplier->id }}">
                                  <td>{{ $supplier->code }}</td>
                                  <td>{{ $supplier->descriptor }}</td>
                                  <td>
                                    <!-- <span 
                                      @if(strtolower($supplier->terms)=='k')
                                        class="label label-info" title="Check"
                                      @else(strtolower($supplier->terms)=='c')
                                        class="label label-success" title="Cash"
                                      @endif
                                      
                                      style="cursor: help;"  data-toggle="tooltip"><small>{{ $supplier->terms }}</small>
                                    </span> -->
                                  </td>
                                  <td class="text-right">{{ number_format($supplier->tcost, 2) }}</td>
                                </tr>
                                <?php $totsup+=$supplier->tcost; ?>
                              @endforeach
                            </tbody>
                            <tfoot><tr><td></td><td></td><td></td><td class="text-right"><b>{{number_format($totsup,2)}}</b></td></tr></tfoot>
                          </table>
                        </div>
                        @if(count($suppliers)>6)
                        <span class="label label-info show toggle">show more</span>
                        @endif
                        <table id="supplier-data" style="display:none;">
                            <thead>
                              <tr>
                                <th>Supplier</th>
                                <th>Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($suppliers as $supplier)
                                <tr>
                                  <td>{{ $supplier->descriptor }}</td>
                                  <td>{{ $supplier->tcost }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                      </div><!-- end: .table-responsive -->
                    </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
            <!-- Invoices Panel -->
            <div class="panel panel-default">
              <div class="panel-heading">Invoices</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-md-5 col-md-push-7">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-invoice" data-table="#invoice-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                  <div class="col-xs-12 col-md-7 col-md-pull-5">
                    <div class="row">
                      <div class="table-responsive">
                        <div class="show less">
                          <table class="tb-invoice-data table table-condensed table-hover table-striped table-sort">
                            <thead>
                              <tr>
                                <th>Code</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th class="text-right">Supp. Ref. #</th>
                                <th></th>
                                <th class="text-right">Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $totsup=0; ?>
                              @foreach($invoices as $invoice)
                                <tr data-supplierid="{{ $supplier->id }}">
                                  <td>{{ $invoice->code }}</td>
                                  <td>{{ $invoice->descriptor }}</td>
                                  <td>{{ $invoice->date->format('Y-m-d') }}</td>
                                  <td class="text-right">
                                    <a href="/invoice?supprefno={{ $invoice->supprefno }}&amp;date={{ $invoice->date->format('Y-m-d') }}&amp;branchid={{strtolower($invoice->branchid)}}" target="_blank" {{ $invoice->save>0?'class=text-danger':'' }}>
                                      {{ $invoice->supprefno }}
                                    </a>
                                  </td>
                                  <td>
                                     <span 
                                    @if(strtolower($invoice->terms)=='k')
                                      class="label label-info" title="Check"
                                    @else(strtolower($invoice->terms)=='c')
                                      class="label label-success" title="Cash"
                                    @endif
                                    
                                    style="cursor: help;"  data-toggle="tooltip"><small>{{ $invoice->terms }}</small></span>
                                    -
                                    <small class="text-muted">
                                      {{ Config::get('giligans.paytype.'.$invoice->paytype) }} 
                                    </small>
                                  </td>
                                  <td class="text-right">{{ number_format($invoice->tcost, 2) }}</td>
                                </tr>
                                <?php $totsup+=$invoice->tcost; ?>
                              @endforeach
                            </tbody>
                            <tfoot><tr><td></td><td></td><td></td><td></td><td></td><td class="text-right"><b>{{number_format($totsup,2)}}</b></td></tr></tfoot>
                          </table>
                        </div>
                        @if(count($invoices)>6)
                        <span class="label label-info show toggle">show more</span>
                        @endif
                        <table id="invoice-data" style="display:none;">
                            <thead>
                              <tr>
                                <th>Invoice</th>
                                <th>Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($invoices as $invoice)
                                <tr>
                                  <td>{{ $invoice->code }} - {{ $invoice->supprefno }}</td>
                                  <td>{{ $invoice->tcost }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                      </div><!-- end: .table-responsive -->
                    </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
             <!-- Expenses Payment/Terms -->
            <div class="panel panel-default">
              <div class="panel-heading">Payment/Terms</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-md-5 col-md-push-7">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-payment" data-table="#payment-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                  <div class="col-xs-12 col-md-7 col-md-pull-5">
                    <div class="row">
                      <div class="table-responsive">
                        <div class="show less">
                          <table class="tb-payment-data table table-condensed table-hover table-striped table-sort">
                            <thead>
                              <tr>
                                <th>Code</th>
                                <th>Mode of Payment</th>
                                <th class="text-right">Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $totpay=0; ?>
                              @foreach($payments as $payment)
                                <tr>
                                  <td>{{ $payment->terms }}</td>
                                  <td>
                                  @if($payment->terms=='C')
                                    Cash
                                  @elseif($payment->terms=="K")
                                    Check
                                  @else
                                    -
                                  @endif
                                  </td>
                                  <td class="text-right">{{ number_format($payment->tcost, 2) }}</td>
                                </tr>
                                <?php $totpay+=$payment->tcost; ?>
                              @endforeach
                            </tbody>
                            <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($totpay,2)}}</b></td></tr></tfoot>
                          </table>
                        </div>
                        <!--
                        <span class="label label-info show toggle">show more</span>
                        -->
                        <table id="payment-data" style="display:none;">
                            <thead>
                              <tr>
                                <th>Category</th>
                                <th>Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($payments as $payment)
                                <tr>
                                  <td>
                                  @if($payment->terms=='C')
                                    Cash
                                  @elseif($payment->terms=="K")
                                    Check
                                  @else
                                    -
                                  @endif
                                  </td>
                                  <td>{{ $payment->tcost}}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                      </div><!-- end: .table-responsive -->
                    </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
             <!-- Expenses Category Panel -->
            <div class="panel panel-default">
              <div class="panel-heading">Expense Category</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-md-5 col-md-push-7">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-expscat" data-table="#expscat-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                  <div class="col-xs-12 col-md-7 col-md-pull-5">
                    <div class="row">
                      <div class="table-responsive">
                        <div class="show less">
                          <table class="tb-expscat-data table table-condensed table-hover table-striped table-sort">
                            <thead>
                              <tr>
                                <th>Code</th>
                                <th>Category</th>
                                <th class="text-right">Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $totexpc=0; ?>
                              @foreach($expscats as $expscat)
                                <tr>
                                  <td>
                                    @if($expscat->expscatcode=='05')
                                      <span class="label label-warning">{{ $expscat->expscatcode }}</span>
                                    @elseif($expscat->expscatcode=='08')
                                      <span class="label label-primary">{{ $expscat->expscatcode }}</span>
                                    @else
                                      {{ $expscat->expscatcode }}
                                    @endif
                                  </td>
                                  <td>{{ $expscat->expscat }}</td>
                                  <td class="text-right">{{ number_format($expscat->tcost, 2) }}</td>
                                </tr>
                                <?php $totexpc+=$expscat->tcost; ?>
                              @endforeach
                            </tbody>
                            <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($totexpc,2)}}</b></td></tr></tfoot>
                          </table>
                        </div>
                        <!--
                        <span class="label label-info show toggle">show more</span>
                        -->
                        <table id="expscat-data" style="display:none;">
                            <thead>
                              <tr>
                                <th>Category</th>
                                <th>Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($expscats as $expscat)
                                <tr>
                                  <td>{{ $expscat->expscat }}</td>
                                  <td>{{ $expscat->tcost}}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                      </div><!-- end: .table-responsive -->
                    </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
            <!-- Expenses Panel -->
            <div class="panel panel-default">
              <div class="panel-heading">Expense</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-md-5 col-md-push-7">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-expense" data-table="#expense-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                  <div class="col-xs-12 col-md-7 col-md-pull-5">
                    <div class="row">
                      <div class="table-responsive">
                        <div class="show less">
                          <table class="tb-expense-data table table-condensed table-hover table-striped table-sort">
                            <thead>
                              <tr>
                                <th>Code</th>
                                <th>Expense</th>
                                <th></th>
                                <th class="text-right">Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $totexp=0; ?>
                              @foreach($expenses as $expense)
                                <tr>
                                  <td>{{ $expense->expensecode }}</td>
                                  <td>{{ $expense->expense }}</td>
                                  <td>
                                    @if($expense->expscatcode=='05')
                                      <span class="label label-warning">{{ $expense->expscatcode }}</span>
                                    @elseif($expense->expscatcode=='08')
                                      <span class="label label-primary">{{ $expense->expscatcode }}</span>
                                    @else
                                      {{ $expense->expscatcode }}
                                    @endif
                                  </td>
                                  <td class="text-right">{{ number_format($expense->tcost, 2) }}</td>
                                </tr>
                                <?php $totexp+=$expense->tcost; ?>
                              @endforeach
                            </tbody>
                            <tfoot><tr><td></td><td></td><td></td><td class="text-right"><b>{{number_format($totexp,2)}}</b></td></tr></tfoot>
                          </table>
                        </div>
                        @if(count($expenses)>6)
                        <span class="label label-info show toggle">show more</span>
                        @endif
                        <table id="expense-data" style="display:none;">
                            <thead>
                              <tr>
                                <th>Expense</th>
                                <th>Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($expenses as $expense)
                                <tr>
                                  <td>{{ $expense->expense }}</td>
                                  <td>{{ $expense->tcost }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                      </div><!-- end: .table-responsive -->
                    </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
            <!-- Component Category Panel -->
            <div class="panel panel-default">
              <div class="panel-heading">Component Category</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-md-5 col-md-push-7">
                    <div class="graph-container pull-right">
                    <div id="graph-pie-compcat" data-table="#compcat-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                  <div class="col-xs-12 col-md-7 col-md-pull-5">
                    <div class="row">
                      <div class="table-responsive">
                        <div class="show less">
                          <table class="tb-compcat-data table table-condensed table-hover table-striped table-sort">
                            <thead>
                              <tr>
                                <th>Category</th>
                                <th>Expense Code</th>
                                <th class="text-right">Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $totcat = 0; ?>
                              @foreach($compcats as $compcat)
                                <tr>
                                  <td>{{ $compcat->compcat }}</td>
                                  <td>{{ $compcat->expensecode }}</td>
                                  <td class="text-right">{{ number_format($compcat->tcost, 2) }}</td>
                                </tr>
                                <?php $totcat+=$compcat->tcost; ?>
                              @endforeach
                            </tbody>
                            <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($totcat,2)}}</b></td></tr></tfoot>
                          </table>
                        </div>
                        <span class="label label-info show toggle">show more</span>
                        <table id="compcat-data" style="display: none;">
                            <thead>
                              <tr>
                                <th>Category</th>
                                <th>Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($compcats as $compcat)
                                <tr>
                                  <td>{{ $compcat->compcat }}</td>
                                  <td>{{ $compcat->tcost }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                      </div><!-- end: .table-responsive -->
                    </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
             <!-- Component Panel -->
            <div class="panel panel-default">
              <div class="panel-heading">Components</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-xs-12 col-md-5 col-md-push-7">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-component" data-table="#component-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                  <div class="col-xs-12 col-md-7 col-md-pull-5">
                    <div class="row">
                      <div class="table-responsive">
                        <span class="label label-info show toggle">show more</span>
                        <div class="show less">
                          <table class="tb-component-data table table-condensed table-hover table-striped table-sort">
                            <thead>
                              <tr>
                                <th>Component</th>
                                <th class="text-right">Tran. Count</th>
                                <th class="text-right">Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $totcomp = 0; ?>
                              @foreach($components as $component)
                                <tr>
                                  <td>{{ $component->component }}</td>
                                  <td class="text-right">{{ $component->tran_cnt }}</td>
                                  <td class="text-right">{{ number_format($component->tcost, 2) }}</td>
                                </tr>
                                <?php $totcomp+=$component->tcost; ?>
                              @endforeach
                            </tbody>
                            <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($totcomp,2)}}</b></td></tr></tfoot>
                          </table>
                        </div>
                        <table id="component-data" style="display:none;">
                            <thead>
                              <tr>
                                <th>Component</th>
                                <th>Total Cost</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($components as $component)
                                <tr>
                                  <td>{{ $component->component }}</td>
                                  <td>{{ $component->tcost }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                      </div><!-- end: .table-responsive -->
                    </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
            
           
           
            
            
          </div>
          <div role="tabpanel" class="tab-pane" id="download">Download</div>
        </div>
        
      </div><!-- end: .col-md-12 -->
			@endif
		</div> <!-- end: .row table -->
</div>


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
            <label>Date Range:</label>
            <div>
            <div class="btn-group" role="group">
            <label class="btn btn-default" for="mdl-dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="mdl-dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="mdl-dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="mdl-dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            </div>
            </div>
          </div>

          <div class="form-group">
            <label>Filter:</label>
            <input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search filter">
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

  initDatePicker();
  branchSelector();
  mdlBranchSelector();

  @if(!is_null($purchases))
  var componentChart = new Highcharts.Chart(getOptions('graph-pie-component', 'component-data'));
  var compcatChart = new Highcharts.Chart(getOptions('graph-pie-compcat', 'compcat-data'));
  var expensetChart = new Highcharts.Chart(getOptions('graph-pie-expense', 'expense-data'));
  var expscatChart = new Highcharts.Chart(getOptions('graph-pie-expscat', 'expscat-data'));
  var supplierChart = new Highcharts.Chart(getOptions('graph-pie-supplier', 'supplier-data'));
  var paymentChart = new Highcharts.Chart(getOptions('graph-pie-payment', 'payment-data'));
  var invoiceChart = new Highcharts.Chart(getOptions('graph-pie-invoice', 'invoice-data'));
  @endif

  /*
  $('.btn-go').on('click', function(){
    loader();
  });
  */
  
  $('.mdl-btn-go').on('click', function(){
    //loader();
    $('#filter-form').submit();
  });
  

  $('#tot-purch-cost').text('{{ number_format($totpurchcost, 2) }}');
  $('#tot-emp').text('{{ number_format($totemp, 2) }}');
  $('#tot-pos-purch').text('{{ number_format($totpurchcost-$totemp, 2) }}');
    

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

    

    $.widget("custom.autocomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
      },
      _renderMenu: function(ul, items) {
        var that = this,
          currentCategory = "";
        $.each(items, function(index, item) {
          var li;
          if (item.category != currentCategory) {
            ul.append('<li class="ui-autocomplete-category"><span class="label label-success">' + item.category + '</span></li>' );
            currentCategory = item.category;
          }
          li = that._renderItemData(ul, item);
          if (item.category) {
            li.attr( "aria-label", item.category + " : " + item.label);
          }
        });
      }
    });


   	$(".searchfield").autocomplete({
     	source: function(request, response) {
        var bid = $('#branchid').val();
      	$.ajax({
        	type: 'GET',
        	url: "/api/search/component",
          dataType: "json",
          data: {
            maxRows: 25,
            q: request.term,
            branchid : bid
          },
          success: function(data) {
            response($.map(data, function(item) {
              return {
                //label: item.item + ', ' + item.table,
                label: item.item,
                value: item.item,
                category: item.table,
								id: item.id
              }
            }));
          }
        });
      },
      minLength: 2,
      select: function(event, ui) {
  			//console.log(ui);
        //log( ui.item ? "Selected: " + ui.item.label : "Nothing selected, input was " + this.value);
  			$("#table").val(ui.item.category); /* set the selected id */
  			$("#item").val(ui.item.value); /* set the selected id */
  			$("#itemid").val(ui.item.id); /* set the selected id */
      },
      open: function() {
        $( this ).removeClass("ui-corner-all").addClass("ui-corner-top");
  			$("#table").val(''); /* set the selected id */
  			$("#item").val(''); /* set the selected id */
  			$("#itemid").val(''); /* set the selected id */
      },
      close: function() {
          $( this ).removeClass("ui-corner-top").addClass("ui-corner-all");
      },
    	messages: {
      	noResults: '',
      	results: function() {}
    	}
    }).on('blur', function(e){
      if ($(this).val().length==0) {
        $( this ).removeClass("ui-corner-all").addClass("ui-corner-top");
        $("#table").val(''); /* set the selected id */
        $("#item").val(''); /* set the selected id */
        $("#itemid").val(''); /* set the selected id */
      }

      //setTimeout(submitForm, 1000);
    });


    var submitForm  = function(){
      console.log('submit Form');
      $('#filter-form').submit();
    }
 
  });

  </script>

@endsection