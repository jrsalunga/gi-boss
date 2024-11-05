@extends('master')

@section('title', '- Customer By Year')

@section('body-class', 'customer-year')

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
    <li><a href="/report/customer/year">Customer</a></li>
    <li class="active">{{ $dr->fr->format('Y') }} - {{ $dr->to->format('Y') }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <button class="btn btn-default active">
              <span class="fa fa-group"></span>
              <span class="hidden-xs hidden-sm">Customer</span>
            </button>
           <a href="/report/transaction/year?fr={{$dr->fr->format('Y-m-d')}}&amp;to={{ $dr->to->format('Y-m-d')}}" class="btn btn-default">
              <span class="gly gly-tag"></span>
              <span class="hidden-xs hidden-sm">Transaction</span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/report/customer/year', 'method' => 'get', 'id'=>'dp-form']) !!}
            <button type="submit" class="btn btn-success btn-go" title="Go"   }}>
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

      
          
          
          <div class="btn-group pull-right clearfix dp-container" role="group">
            <label class="btn btn-default" for="dp-y-date-fr">
            <span class="glyphicon glyphicon-calendar"></span></label>
            <input readonly type="text" class="btn btn-default dp" id="dp-y-date-fr" value="{{ $dr->fr->format('Y') }}" style="max-width: 90px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-y-date-to" value="{{ $dr->to->format('Y') }}" style="max-width: 90px;">
            <label class="btn btn-default" for="dp-y-date-to">
            <span class="glyphicon glyphicon-calendar"></span>
            </label>
          </div><!-- end btn-grp -->

          <div class="btn-group pull-right clearfix" role="group">
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span id="date-type-name">Yearly</span>
                  <span class="caret"></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="date-type">
                  <li><a href="#" data-date-type="monthly">Monthly</a></li>
                  <li><a href="#" data-date-type="yearly">Yearly</a></li>
                </ul>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    @if(count($datas)>0)

      <div class="col-md-12">
        <div id="graph-container" style="overflow:hidden;">
          <div id="graph"></div>
        </div>
      </div>
    </div> 
    <div class="row">
      <div class="col-md-12" style="margin-top:20px;">
        <ul class="nav nav-pills" role="tablist">
          <li role="presentation" class="active">
            <a href="#total" aria-controls="total" role="tab" data-toggle="tab">
              <span class="fa fa-group"></span>
              <span class="hidden-xs">
                Total
              </span>
            </a>
          </li>
          <li role="presentation">
            <a href="#dine" aria-controls="dine" role="tab" data-toggle="tab">
              <span class="gly gly-cutlery"></span>
              <span class="hidden-xs">
                Dine In
              </span> 
            </a>
          </li>
        </ul>
      </div>

      <div class="col-md-12" style="margin-top:10px;">
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="total">
            <div class="table-responsive">
              <table class="table table-hover table-striped table-sort-data">
                <thead>
                  <tr>
                    <th>Branch</th>
                    <?php
                      $last = array_pop($datas);
                      $gtot = 0;
                      $stores = [];
                    ?>
                    @foreach(array_values($datas)[0] as $key => $value)
                      <th class="text-right">{{ $key }}</th>
                    @endforeach
                    <th class="text-right">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    @foreach($datas as $key => $value)
                    <tr>
                      <td>{{ $key }}</td>
                      <?php $tot = 0; ?>
                      @foreach($value as $k => $v)
                        @if(array_key_exists($k, $stores))

                        @else 
                          <?php $stores[$k]=0; ?>
                        @endif

                        @if(is_null($v))
                        <td> </td>
                        @else 
                        <td class="text-right" data-sort="{{number_format($v['custcount'],0,'','')}}">{{ nf($v['custcount'],false) }}</td>
                        <?php 
                          $tot += $v['custcount']; 
                          $stores[$k]++;
                        ?>
                        @endif
                      @endforeach
                      <td class="text-right" data-sort="{{number_format($tot,0,'','')}}">{{ nf($tot,false) }}</td>
                    </tr>
                    @endforeach
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td>Total <div style="font-size:smaller;"> <span class="gly gly-shop"></span> {{ count($datas) }}</div></td>
                    @foreach($last as $key => $value)
                    <td class="text-right">
                      <div>
                        <b>{{ nf($value['custcount'],false) }}</b>
                      </div>
                      <div style="font-size:smaller;">
                        @if($stores[$key]>0)
                          <span class="gly gly-shop"></span> {{ $stores[$key] }}
                        @else

                        @endif
                      </div>
                      @if(count($datas)>20)
                        <div style="font-size:smaller;">{{ $key }}</div>
                      @endif
                    </td>
                    <?php $gtot+=$value['custcount']; ?>
                    @endforeach
                    <td class="text-right"><b>{{ nf($gtot,false) }}</b></td>
                  </tr>
                </tfoot>
              </table>
            </div><!--  end: table-responsive -->
          </div>
          <div role="tabpanel" class="tab-pane" id="dine">
            <table class="table table-hover table-striped table-sort-data" style="margin-top:0;">
              <thead>
                <tr>
                  <th>Branch</th>
                  <?php
                    $gtot = 0;
                    $stores = [];
                  ?>
                  @foreach(array_values($datas)[0] as $key => $value)
                    <th class="text-right">{{ $key }}</th>
                  @endforeach
                  <th class="text-right">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  @foreach($datas as $key => $value)
                  <tr>
                    <td>{{ $key }}</td>
                    <?php $tot = 0; ?>
                    @foreach($value as $k => $v)
                      @if(array_key_exists($k, $stores))

                      @else 
                        <?php $stores[$k]=0; ?>
                      @endif

                      @if(is_null($v))
                      <td> </td>
                      @else 
                      <td class="text-right" data-sort="{{number_format($v['pax_dine'],0,'','')}}">{{ nf($v['pax_dine'],false) }}</td>
                      <?php 
                        $tot += $v['pax_dine']; 
                        $stores[$k]++;
                      ?>
                      @endif
                    @endforeach
                    <td class="text-right" data-sort="{{number_format($tot,0,'','')}}">{{ nf($tot,false) }}</td>
                  </tr>
                  @endforeach
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td>Total <div style="font-size:smaller;"> <span class="gly gly-shop"></span> {{ count($datas) }}</div></td>
                  @foreach($last as $key => $value)
                  <td class="text-right">
                    <div>
                      <b>{{ nf($value['pax_dine'],false) }}</b>
                    </div>
                    <div style="font-size:smaller;">
                      @if($stores[$key]>0)
                        <span class="gly gly-shop"></span> {{ $stores[$key] }}
                      @else

                      @endif
                    </div>
                    @if(count($datas)>20)
                      <div style="font-size:smaller;">{{ $key }}</div>
                    @endif
                  </td>
                  <?php $gtot+=$value['pax_dine']; ?>
                  @endforeach
                  <td class="text-right"><b>{{ nf($gtot,false) }}</b></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div><!--  end: .row -->
    <table id="datatable" class="tb-data" style="display:none;">
      <thead>
        <tr>
            <th>Date</th>
            <th>Total Customer</th>
            <th>Total Dine In</th>
        </tr>
      </thead>
      <tbody>
        @foreach($last as $k => $v)
        <tr>
          <td>{{ c($k.'-12-31')->format('Y-m-d') }}</td>
          <td>{{ $v['custcount'] }}</td>
          <td>{{ $v['pax_dine'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <p>&nbsp;</p>
    @else

    @endif <!-- end: if DS is NULL  -->
  

  </div>
</div><!-- end .container-fluid -->




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

  } /* end inidDatePicker */


  $('document').ready(function(){

    initDatePicker();

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

    $('#graph').highcharts({
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
      colors: ['#15C0C2', '#B09ADB','#D36A71', '#B09ADB', '#5CB1EF', '#F49041', '#4cae4c', '#6AAA96', '#DB4437', '#8d4653'],
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
              return Highcharts.dateFormat('%Y',  this.value-86400000);
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
        }
      ]
    });



   

    $('.date-type-selector .dropdown-menu li a').on('click', function(e){
      e.preventDefault();
      var type = $(this).data('date-type');
      $('#date-type-name').text($(this)[0].text);
      $('.dp-container').html(getDatePickerLayout(type));
      initDatePicker();
    });

    var getDatePickerLayout = function(type) {
      //console.log(type);
      var html = '';
      switch (type) {
        case 'weekly':
          html = '<select id="fr-year" class="btn btn-default dp-w-fr" style="height:34px; padding: 6px 3px 6px 12px">'
                @for($y=2015;$y<2021;$y++)
                  +'<option value="{{$y}}" {{ $dr->fr->copy()->startOfWeek()->year==$y?'selected':'' }}>{{$y}}</option>'
                @endfor
              +' </select>'
              +'<select id="fr-week" class="btn btn-default dp-w-fr" style="height:34px; padding: 6px 0px 6px 12px">'
                @for($x=1;$x<=lastWeekOfYear($dr->fr->copy()->startOfWeek()->year);$x++)
                +'<option value="{{$x}}" {{ $dr->fr->copy()->startOfWeek()->weekOfYear==$x?'selected':'' }}>{{$x}}</option>'
                @endfor
              +'</select>'
              +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
              +'<select id="to-year" class="btn btn-default dp-w-to" style="height:34px; padding: 6px 3px 6px 12px">'
                @for($y=2015;$y<2021;$y++)
                  +'<option value="{{$y}}" {{ $dr->to->copy()->endOfWeek()->year==$y?'selected':'' }}>{{$y}}</option>'
                @endfor
              +'</select>'
              +'<select id="to-week" class="btn btn-default dp-w-to" style="height:34px; padding: 6px 0px 6px 12px">'
                @for($x=1;$x<=lastWeekOfYear($dr->to->copy()->endOfWeek()->year);$x++)
                  +'<option value="{{$x}}" {{ $dr->to->copy()->endOfWeek()->weekOfYear==$x?'selected':'' }}>{{$x}}</option>'
                @endfor
              +'</select>';
            $('#dp-form').prop('action', '/status/branch/week');
          break;
        case 'monthly':
          html = '<label class="btn btn-default" for="dp-m-date-fr">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-m-date-fr" value="{{ $dr->fr->format('m/Y') }}" style="max-width: 90px;">'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-m-date-to" value="{{ $dr->to->format('m/Y') }}" style="max-width: 90px;">'
            +'<label class="btn btn-default" for="dp-m-date-to">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>';
            $('#dp-form').prop('action', '/report/customer/month');
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
            +'<input readonly type="text" class="btn btn-default dp" id="dp-y-date-fr" value="{{ $dr->fr->format('Y') }}" style="max-width: 90px;">'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-y-date-to" value="{{ $dr->to->format('Y') }}" style="max-width: 90px;">'
            +'<label class="btn btn-default" for="dp-y-date-to">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>';
          $('#dp-form').prop('action', '/status/branch/year');
          break;
        default:
          html = '<label class="btn btn-default" for="dp-date-fr">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 90px;">'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 90px;">'
            +'<label class="btn btn-default" for="dp-date-to">'
            +'<span class="glyphicon glyphicon-calendar"></span>'
            +'</label>';
          $('#dp-form').prop('action', '/report/customer/year');
      }

      return html;
    }

   
  });
</script>
@endsection