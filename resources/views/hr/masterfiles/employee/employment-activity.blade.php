@extends('master')

@section('title', '- Employment Activity')

@section('body-class', 'employment-activity')

@section('css-external')
<style type="text/css">
ul.timeline {
  list-style-type: none;
  position: relative;
}
ul.timeline:before {
  content: ' ';
  background: #d4d9df;
  display: inline-block;
  position: absolute;
  left: 29px;
  width: 2px;
  height: 100%;
  z-index: 400;
}
ul.timeline > li {
  margin: 20px 0;
  padding-left: 20px;
}
ul.timeline > li:before {
  content: ' ';
  background: white;
  display: inline-block;
  position: absolute;
  border-radius: 50%;
  /* border: 3px solid #3C7643; */
  border: 3px solid #ccc;
  left: 20px;
  width: 20px;
  height: 20px;
  z-index: 400;
}
ul.timeline > li.active:before {
  content: ' ';
  background: white;
  display: inline-block;
  position: absolute;
  border-radius: 50%;
  border: 3px solid #3C7643;
  left: 20px;
  width: 20px;
  height: 20px;
  z-index: 400;
}
.timeline p, .timeline a {
  color: #BDBDBD;
  /* color: #706d6d; */
}
.timeline p {
  font-size: .9em;
  margin: 3px 0 0 0;
}
.timeline li.active p {
  color: #706d6d; 
}
.timeline li.active a {
  color: #3C7643; 
}
table {
  font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
  font-size: 14px;
  line-height: 1.42857143;
  color: #333;
}
</style>
@show

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
    <li><a href="/"><span class="gly gly-shop"></span> </a></li>
    <li><a href="/employee">Employee</a></li>
    <li class="{{ $empActivity->stage>2?'active':'' }}">Employment Activity</li>
  </ol>

  @include('_partials.alerts')

  <div>
    <!-- <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span> 
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div>
        </div>
      </div>
    </nav> -->

    <div class="row">
      <div class="col-md-6">

        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-success">
              <div class="panel-heading">Employee Information</div>
              <div class="panel-body">
                <table>
                  <tbody>
                    <tr>
                      <td>
                        <img src="http://cashier.giligansrestaurant.com/images/{{ $employee->photo ? $employee->code.'.jpg':'login-avatar.png' }}" style="margin-right: 5px; width: 100px;" class="img-responsive">
                      </td>
                      <td>
                        <h3 class="text-success" style="margin-top: 0px;">
                          {{ $employee->lastname }}, {{ $employee->firstname }}
                          <small data-id="{{ $employee->id }}">{{ $employee->code }}</small>
                        </h3>
                        <div>
                          {{ $employee->position->descriptor }}
                        </div>
                        <div>
                          {{ $employee->branch->code }} - {{ $employee->branch->descriptor }}
                        </div>
                        <div class="hidden-sm hidden-md hidden-lg">
                          {{ $employee->company->code }}
                        </div>
                        <div class="hidden-xs">
                          {{ $employee->company->descriptor }}
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!----------------- Forms --------------------->

          <div class="col-md-12">
            @if($empActivity->status!=3 && $empActivity->stage==2)
            <div class="panel panel-primary">
              <div class="panel-heading">Action</div>
              <div class="panel-body">

                <div id="exreqconfirm">
                  <form id="frm-exreqconfirm" action="/hr/masterfiles/employee/employment-activity" method="POST">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="_method" value="PUT">
                  <p>Confirm the export request to <em>{{ $empActivity->type }}</em> from <strong>{{ $empActivity->branch->code }}</strong> to <strong>{{ $empActivity->branchto->code }}</strong>?</p>
                  <div>
                    <button type="button" class="btn btn-success" id="btn-confirm" data-toggle="modal" data-target=".mdl-sure">Confirm</button>
                    <button type="button" class="btn btn-default" id="btn-cancel" data-toggle="modal" data-target=".mdl-cancel">Decline</button>
                    <input type="hidden" name="id" value="{{  $empActivity->id }}">
                    <input type="hidden" name="stage" value="1">
                    <input type="hidden" id="status" name="status" value="0">
                  </div>
                  </form>
                </div>


              </div>
            </div>
            @endif
          </div>
          <!----------------- End Forms --------------------->
        </div>
      </div>
      <div class="col-md-6">
        <div class="panel panel-success">
          <div class="panel-heading">Process Details</div>
          <div class="panel-body">
            <ul class="timeline">
              <li class="{{ $empActivity->stage>0?'active':'' }}">
                <a href="#">Export Request Upload - {{ $empActivity->branch->code }} Cashier</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage1) && $empActivity->stage>0)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage1 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage1->format('Y-m-d') ? $empActivity->stage1->format('g:i A') : $empActivity->stage1->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>Export request and ETRF was uploaded on cashier's module.</p>
                <p>Email notification has been sent to RM for confirmation.</p>
              </li>
              <li class="{{ $empActivity->stage>1?'active':'' }}">
                <a href="#">Export Request  {{ $empActivity->stage==2 && $empActivity->status==3 ? 'Rejected':'Confirmation' }}  - {{ $empActivity->branch->code }} RM</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage2) && $empActivity->stage>1)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage2 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage2->format('Y-m-d') ? $empActivity->stage2->format('g:i A') : $empActivity->stage2->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                @if(!is_null($empActivity->stage2) && $empActivity->stage==2 && $empActivity->status==3)
                <p>RM rejected the export request.</p>
                <p>Email notification has been sent to branch.</p>
                @else
                <p>RM confirm the export request.</p>
                <p>Email notification has been sent to branch and HR for approval.</p>
                @endif
              </li>
              <li class="{{ $empActivity->stage>2?'active':'' }}">
                <a href="#">Export Request {{ $empActivity->stage==3 && $empActivity->status==3 ? 'Disapproved':'Approval' }}  - HR</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage3) && $empActivity->stage>2)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage3 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage3->format('Y-m-d') ? $empActivity->stage3->format('g:i A') : $empActivity->stage3->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                @if(!is_null($empActivity->stage2) && $empActivity->stage==3 && $empActivity->status==3)
                <p>HR disapproved the export request.</p>
                <p>Email notification has been sent to branch and RM.</p>
                @else
                <p>HR approved the export request.</p>
                <p>Notify the RM that the export request has been approved.</p>
                <p>Email {{ $empActivity->branch->code }} cashier with the .PAS key.</p>
                @endif
              <li class="{{ $empActivity->stage>3?'active':'' }}">
                <a href="#">Passkey Download - {{ $empActivity->branch->code }} Cashier</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage4) && $empActivity->stage>3)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage4 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage4->format('Y-m-d') ? $empActivity->stage4->format('g:i A') : $empActivity->stage4->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>The cashier will download the passkey for export.</p>
              </li>
              <li class="{{ $empActivity->stage>4?'active':'' }}">
                <a href="#">EMP File Upload - {{ $empActivity->branch->code }} Cashier</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage5) && $empActivity->stage>4)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage5 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage5->format('Y-m-d') ? $empActivity->stage5->format('g:i A') : $empActivity->stage5->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>Export request and ETRF was uploaded on cashier's module.</p>
                <p>Email notification has been sent to RM for confirmation.</p>
              </li>
              <li class="{{ $empActivity->stage>5?'active':'' }}">
                <a href="#">Transfer Approval - HR</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage6) && $empActivity->stage>5)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage6 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage6->format('Y-m-d') ? $empActivity->stage6->format('g:i A') : $empActivity->stage6->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>HR approved the transfer.</p>
                <p>Notify the {{ $empActivity->branch->code }} RM that the transfer has been approved.</p>
                <p>Notify the {{ $empActivity->branchto->code }} RM that there's a new employee for import.</p>
                <p>Email the {{ $empActivity->branchto->code }} cashier with the .EMP file.</p>
              </li>
              <li class="{{ $empActivity->stage>6?'active':'' }}">
                <a href="#">EMP File Download - {{ $empActivity->branchto->code }} Cashier</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage7) && $empActivity->stage>6)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage7 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage7->format('Y-m-d') ? $empActivity->stage7->format('g:i A') : $empActivity->stage7->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>The cashier will download the EMP File for import and generate an import request.</p>
              </li>
              <li class="{{ $empActivity->stage>7?'active':'' }}">
                <a href="#">Import Request Upload - {{ $empActivity->branchto->code }} Cashier</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage8) && $empActivity->stage>7)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage8 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage8->format('Y-m-d') ? $empActivity->stage8->format('g:i A') : $empActivity->stage8->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>Import request was uploaded on cashier's module.</p>
                <p>Email notification has been sent to RM for confirmation.</p>
              </li>
              <li class="{{ $empActivity->stage>8?'active':'' }}">
                <a href="#">Import Request Confirmation - {{ $empActivity->branchto->code }} RM</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage9) && $empActivity->stage>8)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage9 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage9->format('Y-m-d') ? $empActivity->stage9->format('g:i A') : $empActivity->stage9->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>RM confirm/reject the import.</p>
                <p>Email confirmation has been sent to HR.</p>
              </li>
              <li class="{{ $empActivity->stage>9?'active':'' }}">
                <a href="#">Import Request Approval - HR</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage10) && $empActivity->stage>9)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage10 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage10->format('Y-m-d') ? $empActivity->stage10->format('g:i A') : $empActivity->stage10->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>HR approved the import request.</p>
                <p>Notify the RM that the import request has been approved.</p>
                <p>Email the {{ $empActivity->branchto->code }} cashier with the .PAS key.</p>
              </li>
              <li class="{{ $empActivity->stage>10?'active':'' }}">
                <a href="#">Passkey Download - {{ $empActivity->branch->code }} Cashier</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage11) && $empActivity->stage>10)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage11 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage11->format('Y-m-d') ? $empActivity->stage11->format('g:i A') : $empActivity->stage11->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>The cashier will download the passkey for import.</p>
              </li>
              <li class="{{ $empActivity->stage>11?'active':'' }}">
                <a href="#">Confirm Import - {{ $empActivity->branch->code }} Cashier</a>
                <span class="pull-right">
                  <small>
                  @if(!is_null($empActivity->stage12) && $empActivity->stage>11)
                  <em data-toggle="tooltip" style="cursor: help;" title="{{ $empActivity->stage12 }}">
                    {{ c()->format('Y-m-d')==$empActivity->stage12->format('Y-m-d') ? $empActivity->stage12->format('g:i A') : $empActivity->stage12->format('D, M j') }}
                  </em>
                  @endif
                  </small>
                </span>
                <p>The cashier will confirm import.</p>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>


