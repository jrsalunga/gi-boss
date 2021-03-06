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
          @include('_partials.menu.logs')
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

            if ($nd->lt($backup->uploaddate) && $nn->lt($backup->uploaddate) && $backup->processed)
              $flag = 'text-danger';
            else if ($nd->lt($backup->uploaddate) && $nn->gt($backup->uploaddate) && $backup->processed)
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
          <td>
            @if(strlen($backup->remarks)>40)
              <span class="help" data-toggle="tooltip" title="{{ $backup->remarks }}">{{ substr($backup->remarks, 0, 40) }}...</span>
            @else
              {{ $backup->remarks }}
            @endif
          </td>
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
