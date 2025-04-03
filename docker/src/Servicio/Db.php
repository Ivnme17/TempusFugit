<?php
class Db{
    
   private static $conn = null;
   
   
   public static function getConexion(){
       if(self::$conn == null){
           self::$conn = new PDO('mysql:host=mysql;dbname=tempus_fugit', 'root', 'root');
       }
       return self::$conn;
   }
   
   public static function cerrarConexion(){
       if(self::$conn != null){
           self::$conn = null;
       }
   }
   
}