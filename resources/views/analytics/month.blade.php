@extends('master')

@section('title', '- By Month Analytics')

@section('body-class', 'analytics-month')

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
    <li><a href="/dashboard"><span class="gly gly-shop"></span> </a></li>
    <li><a href="/status/branch/month">Branch Analytics</a></li>
    <li class="active">{{ $dr->fr->format('M Y') }} - {{ $dr->to->format('M Y') }}</li>
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
            {!! Form::open(['url' => '/status/branch/month', 'method' => 'get']) !!}
            <button type="submit" class="btn btn-success btn-go" title="Go"   }}>
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

          <div class="btn-group" style="margin-left: 5px;">
            <div class="dropdown">
              <button id="dLabel" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-shop"></span>
                @if(is_null(($branch)))
                  <span class="br-code">Select Branch</span>
                  <span class="br-desc hidden-xs hidden-sm"></span>
                @else
                  <span class="br-code">{{ $branch->code }}</span>
                  <span class="br-desc hidden-xs hidden-sm">- {{ $branch->descriptor }}</span>
                @endif
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu br" aria-labelledby="dLabel" style="max-height: 400px; overflow-y: scroll;">
                @foreach($branches as $b)
                <li>
                  <a href="#" data-desc="{{ $b->descriptor }}" data-code="{{ $b->code }}" data-branchid="{{ $b->id }}">{{ $b->code }}</a>
                </li>
                @endforeach
              </ul>
            </div> <!-- .dropdown -->
          </div>
          <?php $months = floor($dr->fr->diffInDays($dr->to, false)/30); ?>
          <!--
          <div class="btn-group" role="group">
            <a href="/status/branch{{ is_null($branch) ? '':'/'.$branch->lid() }}?fr={{$dr->now->copy()->startOfMonth()->format('Y-m-d')}}&to={{$dr->now->format('Y-m-d')}}" class="btn btn-default" title="Back to Main Menu">
              <span class="fa fa-calendar-o"></span>
              <span class="hidden-xs hidden-sm">Daily</span> <span class="badge">{{ $dr->fr->diffInDays($dr->to, false)+1 }}</span>
            </a>
            <button class="btn btn-default active">
              <span class="fa fa-calendar"></span>
              <span class="hidden-xs hidden-sm">Monthly</span> <span class="badge">{{ $months }}</span>
            </button> 
          </div> 
          -->

          <div class="btn-group pull-right clearfix" role="group">
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
          </div><!-- end btn-grp -->
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <ul class="nav nav-tabs" role="tablist" style="margin: -10px 0 10px 0;">
      <li role="presentation">
        <a href="/status/branch?{{is_null($branch)?'':'branchid='.$branch->lid()}}&amp;fr={{$dr->now->copy()->startOfMonth()->format('Y-m-d')}}&amp;to={{$dr->now->format('Y-m-d')}}" role="tab">
          Daily
        </a>
      </li>
      <li role="presentation" class="active">
        <a href="#" role="tab" data-toggle="tab">
          Monthly
        </a>
      </li>
    </ul>


    <div class="row">
      
      @if(is_null($dailysales))

      @else

      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Sales</p>
        <h3 id="h-tot-sales" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Purchased</p>
        <h3 id="h-tot-purch" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Manpower Cost</p>
        <h3 id="h-tot-mancost" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Sales per Employee</p>
        <h3 id="h-tot-tips" style="margin:0">0</h3>
      </div>

    </div>
    <div class="row">

      <div class="col-md-12">
        <div id="graph-container" style="overflow:hidden;">
          <div id="graph"></div>
        </div>
      </div>
    </div>
    <div class="row">

      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-hover table-striped table-sort-data">
            <thead>
              <tr>
                  <th>Month</th>
                  <th class="text-right">Sales</th>
                  <th class="text-right">Purchased</th>
                  <th class="text-right">Customers</th>
                  <th class="text-right">Head Spend</th>
                  <th class="text-right">Emp Count</th>
                  <th class="text-right">Sales per Emp</th>
                  <th class="text-right">
                    <div style="font-weight: normal; font-size: 11px; cursor: help;">
                      <em title="Branch Mancost">{{is_null($branch)?'0.00':$branch->mancost}}</em>
                    </div>
                    Man Cost
                  </th>
                  <th class="text-right">Man Cost %</th>
                  <th class="text-right">Tips</th>
                  <th class="text-right">Tips %</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $tot_sales = 0;
                $tot_purchcost = 0;
                $tot_custcount = 0;
                $tot_empcount = 0;
                $tot_mancost = 0;
                $tot_tips = 0;
                $tot_headspend = 0;
                $tot_sales_emp = 0;
                $tot_mancostpct = 0;
                $tot_tipspct = 0;

                $div_sales = 0;
                $div_purchcost = 0;
                $div_custcount = 0;
                $div_empcount = 0;
                $div_mancost = 0;
                $div_tips = 0;
                $div_headspend = 0;
              ?>
              @foreach($dailysales as $d)
              <?php
                $div_sales+=($d->dailysale['sales']!=0)?1:0;
                $div_purchcost+=($d->dailysale['purchcost']!=0)?1:0; 
                $div_custcount+=($d->dailysale['custcount']!=0)?1:0; 
                $div_empcount+=($d->dailysale['empcount']!=0)?1:0; 
                $div_tips+=($d->dailysale['tips']!=0)?1:0; 
                $div_headspend+=($d->dailysale['headspend']!=0)?1:0;
              ?>


              <tr>
                <td data-sort="{{$d->date->format('Y-m-d')}}">{{ $d->date->format('M Y') }}</td>
                @if(!is_null($d->dailysale))
                <td class="text-right" data-sort="{{ number_format($d->dailysale['sales'], 2,'.','') }}">
                  {{ number_format($d->dailysale['sales'], 2) }}
                </td>
                <td class="text-right" data-sort="{{ number_format($d->dailysale['purchcost'], 2,'.','') }}">
                    {{ number_format($d->dailysale['purchcost'], 2) }}
                  @if($d->dailysale['purchcost']==0) 
                    
                  @else
                  <!--
                  <a href="#" data-date="{{ $d->date->format('Y-m-d') }}" class="text-primary btn-purch">
                    {{ number_format($d->dailysale['purchcost'], 2) }}
                  </a>
                  -->
                  @endif
                </td>
                <td class="text-right" data-sort="{{ number_format($d->dailysale['custcount'], 0) }}">
                  {{ number_format($d->dailysale['custcount'], 0) }}
                </td>
                <!--- head speand -->
                <td class="text-right" data-sort="{{ number_format($d->dailysale['headspend'], 2,'.','') }}">{{ number_format($d->dailysale['headspend'], 2) }}</td>
                <!--- end: head speand -->
                <td class="text-right" data-sort="{{ $d->dailysale['empcount'] }}">
                  {{ number_format($d->dailysale['empcount'], 0) }}
                </td>
                <!--- sales per emp -->
                @if($d->dailysale['empcount']==0)
                  <td class="text-right" data-sort="0.00">
                    -
                  </td>
                @else
                  <td class="text-right" data-sort="{{ number_format($d->dailysale['sales']/$d->dailysale['empcount'], 2,'.','') }}">
                    {{ number_format($d->dailysale['sales']/$d->dailysale['empcount'], 2) }}
                  </td>
                @endif
                <!--- end: sales per emp -->
                <?php
                  $mancost = $d->dailysale['empcount']*$branch->mancost;
                  $div_mancost+=($mancost!=0)?1:0; 
              
                ?>
                <td class="text-right" data-sort="{{ number_format($mancost,2,'.','') }}">
                  {{ number_format($mancost,2) }}
                </td>
                <!--- mancostpct -->
                @if($d->dailysale['sales']==0)
                  <td class="text-right" data-sort="0.00">
                    -
                  </td>
                @else
                  <?php
                    $mancostpct = (($d->dailysale['empcount']*$branch->mancost)/$d->dailysale['sales'])*100;
                  ?>
                  <td class="text-right" data-sort="{{ number_format($mancostpct, 2,'.','') }}"
                    title="(({{$d->dailysale['empcount']}}*{{$branch->mancost}}/{{$d->dailysale['sales']}})*100 ={{$mancostpct}}"
                  >
                    {{ number_format($mancostpct, 2) }}
                  </td>
                @endif
                <!--- end: mancostpct -->
                <td class="text-right" data-sort="{{ number_format($d->dailysale['tips'],2,'.','') }}">
                  {{ number_format($d->dailysale['tips'],2) }}
                </td>
                <!--- sales per emp -->
                @if($d->dailysale['sales']==0)
                  <td class="text-right" data-sort="0.00">
                    -
                  </td>
                @else
                  <td class="text-right" data-sort="{{ number_format(($d->dailysale['tips']/$d->dailysale['sales'])*100, 2,'.','') }}">
                    {{ number_format(($d->dailysale['tips']/$d->dailysale['sales'])*100, 2) }}
                  </td>
                @endif
                <!--- end: sales per emp -->

                <?php
                  $tot_sales      += $d->dailysale['sales'];
                  $tot_purchcost  += $d->dailysale['purchcost'];
                  $tot_custcount  += $d->dailysale['custcount'];
                  $tot_headspend  += $d->dailysale['headspend'];
                  $tot_empcount   += $d->dailysale['empcount'];

                  if($d->dailysale['empcount']!='0') {
                    $tot_sales_emp += number_format(($d->dailysale['sales']/$d->dailysale['empcount']),2, '.', '');
                  }

                  $tot_mancost    += $mancost;
                  $tot_mancostpct += $d->dailysale['mancostpct'];
                  $tot_tips       += $d->dailysale['tips'];
                  $tot_tipspct    += $d->dailysale['tipspct'];
                ?>

                @else <!-- is_null d->dailysale) -->
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td>
                  <strong>
                  {{ $months }}
                  {{ $months > 1 ? 'months':'month' }}
                  </strong>
                <td class="text-right">
                <strong id="f-tot-sales">{{ number_format($tot_sales,2) }}</strong>
                <div>
                <em><small title="{{$tot_sales}}/{{$div_sales}}">
                  {{ $div_sales!=0?number_format($tot_sales/$div_sales,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong id="f-tot-purch">{{ number_format($tot_purchcost,2) }}</strong>
                <div>
                <em><small title="{{$tot_purchcost}}/{{$div_purchcost}}">
                  {{ $div_purchcost!=0?number_format($tot_purchcost/$div_purchcost,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_custcount, 0) }}</strong>
                <div>
                <em><small title="{{$tot_custcount}}/{{$div_custcount}}">
                  {{ $div_custcount!=0?number_format($tot_custcount/$div_custcount,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small title="{{$tot_headspend}}/{{$div_headspend}}">
                  {{ $div_headspend!=0?number_format($tot_headspend/$div_headspend,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_empcount,0) }}</strong>
                <div>
                <em><small title="{{$tot_empcount}}/{{$div_empcount}}">
                  {{ $div_empcount!=0?number_format($tot_empcount/$div_empcount,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small id="f-tot-tips" title="{{$tot_sales}}/{{$tot_empcount}}" >
                  @if($tot_empcount!='0')
                    {{ number_format($tot_sales/$tot_empcount,2) }}
                    <!--
                    {{ number_format($tot_sales-($tot_purchcost+$tot_mancost),2) }}
                    -->
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong id="f-tot-mancost">{{ number_format($tot_mancost,2) }}</strong>
                <div>
                <em><small title="{{$tot_mancost}}/{{$div_mancost}}">
                  @if($div_mancost!='0')
                  {{ number_format($tot_mancost/$div_mancost,2) }}
                   @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small title="(({{$tot_empcount}}*{{$branch->mancost}})/{{$tot_sales}})*100">
                  @if($tot_sales!='0')
                  {{ number_format((($tot_empcount*$branch->mancost)/$tot_sales)*100,2) }}%
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_tips,2) }}</strong>
                <div>
                <em><small title="{{$tot_tips}}/{{$div_tips}}">
                  {{ $div_tips!=0?number_format($tot_tips/$div_tips,2):0 }}</small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp; </strong>
                <div>
                <em><small title="({{$tot_tips}}/{{$tot_sales}})*100 ">
                  @if($tot_sales!='0')
                  {{ number_format(($tot_tips/$tot_sales)*100,2) }}%
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
            </tr>
            </tfoot>  
          </table>

        <table id="datatable" class="tb-data" style="display:none;">
          <thead>
            <tr>
                <th>Date</th>
                <th>Sales</th>
                <th>Purchased</th>
                <th>Emp Count</th>
           
                <th>Tips</th>
                <th>Man Cost</th>
                <th>Sales per Emp</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dailysales as $d)
            <tr>
              <td>{{ $d->date->format('Y-m-d') }}</td>
              @if(!is_null($d->dailysale))
              <td>{{ $d->dailysale['sales'] }}</td>
              <td>{{ $d->dailysale['purchcost'] }}</td>
              <td>{{ $d->dailysale['empcount'] }}</td>
              
              <td>{{ $d->dailysale['mancost'] }}</td>
              <td>{{ $d->dailysale['tips'] }}</td>
              <td>{{ $d->dailysale['empcount']=='0' ? 0:number_format(($d->dailysale['sales']/$d->dailysale['empcount']), 2, '.', '') }}</td>
              @else 
           
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              @endif
            </tr>
            @endforeach
          </tbody>
        </table>
      </div><!--  end: table-responsive -->
      </div>
          @endif
    </div>
  </div>



</div><!-- end .container-fluid -->





@endsection




@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  
<script>
  var fetchPurchased = function(a){
    var formData = a;
    //console.log(formData);
    return $.ajax({
          type: 'GET',
          contentType: 'application/x-www-form-urlencoded',
          url: '/api/t/purchase',
          data: formData,
          //async: false,
          success: function(d, textStatus, jqXHR){

          },
          error: function(jqXHR, textStatus, errorThrown){
            alert('Error on fetching data...');
          }
      }); 
  }


  $('document').ready(function(){

    var getOptions = function(to, table) {
      var options = {
        data: {
          table: table,
          startColumn: 1,
          endColumn: 2,
        },
        chart: {
          renderTo: to,
          type: 'pie',
          height: 300,
          width: 300,
          events: {
            load: function (e) {
              //console.log(e.target.series[0].data);
            }
          }
        },
        title: {
            text: ''
        },
        style: {
          fontFamily: "Helvetica"
        },
        tooltip: {
          pointFormat: '{point.y:.2f}  <b>({point.percentage:.2f}%)</b>'
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true,
            point: {
              events: {
                mouseOver: function(e) {    
                  var orig = this.name;
                  var tb = $(this.series.chart.container).parent().data('table');
                  var tr = $(tb).children('tbody').children('tr');
                   _.each(tr, function(tr, key, list){
                    var text = $(tr).children('td:nth-child(2)').text();             
                    if(text==orig){
                      $(tr).children('td').addClass('bg-success');
                    }
                  });
                },
                mouseOut: function() {
                  var orig = this.name;
                  var tb = $(this.series.chart.container).parent().data('table');
                  var tr = $(tb).children('tbody').children('tr');
                   _.each(tr, function(tr, key, list){
                      $(tr).children('td').removeClass('bg-success');
                  });
                },
                click: function(event) {
                  //console.log(this);
                }
              }
            }
          }
        },
        
        legend: {
          enabled: false,
          //layout: 'vertical',
          //align: 'right',
          //width: 400,
          //verticalAlign: 'top',
          borderWidth: 0,
          useHTML: true,
          labelFormatter: function() {
            //total += this.y;
            return '<div style="width:400px"><span style="float: left; width: 250px;">' + this.name + '</span><span style="float: left; width: 100px; text-align: right;">' + this.percentage.toFixed(2) + '%</span></div>';
          },
          title: {
            text: null,
          },
            itemStyle: {
            fontWeight: 'normal',
            fontSize: '12px',
            lineHeight: '12px'
          }
        },
        
        exporting: {
          enabled: false
        }
      }
      return options;
    }

    Highcharts.setOptions({
      lang: {
        thousandsSep: ','
    }});

    


    $('.btn-purch-').on('click', function(e){
      e.preventDefault();
      var data = {};
      data.date = $(this).data('date');
      data.branchid = "{{session('user.branchid')}}";

      fetchPurchased(data).success(function(d, textStatus, jqXHR){
        console.log(d);
        if(d.code===200){
          $('.modal-title small').text(moment(d.data.items.date).format('ddd MMM D, YYYY'));
          renderToTable(d.data.items.data);  
          renderTable(d.data.stats.categories, '.tb-category-data');  
          var categoryChart = new Highcharts.Chart(getOptions('graph-pie-category', 'category-data'));
          renderTable(d.data.stats.expenses, '.tb-expense-data');  
          var expenseChart = new Highcharts.Chart(getOptions('graph-pie-expense', 'expense-data'));
          renderTable(d.data.stats.suppliers, '.tb-supplier-data');  
          var supplierChart = new Highcharts.Chart(getOptions('graph-pie-supplier', 'supplier-data'));
          $('#link-download')[0].href="/api/t/purchase?date="+moment(d.data.items.date).format('YYYY-MM-DD')+"&download=1";
          //$('#link-print')[0].href="/api/t/purchase?date="+moment(d.date).format('YYYY-MM-DD');
          $('ul[role=tablist] a:first').tab('show');
          $('#mdl-purchased').modal('show');
        } else if(d.code===401) {
          document.location.href = '/analytics';
        } else {
          alert('Error on fetching data. Kindly refresh your browser');
        }
      });

    });


    var renderToTable = function(data) {
      var tr = '';
      var ctr = 1;
      var totcost = 0;
      _.each(data, function(purchase, key, list){
          //console.log(purchase);
          tr += '<tr>';
          tr += '<td class="text-right">'+ ctr +'</td>';
          tr += '<td>'+ purchase.comp +'</td>';
          tr += '<td>'+ purchase.catname +'</td>';
          tr += '<td>'+ purchase.unit +'</td>';
          tr += '<td class="text-right">'+ purchase.qty +'</td>';
          tr += '<td class="text-right">'+ accounting.formatMoney(purchase.ucost, "", 2, ",", ".") +'</td>';
          tr += '<td class="text-right">'+ accounting.formatMoney(purchase.tcost, "", 2, ",", ".") +'</td>';
          tr += '<td class="text-right" data-toggle="tooltip" data-placement="top" title="'+ purchase.supname +'">'+ purchase.supno +'</td>';
          tr += '<td class="text-right">'+ purchase.terms +'</td>';
          tr += '<td class="text-right">'+ purchase.vat +'</td>';
          tr +='</tr>';
          ctr++;
          totcost += parseFloat(purchase.tcost);
      });
      $('#tot-purch-cost').html(accounting.formatMoney(totcost, "", 2, ",", "."));
      $('.tb-purchase-data .tb-data').html(tr);
      $('.table-sort').trigger('update')
                      .trigger('sorton', [[0,0]]);
      
    }




    var renderTable = function(data, table) {
      var tr = '';
      var ctr = 1;
      var totcost = 0;
      tr += '<tbody>';
      _.each(data, function(value, key, list){
          //console.log(key);
          tr += '<tr>';
          tr += '<td class="text-right">'+ ctr +'</td>';
          tr += '<td>'+ key +'</td>';
          tr += '<td style="display:none;">'+value +'</td>';
          tr += '<td class="text-right">'+ accounting.formatMoney(value, "", 2, ",", ".") +'</td>';
          tr +='</tr>';
          ctr++;
          totcost += parseFloat(value);
      });
      tr += '</tbody>';
      //tr += '<tfoot><tr><td></td><td class="text-right"><strong>Total</strong></td>';
      //tr += '<td class="text-right"><strong>'+accounting.formatMoney(totcost, "", 2, ",", ".")+'</strong></td></tr><tfoot>';

      
      $(table+' tfoot').remove();
      $(table+' tbody').remove();
      $(table+' thead').after(tr);
      $(table).tablesorter(); 
      $(table).trigger('update');


      
    }





  	$('#dp-date-fr').datetimepicker({
        //defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        calendarWeeks: true,
        viewMode: 'months'
      }).on('dp.change', function(e){
        $('#dp-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(e.date.format('YYYY-MM-DD'));
        /*
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
        */
      });


      $('#dp-date-to').datetimepicker({
       // defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        calendarWeeks: true,
        viewMode: 'months'
      }).on('dp.change', function(e){
        $('#dp-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(e.date.format('YYYY-MM-DD'));
        /*
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
        */
      });

      $('.br.dropdown-menu li a').on('click', function(e){
        e.preventDefault();
        var el = $(e.currentTarget);
        el.parent().siblings().children().css('background-color', '#fff');
        el.css('background-color', '#d4d4d4');
        $('.br-code').text(el.data('code'));
        $('.br-desc').text('- '+el.data('desc'));
        $('#branchid').val(el.data('branchid'));
        //$('#dLabel').stop( true, true ).effect("highlight", {}, 1000);
        if(el.data('branchid')==$('.btn-go').data('branchid'))
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
        
        //console.log($('.btn-go').data('branchid'));
      });

      Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: "Helvetica"
            }
        }
    });

    var arr = [];

    $('#graph').highcharts({
      data: {
          table: 'datatable'
      },
      chart: {
        type: 'line',
        height: 300,
        //spacingRight: 20,
        marginTop: 40,
        //marginRight: 20,
        //marginRight: 20,
        zoomType: 'x',
        panning: true,
        panKey: 'shift'
      },
      colors: ['#15C0C2','#D36A71', '#B09ADB', '#5CB1EF', '#F49041', '#f15c80', '#F9CDAD', '#91e8e1', '#8d4653'],
      title: {
          text: ''
      },
      xAxis: [
        {
          gridLineColor: "#CCCCCC",
          type: 'datetime',
          //tickInterval: 24 * 3600 * 1000, // one week
          tickWidth: 0,
          gridLineWidth: 0,
          lineColor: "#C0D0E0", // line on X axis
          labels: {
            align: 'center',
            x: 3,
            y: 15,
            formatter: function () {
              //var date = new Date(this.value);
              //console.log(date.getDay());
              //console.log(date);
              return Highcharts.dateFormat('%b %Y', this.value);
            }
          },
          plotLines: arr
        },
        { // slave axis
          type: 'datetime',
          linkedTo: 0,
          opposite: true,
          tickInterval: 7 * 24 * 3600 * 1000,
          tickWidth: 0,
          labels: {
            formatter: function () {
              /*
              arr.push({ // mark the weekend
                color: "#CCCCCC",
                width: 1,
                value: this.value-86400000,
                zIndex: 3
              });
*/
              //return Highcharts.dateFormat('%a', (this.value-86400000));
            }
          }
        }
      ],
      yAxis: [{ // left y axis
        min: 0,
        title: {
          text: null
        },
        labels: {
          align: 'left',
          x: 3,
          y: 16,
          format: '{value:.,0f}'
        },
          showFirstLabel: false
        },
        { // right y axis
        min: 0,
          title: {
            text: null
          },
          labels: {
            align: 'right',
            x: -10,
            y: 15,
            format: '{value:.,0f}'
          },
            showFirstLabel: false,
            opposite: true
          }], 
      legend: {
        align: 'left',
        verticalAlign: 'top',
        y: -10,
        floating: true,
        borderWidth: 0
      },
      tooltip: {
        shared: true,
        crosshairs: true
      },
      plotOptions: {
        series: {
          cursor: 'pointer',
          point: {
            events: {
              click: function (e) {
              console.log(Highcharts.dateFormat('%Y-%m-%d', this.x));
              /*
                hs.htmlExpand(null, {
                    pageOrigin: {
                        x: e.pageX,
                        y: e.pageY
                    },
                    headingText: this.series.name,
                    maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) +':<br/> '+
                        this.y +' visits',
                    width: 200
                });
              */
              }
            }
          },
          marker: {
            symbol: 'circle',
            radius: 3
          },
          lineWidth: 2,
          dataLabels: {
              enabled: false,
              align: 'right',
              crop: false,
              formatter: function () {
                console.log(this.series.index);
                return this.series.name;
              },
              x: 1,
              verticalAlign: 'middle'
          }
        }
      },
      exporting: {
        enabled: false
      },
      series: [
        {
          type: 'line',
          yAxis: 0
        }, {
          type: 'line',
          yAxis: 0
        }, {
          type: 'line',
          dashStyle: 'shortdot',
          yAxis: 1
        }, {
          type: 'line',
          //dashStyle: 'shortdot',
          yAxis: 0
        }, {
          type: 'line',
          yAxis: 0
        }, {
          type: 'line',
          yAxis: 0
        }
      ]
    });



    $('#h-tot-sales').text($('#f-tot-sales').text());
    $('#h-tot-purch').text($('#f-tot-purch').text());
    $('#h-tot-mancost').text($('#f-tot-mancost').text());
    $('#h-tot-tips').text($('#f-tot-tips').text());

   
  });
</script>
@endsection