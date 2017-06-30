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


	'upload_path' => [
		'temp' => public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR,
		'web' => public_path().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR,
		'pos' => [
			'local' => base_path().DIRECTORY_SEPARATOR.'TEST_POS_BACKUP'.DIRECTORY_SEPARATOR,
			'production' => '/home/server-admin/Public/maindepot/TEST_POS_BACKUP/'
		],
		'files' => [
			'local' => base_path().DIRECTORY_SEPARATOR.'TEST_FILES_BACKUP'.DIRECTORY_SEPARATOR,
			'production' => '/home/server-admin/Public/maindepot/TEST_FILES_BACKUP/'
		]
	],

	'hours' => [
		6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5
	]

];