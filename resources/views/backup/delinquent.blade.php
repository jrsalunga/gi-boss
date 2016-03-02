@extends('master')

@section('title', '- Delinquent')

@section('body-class', 'generate-delinquent')

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
    <li class="active">Backup</li>
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
            <!--
            <a href="/storage" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-hdd"></span>
              <span class="hidden-xs hidden-sm">Storage</span>
            </a> 
            -->
            <button type="button" class="btn btn-default active">
              <span class="gly gly-disk-remove"></span> 
              <span class="hidden-xs hidden-sm">Delinquent</span>
            </button>
          </div> <!-- end btn-grp -->
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
   <table class="table">
      <thead>
        <tr>
          <th>Branch</th>
          <th>Backup</th>
          <th>Upload Date</th>
        </tr>
      </thead>
      <tbody>
        @foreach($backups as $backup)
          <tr>
            <td>{{ $backup['code'] }} <span class="hidden-xs">- {{ $backup['descriptor'] }}</span></td>
            <td>
            @if(!empty($backup['filename']))
              {{ $backup['filename'] }} 
            @else
              -
            @endif
            </td>
            <td>
            @if(!empty($backup['date']))
              {{ $backup['uploaddate']->format('D m/d/Y h:i A') }} 
              <em><small>{{ diffForHumans($backup['uploaddate']) }}</small></em>
            @else
              -
            @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>  
    
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  @parent
  
  <script>
  
    
 
  </script>
@endsection
