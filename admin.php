<?php

require_once("../persistent.php");

class Admin extends Persistent{
 
  //protected static function getTableName() {
  //      return 'admin';
  //}
 
 /**
  return hiba kódok array
  
  Létrehozási/módosítási paraméterek ellenőrzése
  Alosztály implementálja  
  */
  public function validate(array $params=null){
  $errors = array();
         if(empty($params['azon']))
         $errors[]='Nincs azon megadva';
  
  return $errors;
  }
  
  /**
  return void
  
  Tetszőleges létrehozási tevékenység. 
  Alosztály implementálja  
  */
  protected function onAfterCreate(array $params=null){
  }
  
  //TODO: getterek, setterek a Persistent-ben lévő getFields és setFields segítségével
    public function getAdminFields(){
      return $this->getFields();
    }
    
    public function setAdminFields(array $values){
      return $this->setFields($values);
    }     
}