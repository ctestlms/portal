<?php
	$block_custom_reports_capabilities = array(
		'block/custom_reports:getattendancereport'	=> array(
			'captype'	=> 'read',
			'contextlevel' => CONTEXT_SYSTEM,
			'legacy'	=> array(
				'guest' 			=>	CAP_PREVENT,
				'student'			=>	CAP_PREVENT,
				'teacher'			=>	CAP_PREVENT,
				'editingteacher'	=> 	CAP_PREVENT,
				'coursecreator'		=> 	CAP_ALLOW,
				'admin'				=> 	CAP_ALLOW
			)
		),
		'moodle/course:downloadmaterial' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
		'block/custom_reports:getauditreport'	=> array(
			'captype'	=> 'read',
			'contextlevel' => CONTEXT_SYSTEM,
			'legacy'	=> array(
				'guest' 			=>	CAP_PREVENT,
				'student'			=>	CAP_PREVENT,
				'teacher'			=>	CAP_PREVENT,
				'editingteacher'	=> 	CAP_PREVENT,
				'coursecreator'		=> 	CAP_ALLOW,
				'admin'				=> 	CAP_ALLOW
			)
		),
                'block/custom_reports:getfeedbackreport'        => array(
                        'captype'       => 'read',
                        'contextlevel' => CONTEXT_SYSTEM,
                        'legacy'        => array(
                                'guest'                         =>      CAP_PREVENT,
                                'student'                       =>      CAP_PREVENT,
                                'teacher'                       =>      CAP_PREVENT,
                                'editingteacher'        =>      CAP_PREVENT,
                                'coursecreator'         =>      CAP_ALLOW,
                                'admin'                         =>      CAP_ALLOW
                        )
                )
,

                'block/custom_reports:getstudentsList'  => array(
                        'captype'       => 'read',
                        'contextlevel' => CONTEXT_SYSTEM,
                        'legacy'        => array(
                                'guest'                         =>      CAP_PREVENT,
                                'student'                       =>      CAP_PREVENT,
                                'teacher'                       =>      CAP_PREVENT,
                                'editingteacher'        =>      CAP_PREVENT,
                                'coursecreator'         =>      CAP_PREVENT,
                                'admin'                         =>      CAP_ALLOW,
                                'examdept'                      =>  CAP_ALLOW,
                                'acb'                           =>  CAP_ALLOW
                        )
                ),

                'block/custom_reports:getusagereport'   => array(
                        'captype'       => 'read',
                        'contextlevel' => CONTEXT_SYSTEM,
                        'legacy'        => array(
                                'guest'                         =>      CAP_PREVENT,
                                'student'                       =>      CAP_PREVENT,
                                'teacher'                       =>      CAP_PREVENT,
                                'editingteacher'        =>      CAP_PREVENT,
                                'coursecreator'         =>      CAP_ALLOW,
                                'admin'                         =>      CAP_ALLOW
                        )
                )

		,
                'block/custom_reports:getmcr'   => array(
                                        'captype'       => 'read',
                                        'contextlevel' => CONTEXT_SYSTEM,
                                        'legacy'        => array(
                                                'guest'                         =>      CAP_PREVENT,
                                                'student'                       =>      CAP_PREVENT,
                                                'teacher'                       =>      CAP_PREVENT,
                                                'editingteacher'        =>      CAP_PREVENT,
                                                'coursecreator'         =>      CAP_PREVENT,
                                                'admin'                         =>      CAP_ALLOW
)
),
		
                'block/custom_reports:getembaregreport' => array(
                                        'captype'       => 'read',
                                        'contextlevel' => CONTEXT_SYSTEM,
                                        'legacy'        => array(
                                                'guest'                         =>      CAP_PREVENT,
                                                'student'                       =>      CAP_PREVENT,
                                                'teacher'                       =>      CAP_PREVENT,
                                                'editingteacher'        =>      CAP_PREVENT,
                                                'coursecreator'         =>      CAP_PREVENT,
                                                'admin'                         =>      CAP_ALLOW

)
),
		
		'block/custom_reports:getresults'	=> array(
			'captype'	=> 'read',
			'contextlevel' => CONTEXT_SYSTEM,
			'legacy'	=> array(
				'guest' 			=>	CAP_PREVENT,
				'student'			=>	CAP_PREVENT,
				'teacher'			=>	CAP_PREVENT,
				'editingteacher'	=> 	CAP_PREVENT,
				'coursecreator'		=> 	CAP_PREVENT,
				'admin'				=> 	CAP_ALLOW
				
			)
		)

,
		'block/custom_reports:get_student_semester_report'	=> array(
					'captype'	=> 'read',
					'contextlevel' => CONTEXT_SYSTEM,
					'legacy'	=> array(
						'guest' 			=>	CAP_PREVENT,
						'student'			=>	CAP_PREVENT,
						'teacher'			=>	CAP_PREVENT,
						'editingteacher'	=> 	CAP_PREVENT,
						'coursecreator'		=> 	CAP_PREVENT,
						'admin'				=> 	CAP_ALLOW

)
)
,


  'block/custom_reports:add_remove_feeDefaulter' => array(
        'captype' => 'write',
        'contextlevel' =>CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW,
            'editingteacher' =>         CAP_PREVENT,
                        'admin'                         =>      CAP_ALLOW
        )
    )
	,
    'block/custom_reports:viewtranscript' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
            'admin' => CAP_ALLOW
        )
    ) ,
    'block/custom_reports:getcoursefeedback_avg' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
            'admin' => CAP_ALLOW
        )
    ),
    'block/custom_reports:getsubmittedresults' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'coursecreator' => CAP_PREVENT,
            'admin' => CAP_ALLOW
        )
    )


	);
?>
