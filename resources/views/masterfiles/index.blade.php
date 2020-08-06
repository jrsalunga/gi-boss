@extends('dash')

@section('title', '- '.nav_caption($active).' List')

@section('body-class', $active.'-list mdc-typography')

@section('content')
<div class="row" style="margin-top: 20px;">
  <div class="col-md-12">
    @include('_partials.alerts')
  </div>
</div>
@if(is_null($active))

@else
<div class="row">
  <div class="col-md-12">
    <h3 class="page-header">{{ nav_caption($active) }} List 
      <a href="/masterfiles/{{$active}}/create" class="pull-right" title="Add Record"><i class="material-icons">note_add</i></a>
    </h3>
    @if(count($datas)>0)        
    <div class="table-responsive">
    <table class="table table-striped">
      <tbody>
        <?php
        $active = is_null($active) ? 'index' : $active;
        $t = 'masterfiles.'. $active.'.table';
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
@endif


@endsection



