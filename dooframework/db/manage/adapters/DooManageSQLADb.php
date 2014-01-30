<?php
/**
 * DooManageSQLADb class file for implementing Sybase SQLAnywhere Version 12.
 *
 * @author David M. Willink <developer99@ebox99.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 * @package doo.db.manage.adapters
 * @since 1.5
 */
Doo::loadCore('db/manage/DooManageDb');

class DooManageSQLADb extends DooManageDb 
{
    

    /**
     * A mapping of DooManageDb generic datatypes to RDBMS native datatypes for columns
     * These must be defined in each specific adapter
     *
     * The datatypes are
     * COL_TYPE_BOOL		: A true or false boolean
     * COL_TYPE_TINYINT             : 1-byte integer (0 to 255)
     * COL_TYPE_SMALLINT            : 2-byte integer (-32,767 to 32,768)
     * COL_TYPE_INT			: 4-byte integer (-2,147,483,648 to 2,147,483,647)
     * COL_TYPE_BIGINT		: 8-byte integer (about -9,000 trilllion to 9,000 trillion)
     * COL_TYPE_DECIMAL		: Fixed point decimal of specific size (total digits) and scope (num digits after decimal point)
     * COL_TYPE_FLOAT		: A double-percision floating point decimal number
     * COL_TYPE_CHAR		: A fixed length string of 1-255 characters
     * COL_TYPE_VARCHAR		: A variable length string of 1-255 characters
     * COL_TYPE_CLOB		: A large character object of up to about 2Gb
     * COL_TYPE_DATE		: an ISO 8601 date eg. 2009-09-27
     * COL_TYPE_TIME		: an ISO 8601 time eg. 18:38:49
     * COL_TYPE_TIMESTAMP           : an ISO 8601 timestamp without a timezone eg. 2009-09-27 18:38:49
     * COL_TYPE_BLOB                : A large binary object of up to about 2Gb
     *
     * @var array
     */
    protected $colTypeMapping = array (
        DooManageDb::COL_TYPE_BOOL		=> 'TINYINT',
        DooManageDb::COL_TYPE_TINYINT	=> 'TINYINT',
        DooManageDb::COL_TYPE_SMALLINT	=> 'SMALLINT',
        DooManageDb::COL_TYPE_INT		=> 'INTEGER',
        DooManageDb::COL_TYPE_BIGINT	=> 'BIGINT',
        DooManageDb::COL_TYPE_DECIMAL	=> 'DECIMAL',
        DooManageDb::COL_TYPE_FLOAT         => 'DOUBLE',
        DooManageDb::COL_TYPE_CHAR		=> 'CHAR',
        DooManageDb::COL_TYPE_VARCHAR	=> 'VARCHAR',
        DooManageDb::COL_TYPE_CLOB		=> 'TEXT',
        DooManageDb::COL_TYPE_DATE		=> 'DATE',
        DooManageDb::COL_TYPE_TIME		=> 'TIME',
        DooManageDb::COL_TYPE_TIMESTAMP	=> 'DATETIME',
        DooManageDb::COL_TYPE_BLOB  	=> 'IMAGE',
    );

    protected $identiferQuotePrefix = '"';

    protected $identiferQuoteSuffix = '"';

    /**
     * Builds the Create Table command to run against SQLAnywhere 12.
     * 
     * @param string $table
     * @param array $cols
     * @return string A CREATE TABLE string to run against MySQL Server
     */
    protected function _sqlCreateTable($table, $cols, $options=null) 
    {
            $statement = parent::_sqlCreateTable($table, $cols, $options);
            return $statement;
    }


    /**
     * Fetch table field names (definition).
     * The SQL for fetching a table definition must be adjusted in this function
     * to work with the particular brand of SQL server and align with the 
     * elements of $tableDefinition.
     * 
     * @param type $table name of table from which to fetch field names
     * @return array describes table fields
     */
    protected function  _fetchTableDefinition($table) 
    {
        $fullTableDefinition = Doo::db()->fetchAll('DESCRIBE ' . $table);
        $tableDefinition = array();
        foreach ($fullTableDefinition as $columnDefinition) 
        {
            $fieldName  = $columnDefinition['Field'];
            $type       = strtolower($columnDefinition['Type']);

            // Check if type has a size parameter.
            $size = null;
            if (strpos($type, '(') !== false) 
            {
                $size = substr($type, strpos($type, '(') + 1, -1);
                $type = substr($type, 0, strpos($type, '('));
                if ($type != 'char' && $type != 'varchar') 
                {
                    $size = null;
                }
            }

            $require = $columnDefinition['Null'] == 'YES' ? false : true;
            $default = $columnDefinition['Default'];
            $primary = $columnDefinition['Key'] == 'PRI' ? true : false;
            $autoinc = $default == 'autoincrement' ? TRUE : FALSE;

            if ($type == 'boolean' || $type == 'tinyint' && $size == 1) $type = DooManageDb::COL_TYPE_BOOL;
            elseif ($type == 'integer') $type = DooManageDb::COL_TYPE_INT;
            elseif ($type == 'double') $type = DooManageDb::COL_TYPE_FLOAT;
            elseif ($type == 'longtext') $type = DooManageDb::COL_TYPE_CLOB;
            elseif ($type == 'datetime') $type = DooManageDb::COL_TYPE_TIMESTAMP;

            $tableDefinition[$fieldName] = array(
                    'autoinc' => $autoInc,
                    'default' => $default,
                    'primary' => $primary,
                    'require' => $require,
                    'scope' => null,
                    'size' => $size,
                    'type' => $type,

            );
        }
        return $tableDefinition;
    }

    /**
     * Drops an index from a table and specifically implemented for each db engine
     * @param string $table Name of the table the index is for
     * @param string $name Name of the index to be removed
     */
    protected function _dropIndex($table, $name) {
            return "DROP INDEX $name";
    }

    /**
     * Adds SQL DB Engine specific auto increment and primary key clauses inplace to the column definition
     * @param string $columnDefinition Reference to the columnDefention to append to
     * @param bool $autoinc True if this column should be a primary key
     * @param bool $primary True if this column should be a primary key
     * @return void
     */
    protected function columnDefineAutoincPrimary(&$columnDefinition, $autoinc, $primary) 
    {
        if ($autoinc === true)
        {
            $columnDefinition .= " AUTOINCREMENT";
        }

        if ( $primary === true )
        {
            $columnDefinition .= " PRIMARY KEY";
        }
    }
}