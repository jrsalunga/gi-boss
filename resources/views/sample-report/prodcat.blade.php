<table cellpadding="0" cellpadding="0" border="1">
	<thead>
		<tr>
			<th>Branch</th>
			<th>Date</th>
			<th>Sales</th>
			<th>Gross</th>
			<th>Food Cost %</th>
			<th>Good Purch</th>
			<th>Transfer COS</th>
			<th>Purchase</th>
			<th>Transfer</th>
			<th>Emp Meal</th>
			<th>Opex</th>
			<th>Transfer NCOS</th>
			<th>Food Sales</th>
		</tr>
	</thead>
	<tbody>
		@foreach($datas as $brcode => $row)
			@foreach($row as $key => $data)
			<tr>
				<td>{{ $brcode }}</td>
				<td>{{ $data['date']->format('Y-m-d') }}</td>
				<td>{{ $data['sales'] }}</td>
				<td>{{ $data['gross'] }}</td>
				<td>{{ $data['cospct'] }}</td>
				<td>{{ $data['cos'] }}</td>
				<td>{{ $data['transcos'] }}</td>
				<td>{{ $data['purchcost'] }}</td>
				<td>{{ $data['transcost'] }}</td>
				<td>{{ $data['emp_meal'] }}</td>
				<td>{{ $data['opex'] }}</td>
				<td>{{ $data['transncos'] }}</td>
				<td>{{ $data['food_sales'] }}</td>
			</tr>
			@endforeach
		@endforeach
	</tbody>
</table>