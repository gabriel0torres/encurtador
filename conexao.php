<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

Class Conexao
{
    private static $connection;

    private function __construct(){}

    public static function getConnection() {

        $pdoConfig  = DB_DRIVER . ":". "Server=" . DB_HOST . ";";
        $pdoConfig .= "dbname=".DB_NAME.";";

        try {
            if(!isset($connection)){
                $connection =  new PDO($pdoConfig, DB_USER, DB_PASSWORD);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return $connection;
         } catch (PDOException $e) {
            $mensagem = "Drivers disponiveis: " . implode(",", PDO::getAvailableDrivers());
            $mensagem .= "\nErro: " . $e->getMessage();
            throw new Exception($mensagem);
         }
     }
}

define('DB_HOST'        , $_ENV['DB_HOST']);
define('DB_USER'        , $_ENV['DB_USER']); 
define('DB_PASSWORD'    , $_ENV['DB_PASSWORD']);
define('DB_NAME'        , $_ENV['DB_NAME']);
define('DB_DRIVER'      , $_ENV['DB_DRIVER']);

try{

    $Conexao    = Conexao::getConnection();
    

 }catch(Exception $e){

    echo $e->getMessage();
    exit;

 }