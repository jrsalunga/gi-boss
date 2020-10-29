@extends('master')

@section('title', '-  Sales Type Summary')

@section('body-class', 'saletype')

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
    <li><a href="/"><span class="gly gly-shop"></span> </a></li>
    <li><a href="/saletype/branch">Sales Type</a></li>
    <li class="active">Date Range @if(!is_null($branch))<small>({{ $dr->fr->format('M j') }} - {{ $dr->to->format('M j') }})</small>@endif</li>   
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group visible-xs-inline-block pull-left" role="group">
            <div style="padding: 6px 12px;">
              <span class="gly gly-shop"></span>
              @if(is_null(($branch)))
                -
              @else
                <span>{{ $branch->code }}</span>
              @endif
            </div>
          </div>
          <div class="btn-group visible-xs-inline-block pull-right" role="group">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#mdl-form">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </button>
          </div>
          <div class="btn-group btn-group pull-right clearfix hidden-xs" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/saletype/branch', 'method' => 'get', 'id'=>'filter-form']) !!}
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

          <div class="btn-group hidden-xs">
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
      
          <div class="btn-group pull-right clearfix dp-container hidden-xs hidden-sm" role="group" >
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly="" type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly="" type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
          </div>

          <div class="btn-group pull-right clearfix hidden-md hidden-lg" role="group" style="margin-right: 5px;">
            <div class="btn btn-default" style="pointer-events: none;">{{ $dr->fr->format('m/d/Y') }} - {{ $dr->to->format('m/d/Y') }}</div>
          </div>


         

         
          
        </div>
      </div>
    </nav>

  @include('_partials.alerts')

  @if(count($datas)>0 && count($stats)>0)
  <div class="row">
    <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
      <p style="margin-bottom:0">Total Sales</p>
      <h3 id="h-tot-sales" style="margin:0">0</h3>
    </div>
    <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
      <p style="margin-bottom:0">DINEIN Sales</p>
        <h3 style="margin:0">
        <small id="h-tot-dinein-pct"></small>
        <span id="h-tot-dinein">0</span>
      </h3>
    </div>
    <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
      <p style="margin-bottom:0">ONLRID Sales</p>
      <h3 style="margin:0">
        <small id="h-tot-onlrid-pct"></small>
        <span id="h-tot-onlrid">0</span>
      </h3>
    </div>
    <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
      <p style="margin-bottom:0">Ave DINEIN Sales/Customer</p>
      <h3 style="margin:0">
        <small id="h-ave-dinein" title="Average Dine-In Sales" data-toggle="tooltip" class="help"></small>
        <span id="h-ave-dinein-cust" title="Average Dine-In Customer" data-toggle="tooltip" class="help">0</span>
      </h3>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12" style="margin: 20px 0;">
      <div id="container" style="overflow: hidden;"></div>
    </div>
      

    <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th rowspan="2">Day</th>
            <th rowspan="2">Daily Sales</th>
            @foreach($stats as $header => $stat)
              <th colspan="4">{{ $header }}</th>
            @endforeach
          </tr>
          <tr>
            @foreach($stats as $header => $stat)
              <th>Sales</th>
              <th>Pct</th>
              <th>Cust</th>
              <th>Txn</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          <?php $tot_sales = $tot_valid = 0 ?>
          @foreach($datas as $key => $data)
          <tr
            @if($data['date']->isToday())
              class="bg-success"
            @elseif($data['date']->dayOfWeek==0)
              class="bg-warning"
            @else

            @endif
          >
          <td>
            <a href="/product/sales?table=&item=&itemid=&branchid={{$branch->lid()}}&fr={{$data['date']->format('Y-m-d')}}&to={{$data['date']->format('Y-m-d')}}">
            {{ $data['date']->format('M j, D') }}
            </a>
          </td>
          <td class="text-right">{{ $data['tot_sales']>0?nf($data['tot_sales']):'' }}</td>
          <?php 

            $tot_sales += $data['tot_sales']; 
            if ($data['tot_valid']>0)
              $tot_valid = $data['tot_valid'];
          ?>
          @if(!is_null($data['data']))
            @foreach($data['data'] as $key => $val)
              <td class="text-right">{{ nf($val['total']) }}</td>
              <td class="text-right"><small><em>{{ $val['ave_sales']>0?$val['ave_sales'].'%':'' }}</em></small></td>
              <td class="text-right"><small>{{ $val['customer'] }}</small></td>
              <td class="text-right"><small>{{ $val['txn'] }}</small></td>
            @endforeach
          @endif
          </tr>
          @endforeach
        </tbody>
        <tfoot style="color: #95A5A6;">
          <tr>
            <th style="font-weight: normal; color: #000;">{{ $tot_valid }}/{{ ($dr->diffInDays()+1) }}</th>
            <th class="text-right" style="font-weight: normal; color: #000;">
              <strong id="f-tot-sales">{{ nf($tot_sales) }}</strong>
              <div>
                  <small><em>{{ nf($tot_sales/$tot_valid) }}</em></small>
                </div>
            </th>
            @foreach($stats as $header => $stat)
              <th class="text-right" style="font-weight: normal; color: #000;">
                <strong id="{{ in_array($header, ['DINEIN','ONLRID'])?'f-tot-'.strtolower($header):'' }}">{{ nf($stat['sales']) }}</strong>
                <div>
                  <small><em id="{{ in_array($header, ['DINEIN'])?'f-ave-'.strtolower($header):'' }}">{{ nf($stat['ave_sales']) }}</em></small>
                </div>
              </th>
              <th class="text-right" style="font-weight: normal; color: #000;">
                <strong id="{{ in_array($header, ['DINEIN','ONLRID'])?'f-tot-'.strtolower($header).'-pct':'' }}">{{ nf(($stat['sales']/$tot_sales)*100) }}%</strong>
                <div>
                  <small><em></em></small>
                </div>
              </th>
              <th class="text-right" style="font-weight: normal; color: #000;">
                <strong>{{ $stat['customer'] }}</strong>
                <div>
                  <small><em id="{{ in_array($header, ['DINEIN'])?'f-ave-'.strtolower($header).'-cust':'' }}">{{ nf($stat['ave_customer'])+0 }}</em></small>
                </div>
              </th>
              <th class="text-right" style="font-weight: normal; color: #000;">
                <strong>{{ $stat['txn'] }}</strong>
                <div>
                  <small><em>{{ nf($stat['ave_txn'])+0 }}</em></small>
                </div>
              </th>
            @endforeach
          </tr>
          @if($dr->diffInDays()>10)
          <tr>
            <th rowspan="3"></th>
            <th rowspan="3"></th>
            @foreach($stats as $header => $stat)
              <th>Sales</th>
              <th>Pct</th>
              <th>Cust</th>
              <th>Txn</th>
            @endforeach
          </tr>
          <tr>
            @foreach($stats as $header => $stat)
              <th colspan="4">{{ $header }}</th>
            @endforeach
          </tr>
          @endif
        </tfoot>
      </table>
      <table id="datatable" class="tb-data table"  style="display:none;">
          <thead>
            <tr>
              <th>Date</th>
              <th>Sales</th>
              @foreach($stats as $header => $stat)
                <th>{{ $header }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($datas as $key => $data)
            <tr>
              <td>{{ $data['date']->format('Y-m-d') }}</td>
              <td>{{ number_format($data['tot_sales'],2,'.','') }}</td>
              @foreach($data['data'] as $key => $val)
                <td>{{ number_format($val['total'],2,'.','') }}</td>
              @endforeach
            </tr>
            @endforeach
          </tbody>
        </table>
    </div>
    </div>
  </div>
  @else
    @if(is_null($branch))
    
    @else
      No Record
    @endif
  @endif
</div><!-- end: .container-fluid  -->
      
<div class="modal fade" id="mdl-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="mdl-formLabel">Filter Parameters</h4>
      </div>
      <div class="modal-body">
        <div>
          <div class="form-group">
            <label>Branch:</label>
            <div class="dropdown">
              <button id="dLabel" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-shop"></span>
                @if(is_null(($branch)))
                  <span class="br-code">Select Branch</span>
                  <span class="br-desc"></span>
                @else
                  <span class="br-code">{{ $branch->code }}</span>
                  <span class="br-desc">- {{ $branch->descriptor }}</span>
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
          
          <div class="form-group">
            <label>Month Range:</label>
            <div>
              <div class="btn-group" role="group">
                <label class="btn btn-default" for="mdl-dp-m-date-fr">
                  <span class="glyphicon glyphicon-calendar"></span>
                </label>
                <input readonly="" type="text" class="btn btn-default dp" id="mdl-dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
                
                <div class="btn btn-default" style="pointer-events: none;">-</div>
                <input readonly="" type="text" class="btn btn-default dp" id="mdl-dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
                <label class="btn btn-default" for="mdl-dp-date-to">
                  <span class="glyphicon glyphicon-calendar"></span>
                </label>
              </div>
            </div>
          </div>

          
            

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success pull-right mdl-btn-go" data-dismiss="modal" data-toggle="loader"><span class="gly gly-search"></span> Go </button>
        <button type="button" class="btn btn-link pull-right" data-dismiss="modal">Discard</button>
      </div>
    </div>
  </div>
</div>
@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="/js/dr-picker.js"> </script>
	
	<script>
  
  


    
  moment.locale('en', { week : {
    dow : 1 // Monday is the first day of the week.
  }});

  Highcharts.setOptions({
    lang: {
      thousandsSep: ','
  }});

  $(document).ready(function(){

  $('[data-toggle="tooltip"]').tooltip();


  initDatePicker();
  branchSelector();
  mdlBranchSelector();

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
        //spacingRight: 20,
        marginTop: 40,
        //marginRight: 20,
        //marginRight: 20,
        zoomType: 'x',
        panning: true,
        panKey: 'shift'
      },
      colors: ['#15C0C2', '#B09ADB', '#5CB1EF', '#F49041', '#f15c80', '#F9CDAD', '#91e8e1','#D36A71', '#8d4653'],
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
        },
        { // right y axis
        min: 0,
          title: {
            text: null
          },
          labels: {
            align: 'right',
            x: -10,
            y: 15,
            format: '{value:.,0f}'
          },
            showFirstLabel: false,
            opposite: true
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
      },
      exporting: {
        enabled: false
      },
      series: [
        {
          type: 'line',
          yAxis: 0
        }
        @foreach($stats as $header => $stat)
        , {
          type: 'line',
          dashStyle: 'shortdot',
          yAxis: 0,
        }
        @endforeach
      ]
    });

  $('#h-tot-sales').text($('#f-tot-sales').text());
  $('#h-tot-dinein').text($('#f-tot-dinein').text());
  $('#h-tot-dinein-pct').text($('#f-tot-dinein-pct').text());
  $('#h-tot-onlrid').text($('#f-tot-onlrid').text());
  $('#h-tot-onlrid-pct').text($('#f-tot-onlrid-pct').text());
  $('#h-ave-dinein-cust').text($('#f-ave-dinein-cust').text());
  $('#h-ave-dinein').text($('#f-ave-dinein').text());

  
  
  $('.mdl-btn-go').on('click', function(){
    //loader();
    $('#filter-form').submit();
  });
  

 
  
    
  

    $('.show.toggle').on('click', function(){
      var div = $(this).siblings('div.show');
      if(div.hasClass('less')) {
        div.removeClass('less');
        div.addClass('more');
        $(this).text('show less');
      } else if(div.hasClass('more')) {
        div.removeClass('more');
        div.addClass('less');
        $(this).text('show more');
      }
    });

    

    


   	


    var submitForm  = function(){
      console.log('submit Form');
      $('#filter-form').submit();
    }
 
  });

  </script>

@endsection