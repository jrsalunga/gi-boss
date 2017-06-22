@extends('master')

@section('title', '- Mansked')

@section('body-class', 'mansked')

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
<?php
$flag = false;
?>
@section('container-body')
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    @if (request()->has('search'))
      <li><a href="/mansked">Mansked</a></li>
      <?php
        $x = explode(':', request()->input('search'));

        if (isset($x[1])) {
          if ($x[0]=='date')
            $b = c($x[1])->year.'-W'.c($x[1])->weekOfYear;
          else  {
            $flag = true;
            $b = strtoupper($x[1]);
          }
        } else {
          $b = '-';
        }
      ?>
      <li class="active">{{ $b }}</li>
    @else 
      <li class="active">Mansked</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <!--
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
            -->
            <button type="button" class="btn btn-default active">
              <span class="gly gly-notes-2"></span>
              <span class="hidden-xs hidden-sm">Mansked</span>
              
            </button>
            @if($flag)
              <a href="/timelog?search=branch.code:{{strtolower($b)}}" class="btn btn-default">
            @else
              <a href="/timelog" class="btn btn-default">
            @endif
              <span class="gly gly-stopwatch"></span>
              <span class="hidden-xs hidden-sm">Timelogs</span>
            </a> 
            
            <a href="/timesheet" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Timesheet</span>
            </a>
          </div> <!-- end btn-grp -->

          @if(request()->has('search'))
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="fa fa-filter"></span>
              <span>{{ $b }}</span>
            </button>
            <a type="button" class="btn btn-default" href="/mansked" title="Remove Filter"><span class="fa fa-close"></span></a>
          </div> <!-- end btn-grp -->
          @endif
          

        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-condensed table-striped">
            <thead>
              <tr>
                <th>Branch</th>
                <th>Week</td>
                <th>Dates</th>
                <th>Ref No</th>
              </tr>
            </thead>
            <tbody>
              @foreach($manskeds as $mansked)
                <tr>
                  <td>
                    <a href="/mansked?search=branch.code:{{strtolower($mansked->branch->code)}}">
                      {{ $mansked->branch->code or ''}}
                    </a>
                  </td>
                  <td>
                    <a href="/mansked/{{$mansked->lid()}}" class="text-ccc" data-toggle="tooltip" title="Go to {{ $mansked->branch->code }}'s Week {{$mansked->weekno}} Mansked">
                      <span class="gly gly-notes-2"></span>
                    </a>
                    <a href="/mansked/{{$mansked->lid()}}">
                      {{ $mansked->year }}-W{{$mansked->weekno}}
                    </a>
                  </td>
                  <td>
                    <a href="/mansked?search=date:{{$mansked->date->format('Y-m-d')}}">
                      {{ $mansked->date->copy()->startOfWeek()->format('M d') }} -
                      {{ $mansked->date->copy()->endOfWeek()->format('M d') }}
                    </a>
                    </td>
                  <td>
                    <span class="label label-default">
                      {{ $mansked->refno }}
                    </span>
                  </td>

                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        {!! $manskeds->render() !!}
        
      </div>
    </div>

    
    

      
    

  <div>

</div><!-- end .container-fluid -->
@endsection




@section('js-external')
  @parent

  <script src="/js/vendors-common.min.js"></script>

  <script>
   moment.locale('en', { week : {
      dow : 1 // Monday is the first day of the week.
    }});

  $('document').ready(function(){

    

   
  });
  </script>
@endsection