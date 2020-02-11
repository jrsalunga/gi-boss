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
			<span class="badge text-info help" title="{{ $sector->children->count() }} {{ $sector->children->count()>1?'Sub Areas':'Sub Area' }}" data-toggle="tooltip">{{ $sector->children->count() }}</span>
			<i class="material-icons" style="top: 3px;">pin_drop</i>
		@endif
	</td>
	<td>
		@if($sector->branch_count()>0)
			<span class="badge text-info help" title="{{ $sector->branch_count() }} {{ $sector->branch_count()>1?'Branches':'Branch' }}" data-toggle="tooltip">{{ $sector->branch_count() }}</span>
			<span class="gly gly-shop" style="top: 3px;"></span>
		@endif
	</td>
  <td>
   <small><a href="/hr/masterfiles/employee/{{ $sector->am->lid() }}" target="_blank">{{ $sector->am->lastname }}, {{ $sector->am->firstname }}</a></small>
  </td>
</tr>