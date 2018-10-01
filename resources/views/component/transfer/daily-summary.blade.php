@extends('master')

@section('title', '-  Daily Transfer Summary')

@section('body-class', 'transfer-daily-summary')

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
    <li class="active">Daily Summary @if(!is_null($branch))<small>({{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }})</small>@endif</li>   
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
            {!! Form::open(['url' => '/component/transfer/daily', 'method' => 'get', 'id'=>'filter-form']) !!}
            <!--
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid="'. $branch->id  }}">
            -->  
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
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
            <label class="btn btn-default" for="dp-ate-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
        
          </div><!-- end btn-grp -->

         
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <?php
      $ttcost = $tac = $trs = $tnc = $tem = 0;
    ?>
    @if(count($ds)>0)
    <div class="row">
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Transfered Emp Meal</p>
        <h3 id="tem" style="margin:0"></h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Transfered Food Cost</p>
        <h3 id="tac" style="margin:0"></h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Transfered Resto Supplies </p>
        <h3 id="trs" style="margin:0"></h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Cost</p>
        <h3 id="ttcost" style="margin:0"></h3>
      </div>

    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
        <table class="table table-condensed table-hover table-striped table-sort" style="margin-top: 0;">
          <thead>
            <tr>
              <th>Date</th>
              <th class="text-right" style="width:17%;">Total Cost</th>
              <th class="text-right" style="width:17%;">Food Cost</th>
              <th class="text-right" style="width:17%;">Drinks/Beers</th>
              <th class="text-right" style="width:17%;">Resto Supplies</th>
              <th class="text-right" style="width:17%;">Trans Emp Meal</th>
            </tr>
          </thead>
          <tbody>
            
            @foreach($ds as $d)
            <?php

              $actual_tranfer = $d->transcos-$d->emp_meal;
              $rs = $d->transcost-($d->transcos+$d->transncos);

              $ttcost += $d->transcost;
              $tac += $actual_tranfer;
              $tnc += $d->transncos;
              $trs += $rs;
              $tem += $d->emp_meal;
            ?>
              <tr>
                <td>
                  {{ $d->date->format('M j, D') }}
                </td>
                <td class="text-right">
                  <a href="/component/transfer?branchid={{ $branch->lid() }}&fr={{ $d->date->format('Y-m-d') }}&to={{ $d->date->format('Y-m-d') }}">
                  {{ nf($d->transcost) }}
                  </a>
                </td>
                <td class="text-right">{{ nf($actual_tranfer) }}</td>
                <td class="text-right">{{ nf($d->transncos) }}</td>
                <td class="text-right">{{ nf($rs) }}</td>
                <td class="text-right text-muted">{{ nf($d->emp_meal) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td class="text-right"><b class="text-muted">{{ nf($ttcost) }}</b></td>
              <td class="text-right"><b class="text-muted">{{ nf($tac) }}</b></td>
              <td class="text-right"><b class="text-muted">{{ nf($tnc) }}</b></td>
              <td class="text-right"><b class="text-muted">{{ nf($trs) }}</b></td>
              <td class="text-right"><b class="text-muted">{{ nf($tem) }}</b></td>
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
  

  $('#ttcost').text('{{ nf($ttcost) }}');
  $('#tac').text('{{ nf($tac) }}');
  $('#trs').text('{{ nf($trs) }}');
  $('#tem').text('{{ nf($tem) }}');
    

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

    

    


   	


    var submitForm  = function(){
      console.log('submit Form');
      $('#filter-form').submit();
    }
 
  });

  </script>

@endsection