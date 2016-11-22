@extends('master')

@section('title', '- Backup Checklist')

@section('body-class', 'backup-checklist')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/"></a></li>
    <li><a href="/storage/log">Backup</a></li>
    <li><a href="/backup/checklist">Checklist</a></li>
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
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/backup/checklist', 'method' => 'get', 'id'=>'dp-form']) !!}
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
          <a href="/backup/checklist?branchid={{$branch->lid()}}&amp;date={{ $date->copy()->subMonth()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->subMonth()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-left"></span>
          </a>
          @endif
          <input type="text" class="btn btn-default" id="dp-date" value="{{ $date->format('m/Y') }}" style="max-width: 90px;" readonly>
          <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
          @if(is_null(($branch)))

          @else
          <a href="/backup/checklist?branchid={{$branch->lid()}}&amp;date={{ $date->copy()->addMonth()->format('Y-m-d') }}" class="btn btn-default" title="{{ $date->copy()->addMonth()->format('Y-m-d') }}">
            <span class="glyphicon glyphicon-chevron-right"></span>
          </a>
          @endif
        </div>


        </div>
      </div>
    </nav>

    @include('_partials.alerts')


    @if(is_null($backups))

    @else

    <div class="table-responsive">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>Backup Date</th>
          <th>Filename</th>
          <th>
            <span style="cursor: help;" title="Shows only the lastest uploader of the same backup.">
              Cashier
            </span>
          </th>
          <th>
            <span style="cursor: help;" title="Tells whether the actual physical backup file is in the server's file system.">
              File in Server?
            </span>
          </th>
          <th>Upload Date</th>
          <th>Log Count</th>
        </tr>
      </thead>
      <tbody>
        @foreach($backups as $key => $b) 
        <tr <?=c()->format('Y-m-d')==$b['date']->format('Y-m-d')?'class="bg-success"':""?>>
        <td>{{ $b['date']->format('M j, D') }}</td>
          @if(is_null($b['backup']))
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
          @else
            <td>
              {{ $b['backup']->filename }}
            </td>
            <td title="Shows only the lastest uploader of the same backup.">
              {{ $b['backup']->cashier }}
            </td>
            <td>
              @if($b['exist'])
                <span class="glyphicon glyphicon-ok text-success"></span>
              @else
                <span class="glyphicon glyphicon-remove text-danger"></span>
              @endif
            </td>
            <td>
              <small><em>
              {{ $b['backup']->uploaddate->format('Y-m-d h:m:i A') }}
              </em>
              </small>
            </td>
            <td>
              <span class="badge">{{ $b['backup']->count }}</span>
            </td>
          @endif
          
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
      document.location.href = '/backup/checklist?branchid={{$branch->lid()}}&amp;date='+e.date.format('YYYY-MM-DD');
      @endif

      $('#date').val(date);
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
