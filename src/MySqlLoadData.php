<?php

namespace Kolmeya\LoadData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\Schema;

trait MySqlLoadData
{
    protected $excludeColumns = [ 'id' ];

    public static function import( $file )
    {
        $file = new \SplFileObject( $file );

        /** @var Model $model */
        $model = new self();

        $model->executeLoadData( $file );
    }

    protected function executeLoadData( $file )
    {
        if(! $this->getConnection() instanceof MySqlConnection ){
            throw new \RuntimeException( "connection must be mysql" );
        }

        if( !is_null( $this->loadDataExcludeColumns ) ){
            $this->excludeColumns = $this->loadDataExcludeColumns;
        }

        $this->validateTable();
        $query = $this->generateLoadDataString( $file );

        $this->getConnection()
            ->getPdo()
            ->exec( $query );
    }

    protected function validateTable()
    {
        if( !Schema::hasTable( $this->getTable() ) ){
            throw new \RuntimeException( "Table not found for mysql load data" );
        }
    }

    protected function getColumnsToLoadData()
    {
        $columns = Schema::getColumnListing( $this->getTable() );

        $columns = array_diff( $columns, $this->excludeColumns );

        return join( ", ", $columns );
    }

    protected function getEndFields()
    {
        return is_null( $this->loadDataEndFields ) ? ";" : $this->loadDataEndFields;
    }

    protected function getEndLine()
    {
        return is_null( $this->loadDataEndLine ) ? "\r\n" : $this->loadDataEndLine;
    }

    protected function generateLoadDataString( \SplFileObject $file )
    {
        $columns = $this->getColumnsToLoadData();
        $table = $this->getTable();

        $queryString = "LOAD DATA LOCAL INFILE '" . $file->getRealPath() . "'
                INTO TABLE {$table}
                FIELDS TERMINATED BY '{$this->getEndFields()}'
                LINES TERMINATED BY '{$this->getEndLine()}'
                ({$columns})";

        if( $this->usesTimestamps() === true ){
            $queryString = $queryString . "SET created_at=NOW(), updated_at=NOW()";
        }

        return $queryString;
    }
}
