<tr>
	<td>
		<a href="/hr/masterfiles/employee/{{ strtolower($employee->code) }}">{{ $employee->code }}</a> 
	</td>
	<td>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}">{{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middlename }}</a> 
	</td>
	<td>
		@if(isset($employee->branch))
			{{ $employee->branch->code }}
		@endif
	</td>
	<td>
		@if(isset($employee->position))
			{{ $employee->position->descriptor }}
		@endif
	</td>
	<td>
		@if($employee->isActive())
			<span class="label label-success">Active</span>
		@else
			<span class="label label-default">Inactive</span>
		@endif
	</td>
</tr>