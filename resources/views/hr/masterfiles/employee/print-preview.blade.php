
<!DOCTYPE html>
<html>
<?php
 $employee->load(['company', 'branch', 'position', 'department', 'religion', 'spouse', 'childrens.acadlevel', 'ecperson', 'workexps', 'educations', 'statutory']);
?>
<head>
	<title>{{ $employee->code }} {{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middlename }} ({{ $employee->branch->code }})</title>
</head>

<style type="text/css">
* {
	margin: 0;
	padding: 0;
}

table td.nbtl {
    border-top: 1px solid #fff;
    border-left: 1px solid #fff;
    border-bottom: 1px solid #fff;
}

.text-right {
	text-align: right;
}

.prn {
	cursor: pointer;
	padding: 6px 12px;
	border: 1px solid #ccc;
	margin: 10px;
	display: inline-block;
	border-radius: 4px;
	text-decoration: none;
	color: #000;
}

.prn:hover {
	/*
	color: #333;
  background-color: #d4d4d4;
  border-color: #8c8c8c;
  border-color: #8c8c8c;
  */
  color: #fff; 
  background-color: #428bca;"
}

.container {
	border: 1px solid #ccc;
	margin: 0 10px;
}

@media print {
 table {
 
 	font-size: 13px;
 }

 table td {
	padding: 2px;
 }
	
 .container {
	border: 0;
	margin: 0;
 }
 .prn {
 	display: none;
 }
}



.container-body {
	margin: 10px;
}

label {
	font-weight: bold;
}

.panel-header {
	color: #fff;
  background-color: #337ab7;
  border-color: #337ab7;
  padding: 5px;
	margin-top: 10px;
}
</style>
<body>

<a class="prn" href="javascript:window.print();">Print</a>


