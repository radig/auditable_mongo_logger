<?php
App::uses('AuditableConfig', 'Auditable.Lib');
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
 * @subpackage    Radig.AuditableMongoLogger.Model
 */
class Logger extends AppModel
{
	public $name = 'Logger';

	public $useTable = 'logs';

	public $primaryKey = '_id';

	public $actsAs = array(
		'Containable',
		'Mongodb.SqlCompatible'
	);

	public $belongsTo = array(
		'LogDetail' => array('className' => 'AuditableMongoLogger.LogDetail')
	);

	public $mongoSchema = array(
		'responsible_id' => array('type' => 'string'),
		'model_alias' => array('type' => 'string'),
		'model_id' => array('type' => 'string'),
		'type' => array('type' => 'integer'),
		'log_detail_id' => array('type' => 'string'),
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
			'conditions' => array("Logger.{$this->primaryKey}" => $id),
		));

		$logger = $this->LogDetail->find('first', array(
			'conditions' => array("LogDetail.{$this->LogDetail->primaryKey}" => $data['Logger']['log_detail_id']),
		));

		$data['LogDetail'] = $logger['LogDetail'];

		$linked = null;

		if($loadResource)
		{
			$Resource = ClassRegistry::init($data[$this->alias]['model_alias']);

			$linked = $Resource->find('first', array(
				'conditions' => array($Resource->primaryKey => $data[$this->alias]['model_id']),
				'recursive' => -1
				)
			);

		}

		if(!empty($linked))
		{
			$data[$Resource->alias] = $linked[$Resource->alias];
		}

		$data['Responsible'] = array();
		if(!empty(AuditableConfig::$responsibleModel) && !empty($data['Logger']['responsible_id']))
		{
			$responsibleModel = ClassRegistry::init(AuditableConfig::$responsibleModel);
			$responsibleModel->recursive = -1;
			$aux = $responsibleModel->read(null, $data['Logger']['responsible_id']);

			if(!empty($aux)) {
				$data['Responsible'] = $aux[AuditableConfig::$responsibleModel];
			} else {
				$data['Responsible'][$responsibleModel->displayField] = '';
			}
		}

		return $data;
	}
}
