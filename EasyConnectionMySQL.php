<?php

    class EasyConnectionMySQL
    {
        #default xampp mysql configuration
        private $DBNAME;
        private $TABLENAME;
        private $HOST = "localhost";
        private $USER = "root";
        private $PASSWORD = "";
        private $CONNECTION_VALUES;
        private $conn;

        public function __construct($DBNAME, $TABLENAME)
        {
            $this->DBNAME = $DBNAME;
            $this->TABLENAME = $TABLENAME;
            $this->CONNECTION_VALUES = array("mysql:dbname=$this->DBNAME;host=$this->HOST", $this->USER, $this->PASSWORD);
            $this->conn = new PDO($this->CONNECTION_VALUES[0], $this->CONNECTION_VALUES[1], $this->CONNECTION_VALUES[2]);
        }

        private function setParam($statement, $key='', $value='')
        {
            $statement->bindParam($key, $value);
        }

        private function setParams($statement, $parameters=array())
        {
            foreach($parameters as $key => $value)
            {
                $this->setParam($statement, $key, $value);
            }
        }

        private function query($rawQuery, $parameters=array())
        {
            $statement = $this->conn->prepare($rawQuery);
            $this->setParams($statement, $parameters);
            $statement->execute();
            return $statement;
        }

        public function selectById($id)
        {
            $rawQuery = "select * from $this->TABLENAME where id = $id;";
            $var = $this->query($rawQuery);
            return $var->fetchAll(PDO::FETCH_ASSOC);
        }

        public function selectALL()
        {
            $rawQuery = "select * from $this->TABLENAME;";
            $var = $this->query($rawQuery);
            return $var->fetchAll(PDO::FETCH_ASSOC);
        }

        public function insert($parameters) #without the ID
        {
            $rawQuery = "insert into $this->TABLENAME (";
            $valuesString = "values(";
            $newParameters = array();

            foreach($parameters as $key=>$value)
            {
                if(end($parameters) == $value)
                {
                    $rawQuery .= "$key) ";
                    $valuesString .= ":$key);";
                }
                else
                {
                    $rawQuery .= "$key, ";
                    $valuesString .= ":$key, ";
                }

                $newParameters[":$key"]=$value;
            }

            $rawQuery .= $valuesString;
            $this->query($rawQuery, $newParameters);

        }

        #$FieldsToUpdate is an array with the fields to update
        #$namedArrayNewValues is an array with key and the new values
        public function update($id, $FieldsToUpdate, $namedArrayNewValues)
        {

            $rawQuery = "update $this->TABLENAME set ";
            $newParameters = array();

            foreach($FieldsToUpdate as $key)
            {
                if(end($FieldsToUpdate) == $key)
                {
                    $rawQuery .= "$key = :$key where id= $id;";
                }else
                {
                    $rawQuery .= "$key = :$key, ";
                }

                $newParameters[":$key"] = $namedArrayNewValues[$key];

            }

            $this->query($rawQuery, $newParameters);

        }

        public function deleteById($id)
        {
            $rawQuery = "delete from $this->TABLENAME where id=:id;";
            $param = array(":id"=>$id);
        }

    }


?>