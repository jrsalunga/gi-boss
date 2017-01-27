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

          <div class="btn-group hidden-xs" role="group">
            <input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search filter">
          </div>
          
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
    <div class="col-md-12">
      <ul class="nav nav-pills" role="tablist">
        <li role="presentation">
          <a href="#items" aria-controls="items" role="tab" data-toggle="tab">
            <span class="gly gly-shopping-cart"></span>
            <span class="hidden-xs">
              Ordered Products
            </span>
          </a>
        </li>
        <li role="presentation" class="active">
          <a href="#stats" aria-controls="stats" role="tab" data-toggle="tab">
            <span class="gly gly-charts"></span>
            <span class="hidden-xs">
              Stats
            </span> 
          </a>
        </li>
        <li role="presentation" style="float: right;">
          <div>
          Total Gross Amount: 
          <h3 id="tot-sales-cost" class="text-right" style="margin:0 0 10px 0;">0.00</h3>
          <div class="diff text-right" style="font-size:12px; margin-top:-10px;"></div>
          </div>
        </li>
        <li role="presentation" style="float: right;margin-right:20px;">
          <div>
          Sales on Cash Audit 
          <h3 id="tot-salesmtd-cost" class="text-right" style="margin:0 0 20px 0;">{{ number_format($ds->sales,2) }}</h3>
          </div>
          
        </li>
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
                      <td>{{ $sale->orddate->format('Y-m-d') }}</td>
                      <td data-id="{{$sale->lid()}}">{{ $sale->product }}</td>
                      <td>{{ number_format($sale->qty, 2)+0 }}</td>
                      <td class="text-right">{{ number_format($sale->uprice, 2) }}</td>
                      <td class="text-right">{{ number_format($sale->grsamt, 2) }}</td>
                      <td>{{ $sale->prodcat }}</td>
                      <td>{{ $sale->menucat }}</td>
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
            <div class="panel-heading">Ordered Products</div>
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
                              <th class="text-right">Sales</th>
                              <th>Category</th>
                              <th>Menu Category</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $prodtot=0; ?> 
                            @foreach($products as $product) 
                              <tr>
                                <td>{{ $product->product }}</td>
                                <td class="text-right">{{ number_format($product->qty,2)+0 }}</td>
                                <td class="text-right">{{ number_format($product->netamt, 2) }}</td>
                                <td><small>{{ $product->prodcat }}</small></td>
                                <td><small>{{ $product->menucat }}</small></td>
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
            <div class="panel-heading">Product Category</div>
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
                              <th>Product</th>
                              <th class="text-right"></th>
                              <th class="text-right">Sales</th>
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
            <div class="panel-heading">Menu Category</div>
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
                              <th>Product</th>
                              <th class="text-right"></th>
                              <th class="text-right">Sales</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($menucats as $menucat)
                              <tr>
                                <td>{{ $menucat->menucat }}</td>
                                <td class="text-right"></td>
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
  @endif



</div>
@endsection



@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="/js/dr-picker.js"> </script>
  
  <script>

  $('#tot-sales-cost').text('{{ number_format($prodtot, 2) }}');

  <?php
    $v = '';
    if (!is_null($products)) {

      $diff = $totsales-$ds->sales;
      $c = $diff>0 ? 'success':'danger';
      $d = $diff>0 ? 'up':'down';

      $v = '<span class="text-'.$c.'"><span class="glyphicon glyphicon-arrow-'.$d.'"></span><b> '.number_format($diff,2).'</b></span>';
    } 
  ?>
  $('.diff').html('{!!$v!!}');

  moment.locale('en', { week : {
    dow : 1 // Monday is the first day of the week.
  }});


  $(document).ready(function() {

    initDatePicker();
    branchSelector();


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
    $('.tb-menucat-data').tablesorter({sortList: [[1,1]]});

   


    @if(!is_null($products))
      var productChart = new Highcharts.Chart(getOptions('graph-pie-product', 'product-data'));
      var prodcatChart = new Highcharts.Chart(getOptions('graph-pie-prodcat', 'prodcat-data'));
      var menucatChart = new Highcharts.Chart(getOptions('graph-pie-menucat', 'menucat-data'));
    @endif
  });
  </script>

  <style type="text/css">
  .show.less {
      max-height: 500px;
      overflow: hidden;
  }
  </style>

@endsection