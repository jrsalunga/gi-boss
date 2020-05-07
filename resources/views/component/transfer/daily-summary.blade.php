@extends('master')

@section('title', '-  Daily Transfer Summary')

@section('body-class', 'transfer-daily-summary')

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
    <li><a href="/component">Component</a></li>
    <li><a href="/component/transfer/daily">Transfers</a></li>
    <li class="active">Daily Summary @if(!is_null($branch))<small>({{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }})</small>@endif</li>   
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
          <!--
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix hidden-xs" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/component/transfer/daily', 'method' => 'get', 'id'=>'filter-form']) !!}
            <!--
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid="'. $branch->id  }}">
            -->  
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

          <div class="btn-group hidden-xs" style="margin-left: 5px;">
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
      
          <div class="btn-group pull-right clearfix dp-container hidden-xs" role="group">
            
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-ate-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
        
          </div><!-- end btn-grp -->

         
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <?php
      $ttcost = $tac = $trs = $tnc = $tem = 0;
    ?>
    @if($ds>0)
    <div class="row">
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Emp Meal</p>
        <h3 id="tem" style="margin:0"></h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Food Item Cost</p>
        <h3 id="tac" style="margin:0"></h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Food Item Transfered</p>
        <h3 id="trs" style="margin:0"></h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Cost</p>
        <h3 id="ttcost" style="margin:0"></h3>
      </div>

    </div>
    <div class="row">
      <div class="col-md-12">
        <div id="container" style="overflow: hidden;"></div>
      </div>

      <div class="col-md-12">
        <div class="table-responsive">
        <table class="table table-condensed table-hover table-striped table-sort" style="margin-top: 0;">
          <thead>
            <tr>
              <th>Date</th>
              <th class="text-right" style="width:17%;">Total Cost</th>
              <th class="text-right" style="width:17%;">Food Item Cost</th>
              <th class="text-right" style="width:17%;">Drinks/Beers</th>
              <th class="text-right" style="width:17%;">Resto Supplies</th>
              <th class="text-right" style="width:17%;">Trans Emp Meal</th>
            </tr>
          </thead>
          <tbody>
            
            @foreach($ds as $d)
              <tr>
                <td>
                  {{ $d->date->format('M j, D') }}
                </td>
                @if(is_null($d->dailysale))
                <td></td><td></td><td></td><td></td><td></td>
                @else
                <?php
                  $actual_tranfer = $d->dailysale->transcos-$d->dailysale->emp_meal;
                  $rs = $d->dailysale->transcost-($d->dailysale->transcos+$d->dailysale->transncos);

                  $ttcost += $d->dailysale->transcost;
                  $tac += $actual_tranfer;
                  $tnc += $d->dailysale->transncos;
                  $trs += $rs;
                  $tem += $d->dailysale->emp_meal;
                ?>
                <td class="text-right">
                  <a href="/component/transfer?branchid={{ $branch->lid() }}&fr={{ $d->date->format('Y-m-d') }}&to={{ $d->date->format('Y-m-d') }}">
                  {{ nf($d->dailysale->transcost) }}
                  </a>
                </td>
                <td class="text-right">{{ nf($actual_tranfer) }}</td>
                <td class="text-right">{{ nf($d->dailysale->transncos) }}</td>
                <td class="text-right">{{ nf($rs) }}</td>
                <td class="text-right text-muted">{{ nf($d->dailysale->emp_meal) }}</td>
                @endif
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td class="text-right"><b class="text-muted">
                <a href="/component/transfer?branchid={{ $branch->lid() }}&fr={{ $d->date->copy()->startOfMonth()->format('Y-m-d') }}&to={{ $d->date->copy()->endOfMonth()->format('Y-m-d') }}">
                {{ nf($ttcost) }}
                </a>
              </b></td>
              <td class="text-right">
                <b class="text-muted">{{ nf($tac) }}</b>
                <div class="text-muted">
                  +{{ nf($tem) }}
                </div>
                <div>
                  <b class="text-muted">{{ nf($tac+$tem) }}</b>
                </div>
              </td>
              <td class="text-right"><b class="text-muted">{{ nf($tnc) }}</b></td>
              <td class="text-right"><b class="text-muted">{{ nf($trs) }}</b></td>
              <td class="text-right"><b class="text-muted">{{ nf($tem) }}</b></td>
            </tr>
          </tfoot>
        </table>
  
        <?php
          $ttcost = $tac = $trs = $tnc = $tem = 0;
        ?>
        <table id="datatable" class="tb-data table" style="display:none;">
          <thead>
            <tr>
              <th>Date</th>
              <th>Total Transfered Cost</th>
              <th>Transfered Food Item Cost</th>
              <th>Transfered Drinks/Beers</th>
              <th>Transfered Resto Supplies</th>
              <th>Transfered Employee Meal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($ds as $d)
            <tr>
              <td>{{ $d->date->format('Y-m-d') }}</td>
              @if(is_null($d->dailysale))
                <td>0</td><td>0</td><td>0</td><td>0</td><td>0</td>
              @else
              <?php
                  $actual_tranfer = $d->dailysale->transcos-$d->dailysale->emp_meal;
                  $rs = $d->dailysale->transcost-($d->dailysale->transcos+$d->dailysale->transncos);

                  $ttcost += $d->dailysale->transcost;
                  $tac += $actual_tranfer;
                  $tnc += $d->dailysale->transncos;
                  $trs += $rs;
                  $tem += $d->dailysale->emp_meal;
                ?>
              <td>{{ number_format($d->dailysale->transcost, '2', '.', '') }}</td>
              <td>{{ number_format($actual_tranfer, '2', '.', '') }}</td>
              <td>{{ number_format($d->dailysale->transncos, '2', '.', '') }}</td>
              <td>{{ number_format($rs, '2', '.', '') }}</td>
              <td>{{ number_format($d->dailysale->emp_meal, '2', '.', '') }}</td>
              @endif
              </tr>
            @endforeach
          </tbody>
        </table>
        </div><!-- table-responsive  -->
  
      </div>
    </div>
    @else
      <!-- no data  -->
    @endif
  </div>



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
            <label>Date Range:</label>
            <div>
            <div class="btn-group" role="group">
            <label class="btn btn-default" for="mdl-dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="mdl-dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="mdl-dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
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
      spacingRight: 0,
      marginTop: 40,
      //marginRight: 20,
      marginRight: 10,
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
        yAxis: 0,
      }, {
        type: 'line',
        yAxis: 0,
      }, {
        type: 'line',
         dashStyle: 'shortdot',
        yAxis: 1
      }
    ]
  });

  
  
  $('.mdl-btn-go').on('click', function(){
    //loader();
    $('#filter-form').submit();
  });
  

  $('#ttcost').text('{{ nf($ttcost) }}');
  $('#tac').text('{{ nf($tac) }}');
  //$('#trs').text('{{ nf($trs) }}');
  $('#trs').text('{{ nf($tac+$tem) }}');
  $('#tem').text('{{ nf($tem) }}');
    

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