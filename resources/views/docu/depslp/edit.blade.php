@extends('master')

@section('title', '- Edit Deposit Slip')

@section('body-class', 'depslp-edit')

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
    <li><a href="/depslp/log">Deposit Slip</a></li>
    <li><a href="/depslp/{{$depslp->lid()}}">{{ $depslp->fileUpload->filename }}</a></li>
    <li class="active">Edit</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/depslp/log" class="btn btn-default" title="Back to Deposit Slip Log">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-hdd"></span> 
                <span class="hidden-xs hidden-sm">Filing System</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="/storage"><span class="fa fa-file-archive-o"></span> Backup</a></li>
                <li><a href="/depslp"><span class="fa fa-bank"></span> Deposit Slip</a></li>
              </ul>
            </div>
            
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="fa fa-calendar-check-o"></span>
                <span class="hidden-xs hidden-sm">Checklist</span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="/backups/checklist"><span class="fa fa-file-archive-o"></span> Backup</a></li>
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
                <li><a href="/backups/log"><span class="fa fa-file-archive-o"></span> Backup</a></li>
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

    <div class="row">
      <div class="col-md-6">
        {!! Form::open(['method'=>'PUT', 'url'=>'put/depslp', 'id'=>'form-file', 'class'=>'form-horizontal', 'enctype'=>'multipart/form-data']) !!}
        <div class="panel panel-success">
          <div class="panel-heading">
            <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-uploads"></span> 
              Deposit Slip Information
              @if($depslp->verified || $depslp->matched)
                <span class="fa fa-lock"></span>
              @endif

            </h3>
          </div>
          <div class="panel-body">
            <h4>
              <span class="gly gly-file"></span> 
              {{ $depslp->fileUpload->filename }} 
              @if($depslp->verified and $depslp->matched)
                <span class="glyphicon glyphicon-ok-sign text-success" data-toggle="tooltip" title="Matched and verified by {{ $depslp->user->name }}"></span>
              @elseif($depslp->verified and !$depslp->matched)
                <span class="gly gly-ok" data-toggle="tooltip" title="Verified by {{ $depslp->user->name }}"></span>
              @else

              @endif
              <small><small>(uploaded filename)</small></small>
            </h4>
            <h4><span class="gly gly-cloud"></span> <small>{{ $depslp->filename }} <small>(filename on server)</small></small></small></h4>

            <div class="row">
              <div class="col-lg-12">
                <div class="input-group">
                  <span class="input-group-addon" id="basic-addon1">
                    <span class="gly gly-money"></span> Amount
                  </span>
                  <input type="text" class="form-control" id="amount" name="amount" required style="text-align: right;" value="{{ number_format($depslp->amount,2) }}">
                </div>
              </div>
            </div>

            <div class="row" style="margin-top: 15px;">
              <div class="col-lg-12">
              <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Deposit Type</span>
                 <select id="type" name="type" class="form-control" style="width: 100%; border-left: 1px solid #ccc;" required>
                  <option value="" disabled selected>-- select deposit type --</option>
                  <option value="1" <?=$depslp->type==1?'selected':''?> >Cash</option>
                  <option value="2" <?=$depslp->type==2?'selected':''?> >Cheque</option>
                </select>
              </div>
              </div>
            </div>

            <div class="row" style="margin-top: 15px;">
              <div class="col-lg-12">
                <div class="input-group date-toggle">
                  <span class="input-group-addon" id="basic-addon1">Deposit Date</span>
                  <input type="text" class="form-control" id="date" name="date" required="" value="{{$depslp->date->format('Y-m-d')}}" maxlength="8">
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </div>
            </div>

            <div class="row" style="margin-top: 15px;">
              <div class="col-lg-12">
                <div class="input-group time-toogle">
                  <span class="input-group-addon" id="basic-addon1">Deposit Time</span>
                  <input type="text" class="form-control" id="time" name="time" required value="{{$depslp->time}}" maxlength="8">
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-time"></span>
                  </span>
                </div>
              </div>
            </div>

            <div class="row" style="margin-top: 20px;">
              <div class="col-lg-12">
                <div class="input-group">
                  <span class="input-group-addon" id="basic-addon1">
                    <span class="glyphicon glyphicon-user"></span> Cashier
                  </span>
                  <input type="text" class="form-control" id="cashier" name="cashier" value="{{$depslp->cashier}}" required placeholder="Anna (cashier's name only, required)" maxlength="20" title="Please put cashiers name only">
                </div><!-- /input-group -->
              </div><!-- /.col-lg-10 -->
            </div>

            <div class="row container-note" style="margin-top: 20px;">
              <div class="col-lg-12">
                <textarea class="form-control" id="notes" name="notes" placeholder="Notes: (optional)" maxlength="300">{{ $depslp->remarks }}</textarea>
              </div><!-- /.col-lg-12 -->
            </div>
            
            <h6><span class="glyphicon glyphicon-cloud-upload"></span> {{ $depslp->created_at->format('D M j h:i:s A') }} <small>{{ diffForHumans($depslp->created_at) }}</small></h6>
          </div>
          <div class="panel-footer">
            @if(($depslp->verified || $depslp->matched) && (!request()->has('edit')))
              <a href="/depslp/log" class="btn btn-link"><span class="gly gly-unshare"></span> Back</a>
            @else
              <input type="hidden" name="id" value="{{$depslp->id}}">
              <button type="submit" id="btn-save" data-loading-text="Saving..." class="btn btn-primary" autocomplete="off">Save</button>
              <a href="/depslp/{{$depslp->lid()}}" class="btn btn-link">Cancel</a>
            @endif
          </div>
        </div><!-- end: .panel -->
        {!! Form::close() !!}
      </div>
      <div class="col-md-6">
        <?php
          $src = '/images/depslp/'.$depslp->lid().'.'.strtolower(pathinfo($depslp->filename, PATHINFO_EXTENSION));
        ?>
        @if(strtolower(pathinfo($depslp->filename, PATHINFO_EXTENSION))==='pdf')
          <iframe style="width: 100%; height: 500px;" src="/images/depslp/{{$depslp->lid()}}.{{strtolower(pathinfo($depslp->filename, PATHINFO_EXTENSION))}}"></iframe>
        @else
        <a href="{{$src}}" target="_blank">
          <img class="img-responsive" src="/images/depslp/{{$depslp->lid()}}.{{strtolower(pathinfo($depslp->filename, PATHINFO_EXTENSION))}}">
        </a>
        @endif
        <a href="{{$src}}" target="_blank" style="text-decoration:none;"><span class="fa fa-clone"></span> <small>view</small></a>
      
      </div>
    </div>

   
     
  </div>

 @if(app()->environment()==='production')
  <div class="row">
    <div class="col-sm-6">
      <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-9897737241100378" data-ad-slot="4574225996" data-ad-format="auto"></ins>
    </div>
   </div>
</div><!-- end container-fluid -->
@endif

@endsection






@section('js-external')
  <script src="/js/vendors-common.min.js"></script>


  <script type="text/javascript">

  $(document).ready(function(){

    if ($('#date')[0]!==undefined) {
      $('#date').datetimepicker({
      //$('.date-toggle').datetimepicker({
        format: 'YYYY-MM-DD',
        ignoreReadonly: true
      });
    }

    if ($('#time')[0]!==undefined) {
      $('.time-toogle').datetimepicker({
        format: 'HH:mm:ss',
        ignoreReadonly: true,
      });
    }

    $('#btn-save').on('click', function () {
      var $btn = $(this).button('loading')
    })
  });
  </script>



@endsection
