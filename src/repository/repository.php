<?php

namespace repository;

/**
 * Description of repository
 *
 * @author amelendezi
 */
class Repository {

    protected $connection = "mysql:host=localhost;dbname=amerepo;charset=utf8mb4";
    protected $username = "amerepouser";
    protected $password = "fGP37qjthhAp9RU8";
    protected $dbparams = array(\PDO::ATTR_EMULATE_PREPARES => false, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
    private $repositoryHelper;

    function __construct() {
        $this->repositoryHelper = new RepositoryHelper();
    }

    function Push(Storable $storable) {
        try {
            // Connect
            $connection = $this->Connect();

            // Insert statement literal
            $insertStatement = $this->repositoryHelper->GetInsertStatement($storable);

            // Prepare statement
            $preparedStatement = $connection->prepare($insertStatement);

            // Bind the columns to object values
            foreach ($storable as $key => $value) {
                if ($key != "id") {
                    $preparedStatement->bindParam(":" . $key, $storable->$key);
                }
            }

            // Execute
            $preparedStatement->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    function Pull(Storable $storable)
    {
        try{
            // Connect
            $connection = $this->Connect();
            
            // Statement
            $preparedStatement = $connection->prepare($this->repositoryHelper->GetSelectStatementByInstanceId($storable));
            
            // Bind the instanceId
            $preparedStatement->bindParam(":instanceId", $storable->instanceId);
            
            echo "\r\nInstanceId: " . $storable->instanceId;
            
            // Execute and return
            return $preparedStatement->execute();
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    function ClearTable($tableName)
    {
        try {
            // Connect
            $connection = $this->Connect();
            
            // Execute
            $connection->exec("TRUNCATE TABLE " . $tableName);
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    private function Connect() {
        return new \PDO($this->connection, $this->username, $this->password, $this->dbparams);
    }
}