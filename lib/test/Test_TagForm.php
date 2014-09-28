<?php

/**
 * test case.
 */
class Test_TagForm extends PHPUnit_Framework_TestCase {
	private $smile;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
	
		// TODO Auto-generated Test_TagForm::setUp()
	

	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated Test_TagForm::tearDown()
		

		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		include dirname(realpath(__FILE__)).'/../index.php';
		$this->smile	=	new Smile();
		Smile::config('secureStr','');
		Smile::config('tplDir',dirname(realpath(__FILE__)).'/form_template/');
	}
	
	function test_input() {
		Smile::config('tplDir',Smile::config('tplDir').'input/');
		$this->check('onlyname','<input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name);?>" />');
		$this->check('noid','<input type="text" name="name" value="<?php echo htmlspecialchars($name);?>" />');
	}
	
	private function check($file,$equalStr,$params=array()) {
		$cacheFilePath	=	Smile::getCacheFilePath($file,'form');
		$this->smile->compile($file,'form',array());
		$this->assertEquals($equalStr, file_get_contents($cacheFilePath));
	}

}

