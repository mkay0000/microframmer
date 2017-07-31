<?php
namespace Core;

use App\Config;
/**
* Model Class
* Controlls the link between Controllers and Database/Models
* 
* PHP > v5.6
* @version 1.0
* @author Mikail Khan a.k.a MKAY <msupper61@yahoo.com || khanmikail3@gmail.com>
*/
class Model
{
	/**
	 * $_connection stores the connection of database
	 * @var string
	 */
	protected $_connection;

	/**
	 * $_query for querying the database
	 * @var array
	 */
	protected $_query;

	/**
	 * $_table The table to be queried
	 * @var string
	 */
	protected $_table;

	/**
	 * $_values store values for the where cluase 
	 * @var array	 
	 */
	protected $_values = []; 

	/**
	 * $_updateValues stores values for the update function
	 * @var array
	 */
	protected $_updateValues = [];

	/**
	 * $_fetchedQuery stores the results from queried data
	 * @var string
	 */
	protected $_fetchedQuery;

	/**
	 * $_columnNames stores column names fetched from database
	 * @var array
	 */
	protected $_columnNames = [];

	/**
	 * $_timestampCreated_at default true for created_at or false for not creating created_at column.
	 * NOTE Set to be false if not in use for good performance!
	 * @var boolean
	 */
	protected $_timestampCreated_at = true;
	
	/**
	 * Connection to the database
	 * @return void
	 */
	public function __construct()
	{
		$database = Config::DBDRIVER . ':host=' . Config::DBHOST . ';dbname=' . Config::DBNAME;
		$this->_connection = new \PDO($database, Config::DBUSER, Config::DBPASS);
		$this->_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->_table = $this->tableNameFormat(get_class($this));
		$this->_query = [];

		// Fetch Columns
		$this->fetchColumns();
		// Default for created_at
		if($this->_timestampCreated_at){
			$this->created_at = date("Y-m-d H:i:s");
		}
	}

	# ####################
	#
	# Query Functions 
	# 
	# ####################
	
	/**
	 * Dev defined query
	 * 
	 * @param  string $query 
	 * @param array $values values for the query
	 * @return results
	 */
	public function query($query, $values = [])
	{
		$query = $this->_connection->prepare($query);
		$query->execute($values);
		return $query->fetchAll();
	}
	
	/**
	 * SELECT QUERY for quering the database
	 * @param  string $select select type i.e id,name,email etc...
	 * @return $this
	 */
	public function select($select = '*')
	{
		$this->_query = ["SELECT $select FROM " . $this->_table];
		return $this;
	}

	/**
	 * all() method is for quering all the data from database 
	 * @return $this
	 */
	public function all()
	{
		$this->_query = ["SELECT * FROM " . $this->_table];
		return $this;
	}

	/**
	 * id() searches for the specified id number
	 * return $this;
	 */
	public function id($id, $select = '*')
	{
		$this->_query = ["SELECT $select FROM " . $this->_table . " WHERE id = ?"];
		$this->_values[] = $id;
		return $this;
	}

	/**
	 * where for condition in query
	 * @param  string $column   column name
	 * @param  string $operator i.e < > = != 
	 * @param  string $value    the value to be looked for
	 * @return $this
	 */
	public function where($column, $operator, $value)
	{
		$this->_query[] = " WHERE $column $operator :where_$column";
		$this->_values[":where_$column"] = $value;
		return $this;
	}

	/**
	 * orWhere for condition in query
	 * @param  string $column   column name
	 * @param  string $operator i.e < > = != 
	 * @param  string $value    the value to be looked for
	 * @return $this
	 */
	public function orWhere($column, $operator, $value)
	{
		$this->_query[] = " OR $column $operator :orWhere_$column";
		$this->_values[":orWhere_$column"] = $value;
		return $this;
	}

	/**
	 * andWhere for condition in query
	 * @param  string $column   column name
	 * @param  string $operator i.e < > = != 
	 * @param  string $value    the value to be looked for
	 * @return $this
	 */
	public function andWhere($column, $operator, $value)
	{
		$this->_query[] = " AND $column $operator :andWhere_$column";
		$this->_values[":andWhere_$column"] = $value;
		return $this;
	}

