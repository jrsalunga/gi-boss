@extends('master')

@section('title', '- Invoice: '. request()->input('supprefno') )

@section('body-class', 'invoice')

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
    <li class="active">Invoice: {{ request()->input('supprefno') }}</li>
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


    @if(count($invoice)>0)
    <div class="row">
      <div class="col-md-6">
        <div class="panel panel-success">
          <div class="panel-heading">
            <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-uploads"></span> 
              Invoice
            </h3>
          </div>
          <div class="panel-body">

          <div>
            <div class="dropdown pull-right">
              <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="glyphicon glyphicon-cog"></span>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                <li>
                  <a href="#" data-toggle="modal" data-target=".mdl-update-posting"><i class="gly gly-history"></i> Change Posting Date</a>
                </li>
                @if($invoice['terms']=='K')
                <li>
                  <a href="#;" data-toggle="modal" data-target=".mdl-payment-status"><span class="peso">₱</span> Change Payment Status</a>
                </li>
                @endif
              </ul>
            </div>
          </div>

          <h4><span class="gly gly-shop"></span> 
            @if(!empty($invoice['branch']->code))
             <small>{{ $invoice['branch']->code }} -</small> 
            @endif
            <small>{{ $invoice['branch']->descriptor }}</small>
          </h4>
          <h4><b style="font-color:#000; font-size: large;">#</b> {{ $invoice['no'] }}</h4>
          <h4><span class="gly gly-cart-in"></span> 
            @if(!empty($invoice['supplier']->code))
             <small>{{ $invoice['supplier']->code }} -</small> 
            @endif
            <small>{{ $invoice['supplier']->descriptor }}</small>
          </h4>
          <h5><span class="glyphicon glyphicon-calendar"></span> {{ $invoice['date']->format(' m/d/Y - D') }}</h5>
          @if (!is_null($invoice['posted_at']))
          <h5><span class="gly gly-history"></span> <span data-toggle="tooltip" title="Original Posting Date" class="help">{{ $invoice['posted_at']->format(' m/d/Y - D') }}</span></h5>
          @endif
          <h4><span class="peso">₱</span> <small>{{ $invoice['total_amount'] }} ( {{ $invoice['terms'] }} - {{ Config::get('giligans.paytype.'.$invoice['paytype']) }} )</small></h4>
          
          <p></p>

          <div class="table-responsive">
            <table class="table table-condensed">
              <thead>
                <tr>
                  <th>Component</th>
                  <th class="text-right">Unit Cost</th>
                  <th class="text-right">Qty</th>
                  <th></th>
                  <th class="text-right">Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php $total = 0; ?>
                @foreach($invoice['items'] as $item)
                  <tr>
                    <td>{{ $item->component->descriptor }}</td>
                    <td class="text-right">{{ nf($item->ucost,2) }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td><small class="text-muted">{{ strtolower($item->component->uom) }}</small></td>
                    <td class="text-right">{{ nf($item->tcost,2) }}</td>
                  </tr>
                <?php $total += $item->tcost; ?>
                @endforeach
              </tbody>
              <tfoot>
                <tr><td></td><td></td><td></td><td></td><td class="text-right"><strong class="help" title="{{ $invoice['total_amount'] }}" data-toggle="tooltip">{{ nf($total,2) }}</strong></td></tr>
              </tfoot>
            </table>
          </div>

          </div>
        </div>
      </div>      
      <div class="col-md-6">
      @if(count($apus)>0)
        @if(count($apus)>1)
        <h4>List of possible invoice/delivery receipt.</h4>
          <div class="table-responsive">
            <table class="table table-condensed">
              <thead>
                <tr>
                  <th>Supplier</th>
                  <th></th>
                  <th class="text-right">Supp Ref No</th>
                  <th class="text-right">Date</th>
                  <th class="text-right">Amount</th>
                </tr>
              </thead>
              <tbody>
              @foreach($apus as $apu)
              <tr>
                <td>{{ $apu->supplier->descriptor }}</td>
                <td class="text-right">
                  @if($apu->file_exists())
                  <small title="View Document: uploaded {{ $apu->created_at->format('m/d/Y') }}" data-toggle="tooltip">
                    <strong>
                    <a href="javascript:void(0)" target="popup" onclick='window.open("/images/apu/{{ $apu->lid() }}.jpg", "_blank", "width=auto,height=auto"); return false'>
                    view doc
                    </a>
                    </strong>
                  </small>
                  @endif
                </td>
                <td class="text-right">{{ $apu->refno }}</td>
                <td class="text-right">{{ $apu->date->format('m/d/Y') }}</td>
                <td class="text-right">{{ $apu->amount }}</td>
              </tr>
              @endforeach      
              </tbody>
            </table>
          </div>
        @else
          @foreach($apus as $apu)
            @if($apu->file_exists())
            <?php
              $src = '/images/apu/'.$apu->lid().'.'.strtolower(pathinfo($apu->filename, PATHINFO_EXTENSION));
            ?>
            @if(strtolower(pathinfo($apu->filename, PATHINFO_EXTENSION))==='pdf')
                <iframe style="width: 100%; height: 500px;" src="{{$src}}"></iframe>
            @else
              <a href="{{$src}}" target="_blank">
                <img class="img-responsive" src="{{$src}}">
              </a>
            @endif
            <a href="javascript:void(0)" target="popup" onclick='window.open("/images/apu/{{ $apu->lid() }}.jpg", "_blank", "width=auto,height=auto"); return false'></span> <small>view</small></a>
            @else
              File not found.
            @endif
          @endforeach   
        @endif
      @endif
      </div>  
    </div>
    @endif
     
  </div>
</div><!-- end container-fluid -->
 

@if(count($invoice)>0)
<div class="modal fade mdl-update-posting" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      {!! Form::open(['url' => '/invoice', 'method' => 'put', 'id'=>'filter-form']) !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Change Posting Date</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="fr">{{ $invoice['save']==1?'Current':'Original' }} Posting Date</label>
          <div>
            {{ $invoice['date']->format('Y-m-d') }}
          </div>
        </div>
        <div class="form-group">
          @if($invoice['save']==1 && !is_null($invoice['posted_at']))
          <label for="to">Original Posting Date To</label>
          <div>
            {{ $invoice['posted_at']->format('Y-m-d') }}
          </div>
          <input type="hidden" name="to" value="{{ $invoice['posted_at']->format('Y-m-d') }}">
          @else
          <label for="to">Change Posting Date To</label>
          <div class="input-group">
            <input type="text" class="form-control datepicker" value="{{ $invoice['date']->format('Y-m-d') }}" id="to" name="to" required="" placeholder="YYYY-MM-DD" maxlength="8">
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
          </div>
          @endif
        </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="branchid" value="{{ $invoice['branch']->id }}">
        <input type="hidden" name="fr" value="{{ $invoice['date']->format('Y-m-d') }}">
        <input type="hidden" name="supprefno" value="{{ $invoice['no'] }}">
        <input type="hidden" name="save" value="{{ $invoice['save'] }}">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary pull-right" data-toggle="loader" style="margin-right: 10px;">Save Changes</button>
      </div>
      </form>
    </div>
  </div>
</div>
@endif


@if(count($invoice)>0)
<div class="modal fade mdl-payment-status" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      {!! Form::open(['url' => '/invoice-payment', 'method' => 'put', 'id'=>'payment-status']) !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Change Payment</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="fr">Terms / Status</label>
          <div>
            {{ $invoice['terms'] }} - {{ Config::get('giligans.paytype.'.$invoice['paytype']) }}
            
          </div>
        </div>
        <div class="form-group">
          
          <label for="to">Change Payment Status To</label>
            <select class="form-control" id="paytype" name="paytype" required="" style="width: 50%;">
            @foreach(Config::get('giligans.paytype') as $k => $p)
              @if($invoice['paytype']==$k)
                <option value="{{ $k }}" selected="">{{ $p }}</option>
              @else
                <option value="{{ $k }}" >{{ $p }}</option>
              @endif
            @endforeach
            </select>
         
        </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="branchid" value="{{ $invoice['branch']->id }}">
        <input type="hidden" name="date" value="{{ $invoice['date']->format('Y-m-d') }}">
        <input type="hidden" name="supprefno" value="{{ $invoice['no'] }}">
        <input type="hidden" name="save" value="{{ $invoice['save'] }}">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary pull-right" data-toggle="loader" style="margin-right: 10px;">Save Changes</button>
      </div>
      </form>
    </div>
  </div>
</div>
@endif

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
