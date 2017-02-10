@extends('master')

@section('title', '- Deposit Slip Logs')

@section('body-class', 'depslp-logs')

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
            <a href="/storage" class="btn btn-default">
              <span class="gly gly-hdd"></span>
              <span class="hidden-xs hidden-sm">Filing System</span>
            </a>
            
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
          <!--<th></th>-->
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
          <td>{{ $depslip->branch->code }}</td>
          <td>
            <a href="/depslp/{{$depslip->lid()}}">{{ $depslip->fileUpload->filename }}</a>
            @if($depslip->verified and $depslip->matched)
              <span class="glyphicon glyphicon-ok-sign text-success" data-toggle="tooltip" title="Matched by {{ $depslip->user->name }}"></span>
            @elseif($depslip->verified and !$depslip->matched)
              <span class="gly gly-ok" data-toggle="tooltip" title="Verified by {{ $depslip->user->name }}"></span>
            @else

            @endif
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
              @if($depslip->deposit_date->format('Y-m-d')==now())
                {{ $depslip->deposit_date->format('h:i A') }}
              @else
                {{ $depslip->deposit_date->format('D M j') }}
              @endif
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
          <td>{{ $depslip->fileUpload->terminal }}</td>
          
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


@endsection