@extends('master')

@section('title', '- Kitchen Log | Raw')

@section('body-class', 'kitlog')

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
    <li><a href="/kitlog/logs">Raw Logs</a></li>
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
            {!! Form::open(['url' => '/kitlog/logs', 'method' => 'get', 'id'=>'filter-form']) !!}
            <!--
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid="'. $branch->id  }}">
            -->  
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go"  `">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="productid" id="productid" value="{{ is_null($product) ? '':$product->lid() }}">
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
            <input type="text" id="searchfield" class="form-control searchfield" value="{{ is_null($product) ? '':$product->descriptor }}" placeholder="Filter Product">
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    @if(is_null($days_diff))
      
    @else
      <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Warning!</strong> Date range is too large. Reduced to 30 days only.
      </div>
    @endif


    <div class="row">
      <div class="col-md-12">
        @if(count($kitlogs)>0)
        <div class="table-responsive">
          <table class="table table-condensed tablesorter" style="margin-top: 0;">
            <thead>
              <tr>
                <th>Date</th>
                <th>Product</th>
                @if(is_null($branch))
                  <td>Branch</td>
                @endif
                <th>Area</th>
                <th>Qty</th>
                <th>Order Time</th>
                <th>Served Time</th>
                <th>Prep Time</th>
                <th>Menu Category</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $ctr = $tot_qty = $tot_minute = 0;
              ?>
              @foreach($kitlogs as $data)
              <tr data-id="{{ $data->id }}">
                <td>{{ $data->date->format('Y-m-d') }}</td>
                <td data-productid="{{ $data->product_id }}">
                  {{ is_null($data->product) ? $data->product_id : $data->product->descriptor }}
                  @if($data->iscombo)
                    <span class="label label-success">G</span>
                  @endif
                </td>
                @if(is_null($branch))
                  <td>{{ is_null($data->branch) ? $data->branch_id : $data->branch->code }}</td>
                @endif
                <td>{{ $data->area }}</td>
                <td>{{ $data->qty+0 }}</td>
                <td>{{ $data->ordtime }}</td>
                <td>{{ $data->served }}</td>
                <td>{{ $data->time }}</td>
                <td>{{ is_null($data->menucat) ? $data->menucat_id : $data->menucat->descriptor }}</td>
              </tr>
              <?php
                $ctr++;
                $tot_qty += $data->qty;
                $tot_minute += $data->minute;
              ?>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td><b data-toggle="tooltip" title="# of transactions" class="help">{{ $ctr }} trans</b></td>
                @if(is_null($branch))
                  <td></td>
                @endif
                <td></td><td></td>
                <td>
                  <b data-toggle="tooltip" title="Total Quantity" class="help">{{ $tot_qty }}</b>
                  <div>
                    <em>
                      @if($ctr>0)
                      <small data-toggle="tooltip" title="Ave Quantity (Tot Qty/Trans)" class="help">
                        {{ nf($tot_qty/$ctr)+0 }}
                      </small>
                      @endif
                    </em>
                  </div>
                </td>
                <td></td><td></td>
                <td title="{{ $tot_minute }} minutes">
                  <b data-toggle="tooltip" title="Total Prep Time ({{ $tot_minute }} mins)" class="help">{{ to_time($tot_minute) }}</b>
                  <div>
                    <em>
                      @if($ctr>0)
                      <small data-toggle="tooltip" title="Ave Prep Time (Tot Prep Time/Trans) ({{ $tot_minute }}/{{ $tot_qty }}={{ nf($tot_minute/$tot_qty) }}mins) " class="help">
                        {{ to_time($tot_minute/$tot_qty, true) }}
                      </small>
                      @endif
                    </em>
                  </div>
                </td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
        @else
          {{ is_null($branch) ? '':'No Data'  }}
        @endif
      </div>
    </div> <!-- end: .row -->
  </div>
</div> <!-- end: container-fluid -->

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

          <div class="form-group">
            <label>Filter Product:</label>
            <input type="text" id="searchfield" class="form-control searchfield" value="{{ is_null($product) ? '':$product->descriptor }}" placeholder="Filter Product">
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

  /*
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
  */ 


  $(".searchfield").autocomplete({
    source: function(request, response) {
      var bid = $('#branchid').val();
      $.ajax({
        type: 'GET',
        url: "/api/s/filter/kitlog",
        dataType: "json",
        data: {
          maxRows: 25,
          q: request.term,
          branchid : bid
        },
        success: function(data) {
          response($.map(data, function(item) {
            console.log(item);
            return {
              label: item.code+' - '+item.descriptor,
              value: item.descriptor,
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
      $("#productid").val(ui.item.id); /* set the selected id */
    },
    open: function() {
      $( this ).removeClass("ui-corner-all").addClass("ui-corner-top");
      $("#productid").val(''); /* set the selected id */
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
      $("#productid").val(''); /* set the selected id */
    }

    //setTimeout(submitForm, 1000);
  });

  $('.mdl-btn-go').on('click', function(){
    $('#filter-form').submit();
  });

  var submitForm  = function(){
    $('#filter-form').submit();
  }

  $('.tablesorter').tablesorter({
      stringTo: 'min',
      sortList: [[0,0]],
      headers: {
        1: { sorter: "digit", string: "min" }, // non-numeric content is treated as a MAX value
        //2: { sorter: "digit", empty : "top" }, // sort empty cells to the top
        //3: { sorter: "digit", string: "min" }  // non-numeric content is treated as a MIN value
      } 
    });

  $(document).ready(function(){

    initDatePicker();

  });
</script>
@endsection