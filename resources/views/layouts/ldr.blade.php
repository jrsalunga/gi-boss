@section('css-internal')
 @parent

 <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">


@endsection

<form action="{{ request()->url() }}" method="GET" accept-charset="UTF-8" id="filter-form">
	

	<input type="hidden" name="a[]" value="1">
	<input type="hidden" name="a[]" value="2">
	<input type="hidden" name="a[]" value="3">
	<input type="hidden" id="componentid" name="componentid" value="{{ $component->id or ''}}">
	<input type="hidden" id="productid" name="productid" value="{{ $product->id or ''}}">
	<input type="hidden" id="mode" name="mode" value="{{ $dr->getMode() }}">
	<input type="hidden" name="branchid" id="branchid" value="{{ is_null($branch) ? '':$branch->lid() }}">
	<input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
	<input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">

</form>



<div class="row">
	<div class="col-md-6">
	<!--
		<input type="search" id="component" class="form-control" value="{{ $component->descriptor or ''}}">
	-->
		<select class="form-control" id="scomp">
			<option value=""></option>
			@foreach ($comps as $comp)
				@if (request()->has('componentid') && strtolower(request()->input('componentid'))==$comp->lid())
					<option selected value="{{ $comp->lid() }}">{{ $comp->descriptor }}</option>
				@else
					<option value="{{ $comp->lid() }}">{{ $comp->descriptor }}</option>
				@endif
			@endforeach
		</select>
	</div>
	<div class="col-md-6">
	<!--
		<input type="search" id="product" class="form-control" value="{{ $product->descriptor or ''}}">
	-->
		<select class="form-control" id="sprod">
			<option value=""></option>
			@foreach($prods as $prod)
				@if (request()->has('productid') && strtolower(request()->input('productid'))==$prod->lid())
					<option selected value="{{ $prod->lid() }}">{{ $prod->code }} - {{ $prod->descriptor }}</option>
				@else
					<option value="{{ $prod->lid() }}">{{ $prod->code }} - {{ $prod->descriptor }}</option>
				@endif
				
			@endforeach
		</select>
	</div>
</div>
<!--
<div class="row">
	<div class="col-md-6"><h1>{{ $branch->code or '' }}</h1></div>
	<div class="col-md-6"><h1>{{ $branch->descriptor or '' }}</h1></div>
</div>

<div class="row">
	<div class="col-md-6"><h1>{{ $dr->getMode() }}</h1></div>
	<div class="col-md-6">{{ $dr->fr->format('Y-m-d') }} - {{ $dr->to->format('Y-m-d') }}</div>
</div>

<div class="row">
	<div class="col-md-6"><h1>{{ request()->has('componentid') }}</h1></div>
	<div class="col-md-6"><h1>{{ request()->has('productid') }}</h1></div>
</div>
-->
<div class="row">
	<div class="col-md-6"><h1>{{ $component->descriptor or ''}}</h1></div>
	<div class="col-md-6"><h1>{{ $product->descriptor or '' }}</h1></div>
</div>


<div class="row">
	<div class="col-md-12">

		@if ($datas)


			<table class="table table-condensed table-striped">
				<thead>
					<tr>
						<th>Date</th>
						<th class="text-right">Component Qty</th>
						<th class="text-right">Component Cost</th>
						<th class="text-right">Product Qty</th>
						<th class="text-right">Product Sales</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$c_tot_qty = 0;
					$c_tot_tcost = 0;
					$p_tot_qty = 0;
					$p_tot_grsamt = 0;
				?>
				@foreach($datas as $data)
					<tr>
						<td>{{ $data['date']->format('Y-m-d') }}</td>

						@if (is_null($data['component']))
							<td></td><td></td>
						@else
							<td class="text-right">{{ $data['component']->qty }}</td>
							<td class="text-right">{{ number_format($data['component']->tcost, 2) }}</td>
							<?php
								$c_tot_qty +=  $data['component']->qty;
								$c_tot_tcost += $data['component']->tcost;
							?>
						@endif

						@if (is_null($data['product']))
							<td></td><td></td>
						@else
							<td class="text-right">{{ $data['product']->qty }}</td>
							<td class="text-right">{{ number_format($data['product']->grsamt, 2) }}</td>
							<?php
								$p_tot_qty += $data['product']->qty;
								$p_tot_grsamt += $data['product']->grsamt;
							?>
						@endif


						
					</tr>
				@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td class="text-right">
							<?=$c_tot_qty>0?number_format($c_tot_qty,0):''?>
						</td>
						<td class="text-right">
							<?=$c_tot_tcost>0?number_format($c_tot_tcost,2):''?>
						</td>
						<td class="text-right">
							<?=$p_tot_qty>0?number_format($p_tot_qty,0):''?>
						</td>
						<td class="text-right">
							<?=$p_tot_grsamt>0?number_format($p_tot_grsamt,2):''?>
						</td>
					</tr>
				</tfoot>
			</table>

		@else
			No Data	
		@endif 

	</div>
</div>




@section('js-external')
	@parent

	@include('_partials.js.comp-purch')
	<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/en.js"></script>
	<script type="text/javascript">
	
		$(document).ready(function(){
			
			$('#scomp').select2({
				placeholder: "Select a component"
			});

			var $scomp = $('#scomp');
			$scomp.on("select2:select", function(e) { 
				console.log(e.params.data.id);
				$('#componentid').val(e.params.data.id)
			});


			$('#sprod').select2({
				placeholder: "Select a product"
			});

			var $sprod = $('#sprod');
			$sprod.on("select2:select", function(e) { 
				console.log(e.params.data.id);
				$('#productid').val(e.params.data.id)
			});


		});	

	</script>
@endsection

