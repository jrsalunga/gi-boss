<tr>
	<td>
		<a class="{{ $branch->status=='3'?'text-muted':(in_array($branch->type, ['3', '4'])?'text-primary':'text-sucess') }}" href="/masterfiles/branch/{{ strtolower($branch->code) }}">{{ $branch->code }}</a> - 
		<a class="{{ $branch->status=='3'?'text-muted':(in_array($branch->type, ['3', '4'])?'text-primary':'text-sucess') }}" href="/masterfiles/branch/{{ $branch->lid() }}">{{ $branch->descriptor }}</a>
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
</tr>