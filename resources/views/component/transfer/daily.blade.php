@extends('master')

@section('title', '- Daily Transfer Details')

@section('body-class', 'daily-transfers')

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
    <li><a href="/component/transfer/daily">Transfers</a></li>
    <li class="active">Details @if(!is_null($branch))<small>({{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }})</small>@endif</li>
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
                <a href="/component/transfer?branchid={{strtolower($branch->id)}}&amp;to={{$dr->to->format('Y-m-d')}}&amp;fr={{$dr->fr->format('Y-m-d')}}" title="Remove filter">
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
            {!! Form::open(['url' => '/component/transfer', 'method' => 'get', 'id'=>'filter-form']) !!}
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
    @if(count($transfers)>0)
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
              <th></th>
              <th>Supplier</th>
              <th>Transfered To</th>
              <th>Comp Category</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($transfers as $transfer)
            <?php
              $cancel = $transfer->tcost>0 ? false:'text-decoration: line-through;';
            ?>
              <tr class="{{ !$cancel?'':'text-muted' }}">
                <td style="{{ $cancel }}">{{ $transfer->date->format('M j, D') }}</td>
                <td style="{{ $cancel }}">{{ $transfer->component }}</td>
                <td class="text-right text-muted">{{ number_format($transfer->qty,2)+0 }}</td>
                <td><small class="text-muted">{{ strtolower($transfer->uom) }}@if($transfer->qty>1 && substr(strtolower($transfer->uom), -1)!='s')s
                        @endif</small></td>
                <td class="text-right text-muted">{{ number_format($transfer->ucost,2) }}</td>
                <td class="text-right">{{ number_format($transfer->tcost,2) }}</td>
                <td class="text-center"><span  data-toggle="tooltip"
                      @if(strtolower($transfer->terms)=='c')
                        class="label label-success" title="Cash"
                      @elseif(strtolower($transfer->terms)=='k')
                        class="label label-info" title="Check"
                      @else
                        class="label label-default" title=""
                      @endif
                      
                      style="cursor: help;"><small>{{ $transfer->terms }}</small></span>
                </td>
                <td style="{{ $cancel }}">
                  <small class="text-muted help" title="{{ $transfer->suppliercode }} - {{ $transfer->supplier }}" data-toggle="tooltip">
                  {{ $transfer->suppliercode }}
                  </small>
                </td>
                <td style="{{ $cancel }}">
                  @if(!is_null($transfer->toBranch) && is_null($transfer->toSupplier))
                  <small class="text-muted help" title="{{ $transfer->toBranch->code }} - {{ $transfer->toBranch->descriptor }}" data-toggle="tooltip">
                    {{ $transfer->toBranch->code }}
                  @endif

                  @if(is_null($transfer->toBranch) && !is_null($transfer->toSupplier))
                  <small class="text-muted help" title="{{ $transfer->toSupplier->code }} - {{ $transfer->toSupplier->descriptor }}" data-toggle="tooltip">
                    {{ $transfer->toSupplier->code }}
                  @endif
                  </small>
                </td>
                <td style="{{ $cancel }}"><small class="text-muted help" title="{{ $transfer->compcatcode }} - {{ $transfer->compcat }}" data-toggle="tooltip">{{ $transfer->compcat }}</small></td>
                <td style="{{ $cancel }}"><small class="text-muted help" title="{{ $transfer->expensecode }} - {{ $transfer->expense }}" data-toggle="tooltip">{{ $transfer->expensecode }}</small></td>
                <td>
                  <span class="label 
                    @if($transfer->expscatcode=='05')
                      label-warning
                    @elseif($transfer->expscatcode=='08')
                      label-primary
                    @else
                      label-default
                    @endif
                     pull-right" title="{{ $transfer->expscatcode }} - {{ $transfer->expscat }}" data-toggle="tooltip" style="cursor: help;">{{ $transfer->expscatcode }}</span>
                </td>
              </tr>
            <?php
              if ($transfer->tcost>0) {
                $tot_transcost += $transfer->tcost;
                $tot_qty += $transfer->qty;
              } else {
                $tot_neg_transcost += $transfer->tcost;
                $tot_neg_qty += $transfer->qty;
              }
            ?>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td class="text-right">
                @if(isset($_GET['table']) && $_GET['table']==='component' && count($transfers)>0)
                  <strong title="Total Quantity" data-toggle="tooltip">{{ number_format($tot_qty, 2,'.','')+0 }}</strong>
                @endif
              </td>
              <td>
                @if(isset($_GET['table']) && $_GET['table']==='component' && count($transfers)>0)
                  <small class="text-muted">
                  {{ strtolower($transfer->uom)}}@if($tot_qty>1 && substr(strtolower($transfer->uom), -1)!='s')s
                  @endif
                  </small>
                @endif
              </td>
              <td class="text-right">
                @if(isset($_GET['table']) && $_GET['table']==='component' && $tot_qty>0 && count($transfers)>0)
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