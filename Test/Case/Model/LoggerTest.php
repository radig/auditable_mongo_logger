<?php
App::uses('AuditableConfig', 'Auditable.Lib');
App::uses('Logger', 'AuditableMongoLogger.Model');

if(!class_exists('User'))
{
	class User extends CakeTestModel
	{
		public $callbackData = array();

		public $useTable = 'users';

		public $name = 'User';
	}
}

class LoggerTest extends CakeTestCase {
	public $Logger = null;

	public $User = null;

	public $plugin = 'Auditable';

	public $fixtures = array(
		'plugin.auditable_mongo_logger.logger',
		'plugin.auditable_mongo_logger.log_detail',
		'plugin.auditable.user'
	);

	public function setUp()
	{
		$this->Logger = ClassRegistry::init('AuditableMongoLogger.Logger');
		$this->User = ClassRegistry::init('User');

		AuditableConfig::$responsibleModel = 'Auditable.User';
	}

	public function startTest($method)
	{
		parent::startTest($method);
		$this->skipIf(!is_a($this->Logger->getDataSource(), 'MongodbSource'), 'Testes válidos apenas quando usando MongodbSource');
	}

	public function tearDown()
	{
		parent::tearDown();

		$this->Logger->deleteAll(true, false);
		$this->Logger->LogDetail->deleteAll(true, false);
		$this->User->deleteAll(true, false);

		unset($this->Logger);
		unset($this->User);
		ClassRegistry::flush();
	}

	public function testLoggerInstance()
	{
		$this->assertTrue(is_a($this->Logger, 'Logger'));
	}

	public function testWriteLog()
	{
		$toSave = array(
			'Logger' => array(
				'responsible_id' => 0,
				'model_alias' => 'Teste',
				'model_id' => 1,
				'type' => 1,
			),
			'LogDetail' => array(
				'difference' => '{}',
				'statement' => 'UPDATE',
			)
		);

		$result = $this->Logger->saveAll($toSave);
	}

	public function testReadLog()
	{
		$this->Logger->recursive = -1;
		$result = $this->Logger->read(null, 1);
		$expected = array(
			'Logger' => array(
				'_id'  => 1,
				'responsible_id'  => 0,
				'model_alias' => 'Auditable.User',
				'model_id' => 1,
				'log_detail_id' => 1,
				'type' => 1,
				'created'  => '2012-03-08 15:20:10',
				'modified'  => '2012-03-08 15:20:10'
			)
		);

		$this->assertEqual($result, $expected);
	}

	public function testGetWithoutResourceLog()
	{
		$result = $this->Logger->get(1, false);

		$expected = array(
			'Logger' => array(
				'_id'  => 1,
				'responsible_id' => 0,
				'model_alias' => 'Auditable.User',
				'model_id' => 1,
				'log_detail_id' => 1,
				'type' => 1,
				'created'  => '2012-03-08 15:20:10',
				'modified'  => '2012-03-08 15:20:10'
			),
			'LogDetail' => array(
				'_id' => 1,
				'difference' => '{}',
				'statement' => '',
				'created'  => '2012-03-08 15:20:10',
				'modified'  => '2012-03-08 15:20:10'
			),
			'Responsible' => array()
		);

		$this->assertEqual($result, $expected);

		AuditableConfig::$responsibleModel = null;
		$result = $this->Logger->get(1, false);

		$expected = array(
			'Logger' => array(
				'_id'  => 1,
				'responsible_id'  => 0,
				'model_alias' => 'Auditable.User',
				'model_id' => 1,
				'log_detail_id' => 1,
				'type' => 1,
				'created'  => '2012-03-08 15:20:10',
				'modified'  => '2012-03-08 15:20:10'
			),
			'LogDetail' => array(
				'_id' => 1,
				'difference' => '{}',
				'statement' => '',
				'created'  => '2012-03-08 15:20:10',
				'modified'  => '2012-03-08 15:20:10'
			),
			'Responsible' => array()
		);

		$this->assertEqual($result, $expected);
	}

	public function testGetWithResourceLog()
	{
		$result = $this->Logger->get(1);

		$expected = array(
			'Logger' => array(
				'_id'  => 1,
				'responsible_id'  => 0,
				'model_alias' => 'Auditable.User',
				'model_id' => 1,
				'log_detail_id' => 1,
				'type' => 1,
				'created'  => '2012-03-08 15:20:10',
				'modified'  => '2012-03-08 15:20:10'
			),
			'LogDetail' => array(
				'_id' => 1,
				'difference' => '{}',
				'statement' => '',
				'created'  => '2012-03-08 15:20:10',
				'modified'  => '2012-03-08 15:20:10'
			),
			'User' => array(
				'id'  => 1,
				'username'  => 'userA',
				'email' => 'user_a@radig.com.br',
				'created'  => '2012-03-08 15:20:10',
				'modified'  => '2012-03-08 15:20:10',
				'created_by' => null,
				'modified_by' => null
			),
			'Responsible' => array()
		);

		$this->assertEqual($result, $expected);
	}
}
