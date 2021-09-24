<?php
//require('config.php');
const HOST = "localhost";
const USER = "root";
const PASSWORD = "1qazxsw2";
const DBNAME = "_hobb";

abstract class DBTemplate {
    abstract function insert($table, $fields, $values);
    abstract function select($params, $table, $condition);
    abstract function update($table, $params_values, $condition);
    abstract function delete($table, $condition);
}

/*
* @author ben_taiba
* only return true or false only for INSERT, UPDATE, DELETE statements
* only return values in arrays for SELECT statements
*/
class Database extends DBTemplate {
    public $pdo = null;
    private $mysqli = null;
    private $statement = null;
    private $sql = "";
    private $feedback = array();
    private $success = false;
    
    function __construct($host=null, $user=null, $password=null, $dbname=null){
        $this->set_db_objects($host, $user, $password, $dbname);
    }
    
    function set_db_objects($host, $user, $password, $dbname){
        $this->pdo = new PDO("mysql:host=" . $host . ";dbname=" . $dbname, $user, $password);;
        $this->mysqli = new mysqli($host, $user, $password, $dbname);
    }
    
    function insert($table, $fields, $values){
        /* string table: name of table
        * array fields: names of fields in array
        * array values: values for fields in array
        */
        $params = "";
        for($i=0; $i<count($values); $i++) { // put number of params for the sql statement
            $params .= ($i+1) == count($values) ? "?" : "?, ";
        }
        $this->sql = "INSERT INTO `" . $table . "` (" . join(',', $fields) . ") VALUES (" . $params . ")";

        $this->pdo->quote($this->sql);
        $this->statement = $this->pdo->prepare($this->sql);
        $this->success = $this->statement->execute($values);
        
        return !$this->success;
    }
    
    function select($params, $table, $condition){
        /* array params: the fields to select either an array or a string
        * string table: the table to select from
        * string condition: the conditions for selecting
        * any other conditons or key word comes with the condition value
        */

        $this->sql = "SELECT " . join(',', $params) . " FROM " . $table . " WHERE " . $condition;
        $this->pdo->quote($this->sql);
        $this->statement = $this->pdo->prepare($this->sql);
        $this->success = $this->statement->execute();
        
        if($this->success):
            while($values = $this->statement->fetch()):
                $this->feedback[] = $values;
            endwhile;
        endif;
        
        return $this->feedback;
    }
    
    function update($table, $params_values, $condition) {
        /* string table: name of table
        ^ string param values: values to set
        * string condition: conditions for updating
        */
        
        $this->sql = "UPDATE `" . $table . "` SET " . $params_values . " WHERE " . $condition;
        mysql_real_escape_string($this->sql);
        $this->success = $this->mysqli->query($this->sql);
        
        return $this->success;
    }
    
    function delete($table, $condition){
        /*
        * string table: name of table
        * string condition: condition for deleting
        */
        
        $this->sql = "DELETE FROM `" . $table . "` WHERE " . $condition;
        mysql_real_escape_string($this->sql);
        $this->success = $this->mysqli->query($this->sql);
        
        return $this->success;
    }
    
    function __destruct(){
        $this->pdo = null;
        $this->mysqli = null;
        $this->statement = null;
        $this->sql = "";
        $this->feedback = null;
        $this->success = false;
    }
    
}

# create a database object
$data_servant = new Database(HOST, USER, PASSWORD, DBNAME);

?>
