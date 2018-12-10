@extends('master')

@section('title', '- Compcat Purchase')

@section('body-class', 'compcat-purchase')

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
    <li><a href="/report/compcat-purchase">Compcat Purchase</a></li>
    <li class="active">Month @if(!is_null($branch))<small>({{ $dr->fr->format('F') }})</small>@endif</li>   
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
            {!! Form::open(['url' => '/report/compcat-purchase', 'method' => 'get', 'id'=>'filter-form']) !!}
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="compcatid" id="compcatid" value="{{ is_null($compcat) ? '':$compcat->lid() }}">
            <!--
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            -->
            <input type="hidden" name="date" id="date" value="{{ $dr->date->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->


          <!--
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
            </div> 
          </div>
        -->

          <div class="btn-group" role="group">
            <select class="selectpickers form-control" name="btn-compcat" id="btn-compcat" data-live-search="true" data-size="10" title="Select Branch" data-width="auto">
              <option selected disabled>Select Component Category</option>
              @foreach($compcats as $cc)
                <option value="{{ $cc->lid() }}" {{ $compcat&&$compcat->id==$cc->id?'selected':'' }}>{{ $cc->descriptor }}</option>
              @endforeach
            </select>
          </div>
      
          <div class="btn-group pull-right clearfix" role="group" style="margin-right: 5px;">
            @if(is_null(($branch)))

            @else
            <a href="/report/compcat-purchase?branchid={{$branch->lid()}}&amp;date={{ $dr->date->copy()->subMonth()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subMonth()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            @endif
            <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/Y') }}" style="max-width: 90px;" readonly>
            <label class="btn btn-default hidden-xs" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
            @if(is_null(($branch)))

            @else
            <a href="/report/compcat-purchase?branchid={{$branch->lid()}}&amp;date={{ $dr->date->copy()->addMonth()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addMonth()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            @endif
          </div>

         
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')
    
    <?php
      $tpurch = $ttrans = $tnet = $tpct = 0;
    ?>
    
    @if(count($datas)>0)
    <div class="row">
      <div class="col-md-12">
        <div id="container" style="overflow: hidden;"></div>
      </div>
  
      <?php $ttpurch = $tttrans = 0 ?>

      <div class="col-md-12">
        
        <div class="table-responsive">
        <table class="table table-condensed table-striped">
          <thead>
            <tr>
            <th>Branch</th>
            <th>Area</th>
            @foreach($components as $component)
            <th>{{ $component->descriptor }}  ({{ strtolower($component->uom) }})</th>
            @endforeach
            </tr>
          </thead>
          <tbody>
              @foreach($datas as $key => $data)
              <tr>
                <td>{{ $data['code'] }}</td>
                <td>{{ $data['area'] }}</td>
               
              </tr>
              @endforeach
          </tbody>
        </table>
        </div>
      
      </div>

      
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
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

  $('#btn-compcat').on('change', function($e) {
    console.log($(this).val());
    $('#compcatid').val($(this).val());
  });

  $('#dp-date').datetimepicker({
    //defaultDate: "2016-06-01",
    format: 'MM/YYYY',
    showTodayButton: true,
    ignoreReadonly: true,
    viewMode: 'months'
  }).on('dp.change', function(e){
    var date = e.date.format('YYYY-MM-DD');
    $('#date').val(date);
    @if(!is_null(($branch)))
    loader();

    document.location.href = '/report/compcat-purchase?branchid='+$('#branchid').val()+'&date='+e.date.format('YYYY-MM-DD');
    @endif
  });

  $('#mdl-dp-date').datetimepicker({
    //defaultDate: "2016-06-01",
    format: 'MM/YYYY',
    showTodayButton: true,
    ignoreReadonly: true,
    viewMode: 'months'
  }).on('dp.change', function(e){
    var date = e.date.format('YYYY-MM-DD');
    $('#date').val(date);
  });

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