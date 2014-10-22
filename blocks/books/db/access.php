<?php
$block_books_capabilities = array(
		'block/books:edit'	=> array(
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

);
?>