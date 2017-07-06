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

@section('container-body')
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    @if(request()->has('date') && request()->has('branchid'))
    	<li><a href="/timesheet">Timesheet</a></li>
    	<li class="active">{{ $dr->date->format('D, M j, Y') }}</li>
    @else
    	<li class="active">Timesheet</li>
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
            @if($branch)
						<a href="/mansked?search=branch.code:{{ $branch->lcode() }}" class="btn btn-default">
            @else
            <a href="/mansked" class="btn btn-default">
            @endif
              <span class="gly gly-notes-2"></span>
              <span class="hidden-xs hidden-sm">Mansked</span>
            </a> 
            @if($branch)
						<a href="/timelog?search=branch.code:{{ $branch->lcode() }}" class="btn btn-default">
            @else
            <a href="/timelog" class="btn btn-default">
            @endif
              <span class="gly gly-stopwatch"></span>
              <span class="hidden-xs hidden-sm">Timelogs</span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Timesheet</span>
            </button>
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            <form method="GET" action="/timesheet" accept-charset="UTF-8" id="dp-form">
            <button type="submit" class="btn btn-success btn-go" title="Go" disabled data-toggle="loader">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="date" id="date" value="{{ $dr->date->format('Y-m-d') }}" data-date="{{ $dr->date->format('Y-m-d') }}">
            </form>
          </div>

          <div class="btn-group pull-right clearfix" role="group">
          	@if(request()->has('branchid') && is_uuid(request()->input('branchid')))
            <a href="/timesheet?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}&branchid={{strtolower(request()->input('branchid'))}}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            @endif
            <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
            <label class="btn btn-default  hidden-sm hidden-xs" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
            @if(request()->has('branchid') && is_uuid(request()->input('branchid')))
            <a href="/timesheet?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}&branchid={{strtolower(request()->input('branchid'))}}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            @endif
          
          </div>
          <div class="btn-group" style="margin-left: 5px;">
            <div class="dropdown">
            	<input type="hidden" name="date" id="date" value="{{ $dr->date->format('Y-m-d') }}">
            	<input type="hidden" name="branchid" id="branchid" value="{{ $branch->id or '' }}">
              <button id="dLabel" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-shop"></span>
                @if(is_null($branch))
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


        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    @if(count($data[1])>0)
      <div class="alert alert-important alert-warning">
        <p>There is other employee timelog from other store. Please contact system administrator</p>
      <ul>
      @foreach($data[1] as $key => $f)
        <?php $f->load('employee.branch'); ?>
        <li>{{ $f->employee->lastname }}, {{ $f->employee->firstname }} of {{ $f->employee->branch->code }} - {{ $f->entrytype==2?'Manual':'Punched' }} {{ $f->getTxnCode() }} - 
          {{ $f->datetime->format('D, M j, Y h:m:s A') }} created at {{ $f->createdate->format('D, M j, Y h:m:s A') }}</li>
      @endforeach
    </ul>
      </div>
    @endif



    

    @if(isset($data[0]) && count($data[0])>0)


    <div>
      <span></span>

    </div>

    <div class="table-responsive">
    <table class="table table-hover table-bordered" style="margin-top: 0px;">
      <thead>
        <tr>
          <th>Employee</th>
          <th class="text-right">Work Hours</th>
          <th class="text-right">Time In</th>
          <th class="text-right">Break Start</th>
          <th class="text-right">Break End</th>
          <th class="text-right">Time Out</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data[0] as $key => $e)
        <tr>
          <td <?=$e['onbr']?'':'class="bg-danger"'?>>
            {{ $key+1}}. 
            <!--
            <a href="/timesheet/employee/{{$e['employee']->lid()}}?fr={{$dr->date->copy()->startOfMonth()->format('Y-m-d')}}&amp;to={{$dr->date->copy()->endOfMonth()->format('Y-m-d')}}">
            -->
            <?php 
              $src=$e['employee']->photo?'employees/'.$e['employee']->code.'.jpg':'login-avatar.png';
            ?>
            <a href="/timelog/employee/{{$e['employee']->lid()}}?date={{$dr->date->format('Y-m-d')}}&back-uri=/timelog" rel="popover-img" data-img="http://cashier.giligansrestaurant.com/images/{{$src}}">
              {{ $e['employee']->lastname or '-' }}, {{ $e['employee']->firstname or '-' }}
            </a>
            <span class="label label-default pull-right help" data-toggle="tooltip" title="{{ $e['employee']->position->descriptor or '' }}">{{ $e['employee']->position->code or '' }}</span>
          </td>
          <td class="text-right">
            @if($e['timesheet']->workHours->format('H:i')==='00:00')
              -
            @else
            	<!--
              <small class="text-muted"><em>
                ({{ $e['timesheet']->workHours->format('H:i') }})</em> 
              </small>
              -->
              <strong class="help" data-toggle="tooltip" title="{{ $e['timesheet']->workHours->format('H') }} hour(s) {{ $e['timesheet']->workHours->format('i') }} minute(s)">
                {{ $e['timesheet']->workedHours }}
              </strong>
            @endif
          </td>
            @foreach($e['timelogs'] as $key => $t)
              @if(is_null($t))
                <td class="text-right">-</td>
              @else
                <td class="text-right {{ $t['entrytype']=='2'?'bg-warning':'bg-success' }}" 
                title="{{ $t['datetime']->format('D, M j, Y h:i:s A') }} @ {{ $t['createdate']->format('D, M j, Y h:i:s A') }}">
									
									@if($e['counts'][$key]>1)

										<a href="/timelog/employee/{{$e['employee']->lid()}}?date={{$dr->date->format('Y-m-d')}}&txncode={{$t['txncode']}}" class="text-danger">
										<span class="label label-danger pull-left" style="font-size: 9px;">{{ $e['counts'][$key] }}</span>
										</a>
                  		{{ $t['datetime']->format('h:i A') }}
									@else
                  	{{ $t['datetime']->format('h:i A') }}
									@endif

                </td>
              @endif
           
            @endforeach
          
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>

    <div style="margin: 10px 0;  font-size: 11px;">
      <span>Legends:</span> 

      <ul style="list-style: none;">
        <li><div style="min-width: 30px; display: inline-block;" class="bg-success">&nbsp;</div> RFID Punch In/Out</li>
        <li><div style="min-width: 30px; display: inline-block;" class="bg-warning">&nbsp;</div> Manual Time In/Out</li>
        <li><div style="min-width: 30px; display: inline-block;" class="bg-danger">&nbsp;</div> Not Assigned on this Branch / Resigned / (RM, AM or SKH) </li>
      </ul>
      
    </div>

    @else
      No Data
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
      defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
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


     $('.br.dropdown-menu li a').on('click', function(e){
      e.preventDefault();
      var el = $(e.currentTarget);
      el.parent().siblings().children().css('background-color', '#fff');
      el.css('background-color', '#d4d4d4');
      $('.br-code').text(el.data('code'));
      $('.br-desc').text('- '+el.data('desc'));
      $('#branchid').val(el.data('branchid'));

      if(el.data('branchid')==$('.btn-go').data('branchid'))
        $('.btn-go').prop('disabled', true);
      else
        $('.btn-go').prop('disabled', false);
      
      //document.location.href = '/timesheet?date='+$('#date').val()+'&branchid='+el.data('branchid');
    });


      



   
  });
</script>
@endsection