@extends('master')

@section('title', '- Comparative')

@section('body-class', 'comparative')

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
    <li class="active">Comparative Analytics</li>
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

          
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
      {!! Form::open(['url' => '/status/post-comparative', 'method' => 'post', 'class'=>'form-horizontal']) !!}
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Branch Picker

            <div><small><em>(max 5 branches)</em></small></div>
          </label>
          <div class="col-sm-7">
            <select class="selectpicker form-control" multiple data-max-options="5" style="display: none;">
              @foreach($branches as $b)
                <option value="{{ $b->id }}" title="{{ $b->code }}"> {{ $b->code }} - {{ $b->descriptor }} </option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Stat to View</label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-default stat active">
                <input type="radio" name="options" id="option1" autocomplete="off" checked>Sales
              </label>
              <label class="btn btn-default stat">
                <input type="radio" name="options" id="option2" autocomplete="off">Man Cost %
              </label>
              <label class="btn btn-default stat">
                <input type="radio" name="options" id="option3" autocomplete="off">Tips %
              </label>
              <label class="btn btn-default stat">
                <input type="radio" name="options" id="option4" autocomplete="off">Sales per Emp
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Time Frame</label>
          <div class="col-sm-6">
            <div class="btn-group" role="group">
            <!--
            <a href="/" class="btn btn-default" title="">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            -->
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
              <input type="hidden" id="fr" value="{{ $dr->fr->format('Y-m-d') }}">
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
              <input type="hidden" id="to" value="{{ $dr->to->format('Y-m-d') }}">
            </label>
            <!--
            <a href="/" class="btn btn-default" title="">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            -->
            </div><!-- end btn-grp -->
          </div>
          <div class="col-sm-4">
            <div class="visible-xs">&nbsp;</div>
            <button id="btn-go" type="button" class="btn btn-success" disabled>Compare</button>
          </div>
        </div>
        
      </form>


      <div id="graph">

      </div>
    





@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
  
  <script>
    var generateGraph = function(data) {
      console.log(data);
      var arr = [];
      $('#graph').highcharts({
            data: {
                csv: data,
              // Parse the American date format used by Google
              parseDate: function (s) {
                console.log(s);
                var match = s.match(/(\d{4})-(\d{2})-(\d{2})/);
                if (match) {
                  return Date.UTC(+(match[1]), match[2] - 1, +match[3]);
                }
              }
            },
            chart: {
              type: 'line',
              height: 300,
              spacingRight: 0,
              marginTop: 40,
              marginRight: 20,
              zoomType: 'x',
              panning: true,
              panKey: 'shift',
              events: {
                redraw: function (event) {
                  console.log(
                    Highcharts.dateFormat('%Y-%m-%d', event.target.xAxis[0].min),
                    Highcharts.dateFormat('%Y-%m-%d', event.target.xAxis[0].max)
                  );
                }
              }
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
                    arr.push({ 
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
          }); // end: graph
    }

    $(document).ready(function(){

      $('#dp-date-fr').datetimepicker({
        defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        ignoreReadonly: true
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        //console.log(date);
        $('#dp-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-date-to').datetimepicker({
        defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
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
    });

    var data = {};

    $('.selectpicker').on('hidden.bs.select', function (e) {
      //console.log($(this).val());
    }).on('changed.bs.select', function (e) {
      data.branches = $(this).val();
      if(data.branches==null)
        $('#btn-go').prop('disabled', true);
      else
        $('#btn-go').prop('disabled', false);
    });


    $('#btn-go').on('click', function(){

      render();
    });

    $('.stat').on('click', function(){
      $(this).children('input').prop('checked', true);
      console.log($(this).children('input'));
      render();
    });

    var render = function(){
      if(data.branches=='undefined' || data.branches==null) {
        console.log('walang branches');
        alert('Please select branches');
      } else {

        data.stat = checkStat();
        console.log('stat: '+ data.stat);
        setDates();

        assignBranch(data).success(function(data, textStatus, jqXHR) {
          var csv = jqXHR.responseText;
          console.log('data');
          //console.log(data);
          console.log(textStatus);
          
          generateGraph(data);

          
        });
      }
    }

    var checkStat = function(){
      if ($('#option1').prop('checked') == true)
        return 1;
      else if ($('#option2').prop('checked') == true)
        return 2;
      else if ($('#option3').prop('checked') == true)
        return 3;
      else if ($('#option4').prop('checked') == true)
        return 4;
      else 
        return 1;
    }

    var setDates = function(){
      data.fr = $('#fr').val();
      data.to = $('#to').val();
    }


    var assignBranch = function(a){
      var formData = a;
      return $.ajax({
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded',
            url: '/api/csv/comparative',
            //dataType: 'text/csv',
            data: formData,
            //async: false,
            success: function(d, textStatus, jqXHR){

            },
            error: function(jqXHR, textStatus, errorThrown){
              alert(textStatus + ' Failed on redering graph');
            }
        }); 
    }

    


 
  </script>
@endsection