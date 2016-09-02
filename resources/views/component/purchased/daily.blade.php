@extends('master')

@section('title', '- Daily Purchases')

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
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span> </a></li>
    <li><a href="/component">Component</a></li>
    @if($branch)
    <li><a href="/component/purchases">Purchases</a></li>
    <li class="active">{{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }}</li>
    @else
    <li class="active">Purchases</li>
    @endif
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

          <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/component/purchases', 'method' => 'get', 'id'=>'dp-form']) !!}
            <!--
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid="'. $branch->id  }}">
            -->  
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
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

          <div class="btn-group" style="margin-left: 5px;">
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
      
          <div class="btn-group pull-right clearfix dp-container" role="group">
            
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

          <div class="btn-group" role="group">
          	<input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}">
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


    <div class="row">
    	@if(is_null($purchases))

      @else
    	<div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-hover table-striped table-sort-data">
            <thead>
            	<tr>
            		<th>Date</th>
            		<th>Component</th>
            		<th>Qty</th>
            		<th class="text-right">Unit Cost</th>
            		<th class="text-right" class="text-right">Total Cost</th>
            		<th>Supplier</th>
            		<th>Component Category</th>
            		<th>Expense</th>
            		<th></th>
            	</tr>
            </thead>
            <tbody>
            	@foreach($purchases as $purchase)
            	<tr>
            		<td style="cursor: help;" title="{{ $purchase->date->format('D M j, Y') }}">
            			{{ $purchase->date->format('Y-m-d') }}
            		</td>
            		<td>{{ $purchase->component }}</td>
            		<td>{{ $purchase->qty }} 
            			<small class="text-muted">
            				{{ strtolower($purchase->uom)}}@if($purchase->qty>1)s
            				@endif
            			</small>
            		</td>
            		<td class="text-right">{{ number_format($purchase->ucost,2) }}</td>
            		<td class="text-right">{{ number_format($purchase->tcost,2) }}</td>
            		<td class="text-muted"><small>{{ $purchase->supplier }}</small></td>
            		<td class="text-muted"><small>{{ $purchase->compcat }}</small></td>
            		<td class="text-muted"><small>{{ $purchase->expense }}</small></td>
            		<td>
            			<span class="label 
            		@if($purchase->expscatcode=='05')
            			label-warning
            		@elseif($purchase->expscatcode=='08')
            			label-primary
            		@else
            			label-default
            		@endif
            		 pull-right" title="{{ $purchase->expscat }}" style="cursor: help;">{{ $purchase->expscatcode }}</span>
            		</td>
            	</tr>
            	@endforeach
            </tbody>
				</div> <!-- end: .table-responsive -->
			</div>
			@endif
		</div> <!-- end: .row table -->
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
                  label: item.item + ', ' + item.table,
                  value: item.item,
                  table: item.table,
  								id: item.id
                }
              }));
            }
        });
            },
        minLength: 3,
        select: function(event, ui) {
    			//console.log(ui);
          //log( ui.item ? "Selected: " + ui.item.label : "Nothing selected, input was " + this.value);
    			$("#table").val(ui.item.table); /* set the selected id */
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
      });

    });

  </script>

@endsection