@extends('master')

@section('title', '- Employee Watchlist')

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

@section('container-body')
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li><a href="/employee">Employee</a></li>
    <li><a href="/employee/tracker">Tracker</a></li>
    <li class="active">{{ $date->format('D, M j') }}</li>
  </ol>

  <nav id="nav-action" class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-form">
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-default active" title="Strarred Branches">
            <span class="fa fa-calendar-o"></span>
            <span class="hidden-xs hidden-sm">Daily</span>
          </button>
          <a data-toggle="loader" href="/employee/tracker/summary" class="btn btn-default">
            <span class="fa fa-calendar"></span>
            <span class="hidden-xs hidden-sm">Cut Off</span>
          </a> 
        </div> <!-- end btn-grp -->

        <div class="btn-group" role="group">
          <a href="/timelog/add" class="btn btn-default">
            <span class="glyphicon glyphicon-plus"></span>
            <span class="hidden-xs hidden-sm">Add Timelog</span>
          </a>
        </div>

        <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
          <form method="GET" action="/employee/watchlist" accept-charset="UTF-8" id="dp-form">
          <button type="submit" class="btn btn-success btn-go" title="Go" disabled data-toggle="loader">
            <span class="gly gly-search"></span>
            <span class="hidden-xs hidden-sm">Go</span>
          </button> 
          
          <input type="hidden" name="date" id="date" value="{{ $date->format('Y-m-d') }}" data-date="{{ $date->format('Y-m-d') }}">
          </form>
        </div>

        <div class="btn-group pull-right clearfix" role="group">
        	
          <a href="/employee/watchlist?date={{ $date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->subDay()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </a>
         
          <input type="text" class="btn btn-default" id="dp-date" value="{{ $date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
          <label class="btn btn-default  hidden-sm hidden-xs" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
          
          <a href="/employee/watchlist?date={{ $date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->addDay()->format('Y-m-d') }}">
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
        <tbody>
          @foreach($datas as $d)
            <tr>
              <td>

              <div class="row">
                <div class="col-md-3">
                  <?php 
                    $src=$d['employee']->photo?'employees/'.$d['employee']->code.'.jpg':'login-avatar.png';
                  ?>
                  <!--
                   <a href="/hr/masterfiles/employee/{{$d['employee']->lid()}}" target="_blank" class="hidden-xs hidden-sm">{{$d['employee']->code  }}</a>-->

                  <a href="/timelog/employee/{{$d['employee']->lid()}}?date={{ $date->format('Y-m-d') }}" rel="popover-img" data-img="http://cashier.giligansrestaurant.com/images/{{$src}}">
                    {{ $d['employee']->lastname }}, {{ $d['employee']->firstname }}
                  </a>
                  <span class="help label label-default pull-right" data-toggle="tooltip" title="{{ $d['employee']->position->descriptor or '' }}">
                    {{ $d['employee']->position->code or '' }}
                  </span>
                </div>
                <div class="col-md-9">
                    @if(count($d['timelogs'])>0)
                      @foreach($d['timelogs'] as $timelog)
                      <span>
                        <span class="help label label-{{ $timelog->txnClass() }}" data-timelogid="{{ $timelog->lid() }}" data-toggle="tooltip" title="{{ $timelog->getEntry() }} {{ $timelog->getTxnCode() }} {{ $timelog->datetime->format('g:i:s A') }}">
                          {{ $timelog->datetime->format('h:i A') }} @ {{ $timelog->branch->code or '' }}
                          <span class="help label label-{{ $timelog->entryClass() }}" >
                            {{ $timelog->getEntry() }}
                          </span>
                        </span>
                      </span>
                      &nbsp;
                      @endforeach
                    @else
                      
                    @endif
                  
                  </div>
                </div>
              </td>
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