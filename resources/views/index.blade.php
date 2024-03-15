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
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
	
  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li class="active">Dashboard</li>
  </ol>



  
  <div class="row">
    <div class="col-md-7">
      <div id="panel-top-sales" class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="fa fa-line-chart"></span> 
            <strong>Today's Top Branch</strong>
          </h3>
        </div>
        <div class="panel-body">
          <div class="text-right">
            <a href="/dailysales?date={{$dr->now->format('Y-m-d')}}" class="btn btn-default btn-sm">
              <span class="glyphicon glyphicon-star"></span> 
              <span class="hidden-xs">Starred</span>
            </a> 
            <a href="/dailysales/all?date={{$dr->now->format('Y-m-d')}}" class="btn btn-default btn-sm">
              <span class="glyphicon glyphicon-list-alt"></span> 
              <span class="hidden-xs">All Branch Sales</span>
            </a>
            <a href="/status/branch" class="btn btn-default btn-sm">
              <span class="gly gly-cardio"></span> 
              <span class="hidden-xs">Branch Daily Sales</span>
            </a>
            <!-- <a href="/status/comparative" class="btn btn-default btn-sm">
              <span class="gly gly-stats"></span> 
              <span class="hidden-xs">Comparative Analytics</span>
            </a> -->
            <!--
            <a href="/component/price/comparative" class="btn btn-default btn-sm">
              <span class="fa fa-line-chart"></span> 
              <span><span class="hidden-xs">Component Cost</span> <small><span class="label label-success">new</span></small></span>
            </a>
            -->
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-line-chart"></span>
                <span class="hidden-xs">Reports </span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li><a href="/product/sales">Sales Summary <small class="text-muted">(By Branch)</small></a></li>
                <li><a href="/component/purchases">Purchase Summary <small class="text-muted">(By Branch)</small></a></li>
                <li><a href="/component/transfer/daily">Stock Transfer Summary <small class="text-muted">(By Branch)</small></a></li>
                <li><a href="/component/begbal">Beginning Stock Summary <small class="text-muted">(By Branch)</small></small></a></li>
                <li><a href="/product/sales/comparative">Product Sales <small class="text-muted">(All Branch)</small></a></li>
                <li><a href="/component/price/comparative">Component Unit Cost <small class="text-muted">(All Branch)</small></a></li>
                <!-- <li><a href="/report/pnl/daily">Daily P&amp;L Summary <small class="text-muted">(By Branch)</small></a></li> -->
                <li><a href="/report/pnl-summary">Monthly P&amp;L Summary <small class="text-muted">(By Branch)</small></a></li>
                <li><a href="/kitlog/month">Kitchen Log Month Summary </a></li>
                <li><a href="report/sales/charges/dr-all">Charges Sales <small class="text-muted">(By Company)</small></a></li>
                <li><a href="/delivery/all">Delivery Sales <small class="text-muted">(All Branch)</small></a></li>
                <li><a href="/saletype/branch">Sales Type <small class="text-muted">(By Branch)</small></a></li>
                <li><a href="/report/daily-cash-flow">Cash Flow <small class="text-muted">(All Branch)</small></a></li>
                <li><a href="/report/cash-audit">Daily Cash Audit <small class="text-muted">(By Branch)</small></a></li>
                <li><a href="https://bit.ly/GI-HRPayrollData" target="_blank">HR Payroll Data <small class="text-muted">(All Branch)</small> <small><span class="label label-danger">new</span></small></a></li>
              </ul>
            </div>
          </div> 
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Branch</th>
                <th class="text-center">
                  <div style="font-weight: normal; font-size: 11px;">
                  Today
                  </div>
                    {{ $dr->now->format('D, M j') }}
                </th>
                <th class="text-center">
                  <div style="font-weight: normal; font-size: 11px;">
                  Yesterday
                  </div>
                    {{ $dr->now->copy()->subDay()->format('D, M j') }}
                </th>
                <th class="text-center hidden-xs">
                    {{ $dr->now->copy()->subDay(2)->format('D, M j') }}
                  <div style="font-weight: normal; font-size: 11px;">
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach($dailysales as $ds)
                <tr>
                  <td>{{ $ds->branch->code }}</td>
                  <td class="text-right">
                    @if($ds->today->slsmtd_totgrs>0)
                      <a href="/product/sales?branchid={{$ds->branch->lid()}}&fr={{$dr->now->format('Y-m-d')}}&to={{$dr->now->format('Y-m-d')}}" data-toggle="loader">
                        {{ number_format($ds->today->sales,2) }}</a>
                      @if($ds->today->sign=='+')
                        <span style="font-size: 70%;" title="{{ number_format($ds->diff->sales,2) }}" class="glyphicon glyphicon-arrow-up text-success"></span>
                      @elseif($ds->today->sign=='-')
                        <span style="font-size: 70%;" title="{{ number_format($ds->diff->sales,2) }}" class="glyphicon glyphicon-arrow-down text-danger"></span>
                      @else

                      @endif
                    @else 
                      {{ number_format($ds->today->sales,2) }}
                    @endif
                  </td>
                  <td class="text-right">
                    @if($ds->yesterday->slsmtd_totgrs>0)
                      <a href="/product/sales?branchid={{$ds->branch->lid()}}&fr={{$ds->yesterday->date->format('Y-m-d')}}&to={{$ds->yesterday->date->format('Y-m-d')}}" data-toggle="loader">
                        {{ number_format($ds->yesterday->sales,2) }}</a>
                      @if($ds->yesterday->sign=='+')
                        <span style="font-size: 70%;" title="{{ number_format($ds->diff->sales1,2) }}" class="glyphicon glyphicon-arrow-up text-success"></span>
                      @elseif($ds->yesterday->sign=='-')
                        <span style="font-size: 70%;" title="{{ number_format($ds->diff->sales1,2) }}" class="glyphicon glyphicon-arrow-down text-danger"></span>
                      @else

                      @endif
                    @else 
                      {{ number_format($ds->yesterday->sales,2) }}
                    @endif
                  </td>
                  <td class="text-right  hidden-xs">
                  @if(is_null($ds->otherday))
                    0.00
                  @else
                    @if($ds->otherday->slsmtd_totgrs>0)
                    <a href="/product/sales?branchid={{$ds->branch->lid()}}&fr={{$ds->otherday->date->format('Y-m-d')}}&to={{$ds->otherday->date->format('Y-m-d')}}" data-toggle="loader">
                      {{ number_format($ds->otherday->sales,2) }}</a>
                    @else 
                      {{ number_format($ds->otherday->sales,2) }}
                    @endif
                  @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>

          &nbsp;&nbsp;<a href="/report/trends/daily"><small>view all</small></a>

          
        </div>
      </div>
    </div> <!-- end: col-md-7 -->
    <div class="col-md-5">
      <div id="panel-latest-backup" class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-upload"></span> 
            <strong>Cashier's Dropbox</strong>
          </h3>
        </div>
        <div class="panel-body">

          <div style="margin-bottom: 10px;">
            
            <!--
            <a href="/storage" class="btn btn-default btn-sm">
              <span class="gly gly-hdd"></span> 
              <span class="hidden-xs">Filing System</span>
            </a> 

            <a href="/backup/checklist" class="btn btn-default btn-sm">
              <span class="fa fa-calendar-check-o"></span> 
              <span class="hidden-xs">Backup Checklist</span>
            </a>
            <a href="/storage/log" class="btn btn-default btn-sm">
              <span class="glyphicon glyphicon-th-list"></span> 
              <span class="hidden-xs">Backup Logs</span>
            </a>
            -->
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-hdd"></span>
                <span class="hidden-xs">Filing System</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="/storage"><span class="fa fa-file-archive-o"></span> Backup</a></li>
                <li><a href="/depslp"><span class="fa fa-bank"></span> Deposit Slip</a></li>
                <li><a href="/setslp"><span class="fa fa-credit-card"></span> Card Settlement Slip</a></li>
              </ul>
            </div>

            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-calendar-check-o"></span>
                <span class="hidden-xs">Checklist</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="/backup/checklist"><span class="fa fa-file-archive-o"></span> Backup</a></li>
                <li><a href="/depslp/checklist"><span class="fa fa-bank"></span> Deposit Slip</a></li>
                <li><a href="/setslp/checklist"><span class="fa fa-credit-card"></span> Card Settlement Slip</a></li>
                <li><a href="/kitlog/checklist"><span class="gly gly-cutlery"></span> Kitchen Log</a></li>
                <li><a href="/mansked"><span class="gly gly-notes-2"></span> Mansked</a></li>
              </ul>
            </div>

            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-th-list"></span>
                <span class="hidden-xs">Logs</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="/storage/log"><span class="fa fa-file-archive-o"></span> Backup</a></li>
                <li><a href="/depslp/log"><span class="fa fa-bank"></span> Deposit Slip</a></li>
                <li><a href="/setslp/log"><span class="fa fa-credit-card"></span> Card Settlement Slip</a></li>
                <li><a href="/timesheet"><span class="gly gly-clock"></span> Timesheet</a></li>
                <li><a href="/timelog"><span class="gly gly-stopwatch"></span> Timelogs</a></li>
              </ul>
            </div>
            <!--
            <a href="/timesheet" class="btn btn-default btn-sm">
              <span class="gly gly-stopwatch"></span> 
              <span class="hidden-xs hidden-sm">Timesheet <small><span class="label label-success">new</span></small></span>
            </a> 
            -->
            @if(in_array(session('user.id'), ['41F0FB56DFA811E69815D19988DDBE1E', '404C5DB436CA11E6B8E2CFB10D2BAFEE', '9B973527DEB811E5B23DCEEA2C1C08D2']))
            <a href="/employee/tracker" class="btn btn-default btn-sm">
              <span class="fa fa-street-view"></span> 
              <span class="hidden-xs hidden-sm">Tracker <small></small></span>
            </a> 
            @endif
            
          </div>

          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
             
            <div class="panel panel-default">
              <div class="panel-heading" role="tab" id="headingThree">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <strong>Delinquent Branch</strong>
                  </a>
                  <span class="badge">{{ count($delinquents[2]) }}</span>
                </h4>
              </div>
              <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
                <div class="panel-body">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th>Branch</th>
                        <th>Backup</th>
                        <th>Delayed</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($delinquents[2] as $delinquent)
                      <tr>
                        <td title="{{ $delinquent['descriptor'] }}">{{ $delinquent['code'] }}</td>
                        <td>{{ $delinquent['filename'] }}</td>
                        <td title="uploaded: {{ $delinquent['uploaddate']->format('D, M j, Y h:i A') }}">
                          <em>
                            <small>
                            <time class="_timeago" datetime="{{ $delinquent['uploaddate']->toIso8601String() }}" title="uploaded: {{ $delinquent['uploaddate']->format('D, M j, Y h:i A') }}">
                              {{ diffForHumans($delinquent['date']) }}
                            </time>
                            </small>
                          </em>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div> <!-- end: panel for delinquent -->        
            <div class="panel panel-default">
              <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                  <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <strong>Latest Backup</strong>
                  </a>
                  <span class="badge">{{ count($delinquents[1]) }}</span>
                </h4>
              </div>
              <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th>Branch</th>
                        <th>Backup</th>
                        <th>Uploaded</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($delinquents[1] as $delinquent)
                      <tr>
                        <td title="{{ $delinquent['descriptor'] }}">{{ $delinquent['code'] }}</td>
                        <td>{{ $delinquent['filename'] }}</td>
                        <td title="{{ $delinquent['uploaddate']->format('D, M j, Y h:i A') }}">
                          <em>
                            <small>
                            <time class="_timeago" datetime="{{ $delinquent['uploaddate']->format('Y-m-dTH:i:sZ') }}" title="uploaded: {{ $delinquent['uploaddate']->format('D, M j, Y h:i A') }}">
                            {{ diffForHumans($delinquent['uploaddate']) }} ago
                            </time>
                            </small>
                          </em>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div> <!-- end: panel for latest -->
            <div class="panel panel-default">
              <div class="panel-heading" role="tab" id="headingTwo">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <strong>Did Not Upload</strong>
                  </a>
                  <span class="badge">{{ count($delinquents[0]) }}</span>
                </h4>
              </div>
              <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                <div class="panel-body">
                  <table class="table table-striped">
                    <!--
                    <thead>
                      <tr>
                        <th>Branch</th>
                        <th>Backup</th>
                        <th>Uploaded</th>
                      </tr>
                    </thead>
                    -->
                    <tbody>
                      @foreach($delinquents[0] as $delinquent)
                          <tr>
                          <td> 
                            {{ $delinquent['code'] }}
                          </td>
                          
                          </tr>
                        @endforeach 
                    </tbody>
                  </table>
                </div>
              </div>
            </div> <!-- end: panel for did not upload -->   
            
          </div> <!-- end: panel-group -->
        </div>
      </div> 
    </div>  <!-- end: col-md-5 -->
    
  



  </div> <!-- end: row -->
@endsection














@section('js-external')
  
  <script src="/js/vendors-common.min.js"></script>
 	<script src="/js/dr-picker.js"></script>
  
@endsection


