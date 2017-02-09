@extends('master')

@section('title', '- Daily Sales')

@section('body-class', 'daily-sales')

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
    <li><a href="/product">Product</a></li>
    @if($branch)
    <li><a href="/product/sales">Sales</a></li>
    <li class="active">{{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }}</li>
    @else
    <li class="active">Sales</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group pull-left" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs">Back</span>
            </a> 
          </div> <!-- end btn-grp -->
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
            {!! Form::open(['url' => '/product/sales', 'method' => 'get', 'id'=>'filter-form']) !!}
            
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="table" id="table" value="{{ $filter->table }}">
            <input type="hidden" name="item" id="item" value="{{ $filter->item }}">
            <input type="hidden" name="itemid" id="itemid" value="{{ $filter->id }}">
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
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
          </div><!-- end btn-grp -->
          <!--
          <div class="btn-group hidden-xs" role="group">
            <input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search filter">
          </div>
          -->
        </div>
      </div>
    </nav>
  </div>
  
  @include('_partials.alerts')

  <?php
    $totsales = 0;
    $totqty = 0;
  ?>

  @if(is_null($products))

  @else
  <div class="row" id="stage">

    <div class="col-md-12">
      <ul class="nav nav-pills" role="tablist">
        <li role="presentation" class="active">
          <a href="#stats" aria-controls="stats" role="tab" data-toggle="tab">
            <span class="gly gly-charts"></span>
            <span class="hidden-xs">
              Stats
            </span> 
          </a>
        </li>
        @if(!is_null($sales))
        <li role="presentation">
          <a href="#items" aria-controls="items" role="tab" data-toggle="tab">
            <span class="gly gly-cutlery"></span>
            <span class="hidden-xs">
              Sales History
            </span>
          </a>
        </li>
        @endif
        <li role="presentation" style="float: right;">
          <div>
          Gross Amount: 
          <h3 id="tot-sales-cost" class="text-right" style="margin:0 0 10px 0;">0.00</h3>
          @if(!request()->has('table'))
            <div class="diff text-right" style="font-size:12px; margin-top:-10px;"></div>
          @endif
          </div>
        </li>
        @if(!request()->has('table'))
        <li role="presentation" style="float: right;margin-right:20px;">
          <div>
          Net Amount:
          <h3 id="tot-salesmtd-cost" class="text-right" style="margin:0 0 20px 0;">{{ number_format($ds->sales,2) }}</h3>
          </div>
          
        </li>
        @endif
      </ul>
    </div><!-- end: .col-md-12 -->

    <div class="col-md-12">
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="items">
          
          @if(is_null($sales))

          @else
          <div class="table-responsive">
              <table class="table table-hover table-striped table-sort-data" style="margin-top: 0;">
                <thead>
                  <tr>
                    <th>Order Date</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Gross Amount</th>
                    <th>Product Category</th>
                    <th>Menu Category</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sales as $sale)
                    <tr>
                      <td>
                        <small class="text-muted" title="{{ $sale->ordtime->format('D M d, Y h:i:s A') }}" data-toggle="tooltip">
                          {{ $sale->orddate->format('Y-m-d') }}
                        </small>
                      </td>
                      <td data-id="{{$sale->lid()}}">{{ $sale->product }}</td>
                      <td>{{ number_format($sale->qty, 2)+0 }}</td>
                      <td class="text-right">{{ number_format($sale->uprice, 2) }}</td>
                      <td class="text-right">{{ number_format($sale->grsamt, 2) }}</td>
                      <td><small class="text-muted">{{ $sale->prodcat }}</small></td>
                      <td><small class="text-muted">{{ $sale->menucat }}</small></td>
                    </tr>
                    <?php
                      $totsales +=$sale->grsamt;
                      $totqty += $sale->qty;
                    ?>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><b>{{ number_format($totsales, 2) }}</b></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
          </div><!-- end: .table-responsive -->   
          @endif      

        </div><!-- end: #items -->
        <div role="tabpanel" class="tab-pane active" id="stats">
          
          <!-- Product Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Product Sales Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-product" data-table="#product-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div class="show less">
                        <table class="tb-product-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Product</th>
                              <th class="text-right">Quantity</th>
                              <th class="text-right">Amount</th>
                              <th>Category</th>
                              <th>Menu Category</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $prodtot=0; ?> 
                            @foreach($products as $product) 
                              <tr>
                                <td>{{ $product->product }}</td>
                                <td class="text-right"><small class="text-muted">{{ number_format($product->qty, 0) }}</small></td>
                                <td class="text-right">{{ number_format($product->netamt, 2) }}</td>
                                <td><small class="text-muted">{{ $product->prodcat }}</small></td>
                                <td><small class="text-muted">{{ $product->menucat }}</small></td>
                              </tr>
                            <?php $prodtot+=$product->netamt; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($prodtot,2)}}</b></td><td></td><td></td></tr></tfoot>
                        </table>
                      </div>
                      <span class="label label-info show toggle" style="margin-left:3px;">show more</span>
                      
                      <table id="product-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Product</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($products as $product)
                              <tr>
                                <td>{{ $product->product }}</td>
                                <td>{{ $product->netamt }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          <!-- Prodcat Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Product Category Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-prodcat" data-table="#prodcat-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div>
                        <table class="tb-prodcat-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Product Category</th>
                              <th class="text-right"></th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($prodcats as $prodcat)
                              <tr>
                                <td>{{ $prodcat->prodcat }}</td>
                                <td class="text-right"></td>
                                <td class="text-right">{{ number_format($prodcat->netamt, 2) }}</td>
                              </tr>
                            <?php $t+=$prodcat->netamt; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      
                      <table id="prodcat-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Product</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($prodcats as $prodcat)
                              <tr>
                                <td>{{ $prodcat->prodcat }}</td>
                                <td>{{ $prodcat->netamt }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          <!-- Menucat Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Menu Category Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-menucat" data-table="#menucat-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div>
                        <table class="tb-menucat-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Menu Category</th>
                              <th>Product Category</th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($menucats as $menucat)
                              <tr>
                                <td>{{ $menucat->menucat }}</td>
                                <td><small class="text-muted">{{ $menucat->prodcat }}</small></td>
                                <td class="text-right">{{ number_format($menucat->netamt, 2) }}</td>
                              </tr>
                            <?php $t+=$menucat->netamt; ?>
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      
                      <table id="menucat-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>menucat</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($menucats as $menucat)
                              <tr>
                                <td>{{ $menucat->menucat }}</td>
                                <td>{{ $menucat->netamt }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

        </div><!-- end: #stats -->
      </div>
    </div><!-- end: .col-md-12 -->
  </div>
  @endif



</div><!-- end: .container-fluid -->

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
          <!--
          <div class="form-group">
            <label>Filter:</label>
            <input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search filter">
          </div>
          -->

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
  <!--
  <script src="//cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"> </script>
  -->
  <script>


  <?php
    $v = '';
    if (!is_null($products)) {

      $diff = $prodtot-$ds->sales;
      $c = $diff>0 ? 'success':'danger';
      $d = $diff>0 ? 'up':'down';

      $v = '<span class="text-'.$c.'"><span class="glyphicon glyphicon-arrow-'.$d.'"></span><b> '.number_format($diff,2).'</b></span>';
      $p = $prodtot;
    } else {
      $p = 0;
    }
  ?>
  $('#tot-sales-cost').text('{{ number_format($p, 2) }}');
  $('.diff').html('{!!$v!!}');

  moment.locale('en', { week : {
    dow : 1 // Monday is the first day of the week.
  }});

  


  $(document).ready(function() {

    
    $('[data-toggle="tooltip"]').tooltip();

    initDatePicker();
    branchSelector();

    var getOptions = function(to, table) {
        var options = {
          data: {
            table: table,
            startColumn: 0,
            endColumn: 1,
          },
          chart: {
            renderTo: to,
            type: 'pie',
            height: 300,
            width: 300,
            events: {
              load: function (e) {
                //console.log(e.target.series[0].data);
              }
            }
          },
          title: {
              text: ''
          },
          style: {
            fontFamily: "Helvetica"
          },
          tooltip: {
            pointFormat: '{point.y:.2f}  <b>({point.percentage:.2f}%)</b>'
          },
          plotOptions: {
            pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: false
              },
              showInLegend: true,
              point: {
                events: {
                  mouseOver: function(e) {    
                    var orig = this.name;
                    var tb = $(this.series.chart.container).parent().data('table');
                    var tr = $(tb).children('tbody').children('tr');
                     _.each(tr, function(tr, key, list){
                      var text = $(tr).children('td:nth-child(2)').text();             
                      if(text==orig){
                        $(tr).children('td').addClass('bg-success');
                      }
                    });
                  },
                  mouseOut: function() {
                    var orig = this.name;
                    var tb = $(this.series.chart.container).parent().data('table');
                    var tr = $(tb).children('tbody').children('tr');
                     _.each(tr, function(tr, key, list){
                        $(tr).children('td').removeClass('bg-success');
                    });
                  },
                  click: function(event) {
                    //console.log(this);
                  }
                }
              }
            }
          },
          
          legend: {
            enabled: false,
            //layout: 'vertical',
            //align: 'right',
            //width: 400,
            //verticalAlign: 'top',
            borderWidth: 0,
            useHTML: true,
            labelFormatter: function() {
              //total += this.y;
              return '<div style="width:400px"><span style="float: left; width: 250px;">' + this.name + '</span><span style="float: left; width: 100px; text-align: right;">' + this.percentage.toFixed(2) + '%</span></div>';
            },
            title: {
              text: null,
            },
              itemStyle: {
              fontWeight: 'normal',
              fontSize: '12px',
              lineHeight: '12px'
            }
          },
          
          exporting: {
            enabled: false
          }
        }
        return options;
      }

      Highcharts.setOptions({
        lang: {
          thousandsSep: ','
      }});


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


    $('.tb-product-data').tablesorter({sortList: [[2,1]]});
    $('.tb-prodcat-data').tablesorter({sortList: [[1,1]]});
    $('.tb-menucat-data').tablesorter({sortList: [[2,1]]});

   


    @if(!is_null($products))
      var productChart = new Highcharts.Chart(getOptions('graph-pie-product', 'product-data'));
      var prodcatChart = new Highcharts.Chart(getOptions('graph-pie-prodcat', 'prodcat-data'));
      var menucatChart = new Highcharts.Chart(getOptions('graph-pie-menucat', 'menucat-data'));
      
    @endif



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
          if (item.category != currentCategory) {
            ul.append('<li class="ui-autocomplete-category"><span class="label label-success">' + item.category + '</span></li>' );
            currentCategory = item.category;
          }
          li = that._renderItemData(ul, item);
          if (item.category) {
            li.attr( "aria-label", item.category + " : " + item.label);
          }
        });
      }
    });


    $(".searchfield").autocomplete({
      source: function(request, response) {
        var bid = $('#branchid').val();
        $.ajax({
          type: 'GET',
          url: "/api/s/product/sales",
          dataType: "json",
          data: {
            maxRows: 25,
            q: request.term,
            branchid : bid
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


    $('.mdl-btn-go').on('click', function(){
    //loader();
    $('#filter-form').submit();
  });

  });
  </script>

  <style type="text/css">
  .show.less {
      max-height: 500px;
      overflow: hidden;
  }
  </style>

@endsection