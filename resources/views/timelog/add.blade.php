@extends('master')

@section('title', '- Employee Timelog')

@section('body-class', 'timesheet-index')

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
    <li><span class="gly gly-shop"></span> </a></li>
    <li><a href="/timelog">Timelog</a></li>
    <li class="active">Add</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/employee/tracker" class="btn btn-default">
              <span class="fa fa-calendar-o"></span>
              <span class="hidden-xs hidden-sm">Daily</span>
            </a>
            <a data-toggle="loader" href="/employee/tracker/summary" class="btn btn-default">
              <span class="fa fa-calendar"></span>
              <span class="hidden-xs hidden-sm">Cut Off</span>
            </a>
          </div> <!-- end btn-grp -->
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-plus"></span>
              <span class="hidden-xs hidden-sm">Add Timelog</span>
            </button>
          </div>
        </div>
      </div>
    </nav>

    @include('_partials.alerts')


    {!! Form::open(['url' => '/timelog', 'accept-charset'=>'utf-8', 'name'=>'frm-timelog']) !!}
    <div class="row">
      <div class="col-sm-8 col-md-6">
        <div class="panel panel-success">
          <div class="panel-heading">Timelog Manual Entry Form</div>
          <div class="panel-body row">
           
            <div class="col-sm-10 col-md-12">
              <div class="form-group">
                <label for="search-employee" class="control-label">Employee</label>
                <input type="text" class="form-control" id="search-employee" placeholder="Search Employee" maxlength="120" required>
                <!-- <input type="text" name="employeeid" id="employeeid" required style="height:0; width:0; padding:0; margin:0; border:0;"> --> 
                <input type="hidden" name="employeeid" id="employeeid"> 
                
              </div>
            </div>

            <div class="col-sm-10 col-md-12">
              <div class="form-group">
                <label for="branchid" class="control-label">Branch</label>
                <select class="form-control" id="branchid" name="branchid" required>
                  <option value="" readonly>-- Select Branch --</option>
                  @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->code }} - {{ $branch->descriptor }}</option>
                  @endforeach
                </select> 
                
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="search-employee" class="control-label">Date</label>
                <div class="input-group date datepicker" id="datepicker">
                  <input type="text" class="form-control datepicker" id="date" name="date" placeholder="Date" maxlength="10" value="{{ carbonCheckorNow()->format('Y-m-d') }}" required readonly>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>

              </div>
              </div>
            </div> 

            <div class="col-sm-6">
              <div class="form-group">
                <label for="search-employee" class="control-label">Time</label>
                <div class="input-group date timepicker" id="timepicker">
                  <input type="text" class="form-control timepicker" id="time" name="time" placeholder="Time" maxlength="8"  value="{{ carbonCheckorNow()->format('g:i A') }}" required readonly>
                  <span class="input-group-addon">
                      <span class="glyphicon glyphicon-time"></span>
                  </span>
                </div>
              </div>
            </div> 

            <div class="col-sm-6">
              <div class="form-group">
                <label for="search-employee" class="control-label">Type</label>
                <select class="form-control" name="txncode" id="txncode"> 
                  <option value="1">Time In</option>
                  <option value="2">Break Start</option>
                  <option value="3">Break End</option>
                  <option value="4">Time Out</option>
                </select>
              </div>
            </div> 
        
          </div>
        </div>
      </div>
    </div>
    <div class="row button-container">
      <div class="col-md-6">
        <a href="{{ isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'/timelog' }}" class="btn btn-default">Cancel</a>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>
  
    {!! Form::close() !!}

    

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection

@section('js-external')
  @parent

  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/vendors/bootstrap-datetimepicker.min.js"></script>

  <script>
  

  function employeeSearch(){
   
    $("#search-employee").autocomplete({
      
      source: function( request, response ) {
        $.when(
          $.ajax({
              type: 'GET',
              url: "/api/search/employee",
              dataType: "json",
              data: {
                maxRows: 20,
                q: request.term
              },
              success: function( data ) {
                response( $.map( data, function( item ) {
                  return {
                    label: item.code + ' - ' + item.lastname+ ', ' + item.firstname,
                    value: item.lastname+ ', ' + item.firstname,
                    id: item.id
                  }
                }));
              }
          })
        ).then(function(data){
          console.log(data);
        });
      },
      minLength: 2,
      select: function(e, ui) {     
        $("#employeeid").val(ui.item.id); /* set the selected id */
      },
      open: function() {
        $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
        $("#employeeid").val('');  /* remove the id when change item */
      },
      close: function() {
        $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
      },
      focus: function (e, ui) {
        $(".ui-helper-hidden-accessible").hide();
      },
      change: function( event, ui ) {

      },
      messages: {
        noResults: '',
        results: function() {}
      }
      
    });
  }

  $(document).ready(function(){
    employeeSearch();

    $('.timepicker').datetimepicker({
        format: 'LT',
        showTodayButton: true,
        ignoreReadonly: true,
        calendarWeeks: true
    });

    $('.datepicker').datetimepicker({
        format: 'YYYY-MM-DD',
        ignoreReadonly: true
    });
  });


  </script>



  

@endsection
