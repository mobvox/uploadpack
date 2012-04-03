<?php
class UploadFixture extends CakeTestFixture { 
	public $name = 'Upload';

	public $fields = array( 
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'created' => 'datetime', 
		'updated' => 'datetime',
		'name' => array('type' => 'string', 'length' => 255, 'null' => false),
		'file' => array('type' => 'string', 'length' => 255, 'null' => true),
		'dir' => array('type' => 'string', 'length' => 255, 'null' => true),
		'size' => array('type' => 'integer', 'length' => 11, 'null' => true),
	);

	public $records = array( 
		array ('id' => 1, 'name' => 'First File', 'file' => 'test.zip', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'),
	);
}