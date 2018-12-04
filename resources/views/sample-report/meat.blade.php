<table cellpadding="0" cellpadding="0" border="1">
	<thead>
		<tr>
		<th>Branch</th>
		<th>Area</th>
		@foreach($components as $component)
		<th>{{ $component->descriptor }}  ({{ strtolower($component->uom) }})</th>
		@endforeach
		</tr>
	</thead>
	<tbody>
			@foreach($datas as $key => $data)
			<tr>
				<td>{{ $data['code'] }}</td>
				<td>{{ $data['area'] }}</td>
				@foreach($data['components'] as $k => $c)
					<td>{{ $c['qty'] }}</td>
				@endforeach
			</tr>
			@endforeach
	</tbody>
</table>