<?php

final class smart_mirror{

    public function db_init(){
        try {
            $dsn = "mysql:host=localhost;port=3306;dbname=smart_mirror;charset=utf8";
    
            $this->db = new PDO($dsn, "ddingg", "persona33");
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "데이터베이스 연결 성공!!\n";
  
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function user_insert($data){
        $sql = "INSERT INTO users VALUES(?, ?, ?);";
        $sth = $this->db->prepare($sql);
        $sth->execute(array('ddingg', 'mattew', '2019-09-18 18:37:00'));
    }

    public function access_log_insert($data){
        try{
            $sql = "INSERT INTO access_log(name, access_date, face_picture_path) VALUES(?, ?, ?);";
            $sth = $this->db->prepare($sql);
            $sth->execute(array($data['name'], $data['access_date'], $date['img_path']));
            
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function leaving_log_update($data){
        try{

            $sql = "UPDATE access_log SET leaving_date = ?, face_picture_path = ?;";
            $sth = $this->db->prepare($sql);
            $sth->execute(array($data['leaving_time'], $data['img_path']));
            
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


}
$IMG_PATH = '/img/';
$IMG_NAME = '';

$smart_mirror = new smart_mirror;
$smart_mirror->db_init();


define("_IP", "0.0.0.0"); 
define("_PORT", "65000"); 
$sSock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); 
socket_bind($sSock, _IP, _PORT); 
socket_listen($sSock); 
$cSock = socket_accept($sSock);
while($read_data = socket_read($cSock, 4096)) { 
    socket_getpeername($cSock, $addr, $port); 
    echo "SERVER >> client connected $addr:$port\n"; 
    
    if($smart_mirror->isJson($read_data)){//로그 저장
        echo "test : ", $read_data, "\n";
        $file_json = json_decode($read_data, true);

        if($file_json['type' == 'AT']){ //access date
            $smart_mirror->access_log_insert($file_json);
        }else{ // leaving date
            if($file_json['name'] == 'unknown'){
                $IMG_NAME = $file_json['name'].'_'.$file_json['access_date'].'.png';
                $file_json['img_path'] = $IMG_PATH.$IMG_NAME;
            }else{
                $file_json['img_path'] = '-';
            }
            $smart_mirror->leaving_log_update($file_json);
        }
        
    
    }else{//이미지 저장
        $fp = fopen($IMG_PATH.$IMG_NAME, 'w');
        fwrite($fp, $read_data);
        fclose($fp);
        $IMG_NAME='';
    }

    
}
socket_close($cSock);
echo "SERVER >> client Close.\n"; 

?>


