@extends('master')

@section('title', '- Kitchen Log | Month Summary')

@section('body-class', 'kitlog')

@section('css-external')
<style type="text/css">

.chart-container {
  position: relative;
  height: 40vh;
  width: 94vw;
} 

.mdl-chart-container {
  height: 300px;
  width: 100%;
}

@media only screen and (min-width : 992px) {

  .chart-container {
    position: relative;
    height: 30vh;
    width: 47vw;
  } 
}
</style>
@endsection

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
    <li><a href="/dashboard"><span class="gly gly-shop"></span> </a></li>
    <li><a href="/kitlog">Kitchen Log</a></li>
    <li><a href="/kitlog/month">Month  <span class="hidden-xs hidden-sm">Summary</span></a></li>
    @if(is_null($branch))
      <li class="hidden-lg hidden-md">All - {{ $date->format('M Y') }}</li>
      <li class="hidden-xs hidden-sm">All Branch - {{ $date->format('F Y') }}</li>
    @else
      <li><a href="/kitlog/month?branchid={{$branch->lid()}}&date={{$date->format('Y-m-d')}}">{{$branch->code}}</a></li>
      <li>{{ $date->format('F Y') }}</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          
          <!-- <div class="btn-group" role="group">
             <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
               <span class="gly gly-unshare"></span>
               <span class="hidden-xs hidden-sm">Back</span>
             </a> 
           </div> --> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix hidden-xs" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/kitlog/month', 'method' => 'get', 'id'=>'filter-form']) !!}
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="date" id="date" value="{{ $date->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

          <div class="btn-group" style="margin-left: 5px;">
            <div class="dropdown">
              <button id="dLabel" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-shop"></span>
                @if(is_null(($branch)))
                  <span class="br-code">All Branch</span>
                  <span class="br-desc hidden-xs hidden-sm"></span>
                @else
                  <span class="br-code">{{ $branch->code }}</span>
                  <span class="br-desc hidden-xs hidden-sm">- {{ $branch->descriptor }}</span>
                @endif
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu br" aria-labelledby="dLabel" style="max-height: 400px; overflow-y: scroll;">
                @if(is_null($branch))
                @else
                <li><a href="#" data-desc="All Branch" data-code="All" data-branchid="">All Branch</a></li>
                @endif
                
                @foreach($branches as $b)
                <li>
                  <a href="#" data-desc="{{ $b->descriptor }}" data-code="{{ $b->code }}" data-branchid="{{ $b->lid() }}">{{ $b->code }}</a>
                </li>
                @endforeach
              </ul>
            </div> <!-- .dropdown -->
          </div>

          <div class="btn-group pull-right clearfix" role="group" style="margin-right: 5px;">
            <a href="/kitlog/month?{{is_null($branch)?'':'branchid='.$branch->lid().'&'}}date={{ $date->copy()->startOfMonth()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->startOfMonth()->subDay()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>

            <input type="text" class="btn btn-default" id="dp-date" value="{{ $date->format('m/Y') }}" style="max-width: 90px;" readonly>
            <label class="btn btn-default hidden-xs" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>

            <a href="/kitlog/month?{{is_null($branch)?'':'branchid='.$branch->lid().'&'}}date={{ $date->copy()->addDay()->endOfMonth()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->addDay()->endOfMonth()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>        
        </div>
    </nav>
    
    @include('_partials.alerts')

    <div class="row">
      <div class="col-md-6">
        @if(count($areas)>0)
        <div class="table-responsive">
          <table class="table table-condensed">
            <!-- <thead>
              <tr>
                <th>Area</th>
                <th>Total Qty</th>
                <th>Average</th>
                <th>Min</th>
                <th>Max</th>
              </tr>
            </thead> -->
            <thead>
              <tr>
                <th colspan="6">Kitchen Area</th>
              </tr>
            </thead>
            <tbody>
              @foreach($areas as $data)
              <tr>
                <td colspan="6">
                  <div class="chart-container">
                    <canvas id="chart_{{ $data->area }}"></canvas>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Area</td>
                <td>Total Prep Qty</td>
                <td>Average Time</td>
                <td>Peak Time</td>
                <td>Min Prep</td>
                <td>Max Prep</td>
              </tr>
              <tr>
                <td>{{ kit_area($data->area) }}</td>
                <td>{{ $data->qty+0 }}</td>
                <td>{{ to_time($data->ave) }}</td>
                <td>{{ to_time($data->peak) }}</td>
                <td>{{ to_time($data->min) }}</td>
                <td>{{ to_time($data->max) }}</td>
              </tr>
              
              @endforeach
            </tbody>
          </table>
        </div>
        @else
          {{ is_null($branch) ? '':'No Data'  }}
        @endif
      </div>
      <div class="col-md-6">
        @if(count($foods)>0)
          <div>
            <ul class="nav nav-tabs" role="tablist">
              <li class="active">
                <a href="#all" role="tab" data-toggle="tab">All Products</a>
              </li>
              <li class="dropdown">
                <a href="#" class="btn dropdown-toggle" data-toggle="dropdown">By Category <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" id="dd-menucat-contents">
                  @foreach($datatables as $k => $dt)
                  <li><a href="#tab{{$k}}" role="tab" data-toggle="tab" >{{ $dt['menucat'] }} <span class="badge">{{ nf($dt['line'],0) }}</span></a></li>
                  @endforeach
                </ul>
              </li>
            </ul>
          </div>

          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="all">
              <div class="table-responsive">
                <table class="table table-condensed table-sort-all">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th class="text-right" data-sorter="false"></th>
                      <th class="text-right">Total Qty</th>
                      <th class="text-right">Average Time</th>
                      <th class="text-right">Peak Time</th>
                      <th class="text-right">Min Prep</th>
                      <th class="text-right">Max Prep</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($foods as $i => $data)
                    <?php
                      $product = is_null($data->product) ? $data->product_id : $data->product->descriptor;
                    ?>
                    <tr data-product_id="{{ $data->product_id }}">
                      <td data-sort-value="{{ $product }}">
                        <a href="/kitlog/logs?branchid={{ stl($data->branch_id) }}&productid={{ stl($data->product_id) }}&fr={{ $date->copy()->startOfMonth()->format('Y-m-d') }}&to={{ $date->copy()->endOfMonth()->format('Y-m-d') }}&iscombo={{ $data->iscombo }}">
                        {{ $product }}
                        </a>
                        @if($data->iscombo)
                          <span class="label label-success">G</span>
                        @endif
                      </td>
                      <td>
                        <a href="#" data-toggle="modal" data-target="#{{ $data->id }}">
                          <span class="gly gly-stats" data-toggle="tooltip" title="Show graph" class="help"></span>
                        </a>
                      </td>
                      <td class="text-right" data-sort-value="{{ $data->qty+0 }}">{{ $data->qty+0 }}</td>
                      <td class="text-right" data-sort-value="{{ to_time($data->ave, true) }}">{{ to_time($data->ave) }}</td>
                      <td class="text-right" data-sort-value="{{ to_time($data->peak, true) }}">{{ to_time($data->peak) }}</td>
                      <td class="text-right" data-sort-value="{{ to_time($data->min, true) }}">{{ to_time($data->min) }}</td>
                      <td class="text-right" data-sort-value="{{ to_time($data->max, true) }}">{{ to_time($data->max) }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div> <!-- end: .table-responsive -->
            </div>
            @foreach($datatables as $k => $dt)
              <div role="tabpanel" class="tab-pane" id="tab{{$k}}">
                <div class="table-responsive">
                  <table class="table table-condensed table-sort-all">
                    <thead>
                      <tr>
                        <th>{{ $dt['menucat'] }}</th>
                        <th class="text-right" data-sorter="false"></th>
                        <th class="text-right">Total Qty</th>
                        <th class="text-right">Average Time</th>
                        <th class="text-right">Peak Time</th>
                        <th class="text-right">Min Prep</th>
                        <th class="text-right">Max Prep</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($dt['items'] as $i => $item)
                      <?php
                        $product = is_null($item->product) ? $item->product_id : $item->product->descriptor;
                      ?>
                      <tr data-product_id="{{ $item->product_id }}">
                        <td data-sort-value="{{ $product }}">
                          <a href="/kitlog/logs?branchid={{ stl($item->branch_id) }}&productid={{ stl($item->product_id) }}&fr={{ $date->copy()->startOfMonth()->format('Y-m-d') }}&to={{ $date->copy()->endOfMonth()->format('Y-m-d') }}&iscombo={{ $item->iscombo }}">
                          {{ $product }}
                          </a>
                          @if($item->iscombo)
                            <span class="label label-success">G</span>
                          @endif
                        </td>
                        <td>
                          <a href="#" data-toggle="modal" data-target="#{{ $item->id }}">
                            <span class="gly gly-stats" data-toggle="tooltip" title="Show graph" class="help"></span>
                          </a>
                        </td>
                        <td class="text-right" data-sort-value="{{ $item->qty+0 }}">{{ $item->qty+0 }}</td>
                        <td class="text-right" data-sort-value="{{ to_time($item->ave, true) }}">{{ to_time($item->ave) }}</td>
                        <td class="text-right" data-sort-value="{{ to_time($item->peak, true) }}">{{ to_time($item->peak) }}</td>
                        <td class="text-right" data-sort-value="{{ to_time($item->min, true) }}">{{ to_time($item->min) }}</td>
                        <td class="text-right" data-sort-value="{{ to_time($item->max, true) }}">{{ to_time($item->max) }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            @endforeach
          </div>

          
        @else
          {{ is_null($branch) ? '':'No Data'  }}
        @endif
      </div>
    </div>

  </div>
</div>
</div>

@foreach($foods as $i => $data)
<div class="modal fade" tabindex="-1" role="dialog" id="{{ $data->id }}">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          {{ is_null($data->product) ? $data->product_id : $data->product->descriptor }}
          </a>
          @if($data->iscombo)
            <span class="label label-success">G</span>
          @endif
        </h4>
      </div>
      <div class="modal-body">
        <div class="mdl-chart-container">
          <canvas id="chart_{{ $data->id }}"></canvas>
        </div>
        <table class="table table-condensed" style="font-size: smaller; margin-top: 20px;">
          <tbody>
            <tr>
              <td>Product</td>
              <td class="text-right">Total Qty</td>
              <td class="text-right">Average Time</td>
              <td class="text-right">Peak Time</td>
              <td class="text-right">Min Prep</td>
              <td class="text-right">Max Prep</td>
            </tr>
            <tr data-product_id="{{ $data->product_id }}">
              <td style="border-bottom: none;">
                {{ is_null($data->product) ? $data->product_id : $data->product->descriptor }}
                @if($data->iscombo)
                  <span class="label label-success">G</span>
                @endif
              </td>
              <td class="text-right" style="border-bottom: none;">{{ $data->qty+0 }}</td>
              <td class="text-right" style="border-bottom: none;">{{ to_time($data->ave) }}</td>
              <td class="text-right" style="border-bottom: none;">{{ to_time($data->peak) }}</td>
              <td class="text-right" style="border-bottom: none;">{{ to_time($data->min) }}</td>
              <td class="text-right" style="border-bottom: none;">{{ to_time($data->max) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endforeach

@endsection






@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="/js/dr-picker.js"> </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

<script>

  $('.table-sort-all').tablesorter({
      stringTo: 'min',
      sortList: [[0,0]],
      headers: {
        1: { sorter: "digit", string: "min" }, // non-numeric content is treated as a MAX value
        //2: { sorter: "digit", empty : "top" }, // sort empty cells to the top
        //3: { sorter: "digit", string: "min" }  // non-numeric content is treated as a MIN value
      } 
    });

  moment.locale('en', { week : {
    dow : 1 // Monday is the first day of the week.
  }});

  $('#dp-date').datetimepicker({
    format: 'MM/YYYY',
    showTodayButton: true,
    ignoreReadonly: true,
    viewMode: 'months'
  }).on('dp.change', function(e){
    var date = e.date.format('YYYY-MM-DD');
    $('#date').val(date);
    loader();
    @if(!is_null(($branch)))
      document.location.href = '/kitlog/month?branchid={{$branch->lid()}}&date='+e.date.format('YYYY-MM-DD');
    @else
      document.location.href = '/kitlog/month?branchid='+$('#branchid').val()+'&date='+e.date.format('YYYY-MM-DD');
    @endif
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
    
    document.location.href = '/kitlog/month?branchid='+el.data('branchid')+'&date='+$('#date').val();
    //console.log($('.btn-go').data('branchid'));
  });




  Highcharts.setOptions({
    lang: {
      thousandsSep: ','
  }});

  var opt = {
    maintainAspectRatio: false,
    responsive: true,
    scales:{
        xAxes: [{
          display: false //this will remove all the x-axis grid lines
        }],
        yAxes: [{
          display: false,
          ticks: {
            beginAtZero: true,
            stepSize: 1
          }
        }]
    },
    legend: {
      display: false,
    },
    elements: {
      point: {
        radius: 1
      },
      line:{
        borderWidth:0
      },
    }
  };

  <?php
  foreach($areas as $i => $area) {
    $z = explode(',', $area->dataset);
    $dataset[$i] = [];
    $label[$i] = [];
    foreach($z as $ds) {
      $y = explode('|', $ds);
      if (isset($y[2]))
        array_push($dataset[$i], $y[2]);
      if (isset($y[0]))
        array_push($label[$i], $y[0]);
    }
  ?>

  var dt = {labels:{!!json_encode($label[$i])!!},datasets:[{backgroundColor:"rgba(255,158,179,0.5)",borderColor:"rgb(255,158,179)",borderWidth:1,data:{!!json_encode($dataset[$i])!!}}]};
  var ctx_{{ $area->area }} = document.getElementById('chart_{{ $area->area }}').getContext('2d');
  var chart_{{ $area->area }} = new Chart.Line(ctx_{{ $area->area }},{data:dt,options:opt});
  <?php } ?>


  var opt2 = {
    maintainAspectRatio: false,
    responsive: true,
    scales:{
      xAxes: [{
        display: true, //this will remove all the x-axis grid lines,
        scaleLabel: {
           display: true,
           labelString: 'Minute'
        },
        ticks: {
          beginAtZero: true
        }
      }],
      yAxes: [{
        display: true,
        scaleLabel: {
           display: true,
           labelString: 'Total  Dispatched Quantity'
        },
        ticks: {
          beginAtZero: true,
          stepSize: 1,
        },
      }]
    },
    legend: {
      display: false
    },
    elements: {
      point: {
        radius: 3
      },
      line:{
        borderWidth:2
      },
    }
  };


  <?php
  //https://jsfiddle.net/leighking2/jfh71ged/
  foreach($foods as $f => $food) {
    $z = explode(',', $food->dataset);
    $dts[$f] = [];
    $lbl[$f] = [];
    foreach($z as $ds) {
      $y = explode('|', $ds);
      if (isset($y[2]))
        array_push($dts[$f], $y[2]);
      if (isset($y[0]))
        array_push($lbl[$f], $y[0]);
    }
  ?>

  var dt = {labels:{!!json_encode($lbl[$f])!!},datasets:[{label: 'Total Qty',backgroundColor:"rgba(75,192,192,0.5)",borderColor:"rgb(75,192,192)",borderWidth:3,data:{!!json_encode($dts[$f])!!}}]};
  var ctx_{{ $food->id }} = document.getElementById('chart_{{ $food->id }}').getContext('2d');
  var chart_{{ $food->id }} = new Chart.Line(ctx_{{ $food->id }},{data:dt,options:opt2});
  <?php } ?>

  console.log('test');


  $(document).ready(function(e){


    // // $('.dropdown').on('click', function(e) {
    // //   e.preventDefault();
     
    // //   console.log($(this).hasClass('active'));
    // //   console.log($(this).hasClass('open'));

    // //   if ($(this).hasClass('active') && ($(this).hasClass('open')==false)) {
    // //     $(this).removeClass('active');
    // //     $(this).addClass('open');
    // //   }

    // // });

    // $('.nav').on('shown.bs.tab', 'a', function (e) {
    //     console.log(e.relatedTarget);
    //     if (e.relatedTarget) {
    //         $(e.relatedTarget).removeClass('active in');
    //     }
    //   console.log('fdasfsadfadsf');
    // });

  });



  
</script>
@endsection