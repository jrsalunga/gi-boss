@extends('master')

@section('title', '- Deposit Slip Logs')

@section('body-class', 'depslp-logs')

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
    <li><a href="/depslp/log">Deposit Slip</a></li>
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
    <table class="table table-striped table-hover" style="margin-top: 0;">
      <thead>
        <tr>
          
          <th>Br Code</th>
          <th>Uploaded Filename</th>
          <th>Size</th>
          <th class="text-right">Amount</th>
          <th>Deposit Date/Time</th>
          <th>Cashier</th>
          <th>Remarks</th>
          <th>Uploaded</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        @foreach($depslips as $depslip)
        <tr>
          <td title="{{ $depslip->branch->descriptor }}">
            <a title="filter by {{ $depslip->branch->descriptor }}" href="/depslp/log?search=branch.code:{{strtolower($depslip->branch->code)}}">
            {{ $depslip->branch->code }}
            </a>
          </td>
          <td>
            <a href="/depslp/{{$depslip->lid()}}">
            <small>
              {{ $depslip->fileUpload->filename }}</a> 
            </small>
            @if($depslip->verified and $depslip->matched)
              <span class="glyphicon glyphicon-ok-sign text-success" data-toggle="tooltip" title="Matched by {{ $depslip->user->name }}"></span>
            @elseif($depslip->verified and !$depslip->matched)
              <span class="gly gly-ok" data-toggle="tooltip" title="Verified by {{ $depslip->user->name }}"></span>
            @else

            @endif
            <td>
              
            <small class="text-muted">{{ human_filesize($depslip->fileUpload->size) }}</small>
            </td>
          </td>  
          <!--
          <td style="padding: 8px 0 8px 0;">
            @if($depslip->verified and $depslip->matched)

            @elseif($depslip->verified and !$depslip->matched)

            @else
            <span class="pull-right">
              <a href="/depslp/{{$depslip->lid()}}" class="btn btn-primary btn-xs" title="View Deposit Slip Information">
                <span class="gly gly-eye-open"></span> view
              </a>
            </span>
            @endif
          </td>
          -->
          <td class="text-right">{{ number_format($depslip->amount,2) }}</td>
          <td>
            
            <span class="hidden-xs" data-toggle="tooltip" title="{{ $depslip->deposit_date->format('D m/d/Y h:i A') }}">
              <a title="filter by {{ $depslip->deposit_date->format('D m/d/Y') }}" href="/depslp/log?search=date:{{$depslip->date->format('Y-m-d')}}">
              @if($depslip->deposit_date->format('Y-m-d')==now())
                {{ $depslip->deposit_date->format('h:i A') }}
              @else
                {{ $depslip->deposit_date->format('D M j') }}
              @endif
              </a>
            </span> 
            <em>
              <small class="text-muted">
              {{ diffForHumans($depslip->deposit_date) }}
              </small>
            </em>

          </td>
          <td>{{ $depslip->cashier }}</td>
          <td>{{ $depslip->remarks }}</td>
          <td>
            <div data-toggle="tooltip" title="{{ $depslip->created_at->format('m/d/Y h:i A') }}">
            <span class="hidden-xs">
              @if($depslip->created_at->format('Y-m-d')==now())
                {{ $depslip->created_at->format('h:i A') }}
              @else
                {{ $depslip->created_at->format('D M j') }}
              @endif
            </span> 
            <em>
              <small class="text-muted">
              {{ diffForHumans($depslip->created_at) }}
              </small>
            </em>
            </div>
          </td>
          <td>
            <a title="filter by {{ $depslip->fileUpload->terminal }}" href="/depslp/log?search=fileUpload.terminal:{{$depslip->fileUpload->terminal}}">
            {{ $depslip->fileUpload->terminal }}
            </a>
          </td>
          
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    
    {!! $depslips->render() !!}
     
  </div>
</div><!-- end container-fluid -->
 


@endsection






@section('js-external')
  @parent

<script src="/js/vendors-common.min.js"></script>
 <script type="text/javascript">
   $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
 </script>

@endsection
