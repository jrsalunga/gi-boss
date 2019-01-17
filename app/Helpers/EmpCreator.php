<?php namespace App\Helpers;

use Carbon\Carbon;
use ZipArchive;


class EmpCreator {

	protected $path;
	protected $fields = [];
	protected $attributes = [];
	public $file_path = '';


	public function __construct($attributes=NULL, $ext='MAS', $path=NULL) {
		$this->set_path($path);
		$this->set_fields();

		if (is_null($attributes))
			return false;
		
		return $this->create($attributes, $ext);
	}


	public function create($attributes=NULL, $ext='MAS') {
		
		if (is_null($attributes)) {
			throw new \Exception('Could not create on blank attributes.');
			return false;
		}

		if (!array_key_exists(strtoupper('MAN_NO'), $attributes)) {
			throw new \Exception('MAN_NO is required');
			return false;
		}


		$this->fill_dbf($attributes, $ext);

		$this->generated_file($attributes['MAN_NO'], $ext);

		return $this;
	}

	private function generated_file($filename, $ext) {
		$this->file_path = $this->get_path().DS.$filename.'.'.$ext; 
	}

	private function fill_dbf($attributes, $ext) {

		//$dt1 = 
		$this->create_paymast($attributes, NULL, $ext);

		if ($ext=='EMP') {
			$this->create_payhist($attributes);
			$this->make_empfile($attributes['MAN_NO']);
		}
	}

