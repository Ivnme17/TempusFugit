<?php
class Db{
    
   private static $conn = null;
   
   
   public static function getConexion(){
       if(self::$conn == null){
           self::$conn = new PDO('mysql:host=localhost;dbname=tempus-fugit', 'root', '');
       }
       return self::$conn;
   }
   
   public static function cerrarConexion(){
       if(self::$conn != null){
           self::$conn = null;
       }
   }
   
}