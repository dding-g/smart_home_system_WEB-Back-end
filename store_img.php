<?php

final class select_access_log{

    public function db_init(){
        try {
            $dsn = "mysql:host=localhost;port=3306;dbname=smart_mirror;charset=utf8";
    
            $this->db = new PDO($dsn, "ddingg", "persona33");
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
 
    public function get_access_log(){
        try{
            $sql = "SELECT * FROM access_log";
            $sth = $this->db->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();

            print_r(json_encode($result));
        }catch(Exception $e){
            echo $e->getMessage();
        }   
    }
    
}

$select_access_log = new select_access_log;
$select_access_log->db_init();
$select_access_log->get_access_log();

?>