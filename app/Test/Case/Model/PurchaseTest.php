<?php
App::uses('Purchase', 'Model');

/**
 * Purchase Test Case
 *
 */
class PurchaseTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.purchase',
		'app.user',
		'app.attendance',
		'app.event',
		'app.category',
		'app.address',
		'app.city',
		'app.state',
		'app.bookmark',
		'app.comment',
		'app.complaint',
		'app.evaluation',
		'app.product',
		'app.item'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Purchase = ClassRegistry::init('Purchase');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Purchase);

		parent::tearDown();
	}

}
