<table cellpadding="0" cellpadding="0" border="1">
	<thead>
		<tr>
		<th>Branch</th>
		@foreach($components as $component)
		<th>{{ $component->descriptor }}</th>
		@endforeach
		</tr>
	</thead>
	<tbody>
			@foreach($datas as $key => $data)
			<tr>
			<td>{{ $data['code'] }}</td>
				@foreach($data['components'] as $k => $c)
					<td>{{ $c['qty'] }}</td>
				@endforeach
			</tr>
			@endforeach
	</tbody>
</table>