<div class="container">
	<div class="container-body">
		<table>
			<tbody>
				<tr>
					<td>
						<img src="{{ $employee->getPhotoUrl() }}" style="margin-right: 5px; width: 100px;" class="img-responsive">
					</td>
					<td>
						<h3 class="text-success" style="margin-top: 10px;">
							{{ $employee->lastname }}, {{ $employee->firstname }} {{ $employee->middlename }} 
						</h3>
						<p>
							{{ $employee->code }} 
						</p>
						@if(isset($employee->position))
						<div>
								{{ $employee->position->descriptor }}
						</div>
						@endif
						@if(isset($employee->branch))
						<div>
				  		
								{{ $employee->branch->code }} - {{ $employee->branch->descriptor }}
				  		
						</div>
						@endif
						@if(isset($employee->company))
						
						<div class="hidden-xs">
								{{ $employee->company->descriptor }}
						</div>
						@endif
					</td>
				</tr>
			</tbody>
		</table>

		<div class="panel-header">Employment Details</div>
		<table style="width: 100%; margin-top: 5px;">
			<tbody>
				<tr>
					<td style="width:25%">
						<div>
							<label>Emp Status</label>
							<div>{{ emp_status($employee->empstatus) }} <small class="text-muted">(<?=$employee->isActive()?'<span style="color: green;">Active</span>':'Inactive';?>)</small></div>
						</div>
					</td>
					<td style="width:25%">
						@if(is_iso_date($employee->datestart->format('Y-m-d')))
						<div class="form-group">
							<label>Started</label>
							<div>{{ $employee->datestart->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->datestart) }} ago)</em></small></div>
						</div>
						@endif
					</td>
					<td style="width:25%">
						<div class="form-group">
							<label>Hired</label>
								<div>
									@if(is_iso_date($employee->datehired->format('Y-m-d')) && $employee->empstatus>=2)
										{{ $employee->datehired->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->datehired) }} ago)</em></small>
									@endif
									&nbsp;
								</div>
						</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
							<label>Regularized</label>
							<div>
							@if($employee->statutory && !is_null($employee->statutory->date_reg))
					  		@if(is_iso_date($employee->statutory->date_reg->format('Y-m-d')) && $employee->empstatus>=3)
											{{ $employee->statutory->date_reg->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->statutory->date_reg) }} ago)</em></small>
								@endif
							@endif
							&nbsp;
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
							<label>Pay Type</label>
							<div>{{ emp_paytype($employee->paytype) }}</div>
						</div>
					</td>
					<td>
						<div class="form-group">
							<label>Rate Type</label>
							<div>{{ emp_ratetype2($employee->ratetype) }}</div>
						</div>
					</td>
					<td>
						<div class="form-group">
							<label>Basic Rate</label>
							<div>{{ number_format($employee->rate, 2) }}</div>
						</div>
					</td>
					<td>
						<div class="form-group">
							<label>ECOLA</label>
							<div>{{ number_format($employee->ecola, 2) }}</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
							<label>Allowance 1</label>
							<div>{{ number_format($employee->allowance1, 2) }}</div>
						</div>
					</td>
					<td>
						<div class="form-group">
							<label>Allowance 2</label>
							<div>{{ number_format($employee->allowance2, 2) }}</div>
						</div>
					</td>
					<td>
						<div class="form-group">
							<label>Meal</label>
							<div>{{ $employee->statutory ? nf($employee->statutory->meal):'0.00' }}&nbsp;</div>
						</div>
					</td>
					<td>
						@if(is_iso_date($employee->datestop->format('Y-m-d')) && $employee->empstatus<=6)
						<div class="form-group">
							<label>{{ $employee->empstatus==5 ? 'Termninated':'Resigned' }}</label>
							<div>{{ $employee->datestop->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->datestop) }} ago)</em></small></div>
						</div>
						@endif
					</td>
				</tr>
			</tbody>
		</table>

		<div class="panel-header">Contribution Details</div>
		<table style="width: 100%; margin-top: 5px;">
			<tbody>
				<tr>
					<td style="width:25%">
						<div class="form-group">
									<label>SSS #</label>
									<div>{{ $employee->sssno() }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Employee Share</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->ee_sss):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Employer Share</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->er_sss):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Tag</label>
									<div>{{ $employee->statutory ? ($employee->statutory->sss_tag==1?'Y':'N'):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
									<label>PhilHealth #</label>
									<div>{{ $employee->phicno }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
							<label>&nbsp;</label>
							<div>{{ $employee->statutory ? nf($employee->statutory->ee_phic):'&nbsp;' }}&nbsp;</div>
						</div>
					</td>
					<td>
						<div class="form-group">
							<label>&nbsp;</label>
							<div>{{ $employee->statutory ? nf($employee->statutory->er_phic):'&nbsp;' }}&nbsp;</div>
						</div>
					</td>
					<td>
						<div class="form-group">
									<label>&nbsp;</label>
									<div>{{ $employee->statutory ? ($employee->statutory->phic_tag==1?'Y':'N'):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
									<label>Pag Ibig #</label>
									<div>{{ $employee->hdmfno() }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>&nbsp;</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->ee_hdmf):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>&nbsp;</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->er_hdmf):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>&nbsp;</label>
									<div>{{ $employee->statutory ? ($employee->statutory->hdmf_tag==1?'Y':'N'):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
									<label>TIN #</label>
									<div>{{ $employee->tin() }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>W/Tax</label>
									<div>{{ $employee->statutory ? nf($employee->statutory->wtax):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
					<td></td>
					<td>
						<div class="form-group">
									<label>&nbsp;</label>
									<div>{{ $employee->statutory ? ($employee->statutory->wtax_tag==1?'Y':'N'):'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="panel-header">Personal Info</div>
		<table style="width: 100%; margin-top: 5px;">
			<tbody>
				<tr>
					<td colspan="4">
						<div class="form-group">
									<label>Address</label>
									<div>{{ $employee->address }}</div>
								</div>
					</td>
				</tr>
				
				<tr>
					<td><div class="form-group">
									<label>Gender</label>
									<div>{{ check_gender($employee->gender, true) }}&nbsp;</div>
								</div></td>
					<td>
						<div class="form-group">
									<label>Civil Status</label>
									<div>{{ check_civil_status($employee->civstatus) }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Birthday</label>
									@if(is_iso_date($employee->birthdate->format('Y-m-d')))
										<div>{{ $employee->birthdate->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->birthdate) }} old)</em></small></div>
									@endif
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Birth Place</label>
									<div>{{ $employee->birthplace }}&nbsp;</div>
								</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="form-group">
									<label>Religion</label>
									<div><?=isset($employee->religion)?$employee->religion->descriptor:'-';?></div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Height</label>
									<div>{{ $employee->height() }}m <em>{{ isset(config('giligans.meter_to_feet')[$employee->height])?'('.config('giligans.meter_to_feet')[$employee->height].'")':'' }}</em></div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Weight</label>
									<div>{{ $employee->weight() }} {{ $employee->weight()>=90 ? 'lbs':'kgs' }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Email</label>
									<div>{{ strtolower($employee->email) }}&nbsp;</div>
								</div>
					</td>
				</tr>
				<tr>
					<td style="width:25%">
						<div class="form-group">
									<label>Mobile</label>
									<div>{{ $employee->mobile }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Phone</label>
									<div>{{ $employee->phone }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Fax</label>
									<div>{{ $employee->fax }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Fax</label>
									<div>{{ $employee->fax }}&nbsp;</div>
								</div>
					</td>
				</tr>
				<tr>
					<td style="width:25%">
						<div class="form-group">
									<label>Uniform</label>
									<div>{{ $employee->statutory ? $employee->statutory->uniform:'&nbsp;' }}&nbsp;</div>
								</div>
					</td>
					<td colspan="3">
						<div class="form-group">
									<label>Notes</label>
									<div>{{ $employee->notes }}&nbsp;</div>
								</div>
							</div>
					</td>
					
				</tr>
			</tbody>
		</table>

		<div class="panel-header">Spouse</div>
		@if(isset($employee->spouse))
		<table style="width: 100%; margin-top: 5px;">
			<tbody>
				<tr>
					<td style="width:50%" colspan="2">
						<div class="form-group">
									<label>Fullname</label>
									<div>{{ $employee->spouse->lastname }}, {{ $employee->spouse->firstname }} {{ $employee->spouse->middlename }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Birthday</label>
									@if(is_iso_date($employee->spouse->birthdate->format('Y-m-d')))
										<div>{{ $employee->spouse->birthdate->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($employee->spouse->birthdate) }} old)</em></small>&nbsp;
										</div>
									@endif
									
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Mobile</label>
									<div>{{ $employee->spouse->mobile }}&nbsp;</div>
								</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="form-group">
									<label>Address</label>
									<div>{{ $employee->spouse->address }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Email</label>
									<div>{{ $employee->spouse->email }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Phone</label>
									<div>{{ $employee->spouse->phone }}&nbsp;</div>
								</div>
					</td>
				</tr>
			</tbody>
		</table>
		@endif

		<div class="panel-header">Children</div>
		@if(isset($employee->childrens) && count($employee->childrens)>0)
		<table style="width: 100%; margin-top: 5px;">
			<tbody>
				@foreach($employee->childrens as $k => $c)
				<tr>
					<td>{{ ($k+1) }}. {{ $c->lastname }}, {{ $c->firstname }} {{ $c->middlename }}</td>
					<td>
						@if(is_iso_date($c->birthdate->format('Y-m-d')))
							<div>{{ $c->birthdate->format('M j, Y') }} <small class="text-muted"><em>({{ diffForHumans($c->birthdate) }} old)</em></small></div>
						@endif
					</td>
					<td>{{ check_gender($c->gender, true) }}</td>
					
				</tr>
				@endforeach
			</tbody>
		</table>
		@endif

		<div class="panel-header">Emergency Contact</div>
		@if(isset($employee->spouse))
		<table style="width: 100%; margin-top: 5px;">
			<tbody>
				<tr>
					<td style="width:50%" colspan="2">
						<div class="form-group">
									<label>Fullname</label>
									<div>{{ $employee->ecperson->lastname }}, {{ $employee->ecperson->firstname }} {{ $employee->ecperson->middlename }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Relationship</label>
									<div>{{ $employee->ecperson->relation }}&nbsp;</div>
								</div>
					</td>
					<td style="width:25%">
						<div class="form-group">
									<label>Mobile</label>
									<div>{{ $employee->ecperson->mobile }}&nbsp;</div>
								</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="form-group">
									<label>Address</label>
									<div>{{ $employee->ecperson->address }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Email</label>
									<div>{{ $employee->ecperson->email }}&nbsp;</div>
								</div>
					</td>
					<td>
						<div class="form-group">
									<label>Phone</label>
									<div>{{ $employee->ecperson->phone }}&nbsp;</div>
								</div>
					</td>
				</tr>
			</tbody>
		</table>
		@endif

		<div class="panel-header">Work Experience</div>
		@if(isset($employee->workexps) && count($employee->workexps)>0)
		<table style="width: 100%; margin-top: 5px;">
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
		@endif
		
		<div class="panel-header">Educational Attainment</div>
		@if(isset($employee->educations) && count($employee->educations)>0)
		<table style="width: 100%; margin-top: 5px;">
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
		@endif


	</div>
</div>

</body>
</html>