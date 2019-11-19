<?php defined('SYSPATH') or die('No direct script access.');

require_once ('vendor/autoload.php');
use GraphAware\Neo4j\Client\ClientBuilder AS Neo4j;

class Task_Neo4jtest extends Minion_Task
{


    public function _execute(array $params)
    {


        $neo4j = Neo4j::create()
            ->addConnection('default', 'http://neo4j:11111@localhost:7474') // Example for HTTP connection configuration (port is optional)
            ->setDefaultTimeout(500)
            ->build(); // соединение


        //создаем ключи и ограничения
        $neo4j->run('CREATE CONSTRAINT ON (c:Category) ASSERT c.category_id IS UNIQUE');
        $neo4j->run('CREATE CONSTRAINT ON (m:Model) ASSERT m.model_id IS UNIQUE');
        $neo4j->run('CREATE CONSTRAINT ON (t:Type) ASSERT t.type_id IS UNIQUE');
        $neo4j->run('CREATE CONSTRAINT ON (p:Part) ASSERT p.part_id IS UNIQUE');

        //чистим базу
        $neo4j->run('MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE n,r');


        $neo4j->run('
        USING PERIODIC COMMIT 10000
        LOAD CSV WITH HEADERS FROM "file:///tmp/export_2.csv" AS file
        MERGE (c:Category{category_id:toInt(file.category_id)})
        MERGE (m:Model{model_id:toInt(file.model_id)})
        MERGE (t:Type{type_id:toInt(file.type_id)})
        MERGE (p:Part{part_id:toInt(file.part_id)})    
        CREATE UNIQUE (c)-[:has]->(m)
        CREATE UNIQUE (m)-[:has]->(t)
        CREATE UNIQUE (t)-[:has]->(p)
        ');

    }


    public function log($message)
    {
        fwrite(STDOUT,  date('Y-m-d H:i:s') . "___________" . $message . "\n");
    }
}
