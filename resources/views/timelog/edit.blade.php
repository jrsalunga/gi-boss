@extends('master')

@section('title', '- Employee Timelog')

@section('body-class', 'timesheet-index')

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
    <li><a href="/timelog">Timelog</a></li>
    <li><a href="/timelog?search=branch.code:{{ strtolower($timelog->branch->code) }}">{{ $timelog->branch->code }}</a></li>
    <li><a href="/timelog/employee/{{strtolower($timelog->employeeid)}}?date={{$date->format('Y-m-d')}}">{{$timelog->employee->code}}</a></li>
    <li class="active">{{ $date->format('D, M j, Y') }}</li>
  </ol>

  <div>
    
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/mansked?search=branch.code:{{ strtolower($timelog->branch->code) }}" class="btn btn-default">
              <span class="gly gly-notes-2"></span>
              <span class="hidden-xs hidden-sm">Mansked</span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="gly gly-stopwatch"></span>
              <span class="hidden-xs hidden-sm">Timelogs</span>
            </button>
            <a href="/timesheet?date={{$date->format('Y-m-d')}}&branchid={{strtolower($timelog->branchid)}}" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Timesheet</span>
            </a>
          </div> <!-- end btn-grp -->
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
    <form name="frm-timelog" action="/timelog/{{$timelog->lid()}}" method="POST" accept-charset="utf-8">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <div class="row">
      <div class="col-sm-8 col-md-6">
        <div class="panel panel-success">
          <div class="panel-heading">Edit Timelog</div>
          <div class="panel-body row">
           
            <div class="col-sm-10 col-md-10">
              <div class="form-group">
                <label for="search-employee" class="control-label">Employee</label>
                <input type="text" class="form-control" id="search-employee" placeholder="Search Employee" maxlength="120" value="{{ $timelog->employee->lastname }}, {{ $timelog->employee->firstname }}" readonly="">
                <!-- <input type="text" name="employeeid" id="employeeid" required style="height:0; width:0; padding:0; margin:0; border:0;"> --> 
                <input type="hidden" name="id" id="id" value="{{ $timelog->id }}"> 
                
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="search-employee" class="control-label">Date</label>
                <div class="input-group date datepicker" id="datepicker">
                  <input type="text" class="form-control datepicker" id="date" value="{{ $timelog->datetime->format('Y-m-d') }}" readonly>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>

              </div>
              </div>
            </div> 

            <div class="col-sm-6">
              <div class="form-group">
                <label for="search-employee" class="control-label">Time</label>
                <div class="input-group date timepicker" id="timepicker">
                  <input type="text" class="form-control" id="time"  value="{{ $timelog->datetime->format('g:i:s A') }}" readonly>
                  <span class="input-group-addon">
                      <span class="glyphicon glyphicon-time"></span>
                  </span>
                </div>
              </div>
            </div> 

            <div class="col-sm-6">
              <div class="form-group">
                <label for="txncode" class="control-label">Type</label>
               
                  <select class="form-control" name="txncode" id="txncode"> 
                  @for($x=1; $x<=4; $x++)
                    <option value="{{$x}}"
                      {{ $timelog->txncode==$x ? 'selected':'' }}
                    >{{ $timelog->getTxnCode($x) }}</option>
                  @endfor
                  </select>
               
              </div>
            </div> 

             <div class="col-sm-6">
              <div class="form-group">
                <label for="search-employee" class="control-label">Entry Type</label>
                <input type="text" class="form-control" id="entrytype"  value="{{ $timelog->getEntry() }}" readonly>
              </div>
            </div> 

            <div class="col-sm-6">
              <div class="checkbox">
                <label for="ignore" class="control-label">
                  <input type="checkbox" name="ignore" id="ignore" {{ $timelog->ignore=='0'?'':'checked' }}>
                  Ignore this timelog?
                </label>
              </div>
            </div> 
        
          </div>
        </div>
      </div>
    </div>
    <div class="row button-container">
      <div class="col-md-6">
        <a href="/timelog/employee/{{strtolower($timelog->employeeid)}}?date={{$date->format('Y-m-d')}}" class="btn btn-default">Cancel</a>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>
  
    {!! Form::close() !!}
    

  </div>
</div><!-- end .container-fluid -->
@endsection




@section('js-external')
  @parent

  <script src="/js/vendors-common.min.js"></script>

@endsection