@if($empActivity->status!=3 && $empActivity->stage==2)
<div class="modal fade mdl-sure" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Confirm</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to confirm?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="mdl-yes">&nbsp;&nbsp;&nbsp;Yes&nbsp;&nbsp;&nbsp;</button>
        &nbsp;
        &nbsp;
        <button type="button" class="btn btn-default" data-dismiss="modal">&nbsp;No&nbsp;</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<div class="modal fade mdl-cancel" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form id="mdl-exreqconfirm" action="/hr/masterfiles/employee/employment-activity" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="_method" value="PUT">
    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Declining the export request to <em>{{ $empActivity->type }}.</h4>
      </div>
      <div class="modal-body">
        <p>Reason:</p>
        <textarea name="notes" style="max-width: 100%; min-width: 100%;" rows="5" maxlength="250" required></textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="mdl-submit">Submit</button>
        &nbsp;
        &nbsp;
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
      
      <input type="hidden" name="id" value="{{  $empActivity->id }}">
      <input type="hidden" name="stage" value="1">
      <input type="hidden" id="status" name="status" value="3">
    </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
@endif

@endsection

@section('js-external')
  @parent

  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/dr-picker.js"> </script>

  <script type="text/javascript">
   $(function () {
    $('[data-toggle="tooltip"]').tooltip();

    @if($empActivity->status!=3 && $empActivity->stage==2)
    
    $('#mdl-yes').on('click', function(e){
      e.preventDefault();
      $('#frm-exreqconfirm #status').val(1);
      $('#frm-exreqconfirm').submit();
      $('.mdl-sure').modal('hide');
      loader();
    });

    $('#mdl-submit').on('click', function(e){
      // e.preventDefault();
      // console.log('mdl-submit');
      var v = $('.mdl-cancel #notes').val();
      // console.log('value: '+v);
      if(v.length>0) {
        $('.mdl-cancel').modal('hide');
        loader();
        // console.log('submit');
      }
    });
    @endif


  });
 </script>
@endsection