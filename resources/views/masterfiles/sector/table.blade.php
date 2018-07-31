<tr>
	<td>
		<span>
			@if($sector->is_parent())
				<span class="gly gly-parents"></span> 
			@else
				<span class="label label-success">{{ $sector->parent->code or '*' }}</span>
			@endif
		</span>
		<a href="/masterfiles/sector/{{ strtolower($sector->code) }}">{{ $sector->code }}</a> - 
		<a href="/masterfiles/sector/{{ $sector->lid() }}">{{ $sector->descriptor }}</a>
	</td>
	<td>
		@if($sector->children->count()>0)
			<span class="badge text-info">{{ $sector->children->count() }}</span>
		@endif
	</td>
</tr>