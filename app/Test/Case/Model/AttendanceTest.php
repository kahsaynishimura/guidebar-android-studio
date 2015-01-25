<?php
App::uses('Attendance', 'Model');

/**
 * Attendance Test Case
 *
 */
class AttendanceTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.attendance',
		'app.user',
		'app.bookmark',
		'app.comment',
		'app.event',
		'app.category',
		'app.address',
		'app.city',
		'app.state',
		'app.complaint',
		'app.evaluation',
		'app.event_image',
		'app.ticket',
		'app.purchase'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Attendance = ClassRegistry::init('Attendance');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Attendance);

		parent::tearDown();
	}

}
