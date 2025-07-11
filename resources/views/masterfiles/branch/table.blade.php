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
	<td style="line-height: 1;">
		@foreach($branch->contacts as $c)
			<span style="margin-right: 5px;">{!! contact_icon($c->type, true) !!}<a href="tel:{{$c->getNumber()}}" style="font-size: smaller;" >{{ $c->getNumber() }}</a></span>
		@endforeach
	</td>
	<td>
		<a href="mailto:{{$branch->email}}" style="font-size: smaller;" >
		{{ $branch->email }}
		</a>
	</td>
	<td>
		@if (!is_null($branch->date_start))
			{{ $branch->date_start->format('j M Y') }}

			@if(($branch->status=='3' || $branch->status=='4') && !is_null($branch->date_end))
				- {{ $branch->date_end->format('j M Y') }}
				@endif
			@endif

		@if (!empty($branch->service_period()))
			<em style="font-size: smaller; color: #888;">
				({{ $branch->service_period(true) }})
			</em>
		@endif
	</td>
	<td>
		@if($branch->status=='1')
			<span class="label label-warning pull-left">Under Construction</span>
		@endif
		@if($branch->status=='3')
			<span class="label label-default pull-left" style="cursor: help">Closed</span>
		@endif
    @if($branch->status=='4')
      <span class="label label-default pull-left" style="cursor: help">Temporary Closed</span>
    @endif
		@if($branch->type=='4')
			<span class="label label-primary pull-left" style="margin-left: 3px;">Office</span>
		@endif
		@if($branch->type=='5')
			<span class="label label-info pull-left" style="margin-left: 3px;">Other</span>
		@endif
	</td>
</tr>
