@extends('master')

@section('title', ' - Dashboard')

@section('css-internal')

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
<div class="container-fluid">
	
  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li class="active">Sales</li>
  </ol>

    <div id="graph" style="width:100%; height:400px;"></div>

    </div>
	
  
    
  



</div>
@endsection














@section('js-external')


  
<script src="/js/vendors-common.min.js"></script>
<script src="//code.highcharts.com/highcharts.js"></script>
<script src="//code.highcharts.com/modules/data.js"></script>
<script src="//code.highcharts.com/modules/exporting.js"></script>


<script>


$(document).ready(function() {



  $.get("api/csv?date={{ $date->format('Y-m-d') }}", function(csv) {
            
    $('#graph').highcharts({
      data: {
        csv: csv,
        // Parse the American date format used by Google
        parseDate: function (s) {
          //console.log(s);
          //var match = s.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/);

          var match = s.match(/(\d{4})-(\d{2})-(\d{2})/);
          //console.log(match);
          if (match) {
            //console.log(match[1]+'-'+match[2]+'-'+match[3]);
            //console.log(Date.UTC(+(match[1]), match[2] - 1, +match[1]))
            return Date.UTC(+(match[1]), match[2] - 1, +match[3]);
          }
        }
      },
      chart: {
        height: 500,
        spacingRight: 0,
        marginTop: 60,
        zoomType: 'x',
        panning: true,
        panKey: 'shift'
      },
      title: {
        text: ''
      },
      subtitle: {
          text: document.ontouchstart === undefined ?
              '' :
              'Pinch the chart to zoom in'
      },
      xAxis: [
        {
          gridLineColor: "#CCCCCC",
          type: 'datetime',
          //tickInterval: 7 * 24 * 3600 * 1000, // one week
          tickWidth: 0,
          gridLineWidth: 1,
          lineColor: "#C0D0E0", // line on X axis
          labels: {
            align: 'center',
            x: 3,
            y: 15
          },
          plotLines: [{ // mark the weekend
            color: 'green',
            width: 1,
            value: window.datenow,
            zIndex: 3
          }]
        },
        { // slave axis
          type: 'datetime',
          linkedTo: 0,
          opposite: true,
          tickInterval: 2 * 24 * 3600 * 1000,
          labels: {
            formatter: function () {
              return Highcharts.dateFormat('%a', this.value);
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
          /*
          { // 
            right y axis
                linkedTo: 0,
                gridLineWidth: 0,
                opposite: true,
                title: {
                    text: null
                },
                labels: {
                    align: 'right',
                    x: -3,
                    y: 16,
                    format: '{value:.,0f}'
                },
                showFirstLabel: false
            }
          */
      ],
      legend: {
        align: 'left',
        verticalAlign: 'top',
        y: -10,
        floating: true,
        borderWidth: 0
      },
      tooltip: {
        shared: false,
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
            radius: 0
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

  });

});

</script>

  
@endsection