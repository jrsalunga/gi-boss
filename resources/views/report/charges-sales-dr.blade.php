@extends('master')

@section('title', ' - All Branch Charges Sales by Date Range')

@section('css-internal')

@endsection

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
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li><a href="/report/sales/charges/dr-all">Charges Sales</a></li>
    <li class="active">All Branch <small>({{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }})</small></li>
  </ol>

    
  
  
  <nav id="nav-action" class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-form">
        <div class="btn-group" role="group">
          <!-- <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
            <span class="gly gly-unshare"></span>
            <span class="hidden-xs hidden-sm">Back</span>
          </a>  -->
          <!-- <a href="/delivery" class="btn btn-default" title="All Branches">
            <span class="glyphicon glyphicon-star"></span>
            <span class="hidden-xs hidden-sm">Starred</span>
          </a>
          <button type="button" class="btn btn-default active" title="Strarred Branches">
            <span class="glyphicon glyphicon-list-alt"></span>
            <span class="hidden-xs hidden-sm">All</span>
          </button> -->
        </div>

        <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/report/sales/charges/dr-all', 'method' => 'get', 'id'=>'dp-form']) !!}
            <button type="submit" class="btn btn-success btn-go" title="Go">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->
        
        <div class="btn-group pull-right clearfix dp-container" role="group">
            
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
        
          </div><!-- end btn-grp -->

          

      </div>
    </div>
  </nav>

  
  <div class="row">
    <div class="col-md-6">
      <div role="tabpanel" class="tab-pane" id="stats">
          <!-- Copany Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Company</div>
            <div class="panel-body">
              <div class="row">
                <div class="table-responsive">
                <table class="table table-condensed table-hover table-striped table-sort tablesorter tablesorter-default" style="margin-top: 0;" role="grid"> 
                  <thead>
                    <tr>
                      <td>Code</td>
                      <td>Company</td>
                      <td class="text-right">Charged Sales</td>
                      <td class="text-right">Deducted Sales</td>
                      <td class="text-right">Deduction</td>
                      <td class="text-right">%</td>
                    </tr>
                    <tbody>
                      @foreach($comps as $key => $comp)
                      <tr>
                        <td>{{ $key }}</td>
                        <td>{{ $comp['company'] }}</td>
                        <td class="text-right">{{ nf($comp['sales_actual']) }}</td>
                        <td class="text-right">{{ nf($comp['sales_deducted']) }}</td>
                        <td class="text-right">{{ nf($comp['sales_actual']-$comp['sales_deducted']) }}</td>
                        <td class="text-right">{{ nf((($comp['sales_actual']-$comp['sales_deducted'])/$comp['sales_actual'])*100) }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </thead>
                </table>
                </div>
              </div>
            </div>
          </div>
      
    </div>
    <div class="col-md-6">
      
    </div>
  </div>
    
</div>
@endsection














@section('js-external')
  
<script src="/js/vendors-common.min.js"></script>
<script src="/js/dr-picker.js"></script>

<script>
   moment.locale('en', { week : {
      dow : 1 // Monday is the first day of the week.
    }});  

  $(document).ready(function(){

    $('.table-sort-all').tablesorter({
      stringTo: 'min',
      sortList: [[1,1]],
      headers: {
        1: { sorter: "digit", string: "min" }, // non-numeric content is treated as a MAX value
        //2: { sorter: "digit", empty : "top" }, // sort empty cells to the top
        //3: { sorter: "digit", string: "min" }  // non-numeric content is treated as a MIN value
      } 
    });
    
    $('#dp-date-fr').datetimepicker({
        //defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        //calendarWeeks: true,
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
       // defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        //calendarWeeks: true,
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

</script>

  
@endsection