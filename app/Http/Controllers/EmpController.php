<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\DateRange;
use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepository as EmployeeRepo;
use App\Helpers\EmpCreator;
use App\Models\Employee;
use MoneyToWords\MoneyToWordsConverter as Convert;


class EmpController extends Controller
{

	protected $dr;
	protected $employee;

	public function __construct(EmployeeRepo $employeeRepo) {
		$this->employee = $employeeRepo;
	}






	public function exportByManNo($man_no, $ext='MAS', $cmd=NULL) {
		//$cmd->info('Hello from EmpController');

		$emp = $this->employee->findByField('code', $man_no)->first();

		if (is_null($emp)) {
			//$cmd->info('Employee not found');
			throw new \Exception("Employee not found", 1);
			return NULL;
		} else {
			//$cmd->info($emp->lastname);
		}



		$attrs = $this->toDbfAttributes($emp, $cmd);


		try {
			$e = new EmpCreator($attrs, $ext, $cmd);
		} catch (\Exception $e) {
			return $e->getMessage();
		}
		return $e->file_path;

		//foreach ($e->pay_mast_fields() as $key => $value) {
		foreach ($e->get_fields() as $key => $value) {
			//$cmd->info($value);
		}


		//$cmd->info($e->file_path);



		//$cmd->info(json_encode($e->get_fields()));
	}


