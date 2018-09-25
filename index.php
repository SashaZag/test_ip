<?php

class countrySearch {
    protected $dbHost = '127.0.0.1';
    protected $dbName = 'testtask';
    protected $dbUser = 'root';
    protected $dbPass = '';
    
    protected $requestRoute = 'http://geoip.nekudo.com/api/';
    public function dbConnect () {
        //DATABASE CONNECTION
        $dsn = "mysql:host=$this->dbHost;dbname=$this->dbName";
        $pdo = new PDO($dsn, $this->dbUser, $this->dbPass);
        return $pdo;
    }
    
    public function sendRequest ($pdo) {
        //GETTING LIST OF IP'S 
        $query = "SELECT ip FROM tb_ip_addresses";    
        $result = $pdo->query($query);
        $row = $result->fetchAll(PDO::FETCH_ASSOC);
        //FINDING LOCATION  
        foreach ($row as $rows) {
          $res = file_get_contents($this->requestRoute.$rows['ip']);
          $json = json_decode($res, true);
          if ($json['country']['name'] == NULL) {
              continue;
          }
          $insertQuery = "UPDATE tb_ip_addresses SET country = '".$json['country']['name']."' WHERE ip = '".$rows['ip']."' ";
          $pdo->query($insertQuery);
          
        }
        
    }
    
}

$object = new countrySearch();
$pdo = $object->dbConnect();
$object->sendRequest($pdo);