CakePHP Plugin auditable_mongo_logger
=====================================

Plugin com os modelos do Auditable adaptado ao CakeMongoDb

Requisitos
----------

[Plugin Auditable](https://github.com/radig/auditable)
[Plugin CakeMongoDb](https://github.com/ichikaway/cakephp-mongodb)

Configuração
------------

Adicione ao seu database.php uma nova conexão, nomeada **mongo**, que
deve ter uma estrutra como:

    public $mongo = array(
        'datasource' => 'Mongodb.MongodbSource',
        'database' => '_MEUDATABASE_',
    );

No callback **beforeFilter** do *app_controller.php* inclua algo como:

    AuditableConfig::$Logger =& ClassRegistry::init('AuditableMongoLogger.Logger');

A partir daí sua aplicação começará a salvar os logs gerados pelo Auditable na sua
base _MEUDATABASE_. Basta criar um controller que busque as informações no modelo
provido pelo plugin para gerar uma paginação ou qualquer outra ação que for de interesse.

Licença
-------

Licença MIT – Isto quer dizer que o código está disponível sem nenhuma garantia, ao mesmo tempo que você pode usa-lo de forma isolada ou em conjunto com seu próprio código.
Segue abaixo os detalhes da licença.

Copyright (c) 2012 [Radig – Soluções em TI](http://radig.com.br)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.