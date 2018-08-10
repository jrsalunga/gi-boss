@extends('hr.dash', ['search_url'=> $active])

@section('title', '- '.hr_nav_caption($active).' List')

@section('body-class', $active.'-list mdc-typography')

@section('content')
<div class="row" style="margin-top: 20px;">
  <div class="col-md-12">
    <h3 class="page-header">{{ hr_nav_caption($active) }} List 
      @if($active=='employee')
      <a href="/hr/masterfiles/{{$active}}/branch" class="pull-right" title="Branch Employee" data-toggle="tooltip"><i class="material-icons">store</i></a>
      @endif
      <a href="/hr/masterfiles/{{$active}}/create" class="pull-right" title="Add Record" data-toggle="tooltip"><i class="material-icons">note_add</i></a>
    </h3>
    @include('_partials.alerts')

    @if(count($datas)>0)        
    <div class="table-responsive">
    <table class="table table-hover">
      <tbody>
        <?php
        $active = is_null($active) ? 'index' : $active;
        $t = 'hr.masterfiles.'. $active.'.table';
        ?>
        @each($t, $datas, $active)
      </tbody>
    </table>
    </div>
  </div>
  <div class="col-sm-9">
    {!! $datas->render() !!}     
  </div> <!-- end: .md-col-6 -->
  <div class="col-sm-3">
    <div class="pagination pull-right">
      <?php
        $page = $datas->toArray();
      ?>
      Showing {{ $page['from'] }} to {{ $page['to'] }} of {{ $page['total'] }} entries
    </div>
  </div>
  @else
    No Records Found!
  @endif
</div>
@endsection



