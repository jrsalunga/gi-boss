@extends('master')

@section('title', '- Backups History')

@section('body-class', 'generate-dtr')

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
            <a href="/storage" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-hdd"></span>
              <span class="hidden-xs hidden-sm">Filing System</span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span> 
              <span class="hidden-xs hidden-sm">Logs</span>
            </button>
            <a href="/backup/delinquent" class="btn btn-default">
              <span class="gly gly-disk-remove"></span> 
              <span class="hidden-xs hidden-sm">Delinquent</span>
            </a> 
          </div> <!-- end btn-grp -->
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>Br Code</th>
          <th class="hidden-xs hidden-sm"></th>
          <th>Filename</th>
          <th>Uploaded</th>
          <th>Cashier</th>
          <th>Processed</th>
          <th class="hidden-xs hidden-sm">Remarks</th>
          <th class="hidden-xs hidden-sm">IP Address</th>
        </tr>
      </thead>
      <tbody>
        @foreach($backups as $backup)
        <tr>
          <td title="{{ $backup->branch->descriptor }}">{{ $backup->branch->code }}</td>
          <td class="hidden-xs hidden-sm">
            @if($backup->processed == '1')
            <a href="/download/{{$backup->branch->code}}/{{$backup->year}}/{{$backup->month}}/{{$backup->filename}}" target="_blank">
              <span class="glyphicon glyphicon-download-alt"></span>
            </a>
            @endif
          </td>
          <td>{{ $backup->filename }} </td>
          <td>
            <span class="hidden-xs">{{ $backup->uploaddate->format('m/d/Y h:i A') }}</span> 
            <em>
              <small title="{{ $backup->uploaddate->format('m/d/Y h:i A') }}">
              {{ diffForHumans($backup->uploaddate) }}
              </small>
            </em>
          </td>
          <td>{{ $backup->cashier }} </td>
          <td class="text-center"><span class="glyphicon glyphicon-{{ $backup->processed == '1' ? 'ok':'remove' }}"></span></td>
          <?php  $x = explode(':', $backup->remarks) ?>
          <td class="hidden-xs hidden-sm">{{ $x['1'] }} </td>
          <td class="hidden-xs hidden-sm">
            <a href="https://www.google.com/maps/search/{{$backup->lat}},{{$backup->long}}/{{urldecode('%40')}}{{$backup->lat}},{{$backup->long}},18z" target="_blank">
              {{ $backup->terminal }} 
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>  
    {!! $backups->render() !!}     
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  @parent
  
  <script>
  
    
 
  </script>
@endsection
