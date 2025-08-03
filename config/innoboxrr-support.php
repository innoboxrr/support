<?php

return [

	'user_class' => 'App\Models\User',

	'excel_view' => 'innoboxrrsupport::excel.',

	'notification_via' => ['mail', 'database'],

	'export_disk' => 's3',

	'jobs' => [
		'force_async' => false, // Forzar ejecución asíncrona de jobs
	],
	
];