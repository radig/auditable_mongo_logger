<?php
App::import('Vendor', 'Auditable.AuditableConfig');
/**
 * Classe que representa as informações de log do sistema.
 * Depende da existência de uma conexão nomeada 'mongo', utilizando
 * o CakeMongoDb ( https://github.com/ichikaway/cakephp-mongodb
 *
 * PHP version 5
 *
 * Copyright 2012, Radig Soluções em TI. (http://www.radig.com.br)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2012, Radig Soluções em TI. (http://www.radig.com.br)
 * @link          http://www.radig.com.br
 * @package       Radig.AuditableMongoLogger
 * @subpackage    Radig.AuditableMongoLogger.Models
 */
class Logger extends AppModel
{
	public $name = 'Logger';

	public $useDbConfig = 'mongo';

	public $useTable = 'logs';

	public $actsAs = array(
		'Containable',
		'Mongodb.SqlCompatible'
	);

	public $belongsTo = array(
		'AuditableMongoLogger.LogDetail' => array('className' => 'LogDetail')
	);

	public $mongoSchema = array(
		'user_id' => array('type' => 'integer'),
		'model_alias' => array('type' => 'string'),
		'model_id' => array('type' => 'integer'),
		'type' => array('type' => 'integer'),
		'log_detail_id' => array('type' => 'integer'),
		'created' => array('type' => 'datetime'),
		'modified' => array('type' => 'datetime'),
	);

	public $validates = array(
		'model_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => array('ID do item de referência é obrigatório')
			)
		),
		'model_alias' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => array('Alias do item de referência é obrigatório')
			)
		)
	);

	/**
	 *
	 * @param int $id
	 * @param  bool $loadResource
	 * @return array
	 */
	public function get($id, $loadResource = true)
	{
		$contain = array('LogDetail');

		$data = $this->find('first', array(
			'conditions' => array('Logger._id' => $id),
		));

		$logger = $this->LogDetail->find('first', array(
			'conditions' => array('LogDetail._id' => $data['Logger']['log_detail_id']),
		));

		$data['LogDetail'] = $logger['LogDetail'];

		$linked = null;

		if($loadResource)
		{
			$Resource = ClassRegistry::init($data[$this->name]['model_alias']);

			$linked = $Resource->find('first', array(
				'conditions' => array('id' => $data[$this->name]['model_id']),
				'recursive' => -1
				)
			);
		}

		if(!empty($linked))
		{
			$data[$Resource->name] = $linked[$Resource->name];
		}

		$data['Responsible']['name'] = '';
		if(!empty(AuditableConfig::$userModel) && !empty($data['Logger']['user_id']))
		{
			$userModel = ClassRegistry::init(AuditableConfig::$userModel);
			$userModel->recursive = -1;
			$user = $userModel->read(null, $data['Logger']['user_id']);

			$data['Responsible'] = $user['User'];
			$data['Responsible']['name'] = $user['User']['name'];
		}

		return $data;
	}

	/**
	 * Implementa função existis da API do Cake 2.0 na versão 1.3
	 *
	 * Verifica se um registro existe no BD
	 *
	 * @return bool
	 */
	public function exists()
	{
		if(empty($this->id))
			return false;

		return ($this->find('count', array('conditions' => array('Logger.id' => $this->id), 'recursive' => -1)) > 0);
	}
}