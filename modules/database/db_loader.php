<?

class DB_Loader extends AbstractLoader{

  protected function getFileNameForClass($classname){
    switch ($classname) {
      case "DatabaseConnection":
        return $this->myfolder."/database.php"; 
    	break;
      case "DBConfig":
        return $this->myfolder."/db_config.php"; 
    	break;
    }
    
  }
}