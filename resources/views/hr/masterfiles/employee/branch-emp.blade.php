@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Branch Employee')

@section('body-class', 'branch-list mdc-typography')

@section('content')
<div class="row" style="margin-top: 20px;">
  <div class="col-md-12" >
    @include('_partials.alerts')
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <h4>Branch Employees</h4>
  </div>
  <div class="col-md-6">
   <form action="/hr/masterfiles/employee/branch" method="POST">
    <div class="form-group @include('_partials.input-error', ['field'=>'branchid'])">
      @if(count($branches)>0)
      <select class="selectpicker form-control show-tick pull-right" name="branchid" id="branchid" data-live-search="true" data-size="10" title="Select Branch" data-width="auto">
        <option value="971077bca54611e5955600ff59fbb323" {{ $branch&&$branch->id=='971077BCA54611E5955600FF59FBB323'?'selected':'' }} data-tokens="GHO HEAD OFFICE">GHO - HEAD OFFICE</option>
        @foreach($branches as $br)
          <option value="{{$br->lid()}}" {{ $branch&&$branch->id==$br->id?'selected':'' }} data-tokens="{{ $br->code }} {{ $br->descriptor }}">
            {{ $br->code }} - {{ $br->descriptor }}
          </option>
        @endforeach
      </select>
      @else
        Add Branch
      @endif
    </div>
   </form>
  </div>
</div>
<div class="row" style="margin-top: 20px;">
  <div class="col-md-7" >
    @if($branch)
      <div class="table-responsive">
      <table class="table">
        <tbody>
        @foreach($datas['employees'] as $k => $employee)
        <tr>
          <td style="padding: 0 0 5px 0;">
            <table>
              <tbody>
                <tr>
                  <td>
                    <img src="{{ $employee->getPhotoUrl() }}" style="margin-right:5px; width: 80px; " class="img-responsive">
                  </td>
                  <td>
                    <div>
                      <a href="/hr/masterfiles/employee/{{ strtolower($employee->code) }}">{{ $employee->code }}</a> 
                    </div>
                    <div>
                      <a href="/hr/masterfiles/employee/{{ $employee->lid() }}">{{ $employee->lastname }}, {{ $employee->firstname }} <span class="text-muted">{{ $employee->middlename }}</span></a> 
                    </div>
                    @if(isset($employee->position))
                      <p><small>{{ $employee->position->descriptor }}</small></p>
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        @endforeach
        </tbody>
      </table>
      </div>
    @endif
  </div><!-- end: .col-nd-6 -->
  <div class="col-md-5">
    @if($branch)
    <div class="panel panel-default">
      <div class="panel-body">
        <table class="table table-condensed">
          <thead>
            <tr><th>Position</th><th class="text-right">#</th></tr>
          </thead>
          <tbody>
            <?php $p = 0; ?>
            @foreach($datas['positions'] as $key => $position)
              <tr>
                <td>{{ $position['position'] }}</td>
                <td class="text-right">{{ $position['ctr'] }}</td>
              </tr>
              <?php $p += $position['ctr'];  ?>
            @endforeach
          </tbody>
          <thead><tr><td></td><td class="text-right">{{ $p }}</td></tr></thead>
        </table>
      </div><!-- end: .panel-body-->
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <table class="table table-condensed">
          <thead>
            <tr><th>Department</th><th class="text-right">#</th></tr>
          </thead>
          <tbody>
            @foreach($datas['deptd'] as $key => $dept)
              <tr>
                <td>{{ $dept['deptd'] }}</td>
                <td class="text-right">{{ $dept['ctr'] }}</td>
              </tr>
            @endforeach
          </tbody>
          <thead><tr><td></td><td class="text-right"><a href="/egc/employee/list">24</a></td></tr></thead>
        </table>
      </div><!-- end: .panel-body-->
    </div>
  @endif
  </div><!-- end: .col-nd-5 -->
</div>
@endsection

@section('js-external')
  @parent

<script type="text/javascript">
$(document).ready(function() {

  $('.selectpicker').on('change', function(e){
    document.location.href = '/hr/masterfiles/employee/branch/'+$(this).val();
    loader();
  });

});
</script>
@endsection



