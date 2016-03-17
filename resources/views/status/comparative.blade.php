@extends('master')

@section('title', '- Comparative')

@section('body-class', 'comparative')

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
    <li class="active">Comparative Analytics</li>
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

          
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
      {!! Form::open(['url' => '/status/post-comparative', 'method' => 'post', 'class'=>'form-horizontal']) !!}
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Branches</label>
          <div class="col-sm-10">
            <select class="selectpicker form-control" multiple data-max-options="5" style="display: none;">
              @foreach($branches as $b)
                <option value="{{ $b->id }}" title="{{ $b->code }}"> {{ $b->code }} - {{ $b->descriptor }} </option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Stat to View</label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-default active">
                <input type="radio" name="options" id="option1" autocomplete="off" checked>Sales
              </label>
              <label class="btn btn-default">
                <input type="radio" name="options" id="option2" autocomplete="off">Man Cost
              </label>
              <label class="btn btn-default">
                <input type="radio" name="options" id="option3" autocomplete="off">Tips
              </label>
              <label class="btn btn-default">
                <input type="radio" name="options" id="option4" autocomplete="off">Sales/Emp
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Time Frame</label>
          <div class="col-sm-10">
            <div class="btn-group" role="group">
            <!--
            <a href="/" class="btn btn-default" title="">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            -->
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('D, M j') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('D, M j') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <!--
            <a href="/" class="btn btn-default" title="">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            -->
          </div><!-- end btn-grp -->
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button id="btn-go" type="button" class="btn btn-success" >Go</button>
          </div>
        </div>
      </form>
    





@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"> </script>
  
  <script>
    $(document).ready(function(){

      $('#dp-date-fr').datetimepicker({
        defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'ddd, MMM D',
        showTodayButton: true,
        ignoreReadonly: true
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-date-to').datetimepicker({
        defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'ddd, MMM D',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });
    });

    var data = {};

    $('.selectpicker').on('hidden.bs.select', function (e) {
      console.log($(this).val());
    }).on('changed.bs.select', function (e) {
      data.branch = $(this).val();
    });


    $('#btn-go').on('click', function(){
      console.log(data);
    });

    $('#option1').on('click', function(){
      console.log('fdsafsa');
    });


 
  </script>
@endsection