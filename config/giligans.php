<?php
return [
	'daytype' => [
		1 => 'Work Day',
		2 => 'Work Day and Regular Holiday',
		3 => 'Work Day and Special Holiday',
		4 => 'Rest Day',
		5 => 'Rest Day and Regular Holiday',
		6 => 'Rest Day and Special Holiday'
	],
	'backup' => 
		[
			'local' => 'C:\myserver\htdocs\gi-cashier\TEST_POS_BACKUP\\',
			'production' => '/home/server-admin/Public/maindepot/TEST_POS_BACKUP/'
		],
	'path' => [
		'backup' => [
			'local' => 'C:\myserver\htdocs\gi-cashier\TEST_POS_BACKUP\\',
			'production' => '/home/server-admin/Public/maindepot/TEST_POS_BACKUP/'
		],
		'files' => [
			'local' => 'C:\myserver\htdocs\gi-cashier\TEST_FILES_BACKUP\\',
			'production' => '/home/server-admin/Public/maindepot/TEST_FILES_BACKUP/'
		]
	],
	'cookie' => [
		'expiry' => 120
	],

	'groupies' => [
		'F1'=>'F1', 'F2'=>'F2', 'F3'=>'F3', 'F4'=>'F4', 'F5'=>'F5', 'F6'=>'F6', 'F7'=>'F7', 'F8'=>'F8', 'F9'=>'F9',
		'S1'=>'S1', 'S2'=>'S2', 'S3'=>'S3', 'S4'=>'S4', 'S5'=>'S5', 'S6'=>'S6', 'S7'=>'S7', 'S8'=>'S8', 'S9'=>'S9', 'S10'=>'S10'
	],

	'hours' => [
		6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5
	],

	'feet_to_meter' => [
		4 => ['1.2192', '1.2446', '1.2700', '1.2954', '1.3208', '1.3462', '1.3716', '1.3970', '1.4224', '1.4478', '1.4732', '1.4986'],
		5 => ['1.5240',	'1.5494',	'1.5748',	'1.6002',	'1.6256',	'1.6510',	'1.6764',	'1.7018',	'1.7272',	'1.7526',	'1.7780',	'1.8034'],
		6 => ['1.8288',	'1.8542',	'1.8796',	'1.9050',	'1.9304',	'1.9558',	'1.9812',	'2.0066',	'2.0320',	'2.0574',	'2.0828',	'2.1082'],
		7 => ['2.1336',	'2.1590',	'2.1844',	'2.2098',	'2.2352',	'2.2606',	'2.286',	'2.3114',	'2.3368',	'2.3622',	'2.3876',	'2.4130'],
		8 => ['2.4384']
	],

	'meter_to_feet' => [
		'1.22' => "4'0",
		'1.24' => "4'1",
		'1.27' => "4'2",
		'1.30' => "4'3",
		'1.32' => "4'4",
		'1.35' => "4'5",
		'1.37' => "4'6",
		'1.40' => "4'7",
		'1.42' => "4'8",
		'1.45' => "4'9",
		'1.47' => "4'10",
		'1.50' => "4'11",
		'1.52' => "5'0",
		'1.55' => "5'1",
		'1.57' => "5'2",
		'1.60' => "5'3",
		'1.63' => "5'4",
		'1.65' => "5'5",
		'1.68' => "5'6",
		'1.70' => "5'7",
		'1.73' => "5'8",
		'1.75' => "5'9",
		'1.78' => "5'10",
		'1.80' => "5'11",
		'1.83' => "6'0",
		'1.85' => "6'1",
		'1.88' => "6'2",
		'1.91' => "6'3",
		'1.93' => "6'4",
		'1.96' => "6'5",
		'1.98' => "6'6",
		'2.01' => "6'7",
		'2.03' => "6'8",
		'2.06' => "6'9",
		'2.08' => "6'10",
		'2.11' => "6'11",
		'2.13' => "7'0",
		'2.16' => "7'1",
		'2.18' => "7'2",
		'2.21' => "7'3",
		'2.24' => "7'4",
		'2.26' => "7'5",
		'2.29' => "7'6",
		'2.31' => "7'7",
		'2.34' => "7'8",
		'2.36' => "7'9",
		'2.39' => "7'10",
		'2.41' => "7'11",
		'2.44' => "8'0"
	],

	'position' => [
		'11E8A50414DDA9E4EAAF5EE430F8BF51' => ['ordinal' => 0, 'code' => 'CEO', 'postion' => 'Chief Executive Officer'],
		'11E8A50414DDA9E4EAAF764A610E8E8F' => ['ordinal' => 1, 'code' => 'COO', 'postion' => 'Chief Operating Officer'],
		'4EDF31E5B9964CADA70BDD9CEC45BEE0' => ['ordinal' => 2, 'code' => 'UM',  'postion' => 'Unit Manager'],
		'007D930D283F4362B893F9A29F6DFE2B' => ['ordinal' => 3, 'code' => 'OUH', 'postion' => 'Operations Unit Head'],
		'94D95DF4CF9A458AB425800861E67C14' => ['ordinal' => 4, 'code' => 'TM',  'postion' => 'Training Manager'],
		'0DB4E7CCCC824F6B8AD5B48CB4C127B6' => ['ordinal' => 5, 'code' => 'HRM', 'postion' => 'HR Manager'],
		'772D31E752D04BFE89377B96B7CF9CEE' => ['ordinal' => 6, 'code' => 'HR',  'postion' => 'HR Assistant'],
		'48851257E73E4A85AAAAD3731DC2AC3E' => ['ordinal' => 7, 'code' => 'HRO', 'postion' => 'HR Officer'],
		'BB4C75E076054453BE757E1E2391CCC8' => ['ordinal' => 8, 'code' => 'OFA', 'postion' => 'Office Assistant'],
		'553820C0A47C11E592E000FF59FBB323' => ['ordinal' => 9, 'code' => 'STC', 'postion' => 'Senior Technician'],
		'F55DA154A47B11E592E000FF59FBB323' => ['ordinal' => 10, 'code' => 'TEC','postion' => 'Technician'],
		'90089175CA214F80949330DDE5C8A19A' => ['ordinal' => 12, 'code' => 'RM',  'postion' => 'Regional Manager'],
		'FC53F748588648F1B4A24F3AE7C1E173' => ['ordinal' => 14, 'code' => 'RKH', 'postion' => 'Regional Kitchen Head'],
		'565DE46943904A40AD2888463A79570C' => ['ordinal' => 16, 'code' => 'AM',  'postion' => 'Area Manager'],
		'2777A30E9C984EDABB1EB5AB670CF7DD' => ['ordinal' => 18, 'code' => 'AAM', 'postion' => 'Assistant Area Manager'],
		'EC5ED785673A11E596ECDA40B3C0AA12' => ['ordinal' => 20, 'code' => 'MT',  'postion' => 'Management Trainee'],
		'11E8E72D14DDA9E4EAAFAE35647288D2' => ['ordinal' => 21, 'code' => 'AHC', 'postion' => 'Area Head Cashier'],
		'A7AECDD2666611E596ECDA40B3C0AA12' => ['ordinal' => 22, 'code' => 'SBM', 'postion' => 'Senior Branch Manager'],
		'B0092A7B666611E596ECDA40B3C0AA12' => ['ordinal' => 24, 'code' => 'BM',  'postion' => 'Branch Manager'],
		'55FC33F0A30211E592E000FF59FBB323' => ['ordinal' => 26, 'code' => 'OIC', 'postion' => 'OIC Branch'],
		'69427592A5E111E385D3C0188508F93C' => ['ordinal' => 28, 'code' => 'SCA', 'postion' => 'Senior Cashier'],
		'B688FC60666611E596ECDA40B3C0AA12' => ['ordinal' => 30, 'code' => 'CAS', 'postion' => 'Cashier'],
		'790CF69B949D469CAD9E2B90FFB26009' => ['ordinal' => 32, 'code' => 'TCA', 'postion' => 'Trainee Cashier'],
		'4C97B1DD673B11E596ECDA40B3C0AA12' => ['ordinal' => 34, 'code' => 'SKH', 'postion' => 'Senior Kitchen Head'],
		'CD359BD0673A11E596ECDA40B3C0AA12' => ['ordinal' => 36, 'code' => 'KH',  'postion' => 'Kitchen Head'],
		'81BCB53BA3D711E592E000FF59FBB323' => ['ordinal' => 38, 'code' => 'KO',  'postion' => 'OIC Kitchen'],
		'B3622DDF666611E596ECDA40B3C0AA12' => ['ordinal' => 40, 'code' => 'DS',  'postion' => 'Dining Supervisor'],
		'8EF16963673A11E596ECDA40B3C0AA12' => ['ordinal' => 42, 'code' => 'DA',  'postion' => 'Dining Assistant'],
		'A7006EB7A3D411E592E000FF59FBB323' => ['ordinal' => 44, 'code' => 'KS',  'postion' => 'Kitchen Supervisor'],
		'D02091AB673A11E596ECDA40B3C0AA12' => ['ordinal' => 46, 'code' => 'KA',  'postion' => 'Kitchen Assistant'],
		'DED0D6E6D8554709B7E5E4677BA9826A' => ['ordinal' => 48, 'code' => 'TAS', 'postion' => 'Training Assistant'],
		'BAC50136C67E4C6DA1B8C426B165A39F' => ['ordinal' => 50, 'code' => 'BT',  'postion' => 'Bartender'],
		'6B46E349A47E11E592E000FF59FBB323' => ['ordinal' => 52, 'code' => 'COK', 'postion' => 'Kitchen Cook'],
		'2862BBA2673B11E596ECDA40B3C0AA12' => ['ordinal' => 54, 'code' => 'DC',  'postion' => 'Dining Crew'],
		'2DA8CBFE673B11E596ECDA40B3C0AA12' => ['ordinal' => 56, 'code' => 'KC',  'postion' => 'Kitchen Crew'],
		'292FC22C808C11E6B7C800FF18C615EC' => ['ordinal' => 58, 'code' => 'TR1', 'postion' => 'Trainee I'],
		'76A923D32879406E8D6D62EB6F81277B' => ['ordinal' => 60, 'code' => 'TR2', 'postion' => 'Trainee II'],
		'179E8AB1C5BD402E90E69A7F14E7F16F' => ['ordinal' => 62, 'code' => 'TR3', 'postion' => 'Trainee III'],
		'C6A67A2F280F4634A5AF1BBECF6D901B' => ['ordinal' => 64, 'code' => 'TR4', 'postion' => 'Trainee IV'],
		'67B0F27F673B11E596ECDA40B3C0AA12' => ['ordinal' => 66, 'code' => 'UTI', 'postion' => 'Utility'],
		'6EA0DF78A18141DBA0E3BED84907F33A' => ['ordinal' => 68, 'code' => 'DJ',  'postion' => 'DJ'],
		'E16F473C86A94EF09C658286BEDEF89A' => ['ordinal' => 70, 'code' => 'TRA', 'postion' => 'Trainee'],
	],

  'paytype' => [
    '0' => ' ',
    '1' => 'Paid: Cash',
    '2' => 'Paid: Cheque',
    '3' => 'Unpaid: Utang',
    '4' => 'Paid: Head Office',
    '5' => 'Paid: GCash\PayMaya',
    '6' => 'Paid: Online',
    '7' => 'Paid: Others',
    '8' => 'Paid: Utang w/ Cash',
  ],

  'expensecode' => [
    'cos'  => ["GR","MP","FS","FV","RC","CK","SS","FC"],
    'ncos' => ["DN","DB","DA","CG","IC"],
    'opex' => ["SE","CA","ST","13","SI","AB","OS","RS","DF","RM","SL","AM","PC","LP","TL","SR","PL","PB","PR","KL","LS","TP","EB","WB","TC","EM","SY","RF","MS","TS"],
  ],
	
];