@extends('dash')

@section('title', '- Branch List')

@section('body-class', 'branch-list')


@section('sidebar')
	@include('_partials.menus.masterfiles', ['active'=>'branch'])
@endsection

@section('content')
<div class="row" style="margin-top: 20px;">
	<div class="col-md-12">
		<h3 class="page-header">Branch List</h3>
		@if(!is_null($branches))				
			<div class="table-responsive">
			<table class="table table-striped">
				<tbody>
					@each('_partials.branches.td-branch', $branches, 'branch')
				</tbody>
			</table>
			</div>

			{!! $branches->render() !!}
		@endif
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