	private function toDbfAttributes(Employee $employee, $cmd) {


		//$cmd->info('RATE:'. $employee->rate);
		//$cmd->info('DEPT:'. $employee->department->code);
		//$cmd->info('DATE:'. json_encode(is_iso_date($employee->datestart->format('Y-m-d'))));

		$position = isset($employee->position->descriptor)
			? $employee->position->descriptor
			: '';

		$brcode = isset($employee->branch->code)
			? $employee->branch->code
			: '';

		$company = isset($employee->company->descriptor)
			? $employee->company->descriptor
			: '';

		$comp_add = isset($employee->company->address)
			? $employee->company->address
			: '';

		$religion = isset($employee->religion->descriptor)
			? $employee->religion->descriptor
			: '';

		$spouse = isset($employee->spouse->lastname) || isset($employee->spouse->firstname)
			? $employee->spouse->firstname.' '.$employee->spouse->lastname
			: '';

		if (isset($employee->ecperson->firstname) || isset($employee->ecperson->lastname) 
		|| isset($employee->ecperson->mobile) || isset($employee->ecperson->phone)) {

			$emer_name = $employee->ecperson->firstname.' '.$employee->ecperson->lastname;
			$emer_no = $employee->ecperson->mobile;

			if (!empty($employee->ecperson->phone)) 
				$emer_no = $emer_no.'/'.$employee->ecperson->phone;

		} else { 
			$emer_no = '';
			$emer_name = '';
		}
			

		$wh = [];
		$works = $employee->workexps()->get();

		//$cmd->info('WORK:'. count($works));
		//$cmd->info('ID:'. $employee->id);

		for ($i=0; $i<4; $i++) { 
			if (count($works)>$i)
				$wh[$i] = substr($works[$i]->position.', '.$works[$i]->company, 0, 40);
			else
				$wh[$i] = ' ';
		}


		$educs = $employee->educations()->get();
		$educ = '';
		$educ_idx = ['MBA', 'GS', 'TS', 'TEC', 'HS', 'ELE', 'TOD', 'INF'];
		//$cmd->info('EDUCS:'. count($educs));
		$exit = false;
		//if (count($educs)==1)
		if (count($educs)>0) {
			foreach ($educ_idx as $e) {
				if ($exit)
					continue;
				else {
					foreach ($educs as $key => $value) {
						//$cmd->info($value->acadlevel->code);
						if (count($educs)==1 && $key==0) {
							$educ = $value->course.', '.$value->school;
							$exit = true;
							continue;
						}

						if (isset($value->acadlevel->code) && $e == $value->acadlevel->code) {
							//$cmd->info('CODE:'. $value->acadlevel->code);
							//$cmd->info('EDUC:'. $value->course.', '.$value->school);
							$educ = $value->course.', '.$value->school;
							$exit = true;
							continue;
						}
					}
				}
			}
		} else {
			$educ = 'N\A';
		}
		//$cmd->info('EDUC:'. $educ);
		
		

		$mid = c(c()->format('Y-m').'-15');
		$paydate = $mid->gt(c()) ? $mid : c()->endOfMonth();

		if (isset($employee->rate)) {
			$r = explode('.', $employee->rate);
			//$cmd->info('Rate:'. $r[0]);
			$converter = new Convert($r[0], 'Pesos');
			$sal_word = up(substr($converter->Convert(), 0, -5)).' & '.$r[1].'/100';
			//$cmd->info('Rate Word:'.$sal_word);
		}


		$c = [];
		
		$childrens = $employee->childrens()->get();

		//$cmd->info('CHILDRENS:'. count($childrens));

		$child_count = is_null($childrens) ? 0 : count($childrens);
		//$cmd->info('ID:'. $employee->id);

		if ($child_count==1) {
			$c1 = $childrens->first();
			$c[0] = $c1->firstname;
			$c[1] = '';

		} elseif ($child_count==2) {
			foreach ($childrens as $key => $value) {
				$c[$key] = $value->firstname;
			}
		} elseif ($child_count>2) {
			$ci = '';
			foreach ($childrens as $key => $value) {
				if ($key==0)
					$ci = $value->firstname;
				else
					$ci = $ci.', '.$value->firstname;
			}
			$c[0] = substr($ci, 0, 25);
			$c[1] = substr($ci, 25, 25);
		} else {
			$c[0] = '';
			$c[1] = '';
		}

		
		$sss_ee			= number_format(0, 2, '.', ''); //['SSS_EE', 	'N', 10,2],
		$sss_er			= number_format(0, 2, '.', ''); //['SSS_ER', 	'N', 10,2],
		$ph_ee			= number_format(0, 2, '.', ''); //['PH_EE', 	'N', 10,2],
		$ph_er			= number_format(0, 2, '.', ''); //['PH_ER', 	'N', 10,2],
		$pbig_ee		= number_format(0, 2, '.', ''); //['PBIG_EE', 	'N', 10,2],
		$pbig_er		= number_format(0, 2, '.', ''); //['PBIG_ER', 	'N', 10,2],
		$wtax				= number_format(0, 2, '.', ''); //['WTAX', 	'N', 10,2],
		$tax_ee			= number_format(0, 2, '.', ''); //['TAX_EE', 	'N', 10,2],
		$tax_er			= number_format(0, 2, '.', ''); //['WTTAX_ERAX', 	'N', 10,2],

		if (!is_null($employee->statutory)) {
			$sss_ee			= $employee->statutory->ee_sss;
			$sss_er			= $employee->statutory->er_sss;
			$ph_ee			= $employee->statutory->ee_phic;
			$ph_er			= $employee->statutory->er_phic;
			$pbig_ee		= $employee->statutory->ee_hdmf;
			$pbig_er		= $employee->statutory->er_hdmf;
			$wtax				= $employee->statutory->wtax;
			$tax_ee			= $employee->statutory->ee_tin;
			$tax_er			= $employee->statutory->er_tin;
		}


		return [
			'SEQ_NO' 			=> number_format(0,2), //['SEQ_NO', 		'N', 3,0],	
			'MAN_NO'			=> substr($employee->code, 0, 6), //['MAN_NO', 		'C', 6],	
			'LAST_NAM'		=> up(substr($employee->lastname, 0, 20)), //['LAST_NAM', 	'C', 20],	
			'FIRS_NAM'		=> up(substr($employee->firstname, 0, 20)),	//['FIRS_NAM', 	'C', 20],	
			'MI'					=> up(substr($employee->middlename, 0, 1)),	//['MI', 				'C', 2],	
			'MIDL_NAM'		=> up(substr($employee->middlename, 0, 20)), //['MIDL_NAM', 	'C', 20],	
			'HIRED'				=> $employee->datehired->format('Ymd'), //['HIRED', 		'D'],	
			'EMP_STUS' 		=> substr(emp_status($employee->empstatus), 0, 20), //['EMP_STUS', 	'C', 9],	
			'STATUS_TAG'	=> ' ', //['STATUS_TAG','C', 1],	
			'RATE_HR'			=> number_format($employee->rate, 2, '.', ''), //['RATE_HR', 	'N', 8,2],	
			'ECOL_RATE'		=> number_format($employee->ecola, 2, '.', ''), //['ECOL_RATE', 	'N', 8,2],	
			'ALW1_RATE'		=> number_format($employee->allowance1, 2, '.', ''), //['ALW1_RATE', 	'N', 8,2],	
			'ALW2_RATE'		=> number_format($employee->allowance2, 2, '.', ''), //['ALW2_RATE', 	'N', 8,2],	
			'CA_BAL'			=> number_format(0, 2, '.', ''), //['CA_BAL', 	'N', 10,2],	
			'CHIT_BAL'		=> number_format(0, 2, '.', ''), //['CHIT_BAL', 	'N', 12,2],	
			'MTD_GRS'			=> number_format(0, 2, '.', ''), //['MTD_GRS', 	'N', 12,2],	
			'YTD_GRS'			=> number_format(0, 2, '.', ''), //['YTD_GRS', 	'N', 12,2],	
			'MTD_BSC'			=> number_format(0, 2, '.', ''), //['MTD_BSC', 	'N', 12,2],	
			'YTD_BSC'			=> number_format(0, 2, '.', ''), //['YTD_BSC', 	'N', 12,2],	
			'YTD_TAX'			=> number_format(0, 2, '.', ''), //['YTD_TAX', 	'N', 10,2],	
			'TAX_EXEM'		=> number_format(0, 2, '.', ''), //['TAX_EXEM', 	'N', 10,2],	
			'DEPT'				=> up(substr($employee->department->code, 0, 3)).'-'.emp_ratetype($employee->ratetype), //['DEPT', 	'C', 10],	
			'POSITION'		=> up(substr($position, 0, 12)), //['POSITION', 	'C', 12],	
			'BRANCH'			=> up(substr($brcode, 0, 3)), //['BRANCH', 	'C', 3],	
			'CO_NAME'			=> up(substr($company, 0, 50)), //['CO_NAME', 	'C', 50],	
			'CO_ADD1'			=> substr($comp_add, 0, 40), //['CO_ADD1', 	'C', 40],	
			'CO_ADD2'			=> substr($comp_add, 40, 40), //['CO_ADD2', 	'C', 40],	
			'TIN_ER'			=> substr($employee->company->tin, 0, 16), //['TIN_ER', 	'C', 14],	
			'PAY_DATE'	  => $paydate->format('Ymd'), //['PAY_DATE', 		'D'],	
			'RUN_TAG'			=> ' ', //['RUN_TAG','C', 1],	
			'SSS_NO'			=> substr($employee->sssno, 0, 14), //['SSS_NO', 	'C', 14],	
			'PHEALTH_NO'	=> substr($employee->phicno, 0, 14), //['PHEALTH_NO', 	'C', 14],	
			'PBIG_NO'			=> substr($employee->hdmfno, 0, 14), //['PBIG_NO', 	'C', 14],	
			'WTAX_NO'			=> substr($employee->tin, 0, 14), //['WTAX_NO', 	'C', 14],
			'SSS_TAG'			=> 'Y', //['SSS_TAG','C', 1],	
			'PH_TAG'			=> 'Y', //['PH_TAG','C', 1],	
			'PBIG_TAG'		=> 'Y', //['PBIG_TAG','C', 1],	
			'WTAX_TAG'		=> 'N', //['WTAX_TAG','C', 1],		
			'SSS_EE'			=> number_format($sss_ee, 2, '.', ''), //['SSS_EE', 	'N', 10,2],
			'SSS_ER'			=> number_format($sss_er, 2, '.', ''), //['SSS_ER', 	'N', 10,2],
			'PH_EE'				=> number_format($ph_ee, 2, '.', ''), //['PH_EE', 	'N', 10,2],
			'PH_ER'				=> number_format($ph_er, 2, '.', ''), //['PH_ER', 	'N', 10,2],
			'PBIG_EE'			=> number_format($pbig_ee, 2, '.', ''), //['PBIG_EE', 	'N', 10,2],
			'PBIG_ER'			=> number_format($pbig_er, 2, '.', ''), //['PBIG_ER', 	'N', 10,2],
			'WTAX'				=> number_format($wtax, 2, '.', ''), //['WTAX', 	'N', 10,2],
			'TAX_EE'			=> number_format($tax_ee, 2, '.', ''), //['TAX_EE', 	'N', 10,2],
			'TAX_ER'			=> number_format($tax_er, 2, '.', ''), //['WTTAX_ERAX', 	'N', 10,2],
			'POL_CLR'			=> ' ', //['POL_CLR', 'D'],		
			'NBI_CLR'			=> ' ', //['NBI_CLR', 'D'],		
			'HEALTH_CLR'	=> ' ', //['HEALTH_CLR', 'D'],		
			'STARTED'			=> is_iso_date($employee->datestart->format('Y-m-d')) ? $employee->datestart->format('Ymd') : '', //['STARTED', 		'D'],	
			'REGULARED'		=> is_iso_date($employee->datehired->format('Y-m-d')) ? $employee->datehired->format('Ymd') : '', //['REGULARED', 		'D'],	
			'RESIGNED'		=> is_iso_date($employee->datestop->format('Y-m-d')) ? $employee->datestop->format('Ymd') : '', //['REGULARED', 		'D'],	
			'WORKHIST1'		=> $wh[0], //['WORKHIST1', 'C', 40],	
			'WORKHIST2'		=> $wh[1], //['WORKHIST2', 'C', 40],	
			'WORKHIST3'		=> $wh[2], //['WORKHIST3', 'C', 40],	
			'WORKHIST4'		=> $wh[3], //['WORKHIST4', 'C', 40],	
			'BIRTHDATE'		=> is_iso_date($employee->birthdate->format('Y-m-d')) ? $employee->birthdate->format('Ymd') : '', //['BIRTHDATE', 'D'],
			'BIRTHPLC'		=> up(substr($employee->birthplace, 0, 10)), //	['BIRTHPLC', 	'C', 10],	
			'RELIGION'		=> up(substr($religion, 0, 10)), //	['RELIGION', 	'C', 10],	
			'SPOUS_NAM'		=> up(substr($spouse, 0, 32)), //['SPOUS_NAM', 'C', 32],	
			'ADDRESS1'		=> substr($employee->address, 0, 40), //['ADDRESS1', 	'C', 40],	
			'ADDRESS2'		=> substr($employee->address, 40, 40), //['ADDRESS2', 	'C', 40],	
			'ADDRESS3'		=> substr($employee->address, 80, 40), //['ADDRESS3', 	'C', 40],	
			'CEL'					=> up(substr($employee->mobile, 0, 40)), //['CEL', 			'C', 40],	
			'TEL'					=> up(substr($employee->phone, 0, 40)), //['TEL', 			'C', 40],	
			'EMAIL'				=> up(substr($employee->email, 0, 40)), //['EMAIL', 		'C', 40],	
			'SAL_WORDS'		=> up(substr('** '.$sal_word.' **', 0, 70)), //['SAL_WORDS', 'C', 70],	
			'EDUCATION'		=> substr($educ, 0, 40), //['EDUCATION', 'C', 40],	
			'DEPENDENTS'	=> number_format($child_count, 2, '.', ''), //[['DEPENDENTS','N', 2,0],	
			'CHILD_NO'		=> number_format($child_count, 2, '.', ''), //[['CHILD_NO',  'N', 2,0],	
			'CHILDREN1'		=> up($c[0]), //['CHILDREN1', 'C', 25],	
			'CHILDREN2'		=> up($c[1]), //['CHILDREN2', 'C', 25],	
			'SEX'					=> up(substr(check_gender($employee->gender), 0, 1)), //['SEX', 'C', 1],	
			'HEIGHT'			=> $employee->height, //['HEIGHT', 'C', 10],	
			'WEIGHT'			=> $employee->weight, //['WEIGHT', 'C', 10],	
			'CIV_STUS'		=> up(substr(check_civil_status($employee->civstatus), 0, 10)), //['CIV_STUS', 'C', 10],	
			'UNIFORM'			=> ' ',//['UNIFORM', 	'C', 10],	
			'EMER_NO'			=> substr($emer_no, 0, 16), //['EMER_NO', 	'C', 16],	
			'EMER_NAM'		=> substr($emer_name, 0, 20), //['EMER_NAM', 	'C', 20],	
			'HOBBIES'			=> substr($employee->hobby, 0, 40), //['HOBBIES', 	'C', 40],	
			'SP_NOTES1'		=> ' ', //['SP_NOTES1', 'C', 40],	
			'SP_NOTES2'		=> ' ', //['SP_NOTES2', 'C', 40],	
			'CA_PRIN'			=> number_format(0, 2, '.', ''), //['CA_PRIN', 	'N', 10,2],	
			'CA_RUND'			=> number_format(0, 2, '.', ''), //['CA_RUND', 	'N', 10,2],	
			'CA_CBAL'			=> number_format(0, 2, '.', ''), //['CA_CBAL', 	'N', 10,2],	
			'CA_DED'			=> number_format(0, 2, '.', ''), //['CA_DED', 	'N', 10,2],	
			'CA_NBAL'			=> number_format(0, 2, '.', ''), //['CA_NBAL', 	'N', 10,2],	
			'CA_DTYPE'		=> ' ', //['CA_DTYPE',  'C', 1],	
			'CHIT_PRIN'		=> number_format(0, 2, '.', ''), //['CHIT_PRIN', 'N', 10,2],	
			'CHIT_RUND'		=> number_format(0, 2, '.', ''), //['CHIT_RUND', 'N', 10,2],	
			'CHIT_CBAL'		=> number_format(0, 2, '.', ''), //['CHIT_CBAL', 'N', 10,2],	
			'CHIT_DED'		=> number_format(0, 2, '.', ''), //['CHIT_DED', 	'N', 10,2],	
			'CHIT_NBAL'		=> number_format(0, 2, '.', ''), //['CHIT_NBAL', 'N', 10,2],	
			'CHIT_DTYPE'	=> ' ', //['CHIT_DTYPE','C', 1],	
			'MEAL_PRIN'		=> number_format(0, 2, '.', ''), //['MEAL_PRIN', 'N', 10,2],	
			'MEAL_RUND'		=> number_format(0, 2, '.', ''), //['MEAL_RUND', 'N', 10,2],	
			'MEAL_CBAL'		=> number_format(0, 2, '.', ''), //['MEAL_CBAL', 'N', 10,2],	
			'MEAL_DED'		=> number_format(0, 2, '.', ''), //['MEAL_DED',  'N', 10,2],	
			'MEAL_NBAL'		=> number_format(0, 2, '.', ''), //['MEAL_NBAL', 'N', 10,2],	
			'MEAL_DTYPE'	=> ' ', //['MEAL_DTYPE','C', 1],	
			'STR_DED1'		=> ' ', //['STR_DED1', 	'C', 12],	
			'OTD1_DATE'		=> ' ', //['OTD1_DATE', 'D'],	
			'OTD1_PRIN'		=> number_format(0, 2, '.', ''), //['OTD1_PRIN', 'N', 10,2],	
			'OTD1_RUND'		=> number_format(0, 2, '.', ''), //['OTD1_RUND', 'N', 10,2],	
			'OTD1_CBAL'		=> number_format(0, 2, '.', ''), //['OTD1_CBAL', 'N', 10,2],	
			'OTD1_DED'		=> number_format(0, 2, '.', ''), //['OTD1_DED',  'N', 10,2],	
			'OTD1_NBAL'		=> number_format(0, 2, '.', ''), //['OTD1_NBAL', 'N', 10,2],	
			'OTD1_DTYPE'	=> ' ', //['OTD1_DTYPE','C', 1],	
			'STR_DED2'		=> ' ', //['STR_DED2',  'C', 12],	
			'OTD2_DATE'		=> ' ', //['OTD2_DATE', 'D'],	
			'OTD2_PRIN'		=> number_format(0, 2, '.', ''), //['OTD2_PRIN', 'N', 10,2],	
			'OTD2_RUND'		=> number_format(0, 2, '.', ''), //['OTD2_RUND', 'N', 10,2],	
			'OTD2_CBAL'		=> number_format(0, 2, '.', ''), //['OTD2_CBAL', 'N', 10,2],	
			'OTD2_DED'		=> number_format(0, 2, '.', ''), //['OTD2_DED',  'N', 10,2],	
			'OTD2_NBAL'		=> number_format(0, 2, '.', ''), //['OTD2_NBAL', 'N', 10,2],	
			'OTD2_DTYPE'	=> ' ', //['OTD2_DTYPE','C', 1],	
			'STR_DED3'		=> ' ', //['STR_DED3',  'C', 12],	
			'OTD3_DATE'		=> ' ', //['OTD3_DATE', 'D'],	
			'OTD3_PRIN'		=> number_format(0, 2, '.', ''), //['OTD3_PRIN', 'N', 10,2],	
			'OTD3_RUND'		=> number_format(0, 2, '.', ''), //['OTD3_RUND', 'N', 10,2],	
			'OTD3_CBAL'		=> number_format(0, 2, '.', ''), //['OTD3_CBAL', 'N', 10,2],	
			'OTD3_DED'		=> number_format(0, 2, '.', ''), //['OTD3_DED',  'N', 10,2],	
			'OTD3_NBAL'		=> number_format(0, 2, '.', ''), //['OTD3_NBAL', 'N', 10,2],	
			'OTD3_DTYPE'	=> ' ', //['OTD3_DTYPE', 'C', 1],	
			'REC1_DATE'		=> ' ', //['REC1_DATE', 'D'],	
			'REC1_PRIN'		=> number_format(0, 2, '.', ''), //['REC1_PRIN', 'N', 10,2],	
			'REC1_RUND'		=> number_format(0, 2, '.', ''), //['REC1_RUND', 'N', 10,2],	
			'REC1_CBAL'		=> number_format(0, 2, '.', ''), //['REC1_CBAL', 'N', 10,2],	
			'REC1_DED'		=> number_format(0, 2, '.', ''), //['REC1_DED',  'N', 10,2],	
			'REC1_NBAL'		=> number_format(0, 2, '.', ''), //['REC1_NBAL', 'N', 10,2],	
			'REC1_DTYPE'	=> ' ', //['REC1_DTYPE', 'C', 1],	
			'REC2_DATE'		=> ' ', //['REC2_DATE', 'D'],	
			'REC2_PRIN'		=> number_format(0, 2, '.', ''), //['REC2_PRIN', 'N', 10,2],	
			'REC2_RUND'		=> number_format(0, 2, '.', ''), //['REC2_RUND', 'N', 10,2],	
			'REC2_CBAL'		=> number_format(0, 2, '.', ''), //['REC2_CBAL', 'N', 10,2],	
			'REC2_DED'		=> number_format(0, 2, '.', ''), //['REC2_DED',  'N', 10,2],	
			'REC2_NBAL'		=> number_format(0, 2, '.', ''), //['REC2_NBAL', 'N', 10,2],	
			'REC2_DTYPE'	=> ' ', //['REC2_DTYPE','C', 1],	
			'KL_DATE'		=> ' ', //['KL_DATE', 	'D'],	
			'KL_PRIN'		=> number_format(0, 2, '.', ''), //['KL_PRIN', 	'N', 10,2],	
			'KL_RUND'		=> number_format(0, 2, '.', ''), //['KL_RUND', 	'N', 10,2],	
			'KL_CBAL'		=> number_format(0, 2, '.', ''), //['KL_CBAL', 	'N', 10,2],	
			'KL_DED'		=> number_format(0, 2, '.', ''), //['KL_DED', 		'N', 10,2],	
			'KL_NBAL'		=> number_format(0, 2, '.', ''), //['KL_NBAL', 	'N', 10,2],	
			'KL_DTYPE'	=> ' ', //['KL_DTYPE', 	'C', 1],	
			'BF_DATE'		=> ' ', //['BF_DATE', 	'D'],	
			'BF_PRIN'		=> number_format(0, 2, '.', ''), //['BF_PRIN', 	'N', 10,2],	
			'BF_RUND'		=> number_format(0, 2, '.', ''), //['BF_RUND', 	'N', 10,2],	
			'BF_CBAL'		=> number_format(0, 2, '.', ''), //['BF_CBAL', 	'N', 10,2],	
			'BF_DED'		=> number_format(0, 2, '.', ''), //['BF_DED', 		'N', 10,2],	
			'BF_NBAL'		=> number_format(0, 2, '.', ''), //['BF_NBAL', 	'N', 10,2],	
			'BF_DTYPE'	=> ' ', //['BF_DTYPE',  'C', 1],	
			'SL1_DATE'	=> ' ', //['SL1_DATE',  'D'],	
			'SL1_PRIN'	=> number_format(0, 2, '.', ''), //['SL1_PRIN', 	'N', 10,2],	
			'SL1_RUND'	=> number_format(0, 2, '.', ''), //['SL1_RUND', 	'N', 10,2],	
			'SL1_CBAL'	=> number_format(0, 2, '.', ''), //['SL1_CBAL', 	'N', 10,2],	
			'SL1_DED'		=> number_format(0, 2, '.', ''), //['SL1_DED', 	'N', 10,2],	
			'SL1_NBAL'	=> number_format(0, 2, '.', ''), //['SL1_NBAL', 	'N', 10,2],	
			'SL1_DTYPE'	=> ' ', //['SL1_DTYPE', 'C', 1],	
			'SL2_DATE'	=> ' ', //['SL2_DATE', 	'D'],	
			'SL2_PRIN'		=> number_format(0, 2, '.', ''), //['SL2_PRIN', 	'N', 10,2],	
			'SL2_RUND'		=> number_format(0, 2, '.', ''), //['SL2_RUND', 	'N', 10,2],	
			'SL2_CBAL'		=> number_format(0, 2, '.', ''), //['SL2_CBAL', 	'N', 10,2],	
			'SL2_DED'			=> number_format(0, 2, '.', ''), //['SL2_DED', 	'N', 10,2],	
			'SL2_NBAL'		=> number_format(0, 2, '.', ''), //['SL2_NBAL', 	'N', 10,2],	
			'SL2_DTYPE'		=> ' ', //['SL2_DTYPE', 'C', 1],	
			'SL3_DATE'		=> ' ', //['SL3_DATE', 	'D'],	
			'SL3_PRIN'		=> number_format(0, 2, '.', ''), //['SL3_PRIN', 	'N', 10,2],	
			'SL3_RUND'		=> number_format(0, 2, '.', ''), //['SL3_RUND', 	'N', 10,2],	
			'SL3_CBAL'		=> number_format(0, 2, '.', ''), //['SL3_CBAL', 	'N', 10,2],	
			'SL3_DED'			=> number_format(0, 2, '.', ''), //['SL3_DED', 	'N', 10,2],	
			'SL3_NBAL'		=> number_format(0, 2, '.', ''), //['SL3_NBAL', 	'N', 10,2],	
			'SL3_DTYPE'		=> ' ', //['SL3_DTYPE', 'C', 1],	
			'PB1_DATE'		=> ' ', //['PB1_DATE', 	'D'],	
			'PB1_PRIN'		=> number_format(0, 2, '.', ''), //['PB1_PRIN', 	'N', 10,2],	
			'PB1_RUND'		=> number_format(0, 2, '.', ''), //['PB1_RUND', 	'N', 10,2],	
			'PB1_CBAL'		=> number_format(0, 2, '.', ''), //['PB1_CBAL', 	'N', 10,2],	
			'PB1_DED'			=> number_format(0, 2, '.', ''), //['PB1_DED', 	'N', 10,2],	
			'PB1_NBAL'		=> number_format(0, 2, '.', ''), //['PB1_NBAL',  'N', 10,2],	
			'PB1_DTYPE'		=> ' ', //['PB1_DTYPE', 'C', 1],	
			'PB2_DATE'		=> ' ', //['PB2_DATE', 	'D'],	
			'PB2_PRIN'		=> number_format(0, 2, '.', ''), //['PB2_PRIN', 	'N', 10,2],	
			'PB2_RUND'		=> number_format(0, 2, '.', ''), //['PB2_RUND', 	'N', 10,2],	
			'PB2_CBAL'		=> number_format(0, 2, '.', ''), //['PB2_CBAL', 	'N', 10,2],	
			'PB2_DED'			=> number_format(0, 2, '.', ''), //['PB2_DED', 	'N', 10,2],	
			'PB2_NBAL'		=> number_format(0, 2, '.', ''), //['PB2_NBAL', 	'N', 10,2],	
			'PB2_DTYPE'		=> ' ', //['PB2_DTYPE', 'C', 1],	
			'PB3_DATE'		=> ' ', //['PB3_DATE', 	'D'],	
			'PB3_PRIN'		=> number_format(0, 2, '.', ''), //['PB3_PRIN', 	'N', 10,2],	
			'PB3_RUND'		=> number_format(0, 2, '.', ''), //['PB3_RUND', 	'N', 10,2],	
			'PB3_CBAL'		=> number_format(0, 2, '.', ''), //['PB3_CBAL', 	'N', 10,2],	
			'PB3_DED'			=> number_format(0, 2, '.', ''), //['PB3_DED', 	'N', 10,2],	
			'PB3_NBAL'		=> number_format(0, 2, '.', ''), //['PB3_NBAL', 	'N', 10,2],	
			'PB3_DTYPE'		=> ' ', //['PB3_DTYPE', 'C', 1],	
			'DIM1_DATE'		=> ' ', //['DIM1_DATE', 'D'],	
			'DIM1_PRIN'		=> number_format(0, 2, '.', ''), //['DIM1_PRIN', 'N', 10,2],	
			'DIM1_RUND'		=> number_format(0, 2, '.', ''), //['DIM1_RUND', 'N', 10,2],	
			'DIM1_CBAL'		=> number_format(0, 2, '.', ''), //['DIM1_CBAL', 'N', 10,2],	
			'DIM1_DED'		=> number_format(0, 2, '.', ''), //['DIM1_DED',  'N', 10,2],	
			'DIM1_NBAL'		=> number_format(0, 2, '.', ''), //['DIM1_NBAL', 'N', 10,2],	
			'DIM1_DTYPE'	=> ' ', //['DIM1_DTYPE','C', 1],	
			'DIM2_DATE'		=> ' ', //['DIM2_DATE', 'D'],	
			'DIM2_PRIN'		=> number_format(0, 2, '.', ''), //['DIM2_PRIN', 'N', 10,2],	
			'DIM2_RUND'		=> number_format(0, 2, '.', ''), //['DIM2_RUND', 'N', 10,2],	
			'DIM2_CBAL'		=> number_format(0, 2, '.', ''), //['DIM2_CBAL', 'N', 10,2],	
			'DIM2_DED'		=> number_format(0, 2, '.', ''), //['DIM2_DED', 	'N', 10,2],	
			'DIM2_NBAL'		=> number_format(0, 2, '.', ''), //['DIM2_NBAL', 'N', 10,2],	
			'DIM2_DTYPE'	=> ' ', //['DIM2_DTYPE','C', 1],	
			'TOT_PRIN'		=> number_format(0, 2, '.', ''), //['TOT_PRIN', 	'N', 12,2],	
			'TOT_RUND'		=> number_format(0, 2, '.', ''), //['TOT_RUND', 	'N', 12,2],	
			'TOT_CBAL'		=> number_format(0, 2, '.', ''), //['TOT_CBAL', 	'N', 12,2],	
			'TOT_DED'			=> number_format(0, 2, '.', ''), //['TOT_DED', 	'N', 12,2],	
			'TOT_NBAL'		=> number_format(0, 2, '.', ''), //['TOT_NBAL', 	'N', 12,2]
		];
	}


	

  


}