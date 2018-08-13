<tr>
	<td style="padding: 0 0 5px 0;">
		<img src="{{ $employee->getPhotoUrl() }}" style="margin-right: 5px; width: 80px; max-width: 80px" class="img-responsive">
	</td>
	<td>
		<a href="/hr/masterfiles/employee/{{ strtolower($employee->code) }}">{{ $employee->code }}</a> 
	</td>
	<td>
		<a href="/hr/masterfiles/employee/{{ $employee->lid() }}">{{ $employee->lastname }}, {{ $employee->firstname }} <span class="text-muted">{{ $employee->middlename }}</span></a> 
	</td>
	<td>
		@if(isset($employee->branch))
		<a href="/hr/masterfiles/employee/branch/{{ $employee->branch->lid() }}">
			{{ $employee->branch->code }}
		</a>
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
			@if(is_iso_date($employee->datestart->format('Y-m-d')))
			<em><small class="text-muted help" title="{{ $employee->datestart->format('m/d/Y') }}" data-toggle="tooltip"> {{ diffForHumans($employee->datestart) }}</small></em>
			@endif
		@else
			<span class="label label-default">Inactive</span>
		@endif
	</td>
</tr>