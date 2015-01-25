<?php
App::uses('m', 'Model');

/**
 * m Test Case
 *
 */
class mTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.m',
		'app.state',
		'app.address',
		'app.city',
		'app.event',
		'app.category',
		'app.user',
		'app.attendance',
		'app.bookmark',
		'app.comment',
		'app.complaint',
		'app.evaluation',
		'app.purchase',
		'app.event_image',
		'app.ticket'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->m = ClassRegistry::init('m');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->m);

		parent::tearDown();
	}

}
