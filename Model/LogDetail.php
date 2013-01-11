<?php
/**
 * Classe que representa os detalhes de um log do sistema.
 * Depende da existência de uma conexão nomeada 'mongo', utilizando
 * o CakeMongoDb ( https://github.com/ichikaway/cakephp-mongodb )
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
class LogDetail extends AppModel
{
	public $name = 'LogDetail';

	public $useDbConfig = 'mongo';

	public $useTable = 'log_details';

	public $primaryKey = '_id';

	public $hasOne = array(
		'Logger' => array('className' => 'AuditableMongoLogger.Logger')
	);

	public $mongoSchema = array(
		'difference' => array('type' => 'text'),
		'statement' => array('type' => 'text'),
		'created' => array('type' => 'datetime'),
		'modified' => array('type' => 'datetime'),
	);
}
