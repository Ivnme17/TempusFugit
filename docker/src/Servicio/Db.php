<?php
class Db{
    
   private static $conexion = null;
   
   
   public static function getConexion(){
       if(self::$conexion == null){
           self::$conexion = new PDO('mysql:host=mysql;dbname=tempus_fugit', 'root', 'root');
       }
       return self::$conexion;
   }
   
   public static function cerrarConexion(){
       if(self::$conexion != null){
           self::$conexion = null;
       }
   }
   
}