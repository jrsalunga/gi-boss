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
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
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
            <button type="submit" class="btn btn-success btn-go" title="Go" data-toggle="loader">
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
    <div class="col-md-8 col-sm-12">
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
                    <th>Code</td>
                    <th>Company</td>
                    <th class="text-right">Charged Sales</th>
                    <th class="text-right">Deducted Sales</th>
                    <th class="text-right">Deduction</th>
                    <th class="text-right">%</th>
                    <th class="text-right">Cash Depo</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $tot_actual = $tot_deduct = $tot_diff = $tot_depo = $pct = 0;
                ?>
                @foreach($comps as $key => $comp)
                <?php
                  $tot_actual += $comp['sales_actual'];
                  $tot_deduct += $comp['sales_deduct'];
                  $tot_diff += $comp['sales_diff'];
                  $tot_depo += $comp['depo_cash'];

                  $pct = $comp['sales_actual']>0 ? ($comp['sales_diff']/$comp['sales_actual'])*100 : 0;
                ?>
                <tr>
                  <td>{{ $key }}</td>
                  <td>{{ $comp['company'] }} &nbsp;&nbsp;<span class="badge" style="font-size: x-small;">{{ $comp['branch_cnt'] }}</span></td>
                  <td class="text-right">{{ nf($comp['sales_actual']) }}</td>
                  <td class="text-right"><b>{{ nf($comp['sales_deduct']) }}</b></td>
                  <td class="text-right">{{ nf($comp['sales_diff']) }}</td>
                  <td class="text-right">{{ nf($pct) }}</td>
                  <td class="text-right text-success">{{ nf($comp['depo_cash']) }}</td>
                </tr>
                @endforeach
                </tbody>
                <tfoot>
                <?php
                  $tot_pct = 0;
                  if ($tot_diff>0)
                    $tot_pct = ($tot_diff/$tot_actual)*100;
                ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td class="text-right"><b>{{ nf($tot_actual) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_deduct) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_diff) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_pct) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_depo) }}</b></td>
                  </tr>
                </tfoot>
              </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div role="tabpanel" class="tab-pane" id="stats">
        <!-- Copany Panel -->
        <div class="panel panel-default">
          <div class="panel-heading">Branch Breakdown</div>
          <div class="panel-body">
            <div class="row">
              <div class="table-responsive">
              <table class="table table-condensed table-hover table-striped tablesorter table-sort-branch" style="margin-top: 0;" role="grid"> 
                <thead>
                  <tr>
                    <th></th>
                    <th></th>
                    <th class="text-right">Sales</th>
                    <th class="text-right">GF Sales</th>
                    <th class="text-right">GF Ded.</th>
                    <th class="text-right">FP Sales</th>
                    <th class="text-right">FP Ded.</th>
                    <th class="text-right">Zap Sales</th>
                    <th class="text-right">Zap Ded.</th>
                    <th class="text-right">Card Sales</th>
                    <th class="text-right">Card Ded.</th>
                    <th class="text-right">Charged Sales</th>
                    <th class="text-right">Deducted Sales</th>
                    <th class="text-right">Deduction</th>
                    <th class="text-right">%</th>
                    <th class="text-right">Cash Depo</th>
                  </tr>
                  <tbody>
                    <?php
                      $tot_sales = $tot_gfc = $tot_gfd = $tot_fpc = $tot_fpd = $tot_zc = $tot_zd = $tot_ccc = $tot_ccd = $tot_salesa  = $tot_salesd = $tot_salesl = $tot_depo = $ctr = 0;
                    ?>
                    @foreach($datas as $key => $ds)
                    <?php
                    
                      $tot_sales += $ds['sales'];
                      $tot_gfc += $ds['grab'];
                      $tot_gfd += $ds['grab_deduct'];
                      $tot_fpc += $ds['panda'];
                      $tot_fpd += $ds['panda_deduct'];
                      $tot_zc += $ds['zap'];
                      $tot_zd += $ds['zap_deduct'];
                      $tot_ccc += $ds['ccard'];
                      $tot_ccd += $ds['ccard_deduct'];
                      $tot_salesa  += $ds['sales_actual'];
                      $tot_salesd += $ds['sales_deduct'];
                      $tot_salesl += $ds['sales_diff'];
                      $tot_depo += $ds['depo_cash'];
                      $ctr++;
                     // $pct = $comp['sales_actual']>0 ? ($comp['sales_diff']/$comp['sales_actual'])*100 : 0; 
                  ?>
                    <tr>
                      <td>{{ $ds['companycode'] }}</td>
                      <td>{{ $ds['branchcode'] }}</td>
                      <td class="text-right">{{ nf($ds['sales']) }}</td>
                      <td class="text-right">{{ nf($ds['grab']) }}</td>
                      <td class="text-right text-info">{{ nf($ds['grab_deduct']) }}</td>
                      <td class="text-right">{{ nf($ds['panda']) }}</td>
                      <td class="text-right text-info">{{ nf($ds['panda_deduct']) }}</td>
                      <td class="text-right">{{ nf($ds['zap']) }}</td>
                      <td class="text-right text-info">{{ nf($ds['zap_deduct']) }}</td>
                      <td class="text-right">{{ nf($ds['ccard']) }}</td>
                      <td class="text-right text-info">{{ nf($ds['ccard_deduct']) }}</td>
                      <td class="text-right"><b>{{ nf($ds['sales_actual']) }}</b></td>
                      <td class="text-right"><b>{{ nf($ds['sales_deduct']) }}</b></td>
                      <td class="text-right"><b>{{ nf($ds['sales_diff']) }}</b></td>
                      <td class="text-right">{{ $ds['pct']>0?nf($ds['pct'])+0:'' }}</td>
                      <td class="text-right text-success">{{ nf($ds['depo_cash']) }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                  <?php
                    $tot_pct = 0;
                    if ($tot_salesl>0)
                      $tot_pct = ($tot_salesl/$tot_salesa)*100;
                  ?>
                  <tfoot>
                    <tr>
                    <td></td>
                    <td><b>{{ $ctr }}</b> <span class="gly gly-shop" style="font-size: smaller; margin-top: 4px;"></span></td>
                    <td class="text-right"><b>{{ nf($tot_sales) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_gfc) }}</b></td>
                    <td class="text-right">
                      <b>{{ nf($tot_gfd) }}</b>
                      <div>
                        <b style="font-size: smaller;">{{ nf($tot_gfc-$tot_gfd) }}</b>
                      </div>
                    </td>
                    <td class="text-right"><b>{{ nf($tot_fpc) }}</b></td>
                    <td class="text-right">
                      <b>{{ nf($tot_fpd) }}</b>
                      <div>
                        <b style="font-size: smaller;">{{ nf($tot_fpc-$tot_fpd) }}</b>
                      </div>
                    </td>
                    <td class="text-right"><b>{{ nf($tot_zc) }}</b></td>
                    <td class="text-right">
                      <b>{{ nf($tot_zd) }}</b>
                      <div>
                        <b style="font-size: smaller;">{{ nf($tot_zc-$tot_zd) }}</b>
                      </div>
                    </td>
                    <td class="text-right"><b>{{ nf($tot_ccc) }}</b></td>
                    <td class="text-right">
                      <b>{{ nf($tot_ccd) }}</b>
                      <div>
                        <b style="font-size: smaller;">{{ nf($tot_ccc-$tot_ccd) }}</b>
                      </div>
                    </td>
                    <td class="text-right"><b>{{ nf($tot_salesa) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_salesd) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_salesl) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_pct) }}</b></td>
                    <td class="text-right"><b>{{ nf($tot_depo) }}</b></td>
                  </tr>
                  </tfoot>
                </thead>
              </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div style="margin-bottom: 50px;"></div>
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

     $('.table-sort-branch').tablesorter({
      stringTo: 'min',
      sortList: [[0,0]],
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