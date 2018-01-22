<?php namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Salesmtd;
use Illuminate\Console\Command;
use App\Repositories\DateRange;
use App\Repositories\DailySalesRepository as DSRepo;
use App\Models\DailySales as DS;

class YearlyProductSales extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'psales {year : YYYY}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Display an inspiring quote';

  /**
   * Execute the console command.
   *
   * @return mixed
   */


  protected $dr;
  protected $ds;

  public function __construct(DateRange $dr, DSRepo $ds) {
    parent::__construct();
    $this->dr = $dr;
    $this->ds = $ds;
  }


  public function handle()
  {
    $year = $this->argument('year');


    

    $branches = Branch::where('opendate', '<>', '0000-00-00')
                        ->where('closedate', '=', '0000-00-00')
                        ->orderBy('code')
                        ->get();

    $this->dr->fr = Carbon::parse($year.'-01-01');
    $this->dr->to = Carbon::parse($year.'-12-31');

    /*
    
    */

    //$this->comment($year);
    //DB::enableQueryLog();
    $s = Salesmtd::whereBetween('salesmtd.orddate', [$this->dr->fr->format('Y-m-d'), $this->dr->to->format('Y-m-d')])
                    ->where('salesmtd.group', '<>', '')
                    ->leftJoin('product', 'product.id', '=', 'salesmtd.product_id')
                    ->select(DB::raw('salesmtd.group, group_cnt as qty, sum(salesmtd.grsamt) as grsamt, cslipno'))
                    ->groupBy('salesmtd.branch_id')
                    ->groupBy('salesmtd.group')
                    ->groupBy('salesmtd.cslipno')
                    ->orderBy(DB::raw('salesmtd.group'), 'asc')
                    ->get();

    $arr = [];
    foreach ($s as $key => $p) {

      if(array_key_exists($p->group, $arr)) {
        $arr[$p->group]['qty']    += $p->qty;       
        $arr[$p->group]['grsamt'] += $p->grsamt;
      } else {
        $arr[$p->group]['group']  = $p->group;
        $arr[$p->group]['qty']    = $p->qty;
        $arr[$p->group]['grsamt'] = $p->grsamt;
      }
    
    }
  
    foreach ($arr as $key => $value) {
      $this->comment($key.','.$value['qty'].','.$value['grsamt']);
    }
  }











  private function products() {
    return [
  ["AK","Ado.Kangkong",58.00],
  /*
  ["BCT","B.Chix Teriyaki",172.00],
  ["BLK","B.Lech.Kwali",282.00],
  ["BOC","B.OYSTR.CHS",169.00],
  ["BOG","B.OYSTR.G/B",169.00],
  ["BOM","B.OYSTR.MX",169.00],
  ["BUKS","B.Pandan (s)",60.00],
  ["BUKW","B.Pandan (W)",620.00],
  ["BMUSS","BAKED MUSSELS",138.00],
  ["GBP","BBQ Platter",240.00],
  ["BCAL","Beef Caldereta",238.00],
  ["BS","Beef Steak",242.00],
  ["BICOL","Bicol Express",190.00],
  ["GBG","Bihon G. Groupies Promo",137.00],
  ["BG","Bihon Guisado",165.00],
  ["BSOUP","Bulalo Soup",310.00],
  ["CPATAL","C.Pata Large",635.00],
  ["CPATAM","C.Pata Medium",575.00],
  ["CPATA","C.Pata Regular",498.00],
  ["CSH","C.Squid Heads",125.00],
  ["CALAM","Calamares",180.00],
  ["GCG","Canton Groupies Promo",137.00],
  ["CG","Canton Guisado",165.00],
  ["CSTICK","Cheese Stick",103.00],
  ["BINA","Chix Binakol",212.00],
  ["CSAL","Chix Salpicao",182.00],
  ["CHOP","Chopsuey",156.00],
  ["DINU","Dinuguan",149.00],
  ["DINUGP","Dinuguan Groupies Promo",135.00],
  ["EGG","EGG",30.00],
  ["XSOUP","Extra Soup",90.00],
  ["FGC1","F.G.Chix 1",482.00],
  ["FGC2","F.G.Chix 1/2",262.00],
  ["FGC4","F.G.Chix 1/4",162.00],
  ["ESCA","Fish Escabeche",184.00],
  ["FT","Fish Teriyaki",184.00],
  ["FF","French Fries",97.00],
  ["PFBB","Fried Bangus",195.00],
  ["FC2","Fried Chix 2pcs",119.00],
  ["GFR","Fried Rice",173.00],
  ["GFC1","G.F.Chix 1",425.00],
  ["GFC2","G.F.Chix 1/2",245.00],
  ["GGPLAT","G.Grd.Platter",780.00],
  ["GSB","G.Salm.Belly",195.00],
  ["GTB","G.Tuna.Belly",289.00],
  ["GFISH","Garlic Fish",184.00],
  ["GSQ","Garlic Squid",193.00],
  ["GB","Grd.Bngus Bely",195.00],
  ["GP","Grd.Pusit",272.00],
  ["IBAB","Inihaw Baboy",170.00],
  ["KAREBE","Kare (Beef)",318.00],
  ["KAREG","Kareng Gulay",172.00],
  ["KTUNA","Kilaw Tuna",172.00],
  ["LK","L.Kawali 1",246.00],
  ["LK2","L.Kawali 1/2",144.00],
  ["LKSIG","L.Kawali Sigang",271.00],
  ["LS","L.Shanghai",113.00],
  ["LSG","L.Shanghai Groupies Promo",104.00],
  ["LF","Leche Flan",45.00],
  ["LKC","LK Cebu / 100grms",117.00],
  ["MFS","M.Float (s)",70.00],
  ["MFW","M.Float (W)",740.00],
  ["MANGGA","Manga Hilaw w/ Bagoong",90.00],
  ["GMIKI","Miki Bihon Groupies Promo",137.00],
  ["MIKI","Miki Bihon Guisado",165.00],
  ["PBBQMP","MP 3pcs PBBQ",102.00],
  ["YAKIMP","MP 3pcs Yakitori w/ IT",102.00],
  ["BCALMP","MP B.Caldereta",124.00],
  ["BCTMP","MP B.Chix Teri w/ IT",102.00],
  ["BSMP","MP Beef Steak",118.00],
  ["BICMP","MP Bicol Express w/ IT",102.00],
  ["DINUMP","MP Dinuguan w/ IT",99.00],
  ["ESCAMP","MP Fish Escabeche w/ IT",102.00],
  ["FCMP","MP Fried Chix 2pcs",124.00],
  ["GFMP","MP Gar.Fish w/ IT",102.00],
  ["GSQMP","MP Gar.Squid w/ IT",102.00],
  ["GFCMP","MP GFC",118.00],
  ["IBABMP","MP I.Baboy w/ I.T",102.00],
  ["LKMP","MP L.Kawali",118.00],
  ["PAGMP","MP Pork Adobo Gata w/IT",102.00],
  ["RCHIXM","MP R.Chix 1/4",114.00],
  ["SPICYM","MP S.S.Squid w/ IT",102.00],
  ["SSFMP","MP Sw'Sr FFil w/ I.T",102.00],
  ["SSPMP","MP Sw'Sr Pork w/ I.T",102.00],
  ["SSMP","MP Sz.Sisig",108.00],
  ["NILAGA","Nilaga Baka",260.00],
  ["PNUTS","Peanuts",76.00],
  ["GPICA","Pica-Pica Platter",369.00],
  ["PAK","Pinakbet",118.00],
  ["PAG","Pork Adob Gata",190.00],
  ["PBBQ","Pork BBQ 1stick",31.00],
  ["PR4G","PR4 Groupies4 Rice Promo",84.00],
  ["PR6G","PR4 Groupies6 Rice Promo",72.00],
  ["RCHIX1","R. Chix 1",425.00],
  ["RCHIX2","R. Chix 1/2",245.00],
  ["RUC","Rice All U Can Promo",52.00],
  ["GCR","Rice Garlic",44.00],
  ["GCR4","Rice Garlic 4",135.00],
  ["PR","Rice Plain",34.00],
  ["PR4","Rice Plain 4",118.00],
  ["PRUC4","RUC Groupies4 Promo",51.00],
  ["PRUC6","RUC Groupies6 Promo",76.00],
  ["STO","Shrmp.Tofu Oriental",263.00],
  ["SIGB","Sig.Baboy",206.00],
  ["SIGBB","Sig.Bangus Belly",178.00],
  ["SIGH","Sig.Hipon",263.00],
  ["SIGIB","Sig.Ihaw Baboy",206.00],
  ["SIGSB","Sig.Salmon Belly",209.00],
  ["STOFU","Siz. Tofu",113.00],
  ["SSP","Swt' Sr Pork",199.00],
  ["SSRF","Swt'Sour Fish Fillet",184.00],
  ["SPICY","Swt.Spc.Squid",193.00],
  ["SBEEF","Sz.Beef Sigang Gravy",230.00],
  ["SGAM","Sz.Gambas",258.00],
  ["SS","Sz.Sisig",205.00],
  ["TOFUE","Tofu.Eggplant",123.00],
  ["TOKWA","Tokwa",97.00],
  ["TB","Tokwa Baboy",171.00],
  ["UPGFR","Up Frd. Rice Promo",91.00],
  ["UPGCR","Up Garl.Rice Promo",49.00],
  ["YAKI","Yakitori Chix 1stick",31.00], */
];

  }

}
