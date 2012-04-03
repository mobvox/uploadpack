<?php 
App::uses('Upload', 'UploadPack.Model/Behavior');

class TestUpload extends CakeTestModel {
	public $useTable = 'uploads';
	public $actsAs = array(
		'UploadPack.Upload' => array(
			'file'
		)
	);
}

class UploadBehaviorTest extends CakeTestCase{
	public $fixtures = array('plugin.uploadPack.upload');
	public $TestUpload = null;
	public $Behavior = null;
	public $data = array(
		'ok' => array(
			array(
				'name' => 'Test File',
				'file' => array(
					'name' => 'test_file.zip',
					'tmp_name' => 'test_file.zip',
					'type' => 'multipart/x-zip',
					'size' => 65536,
					'error' => UPLOAD_ERR_OK
				)
			)
		)
	);

	public function startTest() {
		$this->setupBehavior();
	}

	private function setupBehavior($settings = array()){
		unset($this->TestUpload);
		unset($this->Behavior);

		$this->TestUpload = ClassRegistry::init('TestUpload');
		$this->Behavior = new UploadBehavior();

		if(empty($settings)){
			$settings = $this->TestUpload->actsAs['UploadPack.Upload'];
		}

		$this->Behavior->setup($this->TestUpload, $settings);
		$this->TestUpload->Behaviors->Upload = $this->Behavior;
	}

	public function testDefaultSettings(){
		$expected = array(
			'TestUpload' => array(
				'file' => array(
					'path' => ':webroot/upload/:model/:id/:style/:basename.:extension',
					'styles' => array(),
					'resizeToMaxWidth' => false,
					'quality' => (int) 95,
					'overwrite' => false,
					'fields' => array(
						'dir' => 'dir',
						'extension' => 'extension',
						'size' => 'size',
						'mime_type' => 'mime_type'
					)
				)
			)
		);

		$this->assertEqual($this->TestUpload->Behaviors->Upload->settings, $expected);
	}

	public function testMergeSettings() {
		$settings = array(
			'file' => array(
				'path' => ':webroot/files/:model/:style/:basename.:extension',
				'resizeToMaxWidth' => true,
				'fields' => array(
					'dir' => 'directory',
					'extension' => 'ext'
				)
			)
		);
		$this->setupBehavior($settings);

		$expected = array(
			'TestUpload' => array(
				'file' => array(
					'path' => ':webroot/files/:model/:style/:basename.:extension',
					'styles' => array(),
					'resizeToMaxWidth' => true,
					'quality' => (int) 95,
					'overwrite' => false,
					'fields' => array(
						'dir' => 'directory',
						'extension' => 'ext',
						'size' => 'size',
						'mime_type' => 'mime_type'
					)
				)
			)
		);

		$this->assertEqual($this->TestUpload->Behaviors->Upload->settings, $expected);
	}

	public function testInterpolate() {
		$result = $this->TestUpload->Behaviors->Upload->interpolate($this->TestUpload, 'file', 'file.zip');
		$expected = array(
			'path' => WWW_ROOT . 'upload' . DS . 'test_uploads' . DS . 'file.zip',
			'styles' => array(),
			'resizeToMaxWidth' => false,
			'quality' => (int) 95,
			'overwrite' => false,
			'fields' => array(
				'dir' => 'dir',
				'extension' => 'extension',
				'size' => 'size',
				'mime_type' => 'mime_type'
			)
		);

		$this->assertEqual($result, $expected);

		$settings = array(
			'file' => array(
				'path' => ':webroot/files/:month/:year/:style/:basename.:extension',
			)
		);

		$this->setupBehavior($settings);
		$result = $this->TestUpload->Behaviors->Upload->interpolate($this->TestUpload, 'file', 'test.jpg', 'small');

		$expected = array(
			'path' => WWW_ROOT . 'files' . DS . date('m') . DS . date('Y') . DS . 'small' . DS . 'test.jpg',
			'styles' => array(),
			'resizeToMaxWidth' => false,
			'quality' => (int) 95,
			'overwrite' => false,
			'fields' => array(
				'dir' => 'dir',
				'extension' => 'extension',
				'size' => 'size',
				'mime_type' => 'mime_type'
			)
		);

		$this->assertEqual($result, $expected);
	}

}