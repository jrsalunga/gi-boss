@extends('master')

@section('title', '- Branch Timesheet')

@section('body-class', 'br-ts')

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
 
  $r = $days;
  $first = array_shift($r);
  $last = array_pop($r);

?>

@section('container-body')
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li><a href="/employee">Employee</a></li>
    <li><a href="/employee/tracker">Tracker</a></li>
    <li><a href="/employee/tracker/summary">Summary</a></li>
    <li class="active">{{ $first->format('M j') }} - {{ $date->format('M j') }}</li>
  </ol>

  <nav id="nav-action" class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-form">
        <div class="btn-group" role="group">
          <a href="/employee/tracker" class="btn btn-default">
            <span class="fa fa-calendar-o"></span>
            <span class="hidden-xs hidden-sm">Daily</span>
          </a> 
          <button type="button" class="btn btn-default active" title="Strarred Branches">
            <span class="fa fa-calendar"></span>
            <span class="hidden-xs hidden-sm">Cut Off</span>
          </button>
        </div> <!-- end btn-grp -->

        <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
          <form method="GET" action="/employee/tracker/summary" accept-charset="UTF-8" id="dp-form">
          <button type="submit" class="btn btn-success btn-go" title="Go" disabled data-toggle="loader">
            <span class="gly gly-search"></span>
            <span class="hidden-xs hidden-sm">Go</span>
          </button> 
          
          <input type="hidden" name="date" id="date" value="{{ $date->format('Y-m-d') }}" data-date="{{ $date->format('Y-m-d') }}">
          </form>
        </div>

        <div class="btn-group pull-right clearfix" role="group">
        	
          <a data-toggle="loader" href="/employee/tracker/summary?date={{ $first->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $first->copy()->subDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </a>
         
          <input type="text" class="btn btn-default" id="dp-date" value="{{ $date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
          <label class="btn btn-default  hidden-sm hidden-xs" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
          
          <a data-toggle="loader" href="/employee/tracker/summary?date={{ $last->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $last->copy()->addDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-right"></span>
          </a>
         
        
        </div>
        


      </div>
    </div>
  </nav>

  @include('_partials.alerts')
  

  @if(is_null($datas))

  @else
  <div>
    <div>
      <table class="table table-condensed table-hover" style="font-family: 'Source Code Pro', monospace;">
        <thead>
          <tr>
            <th>Employees</th>
            @foreach($days as $day)
              <th class="text-center {{ $day->dayOfWeek == Carbon\Carbon::SUNDAY ? 'bg-warning':'' }}">
                <a href="/employee/tracker?date={{ $day->format('Y-m-d') }}">
                  <div class="text-center">
                      {{ $day->format('D') }}
                  </div>
                  <div class="text-center">
                      {{ $day->format('j') }}
                  </div>
                </a>
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($datas as $d)
            <tr>
              <td>
                <?php 
                  $src=$d['employee']->photo?'employees/'.$e['employee']->code.'.jpg':'login-avatar.png';
                ?>
                <a href="/timesheet/employee/{{ $d['employee']->lid() }}?fr={{ $first->format('Y-m-d') }}&to={{ $last->format('Y-m-d') }}"  rel="popover-img" data-img="http://cashier.giligansrestaurant.com/images/{{$src}}">
                  {{ $d['employee']->lastname }}, {{ $d['employee']->firstname }}
                </a>
                <span class="help label label-default pull-right" data-toggle="tooltip" title="{{ $d['employee']->position->descriptor or '' }}">
                  {{ $d['employee']->position->code or '' }}
                </span>
              </td>
              @foreach($d['timelogs'] as $t)
                <td class="text-center {{ $t['date']->dayOfWeek == Carbon\Carbon::SUNDAY ? 'bg-warning':'' }}">
                  @if($t['count']>0)
                    <a href="/timelog/employee/{{ $d['employee']->lid() }}?date={{ $t['date']->format('Y-m-d') }}">
                      {{ $t['count'] }}
                    </a>
                  @else

                  @endif
                </td>
              @endforeach
            </tr>
        
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
   

  
    

      
    



</div><!-- end .container-fluid -->
@endsection




@section('js-external')
  @parent

  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/dr-picker.js"></script>

<script>
   moment.locale('en', { week : {
      dow : 1 // Monday is the first day of the week.
    }});

  $('document').ready(function(){


  	

  	$('#dp-date').datetimepicker({
      defaultDate: "{{ $date->format('Y-m-d') }}",
      format: 'MM/DD/YYYY',
      showTodayButton: true,
      ignoreReadonly: true,
    }).on('dp.change', function(e){
      var date = e.date.format('YYYY-MM-DD');
      console.log(date);
      $('#date').val(date);
      //document.location.href = '/timesheet?date='+e.date.format('YYYY-MM-DD')+'&branchid='+$('#branchid').val();
      if($('#to').data('date')==date)
        $('.btn-go').prop('disabled', true);
      else
        $('.btn-go').prop('disabled', false);
      
    });


    


      



   
  });
</script>
@endsection