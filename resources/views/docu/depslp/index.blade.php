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
          @include('_partials.menu.logs')
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    @if(count($depslips)>0)
    <div class="table-responsive">
    <table class="table table-striped table-hover" style="margin-top: 0;">
      <thead>
        <tr>
          
          <th>Br Code</th>
          <th>Uploaded Filename</th>
          <!--<th>Size</th>-->
          <th class="text-right">Amount</th>
          <th></th>
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
          </td>  
          <!--
          <td><small class="text-muted">{{ human_filesize($depslip->fileUpload->size) }}</small></td>
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
            @if($depslip->type==1)
              <span class="label label-success" title="Cash" style="cursor: help;"><small>C</small></span>
            @elseif($depslip->type==2)
              <span class="label label-info" title="Cheque" style="cursor: help;"><small>K</small></span>
            @else

            @endif
          </td>
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

    @else
      No Upload Record Found.
    @endif
     
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
