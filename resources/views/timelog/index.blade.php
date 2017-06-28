@extends('master')

@section('title', '- Timelogs')

@section('body-class', 'timelogs')

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
<?php
$flag = false;
?>
@section('container-body')
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    @if (request()->has('search'))
      <li><a href="/timelog">Timelog</a></li>
      <?php
        $x = explode(':', request()->input('search'));
        if (isset($x[1])) {
          if ($x[0]=='branch.code') {
            $b = strtoupper($x[1]);
            $flag = true;
          } else
            $b = $x[1];
        }
      ?>
      <li class="active">{{ strtoupper($x[1]) }}</li>
    @else 
      <li class="active">Timelog</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <!--
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
            -->
            @if($flag)
              <a href="/mansked?search=branch.code:{{strtolower($b)}}" class="btn btn-default">
            @else
              <a href="/mansked" class="btn btn-default">
            @endif
              <span class="gly gly-notes-2"></span>
              <span class="hidden-xs hidden-sm">Mansked</span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="gly gly-stopwatch"></span>
              <span class="hidden-xs hidden-sm">Timelogs</span>
            </button>
            
            <a href="/timesheet" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Timesheet</span>
            </a>
          </div> <!-- end btn-grp -->

          @if(request()->has('search'))
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="fa fa-filter"></span>
              <span class="hidden-xs">{{ $b }}</span>
            </button>
            <a type="button" class="btn btn-default" href="/timelog" title="Remove Filter"><span class="fa fa-close"></span></a>
          </div> <!-- end btn-grp -->
          @endif
          

        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-condensed table-striped">
            <thead>
              <tr>
                <th>Branch</th>
                <th>&nbsp;</th>
                <th>Employee</th>
                <th>Date/Time</th>
                <th>Entry Type</th>
              </tr>
            </thead>
            <tbody>
              @foreach($timelogs as $timelog)
                <tr class="{{ $timelog->branchid!=$timelog->employee->branchid ? 'bg-danger':'' }}">
                  <td>
                    <a href="/timelog?search=branch.code:{{strtolower($timelog->branch->code)}}">
                      {{ $timelog->branch->code or ''}}
                    </a>
                  </td>
                  <td>
                    <a href="/timesheet?date={{$timelog->datetime->format('Y-m-d')}}&branchid={{strtolower($timelog->branchid)}}" class="text-ccc" data-toggle="tooltip" title="Go to {{ $timelog->branch->code }}'s {{ $timelog->datetime->format('D, M j, Y') }} Timesheet">
                      <span class="glyphicon glyphicon-th-list"></span>
                    </a>
                  </td>
                  <td>
                    <span class="label label-default help" data-toggle="tooltip" title="{{ $timelog->employee->position->descriptor or 'd'}}">
                      {{ $timelog->employee->position->code or 'd'}}
                    </span>
                    &nbsp;
                    <?php 
                      $src=$timelog->employee->photo?'employees/'.$timelog->employee->code.'.jpg':'login-avatar.png';
                    ?>
                    <a href="/timelog?search=employee.code:{{$timelog->employee->code}}" rel="popover-img" data-img="http://cashier.giligansrestaurant.com/images/{{$src}}">
                      {{ $timelog->employee->lastname or '' }}, {{ $timelog->employee->firstname or '' }}
                    </a>
                  </td>
                  <td>

                    <a href="/timelog/employee/{{strtolower($timelog->employeeid)}}?date={{$timelog->datetime->format('Y-m-d')}}">
                    <span class="label label-{{ $timelog->txnClass() }}" data-toggle="tooltip" title="{{ $timelog->getTxnCode() }}">
                      {{ $timelog->txnCode() }}
                    </span>
                    </a>
                    
                    <span class="help" data-toggle="tooltip" title="{{ $timelog->datetime->format('m/d/Y h:i A') }}">&nbsp;
                    
                    <span>
                      @if($timelog->datetime->format('Y-m-d')==now())
                        
                        {{ $timelog->datetime->format('h:i A') }}

                        <em class="hidden-xs">
                          <small class="text-muted">
                          {{ diffForHumans($timelog->datetime) }}
                          </small>
                        </em>
                      @else
                        {{ $timelog->datetime->format('h:i A') }}
                        <small class="text-muted">
                        {{ $timelog->datetime->format('M j, D') }}
                        </small>
                      @endif
                    </span> 
                    
                    </span>
                  </td>
                  <td>
                    <span class="label label-{{ $timelog->entryClass() }} help" data-toggle="tooltip" title="{{ $timelog->getEntry() }}">
                      {{ $timelog->entryCode() }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {!! $timelogs->render() !!}
      </div>
    </div>

    
    

      
    

  <div>

</div><!-- end .container-fluid -->
@endsection




@section('js-external')
  @parent

  <script src="/js/vendors-common.min.js"></script>

  <script>
   moment.locale('en', { week : {
      dow : 1 // Monday is the first day of the week.
    }});

  $('document').ready(function(){

    

   
  });
  </script>
@endsection