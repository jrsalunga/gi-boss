@extends('master')

@section('title', ' - Branch Masterfiles')

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
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li><a href="/masterfiles">Masterfiles</a></li>
    <li class="active">Branch</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/masterfiles" class="btn btn-default" title="Back to Mastefiles">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group">
            <div class="dropdown">
              <button id="dLabel" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-shop"></span>
                Branch
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu br" aria-labelledby="dLabel" style="max-height: 400px; overflow-y: scroll;">
                @foreach($tables as $table)
                <li>
                  <a href="/masterfiles/{{$table}}" data-table="{{ $table }}">{{ ucwords($table) }}</a>
                </li>
                @endforeach
              </ul>
            </div> <!-- .dropdown -->
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix hidden-xs" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/masterfiles/branch', 'method' => 'get', 'id'=>'filter-form']) !!}
            <div class="input-group">
              <input type="text" id="search" name="search" class="form-control searchfield" value="{{ request()->input('search') }}" placeholder="Search">
              <span class="input-group-btn">
                <button type="submit" class="btn btn-default btn-go" title="Go">
                  <span class="gly gly-search"></span>
                  <!-- <span class="hidden-xs hidden-sm">Go</span> -->
                </button> 
              </span>
            </div>
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Code</th>
            <th>Branch</th>
            <th>Active</th>
            <th>Company</th>
            <th>Sector</th>
            <th>Phone</th>
          </tr>
        </thead>
        <tbody>
          @foreach($datas as $branch)
          <?php
            $branch->load('company');
          ?>
          <tr>
            <td>{{ $branch->code }}</td>
            <td>{{ $branch->descriptor }}</td>
            <td>
              @if($branch->active==1)
                <span class="glyphicon glyphicon-ok"></span>
              @else
                <span class="glyphicon glyphicon-remove"></span>
              @endif
            </td>
            <td>{{ $branch->company->descriptor }}</td>
            <td>{{ $branch->sectorid }}</td>
            <td>{{ $branch->phone }}</td>
          </tr>
          @endforeach
        </tbody>
        </table>
      </div> <!-- end: .table-responsive -->
    </div> <!-- end: .md-col-12 -->
    <div class="col-sm-9">
      {!! $datas->render() !!}     
    </div> <!-- end: .md-col-6 -->
    <div class="col-sm-3">
      <div class="pagination pull-right">
        <?php
          $page = $datas->toArray();
        ?>
        Showing {{ $page['from'] }} to {{ $page['to'] }} of {{ $page['total'] }} entries
      </div>
    </div> <!-- end: .md-col-6 -->
  </div> <!-- end: .row -->
</div>
@endsection

@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/dr-picker.js"> </script>
  <script type="text/javascript">
    @if(request()->has('search'))
    $('#search').focusTextToEnd();
    @endif
  </script>
@endsection