@extends('dash')

@section('title', '- Branch List')

@section('body-class', 'branch-list')

@section('sidebar')
	@include('_partials.menus.masterfiles', ['active'=>'branch'])
@endsection
<?php
 $branch->load('company');
?>
@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header">{{ $branch->descriptor }} <small>{{ $branch->code }}</small></h3>
		<div>{{ $branch->tin }}</div>
		<div>{{ $branch->company->descriptor }}</div>
		<div>{{ $branch->phone }}</div>
		<div>{{ $branch->mobile }}</div>
		<div>{{ $branch->email }}</div>
		<br>
		@foreach($branch->boss as $u)
			{{ $u->user->name }} <small>
			<div>
				<em>{{ $u->user->email }}</em></small>
			</div>
		@endforeach
	</div>
</div>
@endsection

@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="/js/dr-picker.js"> </script>

	<script type="text/javascript">
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() >= $(document).height()) {
            //var last_id = $(".post-id:last").attr("id");
            //loadMoreData(last_id);
            console.log($(window).height());
        }
    });


  </script>
@endsection