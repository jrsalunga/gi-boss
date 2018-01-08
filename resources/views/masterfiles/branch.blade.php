@extends('dash')

@section('title', '- '.ucfirst($active).' List')

@section('body-class', $active.'-list')


@section('sidebar')
  @include('_partials.menus.masterfiles', ['active'=>$active])
@endsection

@section('content')
<div class="row" style="margin-top: 20px;">
  @if(!is_null($datas))        
  <div class="col-md-12">
    <h3 class="page-header">{{ ucfirst($active) }} List</h3>
      <div class="table-responsive">
      <table class="table table-striped">
        <tbody>
          <?php
          $t = '_partials.masterfiles.'.$active;
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
  @endif
</div>
@endsection



@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/dr-picker.js"> </script>
  <script type="text/javascript">
    @if(request()->has('search'))
    $('#search').focusTextToEnd();
    @endif
  </script>
@endsection