@extends('master')

@section('title', '- Invoices List')

@section('body-class', 'invoice-list')

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
<div class="backdrop" style="z-index:1070;"></div>
<div class="loader"  style="z-index:1071;"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span> </a></li>
    <li class="active">Invoices: {{ request()->input('supprefno') }}</li>
  </ol>
  
  @include('_partials.alerts')

  <div>
    <!-- <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/invoice/edit" class="btn btn-default">
              <span class="gly gly-edit"></span> 
              <span class="hidden-xs hidden-sm">Edit</span>
            </a>
          </div>
        </div>
      </div>
    </nav> -->


    @if(count($invoices)>0)
    <div class="row">
      <div class="col-md-6">

        <div class="panel panel-success">
          <div class="panel-heading">
            <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-uploads"></span> 
              Invoice
            </h3>
          </div>
          <div class="panel-body">
            
            <div class="table-responsive">
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th>Branch</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th class="text-right">Supp Ref #</th>
                    <th class="text-right">Terms</th>
                    <th class="text-right">Amount</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($invoices as $invoice)
                <tr>
                  <td>{{ $invoice->branchcode }}</td>
                  <td>{{ $invoice->supplier }}</td>
                  <td>{{ $invoice->date->format('Y-m-d') }}</td>
                  <td class="text-right">{{ $invoice->supprefno }}</td>
                  <td class="text-right">{{ $invoice->terms }}</td>
                  <td class="text-right">{{ $invoice->tcost }}</td>
                </tr>
                @endforeach
                </tbody>
              </table>
            </div>

          </div>
      </div>
    </div>
    @endif
     
  </div>
</div><!-- end container-fluid -->
@endsection







@section('js-external')
  @parent

<script src="/js/vendors-common.min.js"></script>
<script src="/js/hc-all.js"> </script>
<script src="/js/dr-picker.js"> </script>

<script type="text/javascript">


$(document).ready(function(){

  moment.locale('en', { week : {
    dow : 1 // Monday is the first day of the week.
  }});

  $('[data-toggle="tooltip"]').tooltip();

  $('.datepicker').datetimepicker({
    format: 'YYYY-MM-DD',
    ignoreReadonly: true,
    showTodayButton: true,
  }).on("dp.show", function (e) {
    console.log(e);
    $(this).data("DateTimePicker").maxDate(e.date);
  });
});
</script>

@endsection
