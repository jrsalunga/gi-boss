@extends('master')

@section('title', '- Batch Downloads')

@section('body-class', 'batch-downloads')

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
    <li>Batch Downloads</li>
    
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
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-hdd"></span> 
                <span class="hidden-xs hidden-sm">Filing System</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="/storage"><span class="fa fa-file-archive-o"></span> Backup</a></li>
                <li><a href="/depslp"><span class="fa fa-bank"></span> Deposit Slip</a></li>
              </ul>
            </div>
            
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-calendar-check-o"></span>
                <span class="hidden-xs hidden-sm">Checklist</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="/backup/checklist"><span class="fa fa-file-archive-o"></span> Backup</a></li>
                <li><a href="/depslp/checklist"><span class="fa fa-bank"></span> Deposit Slip</a></li>
              </ul>
            </div>

            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-th-list"></span>
                <span class="hidden-xs hidden-sm">Logs</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="/storage/log"><span class="fa fa-file-archive-o"></span> Backup</a></li>
                <li><a href="/depslp/log"><span class="fa fa-bank"></span> Deposit Slip</a></li>
              </ul>
            </div>
            <a href="/backup/delinquent" class="btn btn-default">
              <span class="gly gly-disk-remove"></span> 
              <span class="hidden-xs hidden-sm">Delinquent</span>
            </a> 
          </div> <!-- end btn-grp -->
          <!--
          <div class="btn-group" role="group">
            <a href="/backups/upload" class="btn btn-default">
              <span class="glyphicon glyphicon-cloud-upload"></span> DropBox
            </a>
          </div>
        -->
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    @if(session()->has('file'))
      <div class="alert alert-success alert-important">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
         {{ session('count') }} backup(s) found! 
         <a href="/downloads/{{ session('file') }}">
          <strong><span class="glyphicon glyphicon-save"></span> {{ session('file') }}</strong>
         </a>
      </div>
    @endif


    <div>
    <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation">
          <a href="/storage" aria-controls="pos" role="tab">
            Backup Archive
          </a>
        </li>
        <li role="presentation" class="active">
          <a href="/backups" aria-controls="pos" role="tab">
            Batch Downloads
          </a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="file-explorer tab-content">
        <div role="tabpanel" class="tab-pane active" >
          <div style="height: 10px;"></div>
         
              <div class="row">
                <div class="col-sm-6 col-md-4">
                  
                  <div class="navbar-form"  style="padding:0; margin: 8px 15px; border-top: 0;">
                  <form action="/storage/batch-download" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="file" style="display: block;">Select Backup Date:</label>
                          <div class="btn-group dp-container" role="group">
                            <label class="btn btn-default" for="dp-date">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </label>
                            <input readonly="" type="text" class="btn btn-default dp" id="dp-date" style="max-width: 110px;">
                            <input type="hidden" id="date" name="date">
                          </div>
                        </div>
                      </div>
                    </div><!-- end: .row -->
                    <div class="row" style="margin-top: 20px;">
                      <div class="col-md-12">
                        <button type="submit" id="btn-submit" class="btn btn-primary">
                          <span class="gly gly-ok"></span> Submit
                        </button>
                      </div>
                    </div><!-- end: .row -->
                  </form>
                  </div><!-- end: .navbar-form -->
                </div><!-- end: .col-sm-6.col-md-4 -->
                <div class="col-sm-6 col-md-8">
                  @if(session()->has('branches'))
                    <p class="text-info">{{ count(session('branches')) }}
                      @if(count(session('branches'))>1)
                        branches
                      @else
                        branch
                      @endif
                        no backup.
                    </p>
                    @foreach(session('branches') as $branch)
                      <span class="btn">{{ $branch }}</span>
                    @endforeach
                  @endif
                </div><!-- end: .col-sm-6.col-md-8 -->
              </div><!-- end: .row -->
          </div>
        </div>
      </div>
    </div>   
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  
  <script>
  
  $('#date').val(moment().format('YYYY-MM-DD'));
  
  $('#dp-date').datetimepicker({
    defaultDate: moment(),
    format: 'YYYY-MM-DD',
    showTodayButton: true,
    ignoreReadonly: true,
  }).on('dp.change', function(e){
    var date = e.date.format('YYYY-MM-DD');
    console.log(date);
    $('#date').val(date);
  });

 
  </script>
@endsection