	private function make_empfile($filename=NULL) {

		if (is_null($filename)) {
			throw new \Exception('Make empfile no filename');
			return false;
		}

		$flag=true;
		for ($i=1; $i<=2 ; $i++) { 
			if (!file_exists($this->get_path().DS.$filename.'.DT'.$i)) 
				throw new \Exception($filename.'.DT'.$i.' not found');
		}


		$zipname = $this->get_path().DS.$filename.'.EMP';
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE|ZipArchive::OVERWRITE);
		for ($i=1; $i<=2 ; $i++) { 
			$file = $this->get_path().DS.$filename.'.DT'.$i;
		  $zip->addFile($file, $filename.'.DT'.$i);
		  unlink($file);

		}
		$zip->close();

	}

	private function create_paymast($attributes, $filename=NULL, $ext='DBF') {

		$filename = is_null($filename) ? $attributes['MAN_NO'] : $filename;

		if ($ext=='EMP')
			$ext = 'DT1';

		$path = $this->createDBF($filename, $ext, $this->pay_mast_fields());

		$dbf = dbase_open($path, 2);

		$this->add_record($dbf, $this->setToArray($attributes, $this->get_fields()));

		dbase_close($dbf);
		//$this->close_dbf($dbf);

	}

	private function create_payhist($attributes, $filename=NULL, $ext='DT2') {

		$filename = is_null($filename) ? $attributes['MAN_NO'] : $filename;

		$path = $this->createDBF($filename, $ext, $this->pay_hist_fields());

		$dbf = dbase_open($path, 2);

		//$this->add_record($dbf, $this->setToArray($attributes, $this->get_fields()));

		dbase_close($dbf);
		//$this->close_dbf($dbf);

	}

	private function setToArray($attributes, $fields) {
		$arr = [];

		foreach ($fields as $key => $value) {
			array_push($arr, $attributes[$value]);
		}

		return $arr;
	}


	private function add_record($dbf=null, $data=null) {
    dbase_add_record($dbf, $data);
  }

  private function close_dbf($dbf) {
    return dbase_close($dbf);
  }


	private function createDBF($filename=null, $ext='DBF', $fields=null) {

    if (is_null($filename)) 
      throw new \Exception('No filename set on createDBF');

    if (is_null($fields)) 
      throw new \Exception('No fields set on createDBF');

    $dbf = $this->path.DS.$filename.'.'.$ext;
   
    if (!dbase_create($dbf, $fields)) 
      throw new \Exception('Unable to create DBF');
    
    return $dbf;
  }


	public function set_path($path=NULL) {
		$path = is_null($path)
			? storage_path().DS.'emp'
			: $path;

		if (!is_dir($path))
			mkdir($path, 0775, true); 

		return $this->path = $path;
	}

	

	public function get_path() {
		return $this->path;
	}

	private function set_fields() {
		foreach ($this->pay_mast_fields() as $key => $value) {
			array_push($this->fields, $value[0]);
		}
	}

	public function get_fields() {
		return $this->fields;
	}


  public function pay_mast_fields() {
  	return [
			['SEQ_NO', 		'N', 3,0],	
			['MAN_NO', 		'C', 6],	
			['LAST_NAM', 	'C', 20],	
			['FIRS_NAM', 	'C', 20],	
			['MI', 				'C', 2],	
			['MIDL_NAM', 	'C', 20],	
			['HIRED', 		'D'],	
			['EMP_STUS', 	'C', 9],	
			['STATUS_TAG','C', 1],	
			['RATE_HR', 	'N', 8,2],	
			['ECOL_RATE', 'N', 8,2],	
			['ALW1_RATE', 'N', 8,2],	
			['ALW2_RATE', 'N', 8,2],	
			['CA_BAL', 		'C', 10,0],	
			['CHIT_BAL', 	'N', 12,2],	
			['MTD_GRS', 	'N', 12,2],	
			['YTD_GRS', 	'N', 12,2],	
			['MTD_BSC', 	'N', 12,2],	
			['YTD_BSC', 	'N', 12,2],	
			['YTD_TAX', 	'N', 10,2],	
			['TAX_EXEM', 	'N', 10,2],	
			['DEPT', 			'C', 10],	
			['POSITION', 	'C', 12],	
			['BRANCH', 		'C', 3],	
			['CO_NAME', 	'C', 50],	
			['CO_ADD1', 	'C', 40],	
			['CO_ADD2', 	'C', 40],	
			['TIN_ER', 		'C', 16],	
			['PAY_DATE', 	'D'],	
			['RUN_TAG', 	'C', 1],	
			['SSS_NO', 		'C', 14],	
			['PHEALTH_NO','C', 14],	
			['PBIG_NO', 	'C', 14],	
			['WTAX_NO', 	'C', 14],	
			['SSS_TAG', 	'C', 1],	
			['PH_TAG', 		'C', 1],	
			['PBIG_TAG', 	'C', 1],	
			['WTAX_TAG', 	'C', 1],	
			['SSS_EE', 		'N', 10,2],	
			['SSS_ER', 		'N', 10,2],	
			['PH_EE', 		'N', 10,2],	
			['PH_ER', 		'N', 10,2],	
			['PBIG_EE', 	'N', 10,2],	
			['PBIG_ER', 	'N', 10,2],	
			['WTAX', 			'N', 10,2],	
			['TAX_EE', 		'N', 10,2],	
			['TAX_ER', 		'N', 10,2],	
			['POL_CLR', 	'D'],	
			['NBI_CLR', 	'D'],	
			['HEALTH_CLR','D'],	
			['STARTED', 	'D'],	
			['REGULARED', 'D'],	
			['RESIGNED', 	'D'],	
			['WORKHIST1', 'C', 40],	
			['WORKHIST2', 'C', 40],                       	
			['WORKHIST3', 'C', 40],	
			['WORKHIST4', 'C', 40],	
			['BIRTHDATE', 'D'],	
			['BIRTHPLC', 	'C', 10],	
			['RELIGION', 	'C', 10],	
			['SPOUS_NAM', 'C', 32],	
			['ADDRESS1', 	'C', 40],	
			['ADDRESS2', 	'C', 40],	
			['ADDRESS3',  'C', 40],	
			['CEL', 			'C', 40],	
			['TEL', 			'C', 40],	
			['EMAIL', 		'C', 40],	
			['SAL_WORDS', 'C', 70],	
			['EDUCATION', 'C', 40],	
			['DEPENDENTS','N', 2,0],	
			['CHILD_NO',  'N', 2,0],	
			['CHILDREN1', 'C', 25],	
			['CHILDREN2', 'C', 25],	
			['SEX', 			'C', 1],	
			['HEIGHT', 		'C', 10],	
			['WEIGHT', 		'C', 10],	
			['CIV_STUS', 	'C', 10],	
			['UNIFORM', 	'C', 10],	
			['EMER_NO', 	'C', 16],	
			['EMER_NAM', 	'C', 20],	
			['HOBBIES', 	'C', 40],	
			['SP_NOTES1', 'C', 40],	
			['SP_NOTES2', 'C', 40],	
			['CA_PRIN', 	'N', 10,2],	
			['CA_RUND', 	'N', 10,2],	
			['CA_CBAL', 	'N', 10,2],	
			['CA_DED', 		'N', 10,2],	
			['CA_NBAL', 	'N', 10,2],	
			['CA_DTYPE',  'C', 1],	
			['CHIT_PRIN', 'N', 10,2],	
			['CHIT_RUND', 'N', 10,2],	
			['CHIT_CBAL', 'N', 10,2],	
			['CHIT_DED', 	'N', 10,2],	
			['CHIT_NBAL', 'N', 10,2],	
			['CHIT_DTYPE','C', 1],	
			['MEAL_PRIN', 'N', 10,2],	
			['MEAL_RUND', 'N', 10,2],	
			['MEAL_CBAL', 'N', 10,2],	
			['MEAL_DED',  'N', 10,2],	
			['MEAL_NBAL', 'N', 10,2],	
			['MEAL_DTYPE','C', 1],	
			['STR_DED1', 	'C', 12],	
			['OTD1_DATE', 'D'],	
			['OTD1_PRIN', 'N', 10,2],	
			['OTD1_RUND', 'N', 10,2],	
			['OTD1_CBAL', 'N', 10,2],	
			['OTD1_DED',  'N', 10,2],	
			['OTD1_NBAL', 'N', 10,2],	
			['OTD1_DTYPE','C', 1],	
			['STR_DED2',  'C', 12],	
			['OTD2_DATE', 'D'],	
			['OTD2_PRIN', 'N', 10,2],	
			['OTD2_RUND', 'N', 10,2],	
			['OTD2_CBAL', 'N', 10,2],	
			['OTD2_DED',  'N', 10,2],	
			['OTD2_NBAL', 'N', 10,2],	
			['OTD2_DTYPE','C', 1],	
			['STR_DED3',  'C', 12],	
			['OTD3_DATE', 'D'],	
			['OTD3_PRIN', 'N', 10,2],	
			['OTD3_RUND', 'N', 10,2],	
			['OTD3_CBAL', 'N', 10,2],	
			['OTD3_DED',  'N', 10,2],	
			['OTD3_NBAL', 'N', 10,2],	
			['OTD3_DTYPE', 'C', 1],	
			['REC1_DATE', 'D'],	
			['REC1_PRIN', 'N', 10,2],	
			['REC1_RUND', 'N', 10,2],	
			['REC1_CBAL', 'N', 10,2],	
			['REC1_DED',  'N', 10,2],	
			['REC1_NBAL', 'N', 10,2],	
			['REC1_DTYPE', 'C', 1],	
			['REC2_DATE', 'D'],	
			['REC2_PRIN', 'N', 10,2],	
			['REC2_RUND', 'N', 10,2],	
			['REC2_CBAL', 'N', 10,2],	
			['REC2_DED',  'N', 10,2],	
			['REC2_NBAL', 'N', 10,2],	
			['REC2_DTYPE','C', 1],	
			['KL_DATE', 	'D'],	
			['KL_PRIN', 	'N', 10,2],	
			['KL_RUND', 	'N', 10,2],	
			['KL_CBAL', 	'N', 10,2],	
			['KL_DED', 		'N', 10,2],	
			['KL_NBAL', 	'N', 10,2],	
			['KL_DTYPE', 	'C', 1],	
			['BF_DATE', 	'D'],	
			['BF_PRIN', 	'N', 10,2],	
			['BF_RUND', 	'N', 10,2],	
			['BF_CBAL', 	'N', 10,2],	
			['BF_DED', 		'N', 10,2],	
			['BF_NBAL', 	'N', 10,2],	
			['BF_DTYPE',  'C', 1],	
			['SL1_DATE',  'D'],	
			['SL1_PRIN', 	'N', 10,2],	
			['SL1_RUND', 	'N', 10,2],	
			['SL1_CBAL', 	'N', 10,2],	
			['SL1_DED', 	'N', 10,2],	
			['SL1_NBAL', 	'N', 10,2],	
			['SL1_DTYPE', 'C', 1],	
			['SL2_DATE', 	'D'],	
			['SL2_PRIN', 	'N', 10,2],	
			['SL2_RUND', 	'N', 10,2],	
			['SL2_CBAL', 	'N', 10,2],	
			['SL2_DED', 	'N', 10,2],	
			['SL2_NBAL', 	'N', 10,2],	
			['SL2_DTYPE', 'C', 1],	
			['SL3_DATE', 	'D'],	
			['SL3_PRIN', 	'N', 10,2],	
			['SL3_RUND', 	'N', 10,2],	
			['SL3_CBAL', 	'N', 10,2],	
			['SL3_DED', 	'N', 10,2],	
			['SL3_NBAL', 	'N', 10,2],	
			['SL3_DTYPE', 'C', 1],	
			['PB1_DATE', 	'D'],	
			['PB1_PRIN', 	'N', 10,2],	
			['PB1_RUND', 	'N', 10,2],	
			['PB1_CBAL', 	'N', 10,2],	
			['PB1_DED', 	'N', 10,2],	
			['PB1_NBAL',  'N', 10,2],	
			['PB1_DTYPE', 'C', 1],	
			['PB2_DATE', 	'D'],	
			['PB2_PRIN', 	'N', 10,2],	
			['PB2_RUND', 	'N', 10,2],	
			['PB2_CBAL', 	'N', 10,2],	
			['PB2_DED', 	'N', 10,2],	
			['PB2_NBAL', 	'N', 10,2],	
			['PB2_DTYPE', 'C', 1],	
			['PB3_DATE', 	'D'],	
			['PB3_PRIN', 	'N', 10,2],	
			['PB3_RUND', 	'N', 10,2],	
			['PB3_CBAL', 	'N', 10,2],	
			['PB3_DED', 	'N', 10,2],	
			['PB3_NBAL', 	'N', 10,2],	
			['PB3_DTYPE', 'C', 1],	
			['DIM1_DATE', 'D'],	
			['DIM1_PRIN', 'N', 10,2],	
			['DIM1_RUND', 'N', 10,2],	
			['DIM1_CBAL', 'N', 10,2],	
			['DIM1_DED',  'N', 10,2],	
			['DIM1_NBAL', 'N', 10,2],	
			['DIM1_DTYPE','C', 1],	
			['DIM2_DATE', 'D'],	
			['DIM2_PRIN', 'N', 10,2],	
			['DIM2_RUND', 'N', 10,2],	
			['DIM2_CBAL', 'N', 10,2],	
			['DIM2_DED', 	'N', 10,2],	
			['DIM2_NBAL', 'N', 10,2],	
			['DIM2_DTYPE','C', 1],	
			['TOT_PRIN', 	'N', 12,2],	
			['TOT_RUND', 	'N', 12,2],	
			['TOT_CBAL', 	'N', 12,2],	
			['TOT_DED', 	'N', 12,2],	
			['TOT_NBAL', 	'N', 12,2]
		];
  }

  public function pay_hist_fields() {
  	return [
			['TRANSCODE', 'C', 10],	
			['PAY_DATE', 	'D'],	
			['MAN_NO', 		'C', 6],	
			['AMOUNT', 		'N', 10, 2],	
			['TAG', 			'C', 1],	
			['REMARKS', 	'C', 10]
		];
	}

	

}