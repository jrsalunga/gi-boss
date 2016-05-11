@extends('master')

@section('title', '- By Week Analytics')

@section('body-class', 'analytics-week')

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
    <li><a href="/"><span class="gly gly-shop"></span> </a></li>
    <li><a href="/status/branch/week">Branch Analytics</a></li>
    <li class="active">Week {{ $dr->fr->format('W')+0 }} - Week {{ $dr->to->format('W')+0 }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->
          <!--
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/status/branch/week', 'method' => 'get', 'id'=>'dp-form']) !!}
            <!--
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid="'. $branch->id  }}">
            -->  
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

          <div class="btn-group" style="margin-left: 5px;">
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
      
          <div class="btn-group pull-right clearfix dp-container" role="group">
            
            <select id="fr-year" class="btn btn-default dp-w-fr" style="height:34px; padding: 6px 3px 6px 12px">
              @for($y=2015;$y<2021;$y++)
                <option value="{{$y}}" {{ $dr->fr->year==$y?'selected':'' }}>{{$y}}</option>
              @endfor
            </select>
            <select id="fr-week" class="btn btn-default dp-w-fr" style="height:34px; padding: 6px 0px 6px 12px">
              @for($x=1;$x<=lastWeekOfYear($dr->fr->year);$x++)
              <option value="{{$x}}" {{ $dr->fr->weekOfYear==$x?'selected':'' }}>{{$x}}</option>
              @endfor
            </select>
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <select id="to-year" class="btn btn-default dp-w-to" style="height:34px; padding: 6px 3px 6px 12px">
              @for($y=2015;$y<2021;$y++)
                <option value="{{$y}}" {{ $dr->to->year==$y?'selected':'' }}>{{$y}}</option>
              @endfor
            </select>
            <select id="to-week" class="btn btn-default dp-w-to" style="height:34px; padding: 6px 0px 6px 12px">
              @for($x=1;$x<=lastWeekOfYear($dr->to->year);$x++)
                <option value="{{$x}}" {{ $dr->to->weekOfYear==$x?'selected':'' }}>{{$x}}</option>
              @endfor
            </select>
        
          </div><!-- end btn-grp -->

          <div class="btn-group pull-right clearfix" role="group">
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span id="date-type-name">Weekly</span>
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
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')
    <!--
    <ul class="nav nav-tabs" role="tablist" style="margin: -10px 0 10px 0;">
      <li role="presentation" class="active">
        <a href="#" role="tab" data-toggle="tab">
          Daily
        </a>
      </li>
      <li role="presentation">
        <a href="/status/branch/month?{{is_null($branch)?'':'branchid='.$branch->lid()}}&amp;fr={{$dr->now->copy()->subMonths(5)->endOfMonth()->format('Y-m-d')}}&amp;to={{$dr->now->endOfMonth()->format('Y-m-d')}}" role="tab">
          Monthly
        </a>
      </li>
    </ul>
    -->
    @if(!is_null($branch))
    <div class="row"  style="margin: -10px 0 10px 0;">
      <div class="col-md-5" title="Address">
        <span class="glyphicon glyphicon-map-marker"></span> {{ is_null($branch)?'':$branch->address}}
      </div>
      <div class="col-md-3 col-sm-6" title="Branch Manager / OIC">
        <span class="gly gly-user"></span> {{ is_null($branch)?'':''}}
      </div>
      <div class="col-md-3 col-sm-4" title="Contact Nos.">
        <span class="glyphicon glyphicon-phone-alt"></span> 
        <?=is_null($branch)?'':'<a href="tel:'.preg_replace("/[^0-9\s]/", "", $branch->tel).'">'.$branch->tel.'</a>' ?>
        / <span class="glyphicon glyphicon-phone"></span> 
        <?=is_null($branch)?'':'<a href="tel:'.preg_replace("/[^0-9\s]/", "", $branch->mobile).'">'.$branch->mobile.'</a>' ?>
      </div>
      <div class="col-md-1 col-sm-2"  title="Seating Capacity">
        <span class="fa fa-group"></span> {{ is_null($branch)?'':$branch->seating}}
      </div>
    </div>
    @endif

    <div class="row">
      
      @if(is_null($dailysales))

      @else




      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Sales</p>
        <h3 id="h-tot-sales" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Purchased</p>
        <h3 id="h-tot-purch" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Manpower Cost</p>
        <h3 id="h-tot-mancost" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Sales per Employee</p>
        <h3 id="h-tot-tips" style="margin:0">0</h3>
      </div>

    </div>
    <div class="row">

      <div class="col-md-12">
        <div id="container" style="overflow: hidden;"></div>
      </div>
      

      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-hover table-striped table-sort-data">
            <thead>
              <tr>
                  <th>Week</th>
                  <th class="text-right">Sales</th>
                  <th class="text-right">Purchased</th>
                  <th class="text-right">Customers</th>
                  <th class="text-right">Head Spend</th>
                  <th class="text-right">Emp Count</th>
                  <th class="text-right">Sales per Emp</th>
                  <th class="text-right">
                    <div style="font-weight: normal; font-size: 11px; cursor: help;">
                      <em title="Branch Mancost">{{ $branch->mancost }}</em>
                    </div>
                    Man Cost
                  </th>
                  <th class="text-right">Man Cost %</th>
                  <th class="text-right">Tips</th>
                  <th class="text-right">Tips %</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $tot_sales = 0;
                $tot_purchcost = 0;
                $tot_custcount = 0;
                $tot_headspend = 0;
                $tot_empcount = 0;
                $tot_sales_emp = 0;
                $tot_mancost = 0;
                $tot_mancostpct = 0;
                $tot_tips = 0;
                $tot_tipspct = 0;

                $div_sales = 0;
                $div_purchcost = 0;
                $div_custcount = 0;
                $div_headspend = 0;
                $div_empcount = 0;
                $div_mancost = 0;
                $div_tips = 0;
              ?>
            @foreach($dailysales as $d)
              <?php 
                $div_sales+=($d->dailysale['sales']!=0)?1:0; 
                $div_purchcost+=($d->dailysale['purchcost']!=0)?1:0; 
                $div_custcount+=($d->dailysale['custcount']!=0)?1:0; 
                $div_headspend+=($d->dailysale['headspend']!=0)?1:0; 
                $div_empcount+=($d->dailysale['empcount']!=0)?1:0; 
                $div_tips+=($d->dailysale['tips']!=0)?1:0; 
              ?>

            <tr>
              <td data-sort="{{$d->date->format('Y-m-d')}}">
                <span data-toggle="tooltip" data-placement="right"  style="cursor: help;"
                title="{{ $d->date->copy()->startOfWeek()->format('M j, Y') }} - 
                  {{ $d->date->copy()->endOfWeek()->format('M j, Y') }}">
                  {{ $d->date->format('Y') }}-W{{ $d->date->format('W') }}
                </span>
              </td>
              @if(!is_null($d->dailysale))
              <td class="text-right" data-sort="{{ number_format($d->dailysale['sales'], 2,'.','') }}">{{ number_format($d->dailysale['sales'], 2) }}</td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['purchcost'], 2,'.','') }}">
                  {{ number_format($d->dailysale['purchcost'], 2) }}
              </td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['custcount'], 0) }}">{{ number_format($d->dailysale['custcount'], 0) }}</td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['headspend'], 2,'.','') }}">{{ number_format($d->dailysale['headspend'], 2) }}</td>
              <td class="text-right" data-sort="{{ $d->dailysale['empcount'] }}">{{ $d->dailysale['empcount'] }}</td>
              <?php
                $s = $d->dailysale['empcount']=='0' ? '0.00':($d->dailysale['sales']/$d->dailysale['empcount']);
              ?>
              <td class="text-right" data-sort="{{$s}}">{{number_format($s,2)}}</td>
              <?php
                $mancost = $d->dailysale['empcount']*$branch->mancost;
                $div_mancost+=($mancost!=0)?1:0; 
              ?>
              <td class="text-right" data-sort="{{ number_format($mancost,2,'.','') }}">{{ number_format($mancost,2) }}</td>
              <td>
                <?php
                  echo (!empty($d->dailysale['sales'])) ? '1':'0';
                  echo ($d->dailysale['sales']!='0.00') ? '1':'0';
                  echo ($d->dailysale['sales']!='0') ? '1':'0';

                ?>
              </td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['tips'],2,'.','') }}">{{ number_format($d->dailysale['tips'],2) }}</td>
              <?php
                $tipspct = ($d->dailysale['sales']!=0) 
                  ? ($d->dailysale['tips']/$d->dailysale['sales'])*100
                  : 0;
              ?>
              <td class="text-right" data-sort="{{ number_format($tipspct,2,'.','') }}">
                {{ number_format($tipspct, 2) }}
              </td>
              <?php
                $tot_sales      += $d->dailysale['sales'];
                $tot_purchcost  += $d->dailysale['purchcost'];
                $tot_custcount  += $d->dailysale['custcount'];
                $tot_headspend  += $d->dailysale['headspend'];
                $tot_empcount   += $d->dailysale['empcount'];

                if($d->dailysale['empcount']!='0') {
                  $tot_sales_emp += number_format(($d->dailysale['sales']/$d->dailysale['empcount']),2, '.', '');
                }

                $tot_mancost    += $mancost;
                $tot_mancostpct += $d->dailysale['mancostpct'];
                $tot_tips       += $d->dailysale['tips'];
                $tot_tipspct    += $d->dailysale['tipspct'];
              ?>
              @else 
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              @endif
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td>
                <strong>
                {{ count($dailysales) }}
                {{ count($dailysales) > 1 ? 'weeks':'week' }}
                </strong>
              </td>
              <td class="text-right">
                <strong id="f-tot-sales">{{ number_format($tot_sales,2) }}</strong>
                <div>
                <em><small title="{{$tot_sales}}/{{$div_sales}}">
                  {{ $div_sales!=0?number_format($tot_sales/$div_sales,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong id="f-tot-purch">{{ number_format($tot_purchcost,2) }}</strong>
                <div>
                <em><small title="{{$tot_purchcost}}/{{$div_purchcost}}">
                  {{ $div_purchcost!=0?number_format($tot_purchcost/$div_purchcost,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_custcount, 0) }}</strong>
                <div>
                <em><small title="{{$tot_custcount}}/{{$div_custcount}}">
                  {{ $div_custcount!=0?number_format($tot_custcount/$div_custcount,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small title="{{$tot_headspend}}/{{$div_headspend}}">
                  {{ $div_headspend!=0?number_format($tot_headspend/$div_headspend,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_empcount,0) }}</strong>
                <div>
                <em><small title="{{$tot_empcount}}/{{$div_empcount}}">
                  {{ $div_empcount!=0?number_format($tot_empcount/$div_empcount,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small id="f-tot-tips" title="{{$tot_sales}}/{{$tot_empcount}}" >
                  @if($tot_empcount!='0')
                    {{ number_format($tot_sales/$tot_empcount,2) }}
                    <!--
                    {{ number_format($tot_sales-($tot_purchcost+$tot_mancost),2) }}
                    -->
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong id="f-tot-mancost">{{ number_format($tot_mancost,2) }}</strong>
                <div>
                <em><small title="{{$tot_mancost}}/{{$div_mancost}}">
                  @if($div_mancost!='0')
                  {{ number_format($tot_mancost/$div_mancost,2) }}
                   @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small title="(({{$tot_empcount}}*{{$branch->mancost}})/{{$tot_sales}})*100">
                  @if($tot_sales!='0')
                  {{ number_format((($tot_empcount*$branch->mancost)/$tot_sales)*100,2) }}%
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_tips,2) }}</strong>
                <div>
                <em><small title="{{$tot_tips}}/{{$div_tips}}">
                  {{ $div_tips!=0?number_format($tot_tips/$div_tips,2):0 }}</small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp; </strong>
                <div>
                <em><small title="({{$tot_tips}}/{{$tot_sales}})*100 ">
                  @if($tot_sales!='0')
                  {{ number_format(($tot_tips/$tot_sales)*100,2) }}%
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
            </tr>
          </tfoot>
        </table>

        <table id="datatable" class="tb-data table" style="display:none;">
          <thead>
            <tr>
                <th>Date</th>
                <th>Sales</th>
                <th>Purchased</th>
                <th>Emp Count</th>
  
                <th>Man Cost</th>
                <th>Tips</th>
                <th>Sales per Emp</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dailysales as $d)
            <tr>
              <td>{{ $d->date->format('Y-m-d') }}</td>
              @if(!is_null($d->dailysale))
              <td>{{ $d->dailysale['sales'] }}</td>
              <td>{{ $d->dailysale['purchcost'] }}</td>
              <td>{{ $d->dailysale['empcount'] }}</td>
              <td>{{ $d->dailysale['mancost'] }}</td>
              <td>{{ $d->dailysale['tips'] }}</td>
              <td>{{ $d->dailysale['empcount']=='0' ? 0:number_format(($d->dailysale['sales']/$d->dailysale['empcount']), 2, '.', '') }}</td>
              @else 

              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              @endif
              </tr>
            @endforeach
          </tbody>
        </table>

       

      </div><!--  end: table-responsive -->
      </div>
          @endif
    </div>
  </div>
</div><!-- end container-fluid -->



@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  
  
  <script>

    moment.locale('en', { week : {
      dow : 1 // Monday is the first day of the week.
    }});

    Highcharts.setOptions({
      lang: {
        thousandsSep: ','
    }});

    var initDatePicker = function(){

      $('#dp-date-fr').datetimepicker({
        //defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        //calendarWeeks: true,
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-date-to').datetimepicker({
       // defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        //calendarWeeks: true,
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });

      $('#dp-m-date-fr').datetimepicker({
        //defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        viewMode: 'months'
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-m-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-m-date-to').datetimepicker({
       // defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        viewMode: 'months'
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-m-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-y-date-fr').datetimepicker({
        format: 'YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        viewMode: 'years'
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-y-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-y-date-to').datetimepicker({
        format: 'YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        viewMode: 'years'
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-y-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      function getWeekNumber(d) {
        // Copy date so don't modify original
        d = new Date(+d);
        d.setHours(0,0,0);
        // Set to nearest Thursday: current date + 4 - current day number
        // Make Sunday's day number 7
        d.setDate(d.getDate() + 4 - (d.getDay()||7));
        // Get first day of year
        var yearStart = new Date(d.getFullYear(),0,1);
        // Calculate full weeks to nearest Thursday
        var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7)
        // Return array of year and week number
        return [d.getFullYear(), weekNo];
      }

      function weeksInYear(year) {
        var d = new Date(year, 11, 31);
        var week = getWeekNumber(d)[1];
        return week == 1? getWeekNumber(d.setDate(24))[1] : week;
      }

      var changeWeek = function(t, year, week) {
        //console.log(t[0].id);
        var WiY = weeksInYear(t[0].value);
        if(t[0].id===year){
          if($('#'+week+' option').length===52 && WiY===53) {
            //console.log('53 dapat');
            $('#'+week+' option:last-of-type').after('<option value="53">53</option>');
          } else if($('#'+week+' option').length===53 && WiY===52) {
            //console.log('52 lang');
            $('#'+week+' option:last-of-type').detach();
          } else {
            //console.log('sakto lang');
          }
          
        }
        console.log($('.dp-w-fr')[0].value+' '+WiY);
      }


      $('.dp-w-fr').on('change', function(e){

        changeWeek($(this), 'fr-year', 'fr-week');

        var day = moment($('.dp-w-fr')[0].value+'-08-27').startOf('week').isoWeek($('.dp-w-fr')[1].value);
        console.log(day.format('YYYY-MM-DD'));

        $('#fr').val(day.format('YYYY-MM-DD'));
        //console.log(moment().startOf('week').week($('.dp-w-fr')[1].value));
        //console.log(moment($('.dp-w-fr')[0].value+'W0'+$('.dp-w-fr')[1].value+'1'));
      });


      $('.dp-w-to').on('change', function(e){

        changeWeek($(this), 'to-year', 'to-week');

        var day = moment($('.dp-w-to')[0].value+'-08-27').startOf('week').isoWeek($('.dp-w-to')[1].value);
        console.log(day.add(6, 'days').format('YYYY-MM-DD'));
        $('#to').val(day.format('YYYY-MM-DD'));
        
      });


      /***** quarter *****/
      $('.dp-q-fr').on('change', function(e){
        var day = moment($('.dp-q-fr')[0].value+'-'+$('.dp-q-fr')[1].value);
        console.log(day.format('YYYY-MM-DD'));
        $('#fr').val(day.format('YYYY-MM-DD'));
      });

      $('.dp-q-to').on('change', function(e){
        var day = moment($('.dp-q-to')[0].value+'-'+$('.dp-q-to')[1].value);
        console.log(day.format('YYYY-MM-DD'));
        $('#to').val(day.format('YYYY-MM-DD'));
      });
      /***** end:quarter *****/


    } /* end initDatePicker */



    $(document).ready(function(){

      initDatePicker();

      $('[data-toggle="tooltip"]').tooltip();

      $('.br.dropdown-menu li a').on('click', function(e){
        e.preventDefault();
        var el = $(e.currentTarget);
        el.parent().siblings().children().css('background-color', '#fff');
        el.css('background-color', '#d4d4d4');
        $('.br-code').text(el.data('code'));
        $('.br-desc').text('- '+el.data('desc'));
        $('#branchid').val(el.data('branchid'));
        //$('#dLabel').stop( true, true ).effect("highlight", {}, 1000);
        if(el.data('branchid')==$('.btn-go').data('branchid'))
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
        
        //console.log($('.btn-go').data('branchid'));
      });

      Highcharts.setOptions({
          chart: {
              style: {
                  fontFamily: "Helvetica"
              }
          }
      });

      

      $('#container').highcharts({
        data: {
          table: 'datatable'
        },
        chart: {
          type: 'line',
          height: 300,
          spacingRight: 0,
          marginTop: 40,
          marginRight: 25,    //      marginRight: 10,
          zoomType: 'x',
          panning: true,
          panKey: 'shift'
        },
        colors: ['#15C0C2', '#D36A71', '#B09ADB', '#5CB1EF', '#F49041', '#f15c80', '#F9CDAD', '#91e8e1', '#8d4653'],
        title: {
            text: ''
        },
        xAxis: [
          {
            gridLineColor: "#CCCCCC",
            type: 'datetime',
            //tickInterval: 24 * 3600 * 1000, // one week
            tickWidth: 0,
            gridLineWidth: 0,
            lineColor: "#C0D0E0", // line on X axis
            labels: {
              align: 'center',
              x: 3,
              y: 15,
              formatter: function () {
                //var date = new Date(this.value);
                //console.log(date.getDay());
                //console.log(date);
                var date = moment(this.value);
                return date.year()+' '+date.isoWeek();
                return Highcharts.dateFormat('%W %e', this.value);
              }
            }
          }
        ],
        yAxis: [{ // left y axis
          min: 0,
          title: {
            text: null
          },
          labels: {
            align: 'left',
            x: 3,
            y: 13,
            format: '{value:.,0f}'
          },
            showFirstLabel: false
          },
          { // right y axis
            min: 0,
            title: {
              text: null
            },
            labels: {
              align: 'right',
              x: -3,
              y: 13,
              format: '{value:.,0f}'
            },
            showFirstLabel: false,
            opposite: true
          }
        ], 
        legend: {
          align: 'left',
          verticalAlign: 'top',
          y: -10,
          floating: true,
          borderWidth: 0
        },
        tooltip: {
          shared: true,
          crosshairs: true
        },
        plotOptions: { 
          series: {
            cursor: 'pointer',
            point: {
              events: {
                click: function (e) {
                console.log(Highcharts.dateFormat('%Y-%m-%d', this.x));
                /*
                  hs.htmlExpand(null, {
                      pageOrigin: {
                          x: e.pageX,
                          y: e.pageY
                      },
                      headingText: this.series.name,
                      maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) +':<br/> '+
                          this.y +' visits',
                      width: 200
                  });
                */
                }
              }
            },
            marker: {
              symbol: 'circle',
              radius: 3
            },
            lineWidth: 2,
            dataLabels: {
                enabled: false,
                align: 'right',
                crop: false,
                formatter: function () {
                  console.log(this.series.index);
                  return this.series.name;
                },
                x: 1,
                verticalAlign: 'middle'
            }
          }
        },
        exporting: {
          enabled: false
        },
        series: [
          {
            type: 'line',
            yAxis: 0
          }, {
            type: 'line',
            yAxis: 0
          }, {
            type: 'line',
             dashStyle: 'shortdot',
            yAxis: 1
          }, {
            type: 'line',
            yAxis: 0
          }, {
            type: 'line',
            //dashStyle: 'shortdot',
            yAxis: 0
          }, {
            type: 'line',
            yAxis: 0
          }
        ]
      });







    

      $('#h-tot-sales').text($('#f-tot-sales').text());
      $('#h-tot-purch').text($('#f-tot-purch').text());
      $('#h-tot-mancost').text($('#f-tot-mancost').text());
      $('#h-tot-tips').text($('#f-tot-tips').text());


      $('.date-type-selector .dropdown-menu li a').on('click', function(e){
      
        e.preventDefault();

        var type = $(this).data('date-type');
       
          $('#date-type-name').text($(this)[0].text);
    
          
          $('.dp-container').html(getDatePickerLayout(type));

          initDatePicker();
        
      });

      


      var getDatePickerLayout = function(type) {
      console.log(type);
      var html = '';
      switch (type) {
        case 'weekly':
          html = '<select id="fr-year" class="btn btn-default dp-w-fr" style="height:34px; padding: 6px 3px 6px 12px">'
              @for($y=2015;$y<2021;$y++)
                +'<option value="{{$y}}" {{ $dr->fr->year==$y?'selected':'' }}>{{$y}}</option>'
              @endfor
            +' </select>'
            +'<select id="fr-week" class="btn btn-default dp-w-fr" style="height:34px; padding: 6px 0px 6px 12px">'
              @for($x=1;$x<=lastWeekOfYear($dr->fr->year);$x++)
              +'<option value="{{$x}}" {{ $dr->fr->weekOfYear==$x?'selected':'' }}>{{$x}}</option>'
              @endfor
            +'</select>'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<select id="to-year" class="btn btn-default dp-w-to" style="height:34px; padding: 6px 3px 6px 12px">'
              @for($y=2015;$y<2021;$y++)
                +'<option value="{{$y}}" {{ $dr->to->year==$y?'selected':'' }}>{{$y}}</option>'
              @endfor
            +'</select>'
            +'<select id="to-week" class="btn btn-default dp-w-to" style="height:34px; padding: 6px 0px 6px 12px">'
              @for($x=1;$x<=lastWeekOfYear($dr->to->year);$x++)
                +'<option value="{{$x}}" {{ $dr->to->weekOfYear==$x?'selected':'' }}>{{$x}}</option>'
              @endfor
            +'</select>';
            $('#dp-form').prop('action', '/status/branch/week');
          break;
        case 'monthly':
          html = '<label class="btn btn-default" for="dp-m-date-fr">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-m-date-fr" value="{{ $dr->fr->format('m/Y') }}" style="max-width: 110px;">'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-m-date-to" value="{{ $dr->to->format('m/Y') }}" style="max-width: 110px;">'
            +'<label class="btn btn-default" for="dp-m-date-to">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>';
            $('#dp-form').prop('action', '/status/branch/month');
          break;
        case 'quarterly':
          html = '<select id="fr-y" class="btn btn-default dp-q-fr" style="height:34px; padding: 6px 3px 6px 12px">'
              @for($y=2015;$y<2021;$y++)
                +'<option value="{{$y}}" {{ $dr->fr->year==$y?'selected':'' }}>{{$y}}</option>'
              @endfor
            +'</select>'
            +'<select id="fr-q" class="btn btn-default dp-q-fr" style="height:34px; padding: 6px 0px 6px 12px">'
              @for($x=0;$x<4;$x++)
              +'<option value="{{pad(($x*3)+1)}}-01" {{ $dr->fr->quarter==$x+1?'selected':'' }}>{{$x+1}}</option>'
              @endfor
            +'</select>'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<select id="to-y" class="btn btn-default dp-q-to" style="height:34px; padding: 6px 3px 6px 12px">'
              @for($y=2015;$y<2021;$y++)
                +'<option value="{{$y}}" {{ $dr->to->year==$y?'selected':'' }}>{{$y}}</option>'
              @endfor
            +'</select>'
            +'<select id="to-q" class="btn btn-default dp-q-to" style="height:34px; padding: 6px 0px 6px 12px">'
              @for($x=0;$x<4;$x++)
                +'<option value="{{pad(($x*3)+1)}}-01" {{ $dr->to->quarter==$x+1?'selected':'' }}>{{$x+1}}</option>'
              @endfor
            +'</select>';
            $('#dp-form').prop('action', '/status/branch/quarter');
          break;
        case 'yearly':
          html = '<label class="btn btn-default" for="dp-y-date-fr">'
            +'<span class="glyphicon glyphicon-calendar"></span></label>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-y-date-fr" value="{{ $dr->fr->format('Y') }}" style="max-width: 110px;">'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-y-date-to" value="{{ $dr->to->format('Y') }}" style="max-width: 110px;">'
            +'<label class="btn btn-default" for="dp-y-date-to">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>';
          $('#dp-form').prop('action', '/status/branch/year');
          break;
        default:
          html = '<label class="btn btn-default" for="dp-date-fr">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">'
            +'<label class="btn btn-default" for="dp-date-to">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>';
          $('#dp-form').prop('action', '/status/branch');
      }

      return html;
      }





    });
    
 
  </script>
@endsection
