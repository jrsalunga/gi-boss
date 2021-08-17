@extends('master')

<?php
  $code = (is_null($branch)) ? '':' ('.$branch->code.')';
  $title = '- Month Beg Balance'.$code;
?>
@section('title', $title)

@section('body-class', 'daily-begbal')

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
    <li><a href="/component">Component</a></li>
    <li><a href="/component/begbal">Beginning Stock</a></li>
    <li class="active">Details @if(!is_null($branch))<small>({{ $dr->date->format('M j, Y') }})</small>@endif</li>
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

              @if(empty(($filter->item)) || is_null(($branch)))
                
              @else
              <span class="label label-info" style="margin-left: 5px;" data-toggle="loader">
                {{ $filter->item }} 
                <a href="/component/begbal?branchid={{strtolower($branch->id)}}&amp;to={{$dr->to->format('Y-m-d')}}&amp;fr={{$dr->fr->format('Y-m-d')}}" title="Remove filter">
                <span style="color:#ccc; margin-right: 5px; border-radius: .25em;">x</span>
                </a>
              </span>
              @endif

              
            </div>
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
            {!! Form::open(['url' => '/component/begbal', 'method' => 'get', 'id'=>'filter-form']) !!}
            <!--
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid="'. $branch->id  }}">
            -->  
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
            
            @if(is_null(($branch)))

            @else
            <a href="/component/begbal?branchid={{$branch->lid()}}&amp;date={{ $dr->date->copy()->startOfMonth()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->startOfMonth()->subDay()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            @endif
            <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/Y') }}" style="max-width: 90px;" readonly>
            <label class="btn btn-default hidden-xs" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
            @if(is_null(($branch)))

            @else
            <a href="/component/begbal?branchid={{$branch->lid()}}&amp;date={{ $dr->date->copy()->endOfMonth()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->endOfMonth()->addDay()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            @endif
        
          </div><!-- end btn-grp -->

          <div class="btn-group hidden-xs" role="group">
          	<input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search filter">
          	<!--
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="http://example.com" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span id="date-type-name">Daily</span>
                  <span class="caret"></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="date-type">
                  <li><a href="#" data-date-type="daily">Daily</a></li>
                  <li><a href="#" data-date-type="weekly">Weekly</a></li>
                  <li><a href="#" data-date-type="monthly">Monthly</a></li>
                  <li><a href="#" data-date-type="quarterly">Quarterly</a></li>
                  <li><a href="#" data-date-type="yearly">Yearly</a></li>
                </ul>
              </div>
            </div>
          	-->
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <?php
      $tot_transcost = 0;
      $tot_qty = 0;
      $tot_neg_transcost = 0;
      $tot_neg_qty = 0;
    ?>
    @if(count($datas)>0)
    <div class="row">
      <div class="col-md-12">
        <ul class="nav nav-pills" role="tablist">
          <!--
          <li role="presentation" class="active">
            <a href="#items" aria-controls="items" role="tab" data-toggle="tab">
              <span class="gly gly-shopping-cart"></span>
              <span class="hidden-xs">
                Components
              </span>
            </a>
          </li>
          <li role="presentation">
            <a href="#stats" aria-controls="stats" role="tab" data-toggle="tab">
              <span class="gly gly-charts"></span>
              <span class="hidden-xs">
                Stats
              </span> 
            </a>
          </li>
          -->
          <li role="presentation" style="float: right;">
            <div>
            Total Cost: 
            <h3 id="tot-cost" class="text-right" style="margin:0 0 10px 0;">0.00</h3>
            </div>
          </li>
        </ul>
      </div><!-- end: .col-md-12 -->
      
      
      <div class="col-md-12">
        <div class="table-responsive">
        <table class="table table-condensed table-hover table-striped table-sort" style="margin-top: 0;">
          <thead>
            <tr>
              <th>Date</th>
              <th>Component</th>
              <th class="text-right">Qty</th>
              <th></th>
              <th class="text-right">Unit Cost</th>
              <th class="text-right">Total Cost</th>
              
              <th>Expense</th>
              <th>Comp Cat</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($datas as $begbal)
            <?php
              $cancel = $begbal->tcost>0 ? false:'text-decoration: line-through;';
            ?>
              <tr class="{{ !$cancel?'':'text-muted' }}">
                <td style="{{ $cancel }}">{{ $begbal->date->format('M j, D') }}</td>
                <td style="{{ $cancel }}">{{ $begbal->component }}</td>
                <td class="text-right text-muted">{{ number_format($begbal->qty,2) }}</td>
                <td><small class="text-muted">{{ strtolower($begbal->uom) }}@if($begbal->qty>1 && substr(strtolower($begbal->uom), -1)!='s')s
                        @endif</small></td>
                <td class="text-right text-muted">{{ number_format($begbal->ucost,2) }}</td>
                <td class="text-right">{{ number_format($begbal->tcost,2) }}</td>
                <td style="{{ $cancel }}"><small class="text-muted help" title="{{ $begbal->compcatcode }} - {{ $begbal->compcat }}" data-toggle="tooltip">{{ $begbal->compcat }}</small></td>
                <td style="{{ $cancel }}"><small class="text-muted help" title="{{ $begbal->expensecode }} - {{ $begbal->expense }}" data-toggle="tooltip">{{ $begbal->expensecode }}</small></td>
                <td>
                  <span class="label 
                    @if($begbal->expscatcode=='05')
                      label-warning
                    @elseif($begbal->expscatcode=='08')
                      label-primary
                    @else
                      label-default
                    @endif
                     pull-right" title="{{ $begbal->expscatcode }} - {{ $begbal->expscat }}" data-toggle="tooltip" style="cursor: help;">{{ $begbal->expscatcode }}</span>
                </td>
              </tr>
            <?php
              if ($begbal->tcost>0) {
                $tot_transcost += $begbal->tcost;
                $tot_qty += $begbal->qty;
              } else {
                $tot_neg_transcost += $begbal->tcost;
                $tot_neg_qty += $begbal->qty;
              }
            ?>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td class="text-right">
                @if(isset($_GET['table']) && $_GET['table']==='component' && count($begbals)>0)
                  <strong title="Total Quantity" data-toggle="tooltip">{{ number_format($tot_qty, 2,'.','')+0 }}</strong>
                @endif
              </td>
              <td>
                @if(isset($_GET['table']) && $_GET['table']==='component' && count($begbals)>0)
                  <small class="text-muted">
                  {{ strtolower($begbal->uom)}}@if($tot_qty>1 && substr(strtolower($begbal->uom), -1)!='s')s
                  @endif
                  </small>
                @endif
              </td>
              <td class="text-right">
                @if(isset($_GET['table']) && $_GET['table']==='component' && $tot_qty>0 && count($begbals)>0)
                  <div>
                    <strong title="Average Unit Cost" data-toggle="tooltip">{{ number_format($tot_transcost/$tot_qty, 2) }}</strong>
                  </div>
                  <div>
                    
                  </div>
                @endif
              </td>
              <td class="text-right">
                <div>
                  {{ number_format($tot_transcost, 2) }}
                </div>
                <div>
                  <small>{{ number_format($tot_neg_transcost, 2) }}</small>
                </div>
                <div>
                  <strong title="Total Cost" data-toggle="tooltip">{{ number_format($tot_transcost+$tot_neg_transcost, 2) }}</strong>
                </div>
              </td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </tfoot>
        </table>
        </div><!-- table-responsive  -->
  
      </div>
    </div>
    @else
      <!-- no data  -->
      No Records
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

    document.location.href = '/component/begbal?branchid='+$('#branchid').val()+'&date='+e.date.format('YYYY-MM-DD');
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

  
  
  $('.mdl-btn-go').on('click', function(){
    //loader();
    $('#filter-form').submit();
  });
  

  $('#tot-cost').text('{{ number_format($tot_transcost+$tot_neg_transcost, 2) }}');
    

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
          if (item.category=='component' || item.category=='expense') {
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
        var bid = $('#branchid').val();
      	$.ajax({
        	type: 'GET',
        	url: "/api/search/component",
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


    var submitForm  = function(){
      console.log('submit Form');
      $('#filter-form').submit();
    }
 
  });

  </script>

@endsection