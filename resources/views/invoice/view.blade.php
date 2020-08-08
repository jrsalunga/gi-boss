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

    @include('_partials.alerts')


    @if(is_null($invoice))

    @else
    <div class="row">
      <div class="col-md-6">
        <div class="panel panel-success">
          <div class="panel-heading">
            <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-uploads"></span> 
              Invoice
            </h3>
          </div>
          <div class="panel-body">

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
          <h5><span class="gly gly-history"></span> {{ $invoice['date']->format(' m/d/Y - D') }}</h5>
          <h4><span class="peso">₱</span> <small>{{ $invoice['total_amount'] }}</small></h4>
          
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
                  <small title="View Document">
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
 


@endsection






@section('js-external')
  @parent

<script src="/js/vendors-common.min.js"></script>
 <script type="text/javascript">
   $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
 </script>

@endsection
