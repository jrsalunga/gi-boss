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
<div class="container-fluid">
	
  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li class="active">Dashboard</li>
  </ol>



  <div style="margin-top:50px;" class="hidden-xs"></div>
  <div style="margin-top:10px;" class="visible-xs-block"></div>
  <div class="row">
    <div class="col-md-7">
      <div id="panel-top-sales" class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="fa fa-line-chart"></span> 
            <strong>Today's Top Branches</strong>
          </h3>
        </div>
        <div class="panel-body">
          <p class="text-right">
            <a href="/dailysales?date={{$dr->now->format('Y-m-d')}}" class="btn btn-default">
              <span class="glyphicon glyphicon-star"></span> 
              <span class="hidden-xs hidden-sm">Starred</span>
            </a> 
            <a href="/dailysales/all?date={{$dr->now->format('Y-m-d')}}" class="btn btn-default">
              <span class="glyphicon glyphicon-list-alt"></span> 
              <span class="hidden-xs hidden-sm">View All Branches</span>
            </a>
            <a href="/status/branch" class="btn btn-default">
              <span class="gly gly-cardio"></span> 
              <span class="hidden-xs hidden-sm">Branch Analytics</span>
            </a>
            <a href="/status/comparative" class="btn btn-default">
              <span class="gly gly-stats"></span> 
              <span class="hidden-xs hidden-sm">Comparative Analytics</span>
            </a>
          </p> 
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
                <th class="text-center">
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
                    {{ number_format($ds->today->sales,2) }} 
                    @if($ds->today->sign=='+')
                      <span style="font-size: 70%;" title="{{ number_format($ds->diff->sales,2) }}" class="glyphicon glyphicon-arrow-up text-success"></span>
                    @elseif($ds->today->sign=='-')
                      <span style="font-size: 70%;" title="{{ number_format($ds->diff->sales,2) }}" class="glyphicon glyphicon-arrow-down text-danger"></span>
                    @else

                    @endif
                  </td>
                  <td class="text-right">
                    {{ number_format($ds->yesterday->sales,2) }} 
                    @if($ds->yesterday->sign=='+')
                      <span style="font-size: 70%;" title="{{ number_format($ds->diff->sales1,2) }}" class="glyphicon glyphicon-arrow-up text-success"></span>
                    @elseif($ds->yesterday->sign=='-')
                      <span style="font-size: 70%;" title="{{ number_format($ds->diff->sales1,2) }}" class="glyphicon glyphicon-arrow-down text-danger"></span>
                    @else

                    @endif
                  </td>
                  <td class="text-right">{{ number_format($ds->otherday->sales,2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

          
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

          <p class="text-right">
            <a href="/storage" class="btn btn-default">
              <span class="gly gly-hdd"></span> 
              <span class="hidden-xs hidden-sm">Filing System</span>
            </a> 
            <a href="/storage/log" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span> 
              <span class="hidden-xs hidden-sm">View All Logs</span>
            </a>
            <!--
            <a href="/backup/delinquent" class="btn btn-default">
              <span class="gly gly-disk-remove"></span> 
              <span class="hidden-xs hidden-sm">Delinquent</span>
            </a> 
            -->
          </p>

          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
             
            <div class="panel panel-default">
              <div class="panel-heading" role="tab" id="headingThree">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <strong>Delinquent Branches</strong>
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
                        <th>Uploaded</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($delinquents[2] as $delinquent)
                      <tr>
                        <td title="{{ $delinquent['descriptor'] }}">{{ $delinquent['code'] }}</td>
                        <td>{{ $delinquent['filename'] }}</td>
                        <td title="{{ $delinquent['uploaddate']->format('D, M j, Y h:i A') }}">
                          <em><small>{{ diffForHumans($delinquent['uploaddate']) }}</small></em>
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
                          <em><small>{{ diffForHumans($delinquent['uploaddate']) }}</small></em>
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
                      <?php
                        $ctr = 0;
                        $x = 0;
                        $ave = ceil(count($delinquents[0]) / 3);
                      ?>
                       @for($i=0; $i < $ave; $i++) 
                          <tr>
                          <td title="{{ $delinquents[0][$i]['descriptor'] }}"> 
                            {{ $delinquents[0][$i]['code'] }}
                          </td>
                          <td title="{{ $delinquents[0][$ave+$i]['descriptor'] }}">
                            {{ $delinquents[0][$ave+$i]['code'] }}</td>
                          <td>
                          @if(count($delinquents[0]) <= ($ave+$ave+$i))
                            -
                          @else
                            <div title="{{ $delinquents[0][$ave+$ave+$i]['descriptor'] }}">
                            {{ $delinquents[0][$ave+$ave+$i]['code'] }}
                            </div>
                          @endif
                          </td>
                          </tr>
                        @endfor 
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

  
@endsection