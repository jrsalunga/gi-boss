@extends('master')

@section('title', '- Backups History')

@section('body-class', 'backup-log')

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
    <li><a href="/storage">Filing System</a></li>
    <li class="active">Logs</li>
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
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <div class="table-responsive">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Br Code</th>
          <th></th>
          <th>Filename</th>
          <th>Uploaded</th>
          <th>Cashier</th>
          <th>Processed</th>
          <th>Remarks</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        @foreach($backups as $backup)
        <tr>
          <td title="{{ $backup->branch->descriptor }}">
            <a title="filter by {{ $backup->branch->descriptor }}" href="/storage/log?search={{strtolower($backup->branch->code)}}&amp;searchFields=branch.code">
            {{ $backup->branch->code }}
            </a>
          </td>
          <td>
            @if($backup->long != '1' && $backup->processed == '1')
            <a href="/download/{{$backup->branch->code}}/{{$backup->year}}/{{pad($backup->month)}}/{{$backup->filename}}" target="_blank">
              <span class="glyphicon glyphicon-download-alt"></span>
            </a>
            @endif
          </td>
          <?php
            $nd = c($backup->filedate->copy()->addDay()->format('Y-m-d').' 12:00:00'); 
            $nn = c($backup->filedate->copy()->addDays(2)->format('Y-m-d').' 12:00:00'); 

            if ($nd->lt($backup->uploaddate) && $nn->lt($backup->uploaddate))
              $flag = 'text-danger';
            else if (($nd->lt($backup->uploaddate) && $nn->gt($backup->uploaddate)))
              $flag = 'text-warning';
            else
              $flag = '';
          ?>
          <td>{{ $backup->filename }}</td>
          <td class="{{$flag}}">
            <span class="hidden-xs">
            @if($backup->uploaddate->format('Y-m-d')==now())
              {{ $backup->uploaddate->format('h:i A') }}
            @else
              {{ $backup->uploaddate->format('m/d/Y h:i A') }}
            @endif
            </span> 
            <em>
              <small>
              <time class="_timeago" datetime="{{ $backup->uploaddate->toRfc3339String() }}" title="uploaded: {{ $backup->uploaddate->format('D, M j, Y h:i A') }}">
                {{ diffForHumans($backup->uploaddate) }} ago
              </time>
              </small>
            </em>
          </td>
          <td>{{ $backup->cashier }} </td>
          <td class="text-center">
            @if($backup->processed=='0')
              <span class="glyphicon glyphicon-remove"></span>
            @elseif($backup->processed=='1')
              <span class="glyphicon glyphicon-ok"></span>
            @elseif($backup->processed=='2')
              <span class="fa fa-envelope-o" title="Sent to HR" data-toggle="tooltip"></span>
            @else

            @endif
          </td>
          <?php  $x = explode(':', $backup->remarks) ?>
          <td>{{ $backup->remarks }} </td>
          <td>
              {{ $backup->terminal }}

              @if($backup->lat == '1')
                <span class="gly gly-certificate"></span>
              @endif

              @if($backup->long == '1')
                <span class="gly gly-address-book"></span>
              @endif

              @if($backup->long == '2')
                <span class="fa fa-file-powerpoint-o" title="GI PAY Payroll Backup"></span>
              @endif
            <!-- 
            <a href="https://www.google.com/maps/search/{{$backup->lat}},{{$backup->long}}/{{urldecode('%40')}}{{$backup->lat}},{{$backup->long}},18z" target="_blank">
            </a>
            -->
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>  
    </div>
    {!! $backups->render() !!}     
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  
  <script>
  
    
 
  </script>
@endsection
