@extends('master')

@section('title', '- View Mansked')

@section('body-class', 'view-mansked')

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
    <li><a href="/dashboard"><span class="gly gly-shop"></span></a></li>
    <li><a href="/mansked">Mansked</a></li>
    <li><a href="/mansked?search=branch.code:{{strtolower($manday->manskedhdr->branch->code)}}">{{ $manday->manskedhdr->branch->code }}</a></li>
    <li><a href="/mansked/{{strtolower($manday->manskedid)}}">Week {{$manday->date->weekOfYear}}</a></li>
    <li class="active">{{ $manday->date->format('D, M j') }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <!--
            <a href="/mansked" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
            </a>
            -->
            <a href="/mansked" class="btn btn-default">
              <span class="gly gly-notes-2"></span>
              <span class="hidden-xs hidden-sm">Mansked</span>
            </a>
            <a href="/mansked/{{strtolower($manday->manskedid)}}" class="btn btn-default">
              <span class="fa fa-calendar"></span>
              <span class="hidden-sm hidden-xs">{{$manday->date->year}}-W{{$manday->date->weekOfYear}}</span>
            </a>
            
            <button type="button" class="btn btn-default active">
              <span class="fa fa-calendar-o"></span>
              <span class="hidden-sm hidden-xs">{{ $manday->date->format('M j') }}</span>
            </button>   
            
          </div>
          <div class="btn-group" role="group">
            <a href="/timelog" class="btn btn-default">
              <span class="gly gly-stopwatch"></span>
              <span class="hidden-xs hidden-sm">Timelogs</span>
            </a>
            
            <a href="/timesheet" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Timesheet</span>
            </a>
          </div><!-- end btn-grp -->
          
          <div class="btn-group pull-right" role="group">
            @if($manday->previous()==='false')
              <a href="/manday/" class="btn btn-default disabled">
            @else
              <a href="/mansked/manday/{{$manday->previous()->lid()}}" class="btn btn-default">
            @endif
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            @if($manday->next()==='false')
              <a href="/mansked/manday/" class="btn btn-default disabled">
            @else
              <a href="/mansked/manday/{{$manday->next()->lid()}}" class="btn btn-default">
            @endif  
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')
    
    <table class="table table-bordered">
      <tbody>
        <tr>
          <td rowspan="2" colspan="2">
            <div>
            {{ $manday->date->format('F j, Y') }}
            </div>
            @if(strtotime($manday->date) < strtotime('now'))
              <span class="label label-warning">[ readonly ]</span>
            @endif
          </td>
          <td>Forecast Pax</td><td>Head Spend</td><td>Emp Count</td><td>Man Cost</td>
          <td colspan="2">Total Work Hrs</td><td>Over Load</td><td>Under Load</td>
        </tr>
        <tr>
          <td class="text-right text-input">
            
            {{ number_format($manday->custcount,0) }}
          </td>
          <td class="text-right text-input">
            &#8369; {{ number_format($manday->headspend, 2) }}
          </td>
          <td class="text-right">
            {{ $manday->empcount+0 }}
          </td>
          <td class="text-right">
            @if(($manday->custcount*$manday->headspend)!= 0)
              {{ number_format((($manday->empcount*$manday->manskedhdr->mancost)/($manday->custcount*$manday->headspend)*100),2) }} %
            @else 
              -
            @endif
          </td>
          <td colspan="2" class="text-right">
            {{ $manday->workhrs+0 }}
          </td>
          <td class="text-right">
            {{ $manday->overload+0 }}
          </td>
          <td class="text-right">
            {{ $manday->underload+0 }}
          </td>
        </tr>
      </tbody>
    </table>

    <table class="table table-bordered">
      <tbody>
          <tr>
          @foreach ($hours as $key => $value) 
            <td data-value={{ date('g:i A', strtotime($key.'.00')) }}"" title="{{ $key }}" class="text-center"> 
              {{ date('g A', strtotime($key.'.00')) }}
            </td>
          @endforeach
          </tr>
          <tr>
          @foreach ($hours as $key => $value)  
            <td class="text-right">{{ $value }}</td>
          @endforeach
          </tr>
      </tbody>
    </table>

    <table id="tb-mandtl" class="table table-bordered">
      <tbody>
        <tr>
          <td>Dept</td><td>Employee</td><td>Time Start</td><td>Break Start</td>
          <td>Break End</td><td>Time End</td><td>Work Hrs</td><td>Loading</td>
        </tr>
        <?php $ctr=1 ?>
        @foreach($depts as $dept)
          @for($i = 0; $i < count($dept['employees']); $i++)
            <?php
              $bg = $dept['employees'][$i]->lid() == request()->input('employeeid') ? 'bg-success':'';
            ?>
            <tr  data-mandtl-id="{{ $dept['employees'][$i]['manskeddtl']['id'] }}" class="{{ $bg }}">
              <td>{{ strtoupper($dept['code']) }}</td>
              <td>{{ $ctr }}. 
                  
                  <a href="/timelog/employee/{{$dept['employees'][$i]->lid()}}?date={{$manday->date->format('Y-m-d')}}">
                    {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }} 
                  </a>

                  <span class="label label-default pull-right">{{ $dept['employees'][$i]->position->code }}</span></td>
              @if($dept['employees'][$i]['manskeddtl']['daytype']==1)
                <td class="text-right">
                  <?php
                    $d = $dept['employees'][$i]['manskeddtl']['timestart'];
                    if($d=='off')
                      echo '-';
                    else
                      echo date('g:i A', strtotime($d)); 
                    ?>
                </td>
                <td class="text-right">
                  <?php
                    $d = $dept['employees'][$i]['manskeddtl']['breakstart'];
                    if($d=='off')
                      echo '-';
                    else
                      echo date('g:i A', strtotime($d)); 
                    ?>
                </td>
                <td class="text-right">
                  <?php
                    $d = $dept['employees'][$i]['manskeddtl']['breakend'];
                    if($d=='off')
                      echo '-';
                    else
                      echo date('g:i A', strtotime($d)); 
                    ?>
                </td>
                <td class="text-right">
                  <?php
                  $d = $dept['employees'][$i]['manskeddtl']['timeend'];
                  if($d=='off')
                    echo '-';
                  else
                    echo date('g:i A', strtotime($d)); 
                  ?>
                </td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['workhrs'] + 0 }}</td>
                <?php $l = $dept['employees'][$i]['manskeddtl']['loading'] ?>
                @if($l < 0)
                  <td class="text-right text-danger">{{ $l+0 }}</td>
                @elseif($l > 0)
                  <td class="text-right text-info">{{ $l+0 }}</td>
                @else
                  <td class="text-right">-</td>
                @endif

              @else

                @if($dept['employees'][$i]['manskeddtl']['daytype']>1)
                  <td colspan="6" class="text-center">
                    <span style="color: #bbb;">
                      {{ dayDesc($dept['employees'][$i]['manskeddtl']['daytype']) }}
                    </span>
                  </td>
                @else
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                @endif
              @endif
            </tr>
            <?php $ctr++ ?>
          @endfor
        @endforeach
      </tbody>
    </table>

    
    
      
  </div>


<!-- end main -->
</div>
@endsection




@section('js-external')
  @parent
  

<script>
  $('document').ready(function(){

   // $('#date').datepicker({'format':'yyyy-mm-dd'})

    $('select.form-control').on('change', function(e){
      //console.log(e);
      var x = ($(this)[0].value=='off') ? 0:1; 
     $(this).parent().children('.daytype').val(x);
    });



     $("#date").datepicker({ minDate: 1, dateFormat: 'yy-mm-dd',});
     $('.alert').not('.alert-important').delay(5000).slideUp(300);
  });
</script>
@endsection

