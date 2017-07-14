@extends('master')

@section('title', '- Employee Timesheet')

@section('css-external')
  <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.24/daterangepicker.min.css">
  <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet">
@endsection

@section('body-class', 'timesheet-employee')

@section('container-body')
<div class="container-fluid">
  
  <ol class="breadcrumb">
    <li><a href="/dashboard"><span class="gly gly-shop"></span></a></li>
    <li><a href="/timesheet">Timesheet</a></li>
     <li><a href="/timesheet?date={{c()->format('Y-m-d')}}&branchid={{strtolower($employee->branchid)}}">{{ $employee->branch->code }}</a></li>
    <li class="active">{{ $employee->code or '' }}</li>
    <!--
    <li class="active">{{ $dr->date->format('D, M j, Y') }}</li>
    -->
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
            <a href="/mansked" class="btn btn-default">
              <span class="gly gly-notes-2"></span>
              <span class="hidden-xs hidden-sm">Mansked</span>
            </a>
            <a href="/timelog" class="btn btn-default">
              <span class="gly gly-stopwatch"></span>
              <span class="hidden-xs hidden-sm">Timelogs</span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Timesheet</span>
            </button>
          </div> <!-- end btn-grp -->
          <!--
          <div class="btn-group" role="group">
            <a href="/timelog/add?ref=timesheet" class="btn btn-default" title="Back to Main Menu">
              <span class="glyphicon glyphicon-plus"></span>
              <span class="hidden-xs hidden-sm">Add Timelog</span>
            </a> 
          </div>
          -->
          <div class="btn-group pull-right clearfix" role="group">
            <div id="reportrange" class="btn btn-default">
              <span class="glyphicon glyphicon-calendar"></span>
              <span class="p">{{ $dr->fr->format("m/d/Y") }} - {{ $dr->to->format("m/d/Y") }}</span> 
            </div>
            <!--
            <a href="/timesheet/{{$employee->lid()}}?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
            <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
            <a href="/timesheet/{{$employee->lid()}}?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            -->
          </div>
          
        </div>
      </div>
    </nav>

    <div class="row">
      <div class="col-sm-6">
        <table>
          <tr>
            <td>
              <img src="http://cashier.giligansrestaurant.com/images/{{$employee->photo?'employees/'.$employee->code.'.jpg':'login-avatar.png'}}" style="margin-right: 5px; width: 100px;" class="img-responsive">
            </td>
            <td>
              <h3>
                {{ $employee->lastname }}, {{ $employee->firstname }}
                <small>{{ $employee->code }}</small>
              </h3>
              <p>{{ $employee->position->descriptor or '' }}</p>
              <p>
                <em>Timesheet for {{ $dr->fr->format("D M j, Y") }} - {{ $dr->to->format("D M j, Y") }}</em>
              </p>
            </td>
          </tr>
        </table>
        
      </div>
      <div class="col-sm-3">
        
      </div>
      <div class="col-sm-3">
        
      </div>
    </div>


    <p>&nbsp;</p>
    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-hover table-condensed" style="font-family: 'Source Code Pro', monospace;">
            <tbody>
              @foreach($timesheets as $timesheet)
              <tr 
                @if($timesheet['date']->isToday())
                  class="bg-success"
                @elseif($timesheet['date']->dayOfWeek==0)
                  class="bg-warning"
                @else

                @endif
              >
                <td>
                  <a href="/timelog/employee/{{$employee->lid()}}?date={{$timesheet['date']->format('Y-m-d')}}">
                    {{ $timesheet['date']->format("D, M j") }}
                  </a>
                </td>
                <td>
                  @if($timesheet['timelogs'])
                  @foreach($timesheet['timelogs'] as $timelog)
                  <span>
                    <span class="help label label-{{ $timelog->txnClass() }}" data-toggle="tooltip" title="{{ $timelog->getEntry() }} {{ $timelog->getTxnCode() }} {{ $timelog->datetime->format('g:i:s A') }}">
                      {{ $timelog->datetime->format('h:i A') }} @ {{ $timelog->branch->code or '' }}
                      <span class="help label label-{{ $timelog->entryClass() }}" >
                        {{ $timelog->getEntry() }}
                      </span>
                    </span>
                  </span>
                  &nbsp;
                  @endforeach
                  @endif
                </td>                
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>



</div>
@endsection




@section('js-external')
  @parent
  <script src="/js/vendors-common.min.js"></script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.24/daterangepicker.min.js"></script>

<script type="text/javascript">
$(function() {

  console.log(moment());

  var start = moment('{{$dr->fr->format("Y-m-d")}}');
  console.log(start);
  var end = moment('{{$dr->to->format("Y-m-d")}}');
  console.log(end);

  function cb(start, end) {
      $('#reportrange .p').html(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
  }

  $('#reportrange').daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
       'Today': [moment(), moment()],
       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
       'This Month': [moment().startOf('month'), moment().endOf('month')],
       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
  }, cb)
  .on('apply.daterangepicker', function(ev, picker) {
    //console.log(ev);
    var url = "/timesheet/employee/{{$employee->lid()}}?fr="+ picker.startDate.format('YYYY-MM-DD') +"&to="+ picker.endDate.format('YYYY-MM-DD');
    window.location.replace(url)
    console.log(picker.startDate.format('MM/DD/YYYY'));
    console.log(picker.endDate.format('MM/DD/YYYY'));
  });

  cb(start, end);


});
</script>

@endsection