	/**
	 * Like() cluase for querying data from database
	 * @param  string $column column name
	 * @param  string $like   value
	 * @return $this         
	 */
	public function like($column, $like)
	{
		$this->_query[] = " WHERE $column LIKE :like_$column";
		$this->_values[":like_$column"] = $like;
		return $this;
	}

	/**
	 * orLike() cluase for querying data from database
	 * @param  string $column column name
	 * @param  string $like   value
	 * @return $this         
	 */
	public function orLike($column, $like)
	{
		$this->_query[] = " OR $column LIKE :orLike_$column";
		$this->_values[":orLike_$column"] = $like;
		return $this;
	}

	/**
	 * andLike() cluase for querying data from database
	 * @param  string $column column name
	 * @param  string $like   value
	 * @return $this         
	 */
	public function andLike($column, $like)
	{
		$this->_query[] = " AND $column LIKE :andLike_$column";
		$this->_values[":andLike_$column"] = $like;
		return $this;
	}

	/**
	 * first() method return only one row from the data
	 * @return $this
	 */
	public function first()
	{
		$this->_query[] = " LIMIT 1";
		return $this;
	}

	/**
	 * orderBy order the data by column name.
	 * 
	 * @param  string $column column name.
	 * @param  string $order  Asending or Desending orders (ASC or DESC)
	 * @return $this
	 */
	public function orderBy($column, $order = 'ASC')
	{
		$this->_query[] = " ORDER BY $column $order";
		return $this;
	}

	/**
	 * limit limits the data to be fetched
	 * @param  integer $limit limit data i.e 5 
	 * @return $this
	 */
	public function limit($limit)
	{
		$this->_query[] = " LIMIT $limit";
		return $this;
	}

	/**
	 * skip skips data and limit the data 
	 * i.e 5, 10 will skip 5 and show only 10 results
	 * 
	 * @param  integer $skip  skips
	 * @param  integer $limit limits the data
	 * @return $this
	 */
	public function skip($skip, $limit)
	{
		$this->_query[] = " LIMIT $skip, $limit";
		return $this;
	}

	/**
	 * get method fetches data from the database
	 * @return array data fetched from database
	 */
	public function get()
	{
		$query = $this->queryGenerator($this->_query);
		$query = $this->_connection->prepare($query);
		$query->execute($this->_values);
		$this->_fetchedQuery = $query->fetchAll();
		return $this->_fetchedQuery;
	}

	/**
	 * save() for inserting data into tables
	 * @return boolean
	 */
	public function save()
	{
		// Getting Columns
		$columns = new \ArrayIterator($this->_columnNames);
		$columns = new \CachingIterator($columns);
		$cols = '';
		$values = [];
		foreach ($columns as $column) {
			// Col Names
			if($columns->hasNext()){
				$cols .= "$column, ";
			} else {
				// Last column
				$cols .= "$column"; 
			}
			// Values 
			$values[$column] = $this->{$column};
		}
		//Extracting Values
		$colValues = new \ArrayIterator($values);
		$colValues = new \CachingIterator($colValues);
		$vals = '';
		foreach($colValues as $col => $val){
			if($colValues->hasNext()){
				$vals .= ":$col, ";
			}else {
				// Last value
				$vals .= ":$col";
			}
		}

		// Query 
		$sql = "INSERT INTO " . $this->_table . "($cols) VALUES($vals)"; 
		$sql = $this->_connection->prepare($sql);
		if($sql->execute($values)){
			return true;
		}
		return false;
	}

	/**
	 * update() builds update query
	 * @param  array  $update associative array ['column' => 'Value']
	 * @return $this
	 */
	public function update($update = [])
	{
		// Update Query
		// UPDATE table SET column = value WHERE condition;
		$this->_query = ["UPDATE " . $this->_table . " SET"];
		$update = new \ArrayIterator($update);
		$update = new \CachingIterator($update);
		foreach ($update as $column => $value) {
			if($update->hasNext()){
				$this->_query[] = " $column = :update_$column,";
			}else {
				$this->_query[] = " $column = :update_$column";	
			}		
			$this->_values[":update_$column"] = $value;
		}
		return $this;
	}

