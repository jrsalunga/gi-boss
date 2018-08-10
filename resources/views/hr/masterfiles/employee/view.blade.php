@extends('hr.dash', ['search_url'=> 'employee'])

@section('title', '- Employee: '.$employee->code.' '.$employee->lastname.', '.$employee->firstname.' '.$employee->middlename)

@section('body-class', 'employee-view')

<?php
 $employee->load(['company', 'branch', 'position', 'department', 'religion', 'spouse', 'childrens.acadlevel', 'ecperson', 'workexps', 'educations', 'statutory']);
?>
@section('content')
<div class="row">
	<div class="col-md-12"  style="margin-top: 20px;">
		@include('_partials.alerts')
	</div>
</div>
<div class="row">
	<div class="col-md-8">
		<table>
			<tbody>
				<tr>
					<td>
						<img src="{{ $employee->getPhotoUrl() }}" style="margin-right: 5px; width: 100px;" class="img-responsive">
					</td>
					<td>
						<h3 class="text-success" style="margin-top: 10px;">
							{{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middlename }} 
							<small data-id="{{ $employee->id }}">{{ $employee->code }}</small>
						</h3>
						@if(isset($employee->position))
						<div>
								{{ $employee->position->descriptor }}
						</div>
						@endif
						@if(isset($employee->branch))
						<div>
				  		<a href="/hr/masterfiles/employee/branch/{{ $employee->branch->lid() }}">
								{{ $employee->branch->code }} - {{ $employee->branch->descriptor }}
				  		</a>
						</div>
						@endif
						@if(isset($employee->company))
						<div class="hidden-sm hidden-md hidden-lg">
								{{ $employee->company->code }}
						</div>
						<div class="hidden-xs">
								{{ $employee->company->descriptor }}
						</div>
						@endif
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		
		

		<div class="dropdown pull-right">
		  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    <span class="glyphicon glyphicon-cog"></span>
		    <span class="caret"></span>
		  </button>
		  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
		    <li>
		    	<a 
					@if(request()->input('tab')=='personal')
					href="/hr/masterfiles/employee/{{$employee->lid()}}/edit/personal"
					@elseif(request()->input('tab')=='employment')
					href="/hr/masterfiles/employee/{{$employee->lid()}}/edit/employment"
					@elseif(request()->input('tab')=='family')
					href="/hr/masterfiles/employee/{{$employee->lid()}}/edit/family"
					@elseif(request()->input('tab')=='workedu')
					href="/hr/masterfiles/employee/{{$employee->lid()}}/edit/workedu"
					@else
					href="/hr/masterfiles/employee/{{$employee->lid()}}/edit/employment"
					@endif
					 data-toggle="loader"><i class="material-icons">edit</i> Edit</a>
		    </li>
		    <li><a href="javascript:void(0)" ><i class="glyphicon glyphicon-level-up"></i>  Promote</a></li>
		    <li><a href="/hr/masterfiles/employee/{{ $employee->lid() }}/edit/confirm" ><i class="gly gly-disk-saved"></i>  Confirm</a></li>
		    <li role="separator" class="divider"></li>
		    <li>
		    	<a href="/hr/masterfiles/employee/create" data-toggle="loader">
						<span class="glyphicon glyphicon-plus"></span> Create New
		    	</a>
		    </li>
		  </ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		
	<div class="tab-wrap">
		
		<ul class="nav nav-tabs" role="tablist">
	    <li role="presentation" <?=is_null(request()->input('tab'))?'class="active"':'';?>><a href="#emplymt" aria-controls="emplymt" role="tab" data-toggle="tab"><span class="gly gly-folder-closed"></span> <span class="hidden-sm hidden-xs">Employment Info</span></a></li>
	    <li role="presentation" <?=request()->input('tab')=='personal'?'class="active"':'';?>><a href="#personal" aria-controls="personal" role="tab" data-toggle="tab"><span class="gly gly-nameplate-alt"></span> <span class="hidden-sm hidden-xs">Personal Info</span></a></li>
	    <li role="presentation" <?=request()->input('tab')=='family'?'class="active"':'';?>><a href="#family" aria-controls="family" role="tab" data-toggle="tab"><span class="gly gly-group"></span> <span class="hidden-sm hidden-xs">Family Info</span></a></li>
	    <li role="presentation" <?=request()->input('tab')=='workedu'?'class="active"':'';?>><a href="#workeduc" aria-controls="workeduc" role="tab" data-toggle="tab"><span class="gly gly-certificate"></span> <span class="hidden-sm hidden-xs">Work & Educational Info</span></a></li>
	  </ul>

	  <!-- Tab panes -->
	  <div class="tab-content">
	    <div role="tabpanel" class="tab-pane <?=is_null(request()->input('tab'))?'active':'';?>" id="emplymt">
				<div class="panel panel-primary">
				  <div class="panel-heading">Employer</div>
				  <div class="panel-body">
				  	<div class="row">
				  		<div class="col-md-4">
			    			<div class="form-group">
								  <label>Company</label>
								  <div><?=isset($employee->company)?$employee->company->descriptor:'-';?></div>
								</div>
							</div>
				  		<div class="col-md-4">
				  			<div class="form-group">
								  <label>Branch</label>
								  <div><?=isset($employee->branch)?$employee->branch->descriptor:'-';?></div>
								</div>
						  </div>
				  		<div class="col-md-4">
				  			<div class="form-group">
									<label>Department</label>
									<div><?=isset($employee->department)?$employee->department->descriptor:'-';?></div>
								</div>
				  		</div>
				  		<div class="col-md-4">
				  			<div class="form-group">
									<label>Position</label>
									<div title="Ordinal: {{ $employee->punching }}"><?=isset($employee->position)?$employee->position->descriptor:'-';?></div>
								</div>
				  		</div>
				  		<div class="col-md-4">
				  			<div class="form-group">
									<label>Emp Status</label>
									<div>{{ emp_status($employee->empstatus) }} <small class="text-muted">(<?=$employee->isActive()?'<span style="color: green;">Active</span>':'Inactive';?>)</small></div>
								</div>
				  		</div>
				  		<div class="col-md-4">
						  	@if(is_iso_date($employee->datestart->format('Y-m-d')))
								<div class="form-group">
									<label>Started</label>
									<div>{{ $employee->datestart->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->datestart) }} ago)</em></small></div>
								</div>
								@endif
				  		</div>
				  		<div class="col-md-4">
				  			@if(is_iso_date($employee->datehired->format('Y-m-d')))
								<div class="form-group">
									<label>Hired</label>
									<div>{{ $employee->datehired->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->datehired) }} ago)</em></small></div>
								</div>
								@endif
				  		</div>
				  		<div class="col-md-4">
				  			@if(is_iso_date($employee->datestop->format('Y-m-d')) && $employee->empstatus==4)
								<div class="form-group">
									<label>Resigned</label>
									<div>{{ $employee->datestop->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->datestop) }} ago)</em></small></div>
								</div>
								@endif
				  		</div>
				  		<div class="col-md-4">
				  			@if(is_iso_date($employee->datestop->format('Y-m-d')) && $employee->empstatus==5)
								<div class="form-group">
									<label>Terminated</label>
									<div>{{ $employee->datestop->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->datestop) }} ago)</em></small></div>
								</div>
								@endif
				  		</div>
				  	</div>
				  </div>
				</div><!-- end: .panel.panel-primary -->
				<div class="panel panel-primary">
				  <div class="panel-heading">Rates</div>
				  <div class="panel-body">
				  	<div class="row">
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Pay Type</label>
									<div>{{ emp_paytype($employee->paytype) }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Rate Type</label>
									<div>{{ emp_ratetype2($employee->ratetype) }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Basic Rate</label>
									<div>{{ number_format($employee->rate, 2) }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>ECOLA</label>
									<div>{{ number_format($employee->ecola, 2) }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Allowance 1</label>
									<div>{{ number_format($employee->allowance1, 2) }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Allowance 2</label>
									<div>{{ number_format($employee->allowance2, 2) }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Meal</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->meal):'&nbsp;' }}</div>
								</div>
				  		</div>
				  	</div>
				 	</div>
				</div><!-- end: .panel.panel-primary -->
				<div class="panel panel-primary">
				  <div class="panel-heading">Contribution Details</div>
				  <div class="panel-body">
				  	<div class="row">
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>SSS #</label>
									<div><abbr title="{{ $employee->sssno }}" data-toggle="tooltip">{{ $employee->sssno() }}</abbr></div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>SSS EE Share</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->ee_sss):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>SSS ER Share</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->er_sss):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Tag</label>
									<div>{{ $employee->statutory ? ($employee->statutory->sss_tag==1?'Y':'N'):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>PhilHealth #</label>
									<div><abbr title="{{ $employee->phicno }}" data-toggle="tooltip">{{ $employee->phicno }}</abbr></div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>PhilHealth EE Share</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->ee_phic):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>PhilHealth ER Share</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->er_phic):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Tag</label>
									<div>{{ $employee->statutory ? ($employee->statutory->phic_tag==1?'Y':'N'):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Pag Ibig #</label>
									<div><abbr title="{{ $employee->hdmfno }}" data-toggle="tooltip">{{ $employee->hdmfno() }}</abbr></div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Pag Ibig EE Share</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->ee_hdmf):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Pag Ibig ER Share</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->er_hdmf):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Tag</label>
									<div>{{ $employee->statutory ? ($employee->statutory->hdmf_tag==1?'Y':'N'):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>TIN #</label>
									<div><abbr title="{{ $employee->tin }}" data-toggle="tooltip">{{ $employee->tin() }}</abbr></div>
								</div>
				  		</div>
				  		<div class="col-md-6">
				  			<div class="form-group">
									<label>W/Tax</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->wtax):'&nbsp;' }}</div>
								</div>
				  		</div>
				  		<div class="col-md-3">
				  			<div class="form-group">
									<label>Tag</label>
									<div>{{ $employee->statutory ? ($employee->statutory->wtax_tag==1?'Y':'N'):'&nbsp;' }}</div>
								</div>
				  		</div>
						</div>
				 	</div>
				</div><!-- end: .panel.panel-primary -->
			</div><!-- end: .tabpanel -->

	    <div role="tabpanel" class="tab-pane <?=request()->input('tab')=='personal'?'active':'';?>" id="personal">
				<div class="panel panel-primary">
				  <div class="panel-heading">Address & Contacts</div>
				  <div class="panel-body">
				  	<div class="row">
							<div class="col-md-8">
								<div class="form-group">
									<label>Address</label>
									<div>{{ $employee->address }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Email</label>
									<div>{{ $employee->email }}&nbsp;</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Mobile</label>
									<div>{{ $employee->mobile }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Phone</label>
									<div>{{ $employee->phone }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Fax</label>
									<div>{{ $employee->fax }}&nbsp;</div>
								</div>
							</div>
						</div>
				  </div><!-- end: .panel-body -->
				</div><!-- end: .panel.panel-primary -->
				<div class="panel panel-primary">
				  <div class="panel-heading">Personal Info</div>
				  <div class="panel-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>Birthday</label>
									@if(is_iso_date($employee->birthdate->format('Y-m-d')))
										<div>{{ $employee->birthdate->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->birthdate) }} old)</em></small></div>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Gender</label>
									<div>{{ check_gender($employee->gender, true) }}</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Civil Status</label>
									<div>{{ check_civil_status($employee->civstatus) }}</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Religion</label>
									<div><?=isset($employee->religion)?$employee->religion->descriptor:'-';?></div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Birth Place</label>
									<div>{{ $employee->birthplace }}</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Height</label>
									<div>{{ $employee->height() }}m <em>{{ isset(config('giligans.meter_to_feet')[$employee->height])?'('.config('giligans.meter_to_feet')[$employee->height].'")':'' }}</em></div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Weight</label>
									<div>{{ $employee->weight() }}kg</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Hobbies</label>
									<div>{{ $employee->hobby }}</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Notes</label>
									<div>{{ $employee->notes }}</div>
								</div>
							</div>
						</div>
					</div><!-- end: .panel-body -->
				</div><!-- end: .panel.panel-primary -->
	    </div><!-- end: .tab-pane -->

	    <div role="tabpanel" class="tab-pane <?=request()->input('tab')=='family'?'active':'';?>" id="family">
	    	<div class="panel panel-primary">
				  <div class="panel-heading">Spouse</div>
				  <div class="panel-body">
				  @if(isset($employee->spouse))
				  	<div class="row">
	    				<div class="col-md-4">
								<div class="form-group">
									<label>Fullname</label>
									<div>{{ $employee->spouse->lastname }}, {{ $employee->spouse->firstname }} {{ $employee->spouse->middlename }}</div>
								</div>	
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Birthday</label>
									@if(is_iso_date($employee->spouse->birthdate->format('Y-m-d')))
										<div>{{ $employee->spouse->birthdate->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->spouse->birthdate) }} old)</em></small></div>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Mobile</label>
									<div>{{ $employee->spouse->mobile }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Address</label>
									<div>{{ $employee->spouse->address }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Email</label>
									<div>{{ $employee->spouse->email }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Phone</label>
									<div>{{ $employee->spouse->phone }}</div>
								</div>
							</div>
						</div>
				  @endif
				  </div><!-- end: .panel-body -->
				</div><!-- end: .panel.panel-default -->
	    	<div class="panel panel-primary">
				  <div class="panel-heading">Children</div>
				  <div class="panel-body">
						@if(isset($employee->childrens) && count($employee->childrens)>0)
						<div class="table-responsive">
							<table class="table table-condensed">
								<tbody>
									@foreach($employee->childrens as $k => $c)
									<tr>
										<td>{{ ($k+1) }}. {{ $c->lastname }}, {{ $c->firstname }} {{ $c->middlename }}</td>
										<td>{{ check_gender($c->gender) }}</td>
										<td>
											@if(is_iso_date($c->birthdate->format('Y-m-d')))
												<div>{{ $c->birthdate->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($c->birthdate) }} old)</em></small></div>
											@endif
										</td>
										<td>
											@if(isset($c->acadlevel))
												{{ $c->acadlevel->descriptor }}
											@endif
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						@endif
					</div><!-- end: .panel-body -->
				</div><!-- end: .panel.panel-default -->
				<div class="panel panel-primary">
				  <div class="panel-heading">Emergency Contact</div>
				  <div class="panel-body">
				  @if(isset($employee->ecperson))
						<div class="row">
	    				<div class="col-md-4">
								<div class="form-group">
									<label>Fullname</label>
									<div>{{ $employee->ecperson->lastname }}, {{ $employee->ecperson->firstname }} {{ $employee->ecperson->middlename }}</div>
								</div>	
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Relationship</label>
									<div>{{ $employee->ecperson->relation }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Mobile</label>
									<div>{{ $employee->ecperson->mobile }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Address</label>
									<div>{{ $employee->ecperson->address }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Email</label>
									<div>{{ $employee->ecperson->email }}</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Phone</label>
									<div>{{ $employee->ecperson->phone }}</div>
								</div>
							</div>
						</div>
				  @endif
				  </div><!-- end: .panel-body -->
				</div><!-- end: .panel.panel-default -->
	    </div><!-- end: .tab-pane -->
	    <div role="tabpanel" class="tab-pane <?=request()->input('tab')=='workedu'?'active':'';?>" id="workeduc">
	    	<div class="panel panel-primary">
				  <div class="panel-heading">Work Experinces</div>
				  <div class="panel-body">
						@if(isset($employee->workexps) && count($employee->workexps)>0)
						<div class="table-responsive">
							<table class="table table-condensed">
								<tbody>
									@foreach($employee->workexps as $k => $w)
									<tr>
										<td>
											@if(!$w->getPeriodFrom())
												*											
											@else
												{{ $w->getPeriodFrom()->format('M Y') }}
											@endif
											-
											@if(!$w->getPeriodTo())
												*											
											@else
												{{ $w->getPeriodTo()->format('M Y') }}
											@endif

											@if($w->getPeriodFrom() instanceof \Carbon\Carbon && $w->getPeriodTo() instanceof \Carbon\Carbon)
												<small class="text-muted">
													<?php
														$m =$w->getPeriodTo()->diffInMonths($w->getPeriodFrom());
													?>
												<em>
													({{ $m }} month<?=$m>1?'s':''?>)
												</em>
											</small>
											@endif
										</td>
										<td>{{ $w->company }}</td>
										<td>{{ $w->position }}</td>
										<td>{{ $w->remarks }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						@endif
				  </div><!-- end: .panel-body -->
				</div><!-- end: .panel.panel-default -->
				<div class="panel panel-primary">
				  <div class="panel-heading">Education</div>
				  <div class="panel-body">
						@if(isset($employee->educations) && count($employee->educations)>0)
						<div class="table-responsive">
							<table class="table table-condensed">
								<tbody>
									@foreach($employee->educations as $k => $e)
									<tr>
										<td>
											@if(!$e->getPeriodFrom())
												*											
											@else
												{{ $e->getPeriodFrom()->format('M Y') }}
											@endif
											-
											@if(!$e->getPeriodTo())
												*											
											@else
												{{ $e->getPeriodTo()->format('M Y') }}
											@endif

											@if($e->getPeriodFrom() instanceof \Carbon\Carbon && $e->getPeriodTo() instanceof \Carbon\Carbon)
												<small class="text-muted">
												<em>
													<?php
														$i = ($e->getPeriodTo()->year - $e->getPeriodFrom()->year);
													?>
													({{ $i }} year<?=$i>1?'s':''?>)
												</em>
											</small>
											@endif
										</td>
										<td>
										@if(isset($e->acadlevel))
											{{ $e->acadlevel->descriptor }}
										@endif
										</td>
										<td>{{ $e->school }}</td>
										<td>{{ $e->course }}</td>
										<td>{{ $e->remarks }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						@endif
				  </div><!-- end: .panel-body -->
				</div><!-- end: .panel.panel-default -->
	    </div><!-- end: .tab-pane -->
	  </div><!-- end: .tab-content -->
	</div><!-- end: .tab-wrap -->

				
				
	</div>
</div>
	
@endsection