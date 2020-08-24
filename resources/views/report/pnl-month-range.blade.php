@extends('master')

@section('title', '-  Month Range Direct P&L Summary')

@section('body-class', 'month-range-pnl')

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
    <!--<li><a href="/report">Report</a></li>-->
    <li><a href="/report/pnl-summary">Direct P&amp;L Summary</a></li>
    <li class="active">Month Range @if(!is_null($branch))<small>({{ $dr->fr->format('F') }})</small>@endif</li>   
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
            {!! Form::open(['url' => '/report/pnl/month-range', 'method' => 'get', 'id'=>'filter-form']) !!}
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
      
          <div class="btn-group pull-right clearfix dp-container" role="group">
            <label class="btn btn-default" for="dp-m-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly="" type="text" class="btn btn-default dp" id="dp-m-date-fr" value="{{ $dr->fr->format('m/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly="" type="text" class="btn btn-default dp" id="dp-m-date-to" value="{{ $dr->to->format('m/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-m-date-to">
              <span class="glyphicon glyphicon-calendar"></span>\
            </label>
          </div>


          <div class="btn-group pull-right clearfix" role="group">
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                  <span id="date-type-name">Month Range</span>
                  <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="date-type">
                  <li><a href="/report/pnl-summary" data-date-type="month">Month</a></li>
                  <li><a href="#" data-date-type="month-range">Month Range</a></li>
                </ul>
              </div>
            </div>
          </div>

         
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <?php
      $tpurch = $ttrans = $tnet = $tpct = $direct_cost = $direct_profit = $dailysales = $xtnet = 0;
    ?>
    @if(count($datas)>0)
   
    @if(!is_null($ms))
    <div class="row">
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Sales</p>
        <h3 style="margin:0">{{ nf($ms->sales) }}</h3>
        <?php
          $dailysales = $ms->sales;
        ?>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Cost of Goods</p>
        <h3 style="margin:0" id="view-directcost"></h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Operational Expense</p>
        <h3 style="margin:0" id="view-totexpense"></h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Direct Profit</p>
        <h3 style="margin:0" id="view-directprofit"></h3>
      </div>

    </div>
    @endif
    
    <div class="row">
      <div class="col-md-12">
        <div id="container-ssss" style="overflow: hidden;"></div>
      </div>
  
      <?php $ttpurch = $tttrans = 0 ?>

      <div class="col-md-12"style="margin-top: 30px;">
        <div class="panel panel-default">
          <div class="panel-heading">Cost of Goods Purchases Summary</div>
          <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-condensed table-hover table-striped table-sort" style="margin-top: 0;">
          <thead>
            <tr>
              <th style="width:5%;">Code</th>
              <th>Category</th>
              <th class="text-right" style="width:17%;">Purchased</th>
              <th class="text-right" style="width:17%;">Transfered</th>
              <th class="text-right" style="width:17%;" title="Purchased - Transfered = Net Purchased">Purchases less Transfers</th>
              <th class="text-right" style="width:17%;">% on Food Sales</th>
            </tr>
          </thead>
          <tbody>
            
            @foreach($datas as $data)
              <tr data-expenseid="{{ $data['expenseid'] }}">
                <td>{{ $data['expensecode'] }}</td>
                <td>{{ $data['expense'] }}</td>
                <td class="text-right">
                  <a href="/component/purchases?table=expense&item={{$data['expense']}}&itemid={{$data['expenseid']}}&branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}" target="_blank" title="Link to Purchases Report">
                  {{ nf($data['purch']) }}
                  </a>

                </td>
                <td class="text-right">
                  <a href="/component/transfer?table=expense&itemid={{$data['expenseid']}}&branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}&skipname=true" target="_blank" title="Link to Stock Transfer Raw Log Report">
                  {{ nf($data['trans']) }}
                  </a>
                </td>
                <td class="text-right">{{ nf($data['net']) }}</td>
                <td class="text-right" title="({{ $data['net'] }}/{{ $data['food_sales'] }} )*100={{$data['pct']}}" data-toogle="tooltip">{{ nf($data['pct']) }}</td>
              </tr>
              <?php
                $tpurch += $data['purch'];
                $ttrans += $data['trans'];
                $tnet += $data['net'];
                $tpct += $data['pct'];
                $fs = $data['food_sales'];

                
                $ttpurch += $data['purch'];
                $tttrans += $data['trans'];
              ?>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td class="text-right">Total:</td>
              <td class="text-right">
                <b class="text-muted">
                    {{ nf($tpurch) }}
                </b>
              </td>
              <td class="text-right"><b class="text-muted">
                <a href="/component/transfer/daily?branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}" target="_blank" title="link to Stock Transfer Summary">
                {{ nf($ttrans) }}
                </a>
              </b></td>
              <td class="text-right"><b class="text-muted">{{ nf($tnet) }}</b></td>
              <td class="text-right"><b class="text-muted" title="{{$tnet}}/{{$fs}}={{ $fs>0?($tnet/$fs)*100:0 }}">{{ nf($tpct) }}</b></td>
            </tr>
          </tfoot>
        </table>
        </div><!-- table-responsive  -->
        
        <div class="table-responsive" style="margin-top: 10px;">
        <table class="table table-condensed table-hover table-striped table-sort" style="margin-top: 0;">
          <tbody>
            <?php $ntpurch = $nttrans = $ntnet = $ntpct = 0; ?>
            @foreach($noncos_data as $data)
              @if($data['purch']>0)
              <tr data-expenseid="{{ $data['expenseid'] }}">
                <td style="width:5%;">{{ $data['expensecode'] }}</td>
                <td>{{ $data['expense'] }}</td>
                <td class="text-right" class="text-right" style="width:17%;">
                  <a href="/component/purchases?table=expense&item={{$data['expense']}}&itemid={{$data['expenseid']}}&branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}">
                  {{ nf($data['purch']) }}
                  </a>

                </td>
                <td class="text-right" class="text-right" style="width:17%;">{{ nf($data['trans']) }}</td>
                <td class="text-right" class="text-right" style="width:17%;">{{ nf($data['net']) }}</td>
                <td class="text-right" class="text-right" style="width:17%;"></td>
                
              </td>
              </tr>
              <?php
                $ntpurch += $data['purch'];
                $nttrans += $data['trans'];
                $ntnet += $data['net'];
                
                $ttpurch += $data['purch'];
                $tttrans += $data['trans'];
                
                $fs = $data['food_sales'];
              ?>
              @endif
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td class="text-right">Total:</td>
              <td class="text-right">
                <b class="text-muted">
                    {{ nf($ntpurch) }}
                </b>
              </td>
              <td class="text-right"><b class="text-muted">
                <a href="/component/transfer/daily?branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}">
                {{ nf($nttrans) }}
                </a>
              </b></td>
              <td class="text-right"><b class="text-muted">{{ nf($ntnet) }}</b></td>
              <td class="text-right"></td>
            </tr>
          </tfoot>
        </table>

        <div class="table-responsive" style="margin-top: 10px;">
        <table class="table table-condensed table-hover table-striped table-sort" style="margin-top: 0;">
          <tfoot>
            <tr>
              <td style="width:5%;"></td>
              <td class="text-right"><b>Total:</b></td>
              <td class="text-right" style="width:17%;">
                <b class="text-muted">
                    {{ nf($ttpurch) }}
                </b>
              </td>
              <td class="text-right" style="width:17%;"><b class="text-muted">
                {{ nf($tttrans) }}
              </b></td>
              <?php
                $direct_cost = $ttpurch-$tttrans;
              ?>
              <td class="text-right" style="width:17%;"><b class="text-muted">{{ nf($direct_cost) }}</b></td>
              <td class="text-right" style="width:17%;"></td>
            </tr>
          </tfoot>
        </table>
        </div><!-- table-responsivee  -->




      </div><!-- end: .panel-body  -->
      </div><!-- end: .panel.panel-default  -->
      </div><!-- end: .col-md-12  -->

      <div class="col-md-5 " style="margin: 30px 0;">
        <div class="panel panel-default">
          <div class="panel-heading">Sales Summary by Category</div>
          <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-condensed table-hover table-striped table-sort" style="margin-top: 0;">
          <thead>
            <tr>
              <th>Code</th>
              <th>Product Category</th>
              <th class="text-right">Gross Sales</th>
              <th class="text-right">%</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $tot_sales = $tot_pct = 0;
            ?>
            @foreach($prodcats as $data)
              @if(!empty($data['prodcat']))
              <tr data-prodcatid="{{ $data['prodcatid'] }}">
                <td>{{ $data['prodcatcode'] }}</td>
                <td>{{ $data['prodcat'] }}</td>
                <td class="text-right">{{ nf($data['sales']) }}</td>
                <td class="text-right">{{ nf($data['pct']) }}</td>
              </tr>
              <?php
                $tot_sales += $data['sales'];
                $tot_pct += $data['pct'];
              ?>
              @endif
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td class="text-right"><b>Total:</b></td>
              <td class="text-right"><b class="text-muted">
                <a href="/product/sales?branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}" target="_blank" title="link to Daily Sales Summary">
                {{ nf($tot_sales) }}
                </a>
              </b></td>
              <td class="text-right"><b class="text-muted">{{ $tot_pct>0?nf($tot_pct)+0:nf($tot_pct) }}</b></td>
            </tr>
          </tfoot>
        </table>
        </div><!-- table-responsive  -->
        </div><!-- end: .panel-body  -->
        </div><!-- end: .panel.panel-default  -->
      </div><!-- end: .col-md-5  -->


      <div class="col-md-6 col-md-offset-1" style="margin-top: 30px; margin-bottom: 50px;">
        <div class="panel panel-default">
          <div class="panel-heading">Operational Expense Summary</div>
          <div class="panel-body">
        <div class="table-responsive">
        <table class="table table-condensed table-hover table-striped table-sort" style="margin-top: 0;">
          <thead>
            <tr>
              <th>Code</th>
              <th>Expense</th>
              <th class="text-right">Cost</th>
              <th class="text-right">Transfered</th>
              <th class="text-right" title="Cost - Transfered = Net Cost">Cost less Transfers</th>
            </tr>
          </thead>
          <tbody>
            <?php $xtpurch = $xttrans = $xtnet = 0; ?>
            @foreach($expense_data as $data)
              <tr data-expenseid="{{ $data['expenseid'] }}">
                <td>{{ $data['expensecode'] }}</td>
                <td>{{ $data['expense'] }}</td>
                <td class="text-right">
                  <a href="/component/purchases?table=expense&item={{urlencode($data['expense'])}}&itemid={{$data['expenseid']}}&branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}">
                  {{ nf($data['purch']) }}
                  </a>

                </td>
                <td class="text-right">
                  <a href="/component/transfer?table=expense&item={{urlencode($data['expense'])}}&itemid={{$data['expenseid']}}&branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}">
                    {{ nf($data['trans']) }}
                  </a>
                </td>
                <td class="text-right">{{ nf($data['net']) }}</td>
              </tr>
              <?php
                $xtpurch += $data['purch'];
                $xttrans += $data['trans'];
                $xtnet += $data['net'];
              ?>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td class="text-right"><b>Total:</b></td>
              <td class="text-right">
                <b class="text-muted">
                    {{ nf($xtpurch) }}
                </b>
              </td>
              <td class="text-right"><b class="text-muted">
                <a href="/component/transfer/daily?branchid={{$branch->lid()}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}">
                {{ nf($xttrans) }}
                </a>
              </b></td>
              <td class="text-right"><b class="text-muted">{{ nf($xtnet) }}</b></td>
            </tr>
          </tfoot>
        </table>
        </div><!-- table-responsive  -->
      </div><!-- end: .panel-body  -->
      </div><!-- end: .panel.panel-default  -->
      </div><!-- end: .col-md-12  -->

      <table id="datatable" class="tb-data table" style="display:none;">
        <thead>
          <tr>
            <th>Date</th>
            <th>GR</th>
            <th>MP</th>
            <th>FS</th>
            <th>FV</th>
            <th>RC</th>
            <th>CK</th>
            <th>SS</th>
            <th>Food Cost</th>
          </tr>
        </thead>
        <tbody>
          @foreach($hist as $day)
          <tr>
            <td>{{ $day['date']->format('Y-m-d') }}</td>
            @foreach($day['data'] as $fc)
            <td>{{ $fc }}</td>
            @endforeach
            <td>{{ $day['fc'] }}</td>
          </tr>
          @endforeach
        </tbody>
  
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
            <label>Month:</label>
            <div>
            <div class="btn-group" role="group">
            <label class="btn btn-default" for="mdl-dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="mdl-dp-date" value="{{ $dr->date->format('m/Y') }}" style="max-width: 110px;">
            
            
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
              //var date = new Date(this.value);
              //console.log(date.getDay());
              //console.log(date);
              return Highcharts.dateFormat('%b %Y',  this.value-86400000);
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
              /*
              arr.push({ // mark the weekend
                color: "#CCCCCC",
                width: 1,
                value: this.value-86400000,
                zIndex: 3
              });
                */
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
          yAxis: 0
        }, {
          type: 'line',
          yAxis: 0,
        }, {
          type: 'line',
          yAxis: 0,
          visible: false
        }, {
          type: 'line',
          yAxis: 0,
          visible: false
        }, {
          type: 'line',
          yAxis: 0,
          visible: false
        }, {
          type: 'line',
          dashStyle: 'shortdot',
          yAxis: 1,
          dataLabels: {
            enabled: true,
            crop: false,
            format: '{y}%',
            verticalAlign: "bottom",
            align: "center"
          }
        }
      ]
    });


  
  
  $('.mdl-btn-go').on('click', function(){
    //loader();
    $('#filter-form').submit();
  });
  

  $('#ttcost').text('{{ nf($tpurch) }}');
  $('#tac').text('{{ nf($ttrans) }}');
  $('#trs').text('{{ nf($tnet) }}');

  <?php  
    $direct_profit = $dailysales - ($direct_cost + $xtnet);
  ?>
    
  $('#view-directcost').text('{{ nf($direct_cost) }}');
  $('#view-totexpense').text('{{ nf($xtnet) }}');
  $('#view-directprofit').text('{{ nf($direct_profit) }}');
    

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