<?php namespace App\Console\Commands\Import;

use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Branch;
use App\Models\Manpay;
use App\Models\Payreg;
use App\Models\Employee;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Paymast extends Command
{
  protected $signature = 'import:paymast';
  protected $description = 'Import pay_mast.dbf';
  protected $path = 'D:\MAS\PAY_MAST.DBF';


  public function handle() {


    if (!file_exists($this->path)) {
      $this->info('file not found!');
      exit;
    }
      
    $db = dbase_open($this->path, 0);

    if (!$db) {
      $this->info('unable to open: '.$this->path);
      exit;
    }

    $this->info('checking dbf');
    $import = true;

    $header = dbase_get_header_info($db);
    
    $record_numbers = dbase_numrecords($db);

    DB::beginTransaction();
    
    for($i = 1; $i <= $record_numbers; $i++) {

      $row = dbase_get_record_with_names($db, $i);
    
      $this->info($i.' '.$row['BRANCH'].' '.$row['MAN_NO'].' '.$row['LAST_NAM'].' '.$row['FIRS_NAM'].' '.$row['MIDL_NAM']);

      $e = Employee::where('code', trim($row['MAN_NO']))->first();
      if (is_null($e)) {

      //$this->info($i.' '.$row['BRANCH'].' '.$row['MAN_NO'].' '.$row['LAST_NAM'].' '.$row['FIRS_NAM'].' '.$row['MIDL_NAM']);
        $this->info('Employee '.$row['MAN_NO'].' do not exist!');

        $employee = new Employee;
        $employee->id = $employee->get_uid();

        $employee->code         = trim($row['MAN_NO']);
        $employee->lastname     = str_replace('¥', 'Ñ', utf8_encode(trim($row['LAST_NAM'])));
        $employee->firstname    = str_replace('¥', 'Ñ', utf8_encode(trim($row['FIRS_NAM'])));
        $employee->middlename   = str_replace('¥', 'Ñ', utf8_encode(trim($row['MIDL_NAM'])));
        
        $exist_emp = false;

      } else {

        
        $employee = Employee::find($e->id);

        $employee->lastname     = str_replace('¥', 'Ñ', utf8_encode(trim($row['LAST_NAM'])));
        $employee->firstname    = str_replace('¥', 'Ñ', utf8_encode(trim($row['FIRS_NAM'])));
        $employee->middlename   = str_replace('¥', 'Ñ', utf8_encode(trim($row['MIDL_NAM'])));
        
        $exist_emp = true;
      }
      /*
      $this->info($employee->code.' '.$employee->lastname.' '.$employee->firstname);
      if ($exist_emp)
        $this->info('*');
      else
        $this->info('do not exist');
        */


      $employee->companyid    = trim($this->getCompanyId($row['CO_NAME']));
        
      $branch                 = Branch::where('code', trim($row['BRANCH']))->first();
      $employee->branchid     = is_null($branch) ? '971077BCA54611E5955600FF59FBB323': $branch->id;
      $employee->deptid       = $this->getDeptId(trim($row['DEPT']));
      $employee->positionid   = $this->getPositionId(trim($row['POSITION']));
      $employee->paytype      = 2;
      $employee->ratetype     = $this->getRateType(trim($row['DEPT']));
      $employee->rate         = trim($row['RATE_HR']);
      $employee->ecola        = trim($row['ECOL_RATE']);
      $employee->allowance1   = trim($row['ALW1_RATE']);
      $employee->allowance2   = trim($row['ALW2_RATE']);
      $employee->phicno       = trim($row['PHEALTH_NO']);
      $employee->hdmfno       = trim($row['PBIG_NO']);
      $employee->tin          = trim($row['WTAX_NO']);
      $employee->sssno        = trim($row['SSS_NO']);
      $employee->empstatus    = $this->getEmpstatus(trim($row['EMP_STUS']));
      $employee->datestart    = Carbon::parse(trim($row['STARTED']));
      $hired = empty(trim($row['HIRED'])) ? '0000-00-00' : Carbon::parse(trim($row['HIRED']));
      $employee->datehired    = $hired;
      $stop = empty(trim($row['RESIGNED'])) ? '0000-00-00' : Carbon::parse(trim($row['RESIGNED']));
      $employee->datestop     = $stop;
      $employee->punching     = array_key_exists($employee->positionid, config('giligans.position')) ? config('giligans.position')[$employee->positionid]['ordinal']:99;
      $employee->processing   = 1;
      $employee->address      = trim($row['ADDRESS1']).', '.trim($row['ADDRESS2']).', '.trim($row['ADDRESS3']);
      $employee->phone        = trim($row['TEL'])=='N/A' ? '':trim($row['TEL']);
      //$employee->fax          = trim($row['TEL']);
      $employee->mobile       = trim($row['CEL']);
      $employee->email        = trim($row['EMAIL'])=='N/A' ? '':trim($row['EMAIL']);
      $employee->gender       = trim($row['SEX'])=='M' ? 1:2;
      $employee->civstatus    = trim($row['CIV_STUS'])=='SINGLE' ? 1:2;
      $employee->height       = $this->getHeight(trim($row['HEIGHT']));
      $employee->weight       = $this->getWeight(trim($row['WEIGHT']));
      $employee->birthdate    = Carbon::parse(trim($row['BIRTHDATE']));
      $employee->birthplace   = trim($row['BIRTHPLC']);
      $employee->religionid   = trim($this->getReligionId($row['RELIGION']));
      $employee->hobby        = trim($row['HOBBIES']);
      $employee->notes        = 'UNIFORM:'.trim($row['UNIFORM']).'; '.
                                'SP_NOTES1:'.trim($row['SP_NOTES1']).'; '.
                                'SP_NOTES2:'.trim($row['SP_NOTES2']).'; ';

      if ($import) {
        try {
          $employee->save();
        } catch (\Exception $e) {
          $this->info('Error: '.$e->getMessage());
          DB::rollBack();
        }
      }

      if (!$exist_emp) {

        $childrens = [];
        if(!empty(trim($row['CHILDREN1'])) && trim($row['CHILDREN1'])!='N/A') {
          $c1 = new \App\Models\Children;
          $c1->firstname = trim($row['CHILDREN1']);
          $c1->lastname = $employee->lastname;
          $c1->id = $c1->get_uid();
          array_push($childrens, $c1);
        }

        if(!empty(trim($row['CHILDREN2'])) && trim($row['CHILDREN2'])!='N/A') {
          $c2 = new \App\Models\Children;
          $c2->firstname = trim($row['CHILDREN2']);
          $c2->lastname = $employee->lastname;
          $c2->id = $c2->get_uid();
          array_push($childrens, $c2);
        }

        if ($import) {
          try {
            $employee->childrens()->saveMany($childrens);
          } catch (\Exception $e) {
            $this->info('Error: '.$e->getMessage());
            DB::rollBack();
          }
        }

          



        if(!empty(trim($row['EMER_NAM'])) && trim($row['EMER_NAM'])!='N/A') {
          $emer = explode(' ', trim($row['EMER_NAM']));
          $e = new \App\Models\Ecperson;
          $e->firstname = empty($emer[0])?'':$emer[0];
          $e->lastname = empty($emer[1])?'':$emer[1];
          $e->mobile = trim($row['EMER_NO']);
          $e->id = $e->get_uid();
          
          if ($import) {
            try {
              $employee->ecperson()->save($e);  
            } catch (\Exception $e) {
              $this->info('Error: '.$e->getMessage());
              DB::rollBack();
            }
          }
        }


        if(!empty(trim($row['EDUCATION'])) && trim($row['EDUCATION'])!='N/A') {
          $edu = new \App\Models\Education;
          $edu->school = trim($row['EDUCATION']);
          $edu->id = $edu->get_uid();

          if ($import) {
            try {
              $employee->educations()->saveMany([$edu]);  
            } catch (\Exception $e) {
              $this->info('Error: '.$e->getMessage());
              DB::rollBack();
            }
          }
        }
        

        $works = [];
        if(!empty(trim($row['WORKHIST1'])) && trim($row['WORKHIST1'])!='N/A') {
          $w1 = new \App\Models\Workexp;
          $w1->company = trim($row['WORKHIST1']);
          $w1->id = $w1->get_uid();
          array_push($works, $w1);
        }

        if(!empty(trim($row['WORKHIST2'])) && trim($row['WORKHIST2'])!='N/A') {
          $w2 = new \App\Models\Workexp;
          $w2->company = trim($row['WORKHIST2']);
          $w2->id = $w2->get_uid();
          array_push($works, $w2);
        }

        if(!empty(trim($row['WORKHIST3'])) && trim($row['WORKHIST3'])!='N/A') {
          $w3 = new \App\Models\Workexp;
          $w3->company = trim($row['WORKHIST3']);
          $w3->id = $w3->get_uid();
          array_push($works, $w3);
        }

        if(!empty(trim($row['WORKHIST4'])) && trim($row['WORKHIST4'])!='N/A') {
          $w4= new \App\Models\Workexp;
          $w4->company = trim($row['WORKHIST2']);
          $w4->id = $w4->get_uid();
          array_push($works, $w4);
        }

        if ($import) {
          try {
            $employee->workexps()->saveMany($works);
          } catch (\Exception $e) {
            $this->info('Error: '.$e->getMessage());
            DB::rollBack();
          }
        }


        if(!empty(trim($row['SPOUS_NAM'])) && trim($row['SPOUS_NAM'])!='N/A' && trim($row['SPOUS_NAM'])!='NA/A' ) {
          $sp = preg_split("/\s+(?=\S*+$)/", trim($row['SPOUS_NAM']));
          $spou = new \App\Models\Spouse;
          $spou->firstname = empty($sp[0])?'':$sp[0];
          $spou->lastname = empty($sp[1])?'':$sp[1];
          $spou->id = $spou->get_uid();
        
          if ($import) {
            try {
              $employee->spouse()->save($spou); 
            } catch (\Exception $e) {
              $this->info('Error: '.$e->getMessage());
              DB::rollBack();
            }
          }
        }

      } // end: if !exist


      $sttr = new \App\Models\Statutory;  
      $sttr->date_reg = Carbon::parse(trim($row['REGULARED']));
      $sttr->meal     = trim($row['CA_BAL']);
      $sttr->ee_sss   = trim($row['SSS_EE']);
      $sttr->er_sss   = trim($row['SSS_ER']);
      $sttr->SSS_TAG  = trim($row['SSS_TAG'])=='Y' ? 1 : 0;
      $sttr->ee_phic  = trim($row['PH_EE']);
      $sttr->er_phic  = trim($row['PH_ER']);
      $sttr->phic_tag = trim($row['PH_TAG'])=='Y' ? 1 : 0;
      $sttr->ee_hdmf  = trim($row['PBIG_EE']);
      $sttr->er_hdmf  = trim($row['PBIG_ER']);
      $sttr->hdmf_tag = trim($row['PBIG_TAG'])=='Y' ? 1 : 0;
      $sttr->ee_tin   = trim($row['TAX_EE']);
      $sttr->er_tin   = trim($row['TAX_ER']);
      $sttr->wtax     = trim($row['WTAX']);
      $sttr->wtax_tag = trim($row['WTAX_TAG'])=='Y' ? 1 : 0;
      
      $sttr->id = $sttr->get_uid();
      
      if($import)
        $employee->statutory()->save($sttr);

    } // end: for 
    DB::commit();


    dbase_close($db);

  }

  public function getEmpstatus($c){
     
    switch (trim($c)) {
      case "CONTRACT":
        return 2;
        break;
      case "TRAINEE":
        return 1;
        break;
      case "TRAINEE 1":
        return 1;
        break;
      case "TEMPORARY":
        return 1;
        break;
      case "TEMPO":
        return 1;
        break;
      case "REGULAR":
        return 3;
        break;
      default:
        return 1;
        break;
    }
  }



  public function getCompanyId($c){
     
    switch (trim($c)) {
      case "ALQUIROS FOOD CORP.":
        return '29E4E2FA672C11E596ECDA40B3C0AA12';
        break;
      case "GILIGAN'S ISLAND BAGUIO, INC.":
        return '43400E83673811E596ECDA40B3C0AA12';
        break;
      case "IONE-6 FOODS":
        return '6A2F5687673611E596ECDA40B3C0AA12';
        break;
      case "SHA-DINE-6 DINERS":
        return '81D62659673611E596ECDA40B3C0AA12';
        break;
      case "FIJON-6 FOODS":
        return '43B6B571673611E596ECDA40B3C0AA12';
        break;
      case "ROSE FOUR DINERS":
        return '7E8F8AC3673611E596ECDA40B3C0AA12';
        break;
      case "NATHANAEL-6 FOODS":
        return '70F73EAD673611E596ECDA40B3C0AA12';
        break;
      case "FILBERT'S-6 FOODS":
        return '57F10712673611E596ECDA40B3C0AA12';
        break;
      case "FJN6 FOOD CORPORATION":
        return '5C010584673611E596ECDA40B3C0AA12';
        break;
      case "KAWBINADIT CORP.":
        return '7A859059673611E596ECDA40B3C0AA12';
        break;
      case "NIKDER SIX FOODS":
        return '74B1CBDC673611E596ECDA40B3C0AA12';
        break;
      case "FJN6 FOOD CORP.":
        return '5C010584673611E596ECDA40B3C0AA12';
        break;
      case "NEILZACH RESTAURANT":
        return 'DB02D166D56A466D9804BEFD3589E432';
        break;
      case "GILIGAN'S ISLAND REST. & BAR CEBU, INC.":
        return '6275CF5B673611E596ECDA40B3C0AA12';
      default:
        return '';
        break;
    }
  }

  public function getDeptId($dept){

    if(starts_with($dept, 'KIT'))
      return '71B0A2D2674011E596ECDA40B3C0AA12';
    if(starts_with($dept, 'DIN'))
      return '75B34178674011E596ECDA40B3C0AA12';
    if(starts_with($dept, 'OPS'))
      return '201E68D4674111E596ECDA40B3C0AA12';
    if(starts_with($dept, 'CSH'))
      return 'DC60EC42B0B143AFA7D42312DA5D80BF';
    if(starts_with($dept, 'ADM'))
      return 'D2E8E339A47B11E592E000FF59FBB323';
    return '';  
  
  }

  public function getRateType($dept){
    if(ends_with($dept, 'DAY'))
      return '1';
    if(ends_with($dept, 'MON'))
      return '2';
    return '';  
  }


  public function getReligionId($c){
     
    switch (trim($c)) {
      case "R.CATH":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "R. CATH":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "R.CATH,":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "R.CATH,":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "R,CATH":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "R'CATHOLIC":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "R.CATH.":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "MARRIED":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "CATHOLIC":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "CATH":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "CAM. SUR":
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
      case "CHRISTIAN":
        return '2975665F674811E596ECDA40B3C0AA12';
        break;
      case "JEHOVA":
        return '465B9151A30E11E592E000FF59FBB323';
        break;
      case "JEHOVA'S":
        return '465B9151A30E11E592E000FF59FBB323';
        break;
      case "INC":
        return '2D6A8A3A674811E596ECDA40B3C0AA12';
        break;
      case "I.N.C.":
        return '2D6A8A3A674811E596ECDA40B3C0AA12';
        break;
      case "IGLESIA":
        return '2D6A8A3A674811E596ECDA40B3C0AA12';
        break;
      case "AGLIPAYIN":
        return '9ED09932A3D511E592E000FF59FBB323';
        break;
      case "S.D.A":
        return 'A87C6E4EA3DE11E592E000FF59FBB323';
        break;
      case "SDA":
        return 'A87C6E4EA3DE11E592E000FF59FBB323';
        break;
      case "7DAY ADVNT":
        return 'A87C6E4EA3DE11E592E000FF59FBB323';
        break;
      case "BAPTIST":
        return 'AF2E222CA3DE11E592E000FF59FBB323';
        break;
      case "BORN AGAIN":
        return '71FC2C52A3E311E592E000FF59FBB323';
        break;
      case "PROTESTANT":
        return '71FC2C52A3E311E592E000FF59FBB323';
        break;
      case "METHODIST":
        return '14D98381A47A11E592E000FF59FBB323';
        break;
      case "ALLIANCE":
        return '45942FF9A47A11E592E000FF59FBB323';
        break;
      case "L.D.SAINTS":
        return '052FE585A48011E592E000FF59FBB323';
        break;
      case "CRUSADER":
        return '0EEEE7B6A48411E592E000FF59FBB323';
        break;

        
      default:
        return '1A95F32E674811E596ECDA40B3C0AA12';
        break;
    }
  }


  public function getPositionId($pos){
    $p = \App\Models\Position::where('descriptor', $pos)->first();
    if(!is_null($p))
      return $p->id;

    switch (trim($pos)) {
      case "Dining Supv.":
        return 'B3622DDF666611E596ECDA40B3C0AA12';
        break;
      case "Dining Super":
        return 'B3622DDF666611E596ECDA40B3C0AA12';
        break;
      case "Cashier Seni":
        return '69427592A5E111E385D3C0188508F93C';
        break;
      case "Cashier Sr.":
        return '69427592A5E111E385D3C0188508F93C';
        break;
      case "Dining Asst.":
        return '8EF16963673A11E596ECDA40B3C0AA12';
        break;
      case "Kitchen Supe":
        return 'A7006EB7A3D411E592E000FF59FBB323';
        break;
      case "Kitchen Asst":
        return 'D02091AB673A11E596ECDA40B3C0AA12';
        break;
      case "OIC Kitchen":
        return '81BCB53BA3D711E592E000FF59FBB323';
        break;
      case "Kitchen Supv":
        return 'A7006EB7A3D411E592E000FF59FBB323';
        break;
      case "Manager - Op":
        return '55FC33F0A30211E592E000FF59FBB323';
        break;
      case "Mngr Branch":
        return '55FC33F0A30211E592E000FF59FBB323';
        break;
      case "Management T":
        return 'EC5ED785673A11E596ECDA40B3C0AA12';
        break;
      case "Mgmt Trainee":
        return 'EC5ED785673A11E596ECDA40B3C0AA12';
        break;
      case "Utility Staf":
        return '67B0F27F673B11E596ECDA40B3C0AA12';
        break;
      case "Tech'n":
        return 'F55DA154A47B11E592E000FF59FBB323';
        break;
      case "Tech'n Sr.":
        return '553820C0A47C11E592E000FF59FBB323';
        break;
      case "TRAINEE":
        return 'E16F473C86A94EF09C658286BEDEF89A';
        break;
      case "TRAINEE 1":
        return '292FC22C808C11E6B7C800FF18C615EC';
        break;
      case "TRAINEE 2":
        return '76A923D32879406E8D6D62EB6F81277B';
        break;
      case "TRAINEE 3":
        return '179E8AB1C5BD402E90E69A7F14E7F16F';
        break;
      case "TRAINEE 4":
        return 'C6A67A2F280F4634A5AF1BBECF6D901B';
        break;
      case "Kitchen Area":
        return '4C97B1DD673B11E596ECDA40B3C0AA12';
        break;
      case "Mngr Area":
        return '565DE46943904A40AD2888463A79570C';
        break;
      case "Mngr Branch":
        return '565DE46943904A40AD2888463A79570C';
        break;
      default:
        return '';
        break;
    }

  }

  public function getHeight($height) {
    $h = str_replace('"', '', $height);
    $r = array_search($h, config('giligans.meter_to_feet'));
    return is_null($r) ? $height : $r;
  }

  public function getWeight($weight) {
    $w = floatval($weight);
    return ($w >= 90)
      ? $w*0.45359237
      : $w;
  }

}