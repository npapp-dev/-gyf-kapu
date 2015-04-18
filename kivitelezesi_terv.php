<?php 

require_once("../persistent.php");

class KivitelezesiTerv extends Persistent{
 
  //protected static function getTableName() {
  //      return 'kivitelezesi_terv';
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
          if(empty($params['letrehozo']))
         $errors[]='Nincs letrehozo megadva';
          if(empty($params['datum']))
         $errors[]='Nincs datum megadva';
          if(empty($params['igenyles_azon']))
         $errors[]='Nincs igenyles_azon megadva';
          if(empty($params['dokumentum']))
         $errors[]='Nincs dokumentum megadva';
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
    public function getKivitelezesiTervFields(){
      return $this->getFields();
    }
    
    public function setKivitelezesiTervFields(array $values){
      return $this->setFields($values);
    }     
}

  ?>