@extends('master')

@section('title', '- Branch Status')

@section('body-class', 'branch-status')

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
    <li class="active">Branch Analytics</li>
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
            {!! Form::open(['url' => '/status/branch', 'method' => 'post']) !!}
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid='. $branch->id  }}>
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->id }}">
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->fr->format('Y-m-d') }}">
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
                  <a href="#" data-desc="{{ $b->descriptor }}" data-code="{{ $b->code }}" data-branchid="{{ $b->id }}">{{ $b->code }}</a>
                </li>
                @endforeach
              </ul>
            </div> <!-- .dropdown -->
          </div>

          <!--
          <div class="btn-group pull-right clearfix" style="margin-left: 5px;">
            <div type="button" class="btn btn-default" style="pointer-events: none;">
              <span class="gly gly-shop"></span>
              <span class="br-code">Select Branch</span>
            </div>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="caret"></span>
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" style="max-height: 400px; overflow-y: scroll;">
              
            </ul>
          </div>
          -->
          
          <?php


          ?>
          <div class="btn-group pull-right clearfix" role="group">
            <!--
            <a href="/" class="btn btn-default" title="">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            -->
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('D, M j') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('D, M j') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <!--
            <a href="/" class="btn btn-default" title="">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            -->
          </div><!-- end btn-grp -->
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')


    <div class="row">
      
      @if(is_null($dailysales))

      @else

      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 15px;">
        <p style="margin-bottom:0">Total Sales</p>
        <h3 id="h-tot-sales" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 15px;">
        <p style="margin-bottom:0">Total Customer</p>
        <h3 id="h-tot-customer" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 15px;">
        <p style="margin-bottom:0">Total Manpower Cost</p>
        <h3 id="h-tot-mancost" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 15px;">
        <p style="margin-bottom:0">Total Sales per Total Emp</p>
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
                  <th>Date</th>
                  <th class="text-right">Sales</th>
                  <th class="text-right">Customer</th>
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
                $tot_custcount = 0;
                $tot_headspend = 0;
                $tot_empcount = 0;
                $tot_sales_emp = 0;
                $tot_mancost = 0;
                $tot_mancostpct = 0;
                $tot_tips = 0;
                $tot_tipspct = 0;

              ?>
            @foreach($dailysales as $d)
            <tr {{ $d->date->dayOfWeek=='0' ? 'class=warning':''  }}>
              <td data-sort="{{$d->date->format('Y-m-d')}}">{{ $d->date->format('M j, D') }}</td>
              @if(!is_null($d->dailysale))
              <td class="text-right" data-sort="{{ number_format($d->dailysale['sales'], 2,'.','') }}">{{ number_format($d->dailysale['sales'], 2) }}</td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['custcount'], 0) }}">{{ number_format($d->dailysale['custcount'], 0) }}</td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['headspend'], 2,'.','') }}">{{ number_format($d->dailysale['headspend'], 2) }}</td>
              <td class="text-right" data-sort="{{ $d->dailysale['empcount'] }}">{{ $d->dailysale['empcount'] }}</td>
              <?php
                $s = $d->dailysale['empcount']=='0' ? '0.00':($d->dailysale['sales']/$d->dailysale['empcount']);
              ?>
              <td class="text-right" data-sort="{{$s}}">{{number_format($s,2)}}</td>
              <?php
                $mancost = $d->dailysale['empcount']*$branch->mancost;
              ?>
              <td class="text-right" data-sort="{{ number_format($mancost,2,'.','') }}">{{ number_format($mancost,2) }}</td>
              <td class="text-right" data-sort="{{ $d->dailysale['mancostpct'] }}">{{ $d->dailysale['mancostpct'] }}</td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['tips'],2,'.','') }}">{{ number_format($d->dailysale['tips'],2) }}</td>
              <td class="text-right" data-sort="{{ $d->dailysale['tipspct'] }}">{{ $d->dailysale['tipspct'] }}</td>
              <?php
                $tot_sales      += $d->dailysale['sales'];
                $tot_custcount  += $d->dailysale['custcount'];
                $tot_headspend  += $d->dailysale['headspend'];
                $tot_empcount   += $d->dailysale['empcount'];

                if($d->dailysale['empcount']!='0')
                  $tot_sales_emp += number_format(($d->dailysale['sales']/$d->dailysale['empcount']),2, '.', '');

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
              @endif
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td>
                <strong>
                {{ count($dailysales) }}
                {{ count($dailysales) > 1 ? 'days':'day' }}
                </strong>
              </td>
              <td class="text-right">
                <strong id="f-tot-sales">{{ number_format($tot_sales,2) }}</strong>
                <div>
                <em><small title="{{$tot_sales}}/{{count($dailysales)}}">
                  {{ number_format($tot_sales/count($dailysales),2) }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong id="f-tot-customer">{{ number_format($tot_custcount, 0) }}</strong>
                <div>
                <em><small title="{{$tot_custcount}}/{{count($dailysales)}}">
                  {{ number_format($tot_custcount/count($dailysales),2) }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small title="{{$tot_headspend}}/{{count($dailysales)}}">
                  {{ number_format($tot_headspend/count($dailysales),2) }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_empcount,0) }}</strong>
                <div>
                <em><small title="{{$tot_empcount}}/{{count($dailysales)}}">
                  {{ number_format($tot_empcount/count($dailysales),2) }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small id="f-tot-tips" title="{{$tot_sales}}/{{$tot_empcount}}" data-ave="{{ number_format($tot_sales_emp/count($dailysales),2) }}">
                  @if($tot_empcount!='0')
                    {{ number_format($tot_sales/$tot_empcount,2) }}
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong id="f-tot-mancost">{{ number_format($tot_mancost,2) }}</strong>
                <div>
                <em><small title="{{$tot_mancost}}/{{count($dailysales)}}">
                  {{ number_format($tot_mancost/count($dailysales),2) }}
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
                <em><small>{{ number_format($tot_tips/count($dailysales),2) }}</small></em>
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

        <table id="datatable" class="tb-data" style="display:none;">
            <thead>
              <tr>
                  <th>Date</th>
                  <th>Sales</th>
                  <th>Tips</th>
                  <th>Man Cost</th>
                  <th>Sales/Emp</th>
              </tr>
            </thead>
            @foreach($dailysales as $d)
            <tr>
              <td>{{ $d->date->format('Y-m-d') }}</td>
              @if(!is_null($d->dailysale))
              <td>{{ $d->dailysale['sales'] }}</td>
              <td>{{ $d->dailysale['tips'] }}</td>
              <td>{{ ($d->dailysale['empcount']*$branch->mancost) }}</td>
              <td>{{ $d->dailysale['empcount']=='0' ? 0:number_format(($d->dailysale['sales']/$d->dailysale['empcount']), 2, '.', '') }}</td>
              @else 
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              @endif
              </tr>
            @endforeach
          <tbody>
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
    $(document).ready(function(){
    
      $('#dp-date-fr').datetimepicker({
        defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'ddd, MMM D',
        showTodayButton: true,
        ignoreReadonly: true
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
        defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'ddd, MMM D',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
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

    var arr = [];

    $('#container').highcharts({
      data: {
          table: 'datatable'
      },
      chart: {
        type: 'line',
        height: 300,
        spacingRight: 0,
        marginTop: 40,
        //marginRight: 20,
        marginRight: 10,
        zoomType: 'x',
        panning: true,
        panKey: 'shift'
      },
      colors: ['#15C0C2', '#B09ADB', '#5CB1EF', '#F49041', '#D36A71', '#f15c80', '#F9CDAD', '#91e8e1', '#8d4653'],
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
              return Highcharts.dateFormat('%b %e', this.value);
            }
          },
          plotLines: arr
        },
        { // slave axis
          type: 'datetime',
          linkedTo: 0,
          opposite: true,
          tickInterval: 7 * 24 * 3600 * 1000,
          tickWidth: 0,
          labels: {
            formatter: function () {
              arr.push({ // mark the weekend
                color: "#CCCCCC",
                width: 1,
                value: this.value-86400000,
                zIndex: 3
              });
              //return Highcharts.dateFormat('%a', (this.value-86400000));
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
            y: 16,
            format: '{value:.,0f}'
          },
            showFirstLabel: false
          }], 
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
      }
    });



    $('#h-tot-sales').text($('#f-tot-sales').text());
    $('#h-tot-customer').text($('#f-tot-customer').text());
    $('#h-tot-mancost').text($('#f-tot-mancost').text());
    $('#h-tot-tips').text($('#f-tot-tips').text());




    });
    
 
  </script>
@endsection
