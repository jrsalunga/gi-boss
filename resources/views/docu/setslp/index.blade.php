@extends('master')

@section('title', '- Card Settlement Slip Logs')

@section('body-class', 'setslp-logs')

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
    <li><a href="/setslp/log">Card Settlement Slip</a></li>
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
    <table class="table table-striped table-hover" style="margin-top: 0;">
      <thead>
        <tr>
          
          <th>Br Code</th>
          <th>Uploaded Filename</th>
          <!--<th>Size</th>-->
          <th class="text-right">Amount</th>
          <th></th>
          <th>Settlement Date/Time</th>
          <th>Cashier</th>
          <th>Remarks</th>
          <th>Uploaded</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        @foreach($setslps as $setslp)
        <tr>
          <td title="{{ $setslp->branch->descriptor }}">
            <a title="filter by {{ $setslp->branch->descriptor }}" href="/setslp/log?search=branch.code:{{strtolower($setslp->branch->code)}}">
            {{ $setslp->branch->code }}
            </a>
          </td>
          <td>
            <a href="/setslp/{{$setslp->lid()}}">
            <small>
              {{ $setslp->fileUpload->filename }}</a> 
            </small>
            @if($setslp->verified and $setslp->matched)
              <span class="glyphicon glyphicon-ok-sign text-success" data-toggle="tooltip" title="Matched by {{ $setslp->user->name }}"></span>
            @elseif($setslp->verified and !$setslp->matched)
              <span class="gly gly-ok" data-toggle="tooltip" title="Verified by {{ $setslp->user->name }}"></span>
            @else

            @endif
          </td>  
          <!--
          <td><small class="text-muted">{{ human_filesize($setslp->fileUpload->size) }}</small></td>
          <td style="padding: 8px 0 8px 0;">
            @if($setslp->verified and $setslp->matched)

            @elseif($setslp->verified and !$setslp->matched)

            @else
            <span class="pull-right">
              <a href="/setslp/{{$setslp->lid()}}" class="btn btn-primary btn-xs" title="View Card Settlement Slip Information">
                <span class="gly gly-eye-open"></span> view
              </a>
            </span>
            @endif
          </td>
          -->
          <td class="text-right">{{ number_format($setslp->amount,2) }}</td>
          <td>
            @if($setslp->terminal_id==1)
              <span class="label label-primary"><small>BDO</small></span>
            @elseif($setslp->terminal_id==2)
              <span class="label label-default"><small>RCBC</small></span>
            @elseif($setslp->terminal_id==3)
              <span class="label label-warning"><small>HSBC</small></span>
            @else

            @endif
          <td>
            
            <span class="hidden-xs" data-toggle="tooltip" title="{{ $setslp->datetime->format('D m/d/Y h:i A') }}">
              @if($setslp->datetime->format('Y-m-d')==now())
                {{ $setslp->datetime->format('h:i A') }}
              @else
                {{ $setslp->datetime->format('D M j') }}
              @endif
            </span> 
            <em>
              <small class="text-muted">
              {{ diffForHumans($setslp->datetime) }}
              </small>
            </em>

          </td>
          <td>{{ $setslp->cashier }}</td>
          <td>{{ $setslp->remarks }}</td>
          <td>
            <div data-toggle="tooltip" title="{{ $setslp->created_at->format('m/d/Y h:i A') }}">
            <span class="hidden-xs">
              @if($setslp->created_at->format('Y-m-d')==now())
                {{ $setslp->created_at->format('h:i A') }}
              @else
                {{ $setslp->created_at->format('D M j') }}
              @endif
            </span> 
            <em>
              <small class="text-muted">
              {{ diffForHumans($setslp->created_at) }}
              </small>
            </em>
            </div>
          </td>
          <td>
            <a title="filter by {{ $setslp->fileUpload->terminal }}" href="/setslp/log?search=fileUpload.terminal:{{$setslp->fileUpload->terminal}}">
            {{ $setslp->fileUpload->terminal }}
            </a>
          </td>
          
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    
    {!! $setslps->render() !!}
     
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
