<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
   'soundizer-sections' => array(
		'music' => array(
			'id' => 1,
			'filters' => array('source', 'category', 'track', 'bpm', 'instrument', 'mood', 'genre', 'author'),
		),
		'fx-sounds' => array(
			'id' => 2,
			'filters' => array('source', 'category', 'track', 'author')
		),
		'logos-and-idents' => array(
			'id' => 3,
			'filters' => array('source', 'category', 'track', 'bmp', 'instrument', 'mood'. 'author')
		),
	)
);
