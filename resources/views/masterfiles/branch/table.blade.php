<tr>
	<td>
		<a class="{{ $branch->status=='3'
			? 'text-muted'
			: (in_array($branch->type, ['5', '4']) 
				? 'text-primary'
				: ($branch->status=='1') 
					? 'text-warning':
						'text-sucess' }}" href="/masterfiles/branch/{{ strtolower($branch->code) }}">{{ $branch->code }}</a> - 
		<a class="{{ $branch->status=='3'?'text-muted':(in_array($branch->type, ['5', '4'])?'text-primary':'text-sucess') }}" href="/masterfiles/branch/{{ $branch->lid() }}">{{ $branch->descriptor }}</a>

		
	</td>
	<td>
		@if(isset($branch->company))
			<a href="/masterfiles/company/{{ $branch->company->lid() }}">{{ $branch->company->code }}</a>
		@endif
	</td>
	<td>
		@if(isset($branch->lessor))
			<a href="/masterfiles/lessor/{{ $branch->lessor->lid() }}">{{ $branch->lessor->code }}</a>
		@endif
	</td>
	<td>
		@if($branch->status=='1')
			<span class="label label-warning pull-right">Under Construction</span>
		@endif
		@if($branch->status=='3')
			<span class="label label-default pull-right">Closed</span>
		@endif
		@if($branch->type=='4')
			<span class="label label-primary pull-right" style="margin-right: 3px;">Office</span>
		@endif
		@if($branch->type=='5')
			<span class="label label-info pull-right" style="margin-right: 3px;">Other</span>
		@endif
	</td>
</tr>