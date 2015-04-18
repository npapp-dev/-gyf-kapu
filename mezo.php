<?php 

require_once("../persistent.php");

class Mezo extends Persistent{
 
  //protected static function getTableName() {
  //      return 'mezo';
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
          if(empty($params['tipus']))
         $errors[]='Nincs tipus megadva';
          if(empty($params['kotelezoseg']))
         $errors[]='Nincs kotelezoseg megadva';
          if(empty($params['sablon_azon']))
         $errors[]='Nincs sablon_azon megadva';
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
    public function getMezoFields(){
      return $this->getFields();
    }
    
    public function setMezoFields(array $values){
      return $this->setFields($values);
    }     
}

  ?>