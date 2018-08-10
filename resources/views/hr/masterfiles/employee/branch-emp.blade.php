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
  <div class="col-md-6" >
    @if($branch)
  
      <div class="table-responsive">
      <table class="table">
        <tbody>
        @foreach($branch->active_employee as $k => $employee)
        <tr>
          <td>
            <table>
              <tbody>
                <tr>
                  <td>
                    <img src="{{ $employee->getPhotoUrl() }}" style="margin-right:10px; width: 60px; " class="img-responsive">
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
  </div>
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



