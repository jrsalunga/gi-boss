<?php namespace App\Models;

use Carbon\Carbon;
use App\Models\BaseModel;

class DailySales extends BaseModel {

	//protected $connection = 'boss-live';
	protected $table = 'dailysales';
	public $timestamps = false;
 	//protected $fillable = ['date', 'branchid', 'managerid', 'sales', 'cos', 'tips', 'custcount', 'empcount'];
  protected $dates = ['opened_at', 'closed_at'];
	protected $guarded = ['id'];
	protected $casts = [
    'sales' => 'float',
    'food_sales' => 'float',
    'cos' => 'float',
    'tips' => 'float',
    'custcount' => 'integer',
    'empcount' => 'integer',
    'headspend' => 'float',
    'tipspct' => 'float',
    'mancostpct' => 'float',
    'mancost' => 'float',
    'cospct' => 'float',
    'purchcost' => 'float',
    'crew_kit' => 'integer',
    'crew_din' => 'integer',
    'salesemp' => 'float',
    'chrg_total' => 'float',
    'chrg_csh' => 'float',
    'chrg_chrg' => 'float',
    'chrg_othr' => 'float',
    'bank_totchrg' => 'float',
    'disc_totamt' => 'float',
    'vat_xmpt' => 'float',
    'slsmtd_totgrs' => 'float',
    'depo_check' => 'float',
    'depo_cash' => 'float',
    'sale_csh' => 'float',
    'sale_chg' => 'float',
    'sale_sig' => 'float',
    'transcost' => 'float',
    'transcos' => 'float',
    'transncos' => 'float',
    'grab' => 'float',
    'grabc' => 'float',
    'panda' => 'float',
    'zap' => 'float',
    'zap_sales' => 'float',
    'grab_fee' => 'float',
    'grabc_fee' => 'float',
    'panda_fee' => 'float',
    'zap_fee' => 'float',
    'zap_delfee' => 'float',
    'totdeliver' => 'float',
    'totdeliver_fee' => 'float',
    'opex' => 'float',
    'ccard' => 'float',
    'emp_meal' => 'float',
    'trans_cnt' => 'integer',
  ];


	public function branch() {
    return $this->belongsTo('App\Models\Branch', 'branchid');
  }

  public function branch_min() {
    return $this->belongsTo('App\Models\Branch', 'branchid')->select(['code', 'descriptor', 'id', 'companyid']);
  }

  public function getDateAttribute($value){
    return Carbon::parse($value.' 00:00:00');
  }

  public function getBeerPurch() {
    if(Carbon::parse($this->date->format('Y-m-d'))->lt(Carbon::parse('2016-01-01')))
      return 0;
    else
      return $this->purchcost - ($this->cos + $this->opex);
  }

  public function get_beerpurchpct($format=true) {
    if ($this->sales>0) {
      if ($format)
        return number_format(($this->getBeerPurch()/$this->sales)*100, 2);
      else
        return ($this->getBeerPurch()/$this->sales)*100;
    }
    return 0;
  }

  public function getOpex() {
    if(Carbon::parse($this->date->format('Y-m-d'))->lt(Carbon::parse('2016-01-01')))
      return 0;
    else
      return $this->netOpex(); //return $this->opex;
  }

  public function get_opexpct($format=true) {
    if ($this->sales>0) {
      if ($format)
        return number_format(($this->netOpex()/$this->sales)*100, 2);
      else
        return ($this->netOpex()/$this->sales)*100;
    }
    return 0;
  }

  public function get_cospct($format=true) {
    if ($this->food_sales>0) {
      if ($format)
        return number_format((($this->cos-$this->transcos)/$this->food_sales)*100, 2);
      else
        return (($this->cos-$this->transcos)/$this->food_sales)*100;
    }
    return 0;
  }

  public function get_mancostpct($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->mancost/$this->sales)*100, 2);
      else
        return ($this->mancost/$this->sales)*100;
    }
    return 0;
  }

  public function get_tipspct($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->tips/$this->sales)*100, 2);
      else
        return ($this->tips/$this->sales)*100;
    }
    return 0;
  }

  public function get_purchcostpct($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->purchcost/$this->sales)*100, 2);
      else
        return ($this->purchcost/$this->sales)*100;
    }
    return 0;
  }

  public function get_receipt_ave($format=true) {
    if ($this->trans_cnt>0){
      if ($format)
        return number_format($this->sales/$this->trans_cnt, 2);
      else
        return $this->sales/$this->trans_cnt;
    }
    return 0;
  }

  public function get_food_cost($format=true) {
    if ($this->food_sales>0){
      if ($format)
        return number_format(($this->netCos()/$this->food_sales)*100, 2);
      else
        return ($this->netCos()/$this->food_sales)*100;
    }
    return 0;
  }

  /********************** real computation based on new db ***************/

  
  public function grossOpex() {
    return $this->opex;
  }

  public function transOpex() {
    return ($this->transcost - ($this->transcos+$this->transncos));
  }

  public function netOpex() {
    return $this->grossOpex()+$this->totdeliver_fee+$this->emp_meal-$this->transOpex();
    return $this->grossOpex();
  }

  public function netCos() {
    return $this->cos-$this->transcos;
  }

  public function nCos() {
    return $this->purchcost-($this->cos+$this->opex);
  }

  public function costOfGoods() {
    return ($this->cos-$this->transcos)+($this->nCos()-$this->transncos);
  }

  public function totalExpense() {
    return $this->costOfGoods()+$this->netOpex();
  }

  public function directProfit() {
    return $this->sales-$this->totalExpense();
  }

  public function get_cogpct($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->costOfGoods()/$this->sales)*100, 2);
      else
        return ($this->costOfGoods()/$this->sales)*100;
    }
    return 0;
  }

  public function get_dprofitpct($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->directProfit()/$this->sales)*100, 2);
      else
        return ($this->directProfit()/$this->sales)*100;
    }
    return 0;
  }


  public function get_pct_disc_totamt($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->disc_totamt/$this->sales)*100, 2);
      else
        return ($this->disc_totamt/$this->sales)*100;
    }
    return 0;
  }


  public function get_pct_totdeliver($format=true) {
    if ($this->sales>0){
      if ($format)
        return number_format(($this->totdeliver/$this->sales)*100, 2);
      else
        return ($this->totdeliver/$this->sales)*100;
    }
    return 0;
  }

	
	
 

 
	
  
}