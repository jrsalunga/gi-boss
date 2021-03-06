@extends('master')

@section('title', '- Price Range')

@section('body-class', 'daily-purchases')

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
    <li><a href="/component">Component</a></li>
    @if($filter->isset)
    <li><a href="/component/price/comparative">Price Range</a></li>
    <li class="active">{{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }}</li>
    @else
    <li class="active">Price Range</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group pull-left" role="group">
          
            <a href="/dashboard" class="btn btn-default" title="Back">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs">Back</span>
            </a> 
          </div> <!-- end btn-grp -->
          
          <div class="btn-group visible-xs-inline-block pull-left" role="group" style="padding-left: 5px;">
            @if($filter->isset)
              <button type="button" class="btn btn-default active">
                <span class="glyphicon glyphicon-search"></span>
                <span>{{ $filter->item }}</span>
              </button>
              <a type="button" class="btn btn-default" href="/component/price/comparative" title="Remove Filter"><span class="fa fa-close"></span></a>
            @endif
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
            {!! Form::open(['url' => '/component/price/comparative', 'method' => 'get', 'id'=>'filter-form']) !!}
            
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="table" id="table" value="{{ $filter->table }}">
            <input type="hidden" name="item" id="item" value="{{ $filter->item }}">
            <input type="hidden" name="itemid" id="itemid" value="{{ $filter->id }}">
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

      
          <div class="btn-group pull-right clearfix dp-container hidden-xs" role="group">
            
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
        
          </div><!-- end btn-grp -->

          <div class="btn-group hidden-xs" role="group" style="margin-left: 5px;">
          	<input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search Component">
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <div class="row">

      <div class="col-md-12">
        <div id="container" style="overflow: hidden;"></div>
      </div>
    </div> <!-- end: .row -->

    @if($datas)
    <div class="table-responsive">
      <table class="table table-hover table-sort-all">
        <thead>
          <tr>
            <th>Branch</th>
            <th class="text-right">Ave Cost</th>
            <th class="text-right">Last Purchase Cost</th>
            <th class="text-right">Total Qty</th>
            <th class="text-right">Total Cost</th>
            <th class="text-right">Min-Max Cost</th>
            <th class="text-right">Min-Max Purch</th>
            <th class="text-right">Trans Cnt</th>
          </tr>
        </thead>
        <tbody>
        @foreach($datas as $data)
          <tr>
            <td>
              <a href="/component/purchases?table=component&item={{$filter->item}}&itemid={{$filter->id}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}&branchid={{strtolower($data['component']->branchid)}}">
                {{ $data['component']->code }}
              </a>
            </td>
            <td class="text-right">{{ number_format($data['component']->ave, 2) }}</td>
            <td class="text-right">
              <span class="help" title="{{ $data['last']->date->format('m/d/Y') }} @ {{ $data['last']->supplier->descriptor }}" data-toggle="tooltip">
                {{ number_format($data['last']->ucost, 2) }}
              </span> 
            </td>
            
            <td class="text-right">{{ number_format($data['component']->tot_qty, 0) }}</td>
            <td class="text-right">{{ number_format($data['component']->tcost, 2) }}</td>
            <td class="text-right">{{ number_format($data['component']->ucost_min, 2) }} - {{ number_format($data['component']->ucost_max, 2) }}</td>
            <td class="text-right">{{ number_format($data['component']->qty_min, 2) }} - {{ number_format($data['component']->qty_max, 2) }}</td>
            <td class="text-right help" title="Negative transactions are not included">{{ number_format($data['component']->trancnt, 0) }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <p>&nbsp;</p>
    @else
      
    @endif


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

          <div class="form-group">
            <label>Filter:</label>
            <input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search Component">
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/5.0.12/highcharts.js"> </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/5.0.12/js/highcharts-more.js"> </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/5.0.12/js/modules/data.js"> </script>
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


  @if($graphs)

    <?php ksort($graphs); ?>
  
    var rangesData = [
      @foreach($graphs as $graph)
        [{{ $graph['component']->ucost_min }}, {{ $graph['component']->ucost_max }}], 
      @endforeach
    ];
    
    var powerData =  [
      @foreach($graphs as $graph)
        {{ number_format($graph['component']->ave, 2) }}, 
      @endforeach
    ];

    window.chart = new Highcharts.Chart({    
        chart: { 
          renderTo: 'container',
          height: 300,
          marginTop: 40,
          marginLeft: 0,
          zoomType: 'x',
          panning: true,
          panKey: 'shift'
        },
        credits: {
          enabled: false
        },
        colors: ['#15C0C2','#D36A71', '#B09ADB', '#5CB1EF', '#F49041', '#f15c80', '#F9CDAD', '#91e8e1', '#8d4653'],
        title: {
          text: null
        },
        xAxis: {
        categories: [
          @foreach($graphs as $graph)
            '{{ $graph['component']->code }}', 
          @endforeach
        ]},
        yAxis: [
          { 
            title: { 
              text: 'Price',
              rotation: 270,
              x: 30,
              y: -30,
              margin: -10
            },
            labels: {
            align: 'left',
            x: 3,
            y: -5,
            format: '{value:.,0f}'
          },
          }
        ],
        legend: { enabled: false },
         tooltip: {
        shared: true,
          crosshairs: true
        },
        series: [
          {
            type: 'columnrange',
            name: 'Range',
            inverted: true,
            opposite: true,
            data: rangesData
          },
          { 
            type: 'spline',
            name: 'Average',
            data: powerData 
           }
        ]
    });
    @endif
 
  
  $('.table-sort-all').tablesorter({
    stringTo: 'min',
    sortList: [[0,0]],
    headers: {
      1: { sorter: "digit", string: "min" }, // non-numeric content is treated as a MAX value
      //2: { sorter: "digit", empty : "top" }, // sort empty cells to the top
      //3: { sorter: "digit", string: "min" }  // non-numeric content is treated as a MIN value
    } 
  });

  
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

    

    $.widget("custom.autocomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
      },
      _renderMenu: function(ul, items) {
        var that = this,
          currentCategory = "";
        $.each(items, function(index, item) {
          var li;
          if (item.category=='component') {
            if (item.category != currentCategory) {
              ul.append('<li class="ui-autocomplete-category"><span class="label label-success">' + item.category + '</span></li>' );
              currentCategory = item.category;
            }
            li = that._renderItemData(ul, item);
            if (item.category) {
              li.attr( "aria-label", item.category + " : " + item.label);
            }
          }
        });
      }
    });


   	$(".searchfield").autocomplete({
     	source: function(request, response) {
      	$.ajax({
        	type: 'GET',
        	url: "/api/search/component",
          dataType: "json",
          data: {
            maxRows: 25,
            q: request.term
          },
          success: function(data) {
            response($.map(data, function(item) {
              return {
                //label: item.item + ', ' + item.table,
                label: item.item,
                value: item.item,
                category: item.table,
								id: item.id
              }
            }));
          }
        });
      },
      minLength: 2,
      select: function(event, ui) {
  			//console.log(ui);
        //log( ui.item ? "Selected: " + ui.item.label : "Nothing selected, input was " + this.value);
  			$("#table").val(ui.item.category); /* set the selected id */
  			$("#item").val(ui.item.value); /* set the selected id */
  			$("#itemid").val(ui.item.id); /* set the selected id */
      },
      open: function() {
        $( this ).removeClass("ui-corner-all").addClass("ui-corner-top");
  			$("#table").val(''); /* set the selected id */
  			$("#item").val(''); /* set the selected id */
  			$("#itemid").val(''); /* set the selected id */
      },
      close: function() {
          $( this ).removeClass("ui-corner-top").addClass("ui-corner-all");
      },
    	messages: {
      	noResults: '',
      	results: function() {}
    	}
    }).on('blur', function(e){
      if ($(this).val().length==0) {
        $( this ).removeClass("ui-corner-all").addClass("ui-corner-top");
        $("#table").val(''); /* set the selected id */
        $("#item").val(''); /* set the selected id */
        $("#itemid").val(''); /* set the selected id */
      }

      //setTimeout(submitForm, 1000);
    });


    var submitForm  = function(){
      console.log('submit Form');
      $('#filter-form').submit();
    }
 
  });

  </script>

@endsection