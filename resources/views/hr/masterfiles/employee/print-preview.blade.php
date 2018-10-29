
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
	color: #333;
  background-color: #d4d4d4;
  border-color: #8c8c8c;
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
									@else
									&nbsp;
									@endif
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
								@else
								&nbsp;
								@endif
							@endif
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
									<div>{{ $employee->statutory ? nf($employee->statutory->wtax):'&nbsp;' }}</div>
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

		<div class="panel-header">Contribution Details</div>
		<table style="width: 100%; margin-top: 5px;">
			<tbody>
				<tr>
					<td style="width:25%"></td>
					<td style="width:25%"></td>
					<td style="width:25%"></td>
					<td style="width:25%"></td>
				</tr>
			</tbody>
		</table>

		<div class="panel-header">Contribution Details</div>
		<table style="width: 100%; margin-top: 5px;">
			<tbody>
				<tr>
					<td style="width:25%"></td>
					<td style="width:25%"></td>
					<td style="width:25%"></td>
					<td style="width:25%"></td>
				</tr>
			</tbody>
		</table>
	
		
	</div>
</div>

</body>
</html>