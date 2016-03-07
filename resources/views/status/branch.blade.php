@extends('master')

@section('title', '- Branch Status')

@section('body-class', 'branch-status')

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
    <li class="active">Branch Status</li>
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
          </div> <!-- end btn-grp -->
          <!--
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            <button class="btn btn-success" title="Go">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
          </div> <!-- end btn-grp -->

          <div class="btn-group pull-right clearfix">
            <div type="button" class="btn btn-default" style="pointer-events: none; margin-left: 5px;">
              <span class="gly gly-shop"></span>
              <span class="br-code">TRI</span>
            </div>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="caret"></span>
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu">
              <li><a href="#">Action</a></li>
              <li><a href="#">Another action</a></li>
              <li><a href="#">Something else here</a></li>
              <li><a href="#">Separated link</a></li>
            </ul>
          </div>
          
          <?php


          ?>
          <div class="btn-group pull-right clearfix" role="group">
            <!--
            <a href="/" class="btn btn-default" title="">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            -->
            <label class="btn btn-default" for="dp-date-fr"><span class="glyphicon glyphicon-calendar"></span></label>
            <input type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('D, M j') }}" style="pointer-events: none; cursor: text; max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('D, M j') }}" style="pointer-events: none; cursor: text; max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to"><span class="glyphicon glyphicon-calendar"></span></label>
            <!--
            <a href="/" class="btn btn-default" title="">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            -->
          </div><!-- end btn-grp -->
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
        
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
  <script src="//d3js.org/d3.v3.min.js"></script>
  
  <script>
    $(document).ready(function(){
    
      $('#dp-date-fr').datetimepicker({
        defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'ddd, MMM D',
        showTodayButton: true
      }).on('dp.change', function(e){
        console.log(e.date.year()+'-'+e.date.format("MM")+'-'+e.date.format('DD'));
      });


      $('#dp-date-to').datetimepicker({
        defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'ddd, MMM D',
        showTodayButton: true
      }).on('dp.change', function(e){
        console.log(e.date.year()+'-'+e.date.format("MM")+'-'+e.date.format('DD'));
      });


    });
    
 
  </script>
@endsection
