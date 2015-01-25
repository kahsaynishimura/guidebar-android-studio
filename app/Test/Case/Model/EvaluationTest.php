<?php
App::uses('Evaluation', 'Model');

/**
 * Evaluation Test Case
 *
 */
class EvaluationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.evaluation',
		'app.event',
		'app.category',
		'app.user',
		'app.attendance',
		'app.bookmark',
		'app.comment',
		'app.complaint',
		'app.purchase',
		'app.address',
		'app.city',
		'app.state',
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
		$this->Evaluation = ClassRegistry::init('Evaluation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Evaluation);

		parent::tearDown();
	}

}
