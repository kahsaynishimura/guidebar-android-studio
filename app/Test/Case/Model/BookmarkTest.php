<?php
App::uses('Bookmark', 'Model');

/**
 * Bookmark Test Case
 *
 */
class BookmarkTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.bookmark',
		'app.user',
		'app.attendance',
		'app.event',
		'app.category',
		'app.address',
		'app.city',
		'app.state',
		'app.comment',
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
		$this->Bookmark = ClassRegistry::init('Bookmark');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Bookmark);

		parent::tearDown();
	}

}