	/**
	 * saveUpdate() is extension for update() method to execute the query for updating the rows
	 * @return boolean
	 */
	public function saveUpdate()
	{
		// Query bind | Making query
		var_dump($this->_values);
		$sql = $this->queryGenerator($this->_query);
		$sql = $this->_connection->prepare($sql);
		if($sql->execute($this->_values)){
			return true;
		}
		return false;
	}

	/**
	 * remove() method deletes rows from database with N numbers of AND & or conditions 
	 * with extension of delete()
	 * 
	 * @return $this
	 */
	public function remove(){
		// Delete query
		$this->_query = ["DELETE FROM " . $this->_table];
		return $this;
	}

	/**
	 * delete() is extension to remove() in order to complete the process.
	 * Executes the query.
	 * @return $this
	 */
	public function delete()
	{
		$sql = $this->queryGenerator($this->_query);
		$sql = $this->_connection->prepare($sql);
		if($sql->execute($this->_values)){
			return true;
		}
		return false;
	}

	public function join($table, $column, $operator, $column2)
	{
		$this->_query[] = " INNER JOIN $table ON " . $this->_table . ".$column $operator $table.$column2";
		return $this;
	}

	public function leftJoin($table, $column, $operator, $column2)
	{
		$this->_query[] = " LEFT JOIN $table ON " . $this->_table . ".$column $operator $table.$column2";
		return $this;
	}

	public function rightJoin($table, $column, $operator, $column2)
	{
		$this->_query[] = " RIGHT JOIN $table ON " . $this->_table . ".$column $operator $table.$column2";
		return $this;
	}	

	public function fullOuterJoin($table, $column, $operator, $column2)
	{
		$this->_query[] = " FULL OUTER JOIN $table ON " . $this->_table . ".$column $operator $table.$column2";
		return $this;
	}


	/**
	 * Count the rows affected
	 * @return integer
	 */
	public function countRows()
	{
		return count($this->_fetchedQuery);	
	}


	# #############################
	#
	# Protected Functions for inner use only
	# 
	# ############################# 
	
	/**
	 * tableNameFormat formats the model name for table
	 * i.e
	 * Model User
	 * Table users
	 * tableNameFormat(User); // outputs (users)
	 *
	 * @param string $table name for formatting
	 * @return string formatted name
	 */
	protected function tableNameFormat($table)
	{
		// To remove the namespace
		$table = explode('\\', $table);
		$table = end($table);
		// To make the first later lowercase and to make the name plural
		$table = lcfirst($table) . 's';

		return $table; 
	}

	/**
	 * Generates query from $_query array by imploding it
	 * @param  array $query query array
	 * @return string        generated query 
	 */
	protected function queryGenerator($query)
	{
		return implode('', $query);	
	}

	/**
	 * fetchColumns fetches names of the columns from database table
	 * @return array column names
	 */
	protected function fetchColumns()
	{
		// Fetches Column Names
		$columnsFetch = "SHOW COLUMNS FROM " . $this->_table;
		$columnsFetch = $this->_connection->query($columnsFetch);
		$columnsFetch = $columnsFetch->fetchAll();
		foreach ($columnsFetch as $column) {
			if($column['Key'] === "PRI"){
				continue;
			}
			foreach ($column as $field => $property) {
				if($field === "Field"){
					$this->_columnNames[] = $property;
					$this->{$property} = '';
				}
			}
		}
	}

	# #####################
	# 
	# Get Functions 
	# 
	# #####################
	
	/**
	 * getQuery return the full query
	 * @return string query
	 */
	public function getQuery()
	{
		return $this->_query;
	}
	
	/**
	 * getValues return the full query values
	 * @return string query
	 */
	public function getValues()
	{
		return $this->_values;
	}
	

	/**
	 * Closes the connection of database
	 * @return void 
	 */
	public function __destruct()
	{
		$this->_connection = null;
	}

}
?>