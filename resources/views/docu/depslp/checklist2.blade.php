@extends('master')

@section('title', '- Depslip Checklist')

@section('body-class', 'depslip-checklist')

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
    <li><a href="/depslp/log">Deposit Slip</a></li>
    <li><a href="/depslp/checklist">Checklist</a></li>
    <li class="active">{{ $date->format('M Y') }}</li>
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
            <a href="/depslp/log" class="btn btn-default" title="Deposit Slip Logs">
              <span class="fa fa-bank"></span>
              <span class="hidden-xs hidden-sm">Logs</span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/depslp/checklist', 'method' => 'get', 'id'=>'dp-form']) !!}
            <!--
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'disabled=disabled data-branchid="'. $branch->id  }}">
            -->  
            <button type="submit" class="btn btn-success btn-go" title="Go"  {{ is_null($branch) ? '':'data-branchid="'. $branch->id  }}">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="date" id="date" value="{{ $date->format('Y-m-d') }}">
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


          <div class="btn-group pull-right clearfix" role="group">
          @if(is_null(($branch)))

          @else
          <a href="/depslp/checklist?branchid={{$branch->lid()}}&amp;date={{ $date->copy()->subMonth()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->subMonth()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </a>
          @endif
          <input type="text" class="btn btn-default" id="dp-date" value="{{ $date->format('m/Y') }}" style="max-width: 90px;" readonly>
          <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
          @if(is_null(($branch)))

          @else
          <a href="/depslp/checklist?branchid={{$branch->lid()}}&amp;date={{ $date->copy()->addMonth()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->addMonth()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-right"></span>
          </a>
          @endif
        </div>


        </div>
      </div>
    </nav>

    @include('_partials.alerts')


    @if(is_null($datas))

    @else
    <div class="table-responsive">
    <table class="table table-hover table-striped" style="font-family: 'Source Code Pro', monospace;">
      <thead>
        <tr>
          <th>Deposit Date</th>
          <th class="text-right">POS Cash</th>
          <th class="text-right">POS Cheque</th>
          <th class="text-right">Total POS</th>
          <th class="text-right">Cash&amp;Cheque</th>
          <th class="text-right">
            <span style="cursor: help;" title="Shows only the lastest uploader of the same backup.">
              Cash
            </span>
          </th>
          <th class="text-right">Cheque</th>
          <th class="text-right">Total Uploaded</th>
          <th class="text-right">
            <span style="cursor: help;" title="Tells whether the actual physical backup file is in the server's file system.">
              Match?
            </span>
          </th>
        </tr>
      </thead>
      <tbody>
        @foreach($datas as $key => $b) 
        <?php
          $bg = c()->format('Y-m-d')==$b['date']->format('Y-m-d') ? 'bg-success':'';
        ?>
        <tr>
          <td class="{{ $bg }}">{{ $b['date']->format('M j, D') }}</td>
                      
          @foreach($b['pos'] as $pos)
          <td class="{{ $bg }} text-right">
            @if(!$pos['amount'])
              
            @else
              {{ number_format($pos['amount'],2) }}
            @endif
          </td>
          @endforeach

          <td class="{{ $bg }} text-right">
            @if($b['pos_totamt']>0)
              <b>
              {{ number_format($b['pos_totamt'],2) }}
              </b>
            @else
              
            @endif
          </td>

          @foreach($b['depo_type'] as $type)
            <td class="{{ $bg }} text-right">
            @if(!$type['slips'])
              
            @else
              {{ number_format($type['amount'],2) }}
            @endif
            </td>
          @endforeach
          <td class="{{ $bg }} text-right">
            @if($b['depo_totcnt']>0)
              <b>
              {{ number_format($b['depo_totamt'],2) }}
              </b>
            @else
              
            @endif
          </td>
          <td class="{{ $bg }} text-center">
           
              @if($b['depo_totamt'] > $b['pos_totamt'])
                <span class="glyphicon glyphicon-remove text-danger"></span>
              @else
                <span class="glyphicon glyphicon-ok text-success"></span>
              @endif
                {{ $b['depo_totamt'] }} = {{ $b['pos_totamt'] }}
            
          </td>
          
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    @endif
    
   

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script type="text/javascript">
  $(document).ready(function(){

    $('#dp-date').datetimepicker({
      //defaultDate: "2016-06-01",
      format: 'MM/YYYY',
      showTodayButton: true,
      ignoreReadonly: true,
      viewMode: 'months'
    }).on('dp.change', function(e){
      var date = e.date.format('YYYY-MM-DD');
      @if(!is_null(($branch)))
      document.location.href = '/depslp/checklist?branchid={{$branch->lid()}}&date='+e.date.format('YYYY-MM-DD');
      @endif
    });


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
  });
  </script>
@endsection
