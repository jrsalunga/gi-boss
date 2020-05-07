@extends('master')

@section('title', '- Kitchen Log | Checklist')

@section('body-class', 'kitlog')

@section('css-external')
<style type="text/css">

</style>
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
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
  <ol class="breadcrumb">
    <li><a href="/dashboard"><span class="gly gly-shop"></span> </a></li>
    <li><a href="/kitlog">Kitchen Log</a></li>
    @if(is_null($branch))
      <li>Checklist</li>
    @else
      <li><a href="/kitlog/checklist">Checklist</a></li>
      <li><a href="/kitlog/checklist?branchid={{$branch->lid()}}&date={{$date->format('Y-m-d')}}">{{$branch->code}}</a></li>
      <li>{{ $date->format('F Y') }}</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          
          <!-- <div class="btn-group" role="group">
             <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
               <span class="gly gly-unshare"></span>
               <span class="hidden-xs hidden-sm">Back</span>
             </a> 
           </div> --> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix hidden-xs" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/kitlog/checklist', 'method' => 'get', 'id'=>'filter-form']) !!}
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
                  <span class="br-code">All Branch</span>
                  <span class="br-desc hidden-xs hidden-sm"></span>
                @else
                  <span class="br-code">{{ $branch->code }}</span>
                  <span class="br-desc hidden-xs hidden-sm">- {{ $branch->descriptor }}</span>
                @endif
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu br" aria-labelledby="dLabel" style="max-height: 400px; overflow-y: scroll;">
                @if(is_null($branch))
                @else
                <li><a href="#" data-desc="All Branch" data-code="All" data-branchid="">All Branch</a></li>
                @endif
                
                @foreach($branches as $b)
                <li>
                  <a href="#" data-desc="{{ $b->descriptor }}" data-code="{{ $b->code }}" data-branchid="{{ $b->lid() }}">{{ $b->code }}</a>
                </li>
                @endforeach
              </ul>
            </div> <!-- .dropdown -->
          </div>

          <div class="btn-group pull-right clearfix" role="group" style="margin-right: 5px;">
            <a href="/kitlog/checklist?{{is_null($branch)?'':'branchid='.$branch->lid().'&'}}date={{ $date->copy()->startOfMonth()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->startOfMonth()->subDay()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>

            <input type="text" class="btn btn-default" id="dp-date" value="{{ $date->format('m/Y') }}" style="max-width: 90px;" readonly>
            <label class="btn btn-default hidden-xs" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>

            <a href="/kitlog/checklist?{{is_null($branch)?'':'branchid='.$branch->lid().'&'}}date={{ $date->copy()->addDay()->endOfMonth()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->addDay()->endOfMonth()->format('Y-m-d') }}" data-toggle="loader">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>        
        </div>
    </nav>
    
    @include('_partials.alerts')

    <div class="row">
      <div class="col-md-12">

        @if(count($datas)>0)
          <table class="table table-condensed tablesorter" style="margin-top: 0;">
            <thead>
              <tr>
                <th>Date</th>
                <th>Kitlog</th>
                <th>Change Item Transaction</th>
                <th class="text-right">Change Item Difference</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $ctr = $tot_log = $tot_txn = $tot_diff = 0;
              ?>
              @foreach($datas as $data)
              <tr>
                @if(is_null($data['ds']))
                  <td>{{ $data['date']->format('Y-m-d') }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                @else
                  <td>
                    @if($data['ds']->kitlog>0)
                    <a href="/kitlog/logs?branchid={{ stl($data['ds']->branchid) }}&productid=&fr={{ $data['date']->format('Y-m-d') }}&to={{ $data['date']->format('Y-m-d') }}">
                    {{ $data['date']->format('Y-m-d') }}
                    </a>
                    @else 
                      {{ $data['date']->format('Y-m-d') }}
                    @endif
                  </td>
                  <td>
                    @if($data['ds']->kitlog>0) 
                      <a href="/kitlog/logs?branchid={{ stl($data['ds']->branchid) }}&productid=&fr={{ $data['date']->format('Y-m-d') }}&to={{ $data['date']->format('Y-m-d') }}">
                        <span class="glyphicon glyphicon-list" data-toggle="tooltip" title="Go To Raw Kitchen Log"></span>
                      </a>
                    @else
                      
                    @endif
                  </td>
                  <td>{{ $data['ds']->change_item }}</td>
                  <td class="text-right">{{ nf($data['ds']->change_item_diff) }}</td>
                  <?php
                    $ctr++;
                    $tot_log += $data['ds']->kitlog;
                    $tot_txn += ($data['ds']->change_item+0);
                    $tot_diff += $data['ds']->change_item_diff;
                  ?>
                @endif
              </tr>
              @endforeach

            </tbody>
            <tfoot>
              <tr>
                <td><b data-toggle="tooltip" title="# of transactions per day" class="help">{{ $ctr }}</b></td>
                <td>
                  <b data-toggle="tooltip" title="Total Kitlog" class="help">{{ $tot_log }}</b>
                </td>
                <td> 
                  <b data-toggle="tooltip" title="Total Change Item Transaction" class="help">{{ $tot_txn }}</b>
                  <div>
                    <em>
                      @if($ctr>0)
                      <small data-toggle="tooltip" title="Ave Change Item Transaction ({{ $tot_txn }}/{{ $ctr }}={{ nf($tot_txn/$ctr) }}) " class="help">
                        {{ nf($tot_txn/$ctr) }}
                      </small>
                      @endif
                    </em>
                  </div>
                </td>
                <td class="text-right">
                  <b data-toggle="tooltip" title="Total Change Item Difference" class="help">{{ nf($tot_diff) }}</b>
                  <div>
                    <em>
                      @if($ctr>0)
                      <small data-toggle="tooltip" title="Ave Change Item Difference ({{ $tot_diff }}/{{ $ctr }}={{ nf($tot_diff/$ctr) }}) " class="help">
                        {{ nf($tot_diff/$ctr) }}
                      </small>
                      @endif
                    </em>
                  </div>
                </td>
              </tr>
            </tfoot>
          </table>
        @else
          {{ is_null($branch) ? '':'No Data'  }}
        @endif
      </div>
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

  $('#dp-date').datetimepicker({
    format: 'MM/YYYY',
    showTodayButton: true,
    ignoreReadonly: true,
    viewMode: 'months'
  }).on('dp.change', function(e){
    var date = e.date.format('YYYY-MM-DD');
    $('#date').val(date);
    loader();
    @if(!is_null(($branch)))
      document.location.href = '/kitlog/checklist?branchid={{$branch->lid()}}&date='+e.date.format('YYYY-MM-DD');
    @else
      document.location.href = '/kitlog/checklist?branchid='+$('#branchid').val()+'&date='+e.date.format('YYYY-MM-DD');
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
    
    document.location.href = '/kitlog/checklist?branchid='+el.data('branchid')+'&date='+$('#date').val();
    //console.log($('.btn-go').data('branchid'));
  });


  







  
</script>
@endsection