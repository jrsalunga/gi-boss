<tr>
	<td>
		<a class="{{ $branch->status=='3'?'text-muted':(in_array($branch->type, ['5', '4'])?'text-primary':($branch->status=='1'?'text-warning2':'text-sucess')) }}" href="/masterfiles/branch/{{ strtolower($branch->code) }}">{{ $branch->code }}</a> - 
		<a class="{{ $branch->status=='3'?'text-muted':(in_array($branch->type, ['5', '4'])?'text-primary':($branch->status=='1'?'text-warning2':'text-sucess')) }}" href="/masterfiles/branch/{{ $branch->lid() }}">{{ $branch->descriptor }}</a>

		
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
		@foreach($branch->contacts() as $contact)
			<span>{{ $contact->number }}</span>
		@endforeach
	</td>
	<td>
		@if($branch->status=='1')
			<span class="label label-warning pull-left">Under Construction</span>
		@endif
		@if($branch->status=='3')
			<span class="label label-default pull-left">Closed</span>
		@endif
		@if($branch->type=='4')
			<span class="label label-primary pull-left" style="margin-left: 3px;">Office</span>
		@endif
		@if($branch->type=='5')
			<span class="label label-info pull-left" style="margin-left: 3px;">Other</span>
		@endif
	</td>
</tr>