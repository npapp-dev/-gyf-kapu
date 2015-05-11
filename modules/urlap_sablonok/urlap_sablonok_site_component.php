<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 2015.04.25.
 * Time: 8:39
 */

class Urlap_sablonok_Site_Component extends Site_Component{

    private $perm;
    private $limit=50;
    private $offset=0;
    private $paginationNumber=1;
    private $sorszam=1;
    private $szerkesztes=false;
    private $showAddForm=false;
    private $showFieldList=false;
    private $addFieldForm=false;
    private $dropDownType=false;

    protected function afterConstruction(){
        $this->perm=PersistenceManager::getInstance();
    }

    function process(){
        $this->perm=PersistenceManager::getInstance();
        if(!empty($_POST['new'])){
            $this->showAddForm=true;
        }
        if(isset($_POST['editButton']) && isset($_POST['szerkAzon']))
            $this->szerkesztes=true;

        if(isset($_POST['deleteButton']) && isset($_POST['deleteAzon'])){
            $azon = $_POST['deleteAzon'];
            $u = $this->perm->getObjectsByField("UrlapSablon", array('azon'=>$azon))[0];
            $u->delete();
        }
        if(isset($_POST['deleteField']) && isset($_POST['fieldAzon'])){
            $fieldAzon=$_POST['fieldAzon'];
            $u = $this->perm->getObjectsByField("Mezo", array('azon'=>$fieldAzon))[0];
            $u->delete();
            $this->showFieldList=true;
        }
        if(isset($_POST['deleteValue']) && isset($_POST['azon'])){
            $fieldAzon=$_POST['azon'];
            $u = $this->perm->getObjectsByField("Ertek", array('azon'=>$fieldAzon))[0];
            $u->delete();
            $_POST['tipus']="Legördülős";
            $_POST['azon']=$_POST['mezo_azon'];
        }
        if(!empty($_POST['GetFields'])) {
            $this->showFieldList = true;
        }
        if(!empty($_POST['back']) || !empty($_POST['save']) || !empty($_POST['change'])) {
            $this->szerkesztes = false;
            $this->addFieldForm=false;
            unset($_POST["tipus"]);
        }
        if(!empty($_POST['sablon_azon']) && !empty($_POST['addFieldButton'])){
            $this->addFieldForm=true;
        }
        if(!empty($_POST['change'])){
            $lekérdezés=array(
                'id'=>"".$_SESSION['PHPSESSID']
            );
            $admin=$this->perm->getObjectsByField("Felhasznalo",$lekérdezés);
            $adatok = array(
                'azon' => $_POST['azon'],
                'allapot' => $_POST['allapot'],
                'letrehozas_datuma'=> date("j - n - Y"),
                'admin_azon' => $admin[0]->getFelhasznaloFields()['azon']
            );
            //$this->perm->updateObjectByFields('UrlapSablon',$adatok);
            $uk = $this->perm->getObjectsByField('UrlapSablon', array("azon" => $_POST['azon']))[0];
            $uk->setUrlapSablonFields($adatok);
            // $this->perm->createObject("UrlapSablon", $adatok);
        }
        if(!empty($_POST['save'])){
            $lekérdezés=array(
                'id'=>"".$_SESSION['PHPSESSID']
            );
            $admin=$this->perm->getObjectsByField("Felhasznalo",$lekérdezés);
            $adatok = array(
                'azon' => $_POST['azon'],
                'allapot' => $_POST['allapot'],
                'letrehozas_datuma'=> date("Y.m.d"),
                'admin_azon' => $admin[0]->getFelhasznaloFields()['azon']

            );
            //$this->perm->updateObjectByFields('UrlapSablon',$adatok);
          /*  $uk = $this->perm->getObjectsByField("UrlapSablon", array("azon" => $_POST['azon']))[0];
            $uk->setUgyfelFields($adatok);*/
             $this->perm->createObject("UrlapSablon", $adatok);
        }
        if(!empty($_POST['saveField'])){
            if($_POST['kotelezoseg']==1) {
                $adatok = array(
                    'azon' => $_POST['azon'],
                    'tipus' => $_POST['tipus'],
                    'kotelezoseg' => true,
                    'sablon_azon' => "" . $_POST['sablon_azon']
                );
            } else{
                $adatok = array(
                    'azon' => $_POST['azon'],
                    'tipus' => $_POST['tipus'],
                    'kotelezoseg' => false,
                    'sablon_azon' => "" . $_POST['sablon_azon']
                );
            }
            $this->perm->createObject("Mezo", $adatok);
        }
        if(!empty($_POST['AddValue'])){
            $adatok=array(
                'ertek'=>$_POST['ertek'],
                'mezo_azon'=>$_POST['azon']
            );
            $this->perm->createObject("Ertek",$adatok);
        }
        $this->pagination();
    }

    function show(){
        if(!empty($_POST['tipus']) && ($_POST['tipus']=='Legördülős')){
            {
                //echo $_POST['sab_azon'];
                $adatok=array(
                    'mezo_azon' => $_POST['azon']
                );
                $ertek=$this->perm->getObjectsByField("Ertek",$adatok);
                echo '<form method="post">
            <div class="form_box">
            <h1>Érték adatai</h1>
                <input type="submit" name="back" value="Vissza" class="back_button">
            </div>
            <br/>
            <h2>Új érték</h2>
                <div class="form_box">
                <form method="post">
                     <tr>
                        <td><span>Érték</span></td>
                        <td><input type="text" name="ertek" value=""></td>';?>
                        <td><input type="hidden" name="azon" value="<?echo $_POST['azon']?>"></td>
                        <td><input type="hidden" name="tipus" value="Legördülős"></td>';
                      <?
                    echo '</tr>
                        <td><input type="submit" name="AddValue" value="Új legördülő érték hozzáadása"></td>
                </div>
                </form>
            <br/>
            <br/>
            <div class="listtable">
                <table style="width:100%">
                        <tr>
                            <th>Érték</th>
                            <th>Mező azonosító</th>
                            <th>Érték törlése</th>
                        </tr>
                        ';
                $count=count($ertek);
                for($i=0;$i<$count;$i++){
                    echo '<tr>';
                    echo '<td>'.$ertek[$i]->getErtekFields()['ertek'].'</td>';
                    echo '<td>'.$ertek[$i]->getErtekFields()['mezo_azon'].'</td>';
                    ?> <td> <form action="" method="post">
                            <input type="submit" name="deleteValue" value="Törlés" onclick="return confirm('Biztosan törli a kiválasztott értéket?')" >
                            <input type="hidden" name="azon" value="<? echo $ertek[$i]->getErtekFields()['azon'] ?>">
                            <input type="hidden" name="mezo_azon" value="<? echo $ertek[$i]->getErtekFields()['mezo_azon']?>">
                        </form></td>;<?
                    echo '</tr>';
                }
                echo '
                </table>
            </div>
            </form>';
            }
        }else
        if($this->addFieldForm){
            ?><form method="post">
            <div class="form_box">
                <h1>Mező hozzáadása</h1>
                <input type="submit" name="saveField" value="Új mező hozzáadása" class="save_button">
                <input type="submit" name="cancel" value="Mégse" class="back_button">
            </div>
            <br/>
            <br/>
            <div>
                    <table class="formtable">
                        <tbody>
                        <tr>
                            <td valign="top">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td><span>Mező Azonosító</span></td>
                                            <td><input type="text" name="azon"  value=""></td>
                                        </tr>
                                        <tr>
                                            <td><span>Típus</span></td>
                                            <td>
                                            <select name="tipus">
                                                <option value="Szám">Szám</option>
                                                <option id="legordulos" value="Legördülős">Legördülős</option>
                                                <option value="Szöveg">Szöveg</option>
                                            </select>
                                                </td>
                                            <!--<td><input type="radio" name="tipus" checked value="Szám">Szám
                                                <br>
                                                <input id="legordulos" type="radio" name="tipus" value="Legördülős">Legördülős
                                                <br>
                                                <input type="radio" name="tipus" value="Szöveg">Szöveg
                                            </td>-->
                                        </tr>
                                        <tr>
                                            <td><span>Kell-e</span></td>
                                            <td><input type="radio" name="kotelezoseg" checked value="1">Kötelező
                                                <br>
                                                <input type="radio" name="kotelezoseg" value="0">Opcionális
                                            </td>
                                            <input type="hidden" name="sablon_azon" value="<?echo $_POST['sablon_azon']?>" >
                                        </tr>
                                        </form>

                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </form><?
        } else
        if($this->szerkesztes){
            $lekerdezes_adatok=array(
                'azon'=>"{$_POST['szerkAzon']}"
            );
            //var_dump($lekerdezes_adatok);
            $customer=$this->perm->getObjectsByField('UrlapSablon',$lekerdezes_adatok);
            // var_dump($customer);
            ?>
            <form action="" method="POST">
            <div class="form_box">
                <h1>Sablon adatainak módosítása</h1>
                <input type="submit" name="change" value="Változtatás" class="save_button">
                <input type="submit" name="back" value="Vissza" class="back_button">
                <br/>
                <br/>
                <div>
                    <table class="formtable">
                        <tbody>
                        <tr>
                            <td valign="top">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td><span>Azonosító</span></td>
                                        <td><input type="text" name="azon"  readonly="true" value="<? echo $customer[0]->getUrlapSablonFields()['azon'] ?>"></td>
                                    </tr>
                                    <? if($customer[0]->getUrlapSablonFields()['allapot']=='Aktív'){ ?>
                                    <tr><td><span>Állapot</span></td>
                                        <td><input type="radio" name="allapot" value="Aktív" checked>Aktív
                                            <br>
                                            <input type="radio" name="allapot" value="Passzív">Passzív
                                        </td>
                                    </tr>
                                    <? }else {?>
                                    <tr><td><span>Állapot</span></td>
                                        <td><input type="radio" name="allapot" value="Aktív">Aktív
                                            <br>
                                            <input type="radio" name="allapot" value="Passzív" checked>Passzív
                                        </td>
                                    </tr>
                                    <?} ?>
                                    <tr>
                                        <td><span>Létrehozás dátuma</span></td>
                                        <td><input type="text" name="letrehozas_datuma" readonly="readonly"  value="<? echo $customer[0]->getUrlapSablonFields()['letrehozas_datuma'] ?>"></td>
                                    </tr>
                                    <tr>
                                        <td><span>Admin azonosító</span></td>
                                        <td><input type="text" name="admin_azon" readonly="readonly"  value="<? echo $customer[0]->getUrlapSablonFields()['admin_azon'] ?>"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </form><?
        }
        else if($this->showFieldList){
            echo $_POST['sab_azon'];
            $adatok=array(
                'sablon_azon' => "".$_POST['sab_azon']
            );
            $mezok=$this->perm->getObjectsByField("Mezo",$adatok);
            echo '<form method="post">
            <div class="form_box">
            <h1>Mezők adatai</h1>
                <input type="submit" name="back" value="Vissza" class="back_button">
            </div>
            <br/>
            <br/>
            <div class="listtable">
                <table style="width:100%">
                        <tr>
                            <th>Azonosító</th>
                            <th>Típus</th>
                            <th>Kötelezőség</th>
                            <th>Művelet</th>
                            <th>Értekek</th>
                        </tr>
                        ';
            $count=count($mezok);
            for($i=0;$i<$count;$i++){
                echo '<tr>';
                echo '<td>'.$mezok[$i]->getMezoFields()['azon'].'</td>';
                echo '<td>'.$mezok[$i]->getMezoFields()['tipus'].'</td>';
                if($mezok[$i]->getMezoFields()['kotelezoseg']==true) {
                    echo '<td>Kötelező</td>';
                }
                else{
                    echo '<td>Opcionális</td>';
                }
                ?> <td> <form action="" method="post">
                    <input type="submit" name="deleteField" value="Törlés" onclick="return confirm('Biztosan törli a kiválasztott Mezőt?')" >
                    <input type="hidden" name="fieldAzon" value="<? echo $mezok[$i]->getMezoFields()['azon'] ?>">
                    <input type="hidden" name="sab_azon" value="<?echo $_POST['sab_azon']?>">
                </form></td>;<?
                echo '<td>';
                if($mezok[$i]->getMezoFields()['tipus']=='Legördülős'){
                    ?><form action="" method="post">
                    <input type="submit" name="getValuesOfFields" value="Értékek" >
                    <input type="hidden" name="azon" value="<? echo $mezok[$i]->getMezoFields()['azon'] ?>">
                    <input type="hidden" name="tipus" value="Legördülős">
                    </form></td><?
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '
                </table>
            </div>
            </form>';
        }
        else if ($this->showAddForm) {
            echo '<form action="" method="POST">
        <div class="form_box">
        <h1>Sablon létrehozása</h1>
        <input type="submit" name="save" value="Mentés" class="save_button">
        <input type="submit" name="back" value="Vissza" class="back_button">
        <br/>
        <br/>
        <div>
           <table class="formtable">
                <tbody>
                <tr>
                    <td valign="top">
                            <table>
                            <tbody>
                            <tr>
                                <td><span>Azonosító</span></td>
                                <td><input size="32" type="text" name="azon" value=""></td>
                            </tr>
                          <!--  <tr>
                                <td><span>Állapot</span></td>
                                <td><input size="32" type="text" name="allapot" value=""></td>
                            </tr> -->
                            <tr><td><span>Állapot</span></td>
                                <td><input type="radio" name="allapot" value="Aktív" checked>Aktív
                                     <br>
                                <input type="radio" name="allapot" value="Passzív">Passzív
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        </div>
    </form>';
        } else {
            $sablonok=$this->perm->getObjectsByLimitOffsetOrderBy("UrlapSablon",$this->limit,$this->offset,'azon');
            $osszes=$this->perm->getAllObjects("UrlapSablon");
            echo '<form action="" method="post">
                <button type="submit" name="new" value="new">Új sablon hozzáadása</button>
        </form>';
            echo '<form method="post">
            <div class="form_box">
                <h1>Sablonok adatai</h1>
            </div>
            <br/>
            <br/>
            <div class="listtable">
                <table style="width:100%">
                        <tr>
                            <th>#</th>
                            <th>Azonosító</th>
                            <th>Létrehozás dátuma</th>
                            <th>Állapot</th>
                            <th>Létrehozó admin</th>
                            <th>Művelet</th>
                            <th>Szerkesztés</th>
                            <th>Törlés</th>
                            <th>Hozzáadás</th>
                        </tr>
                        ';
            $this->sorszam=$this->offset;
            $count=count($sablonok);
            for($i=0;$i<$count;$i++){
                $s = $sablonok[$i]->getUrlapSablonFields();
                echo '<tr>';
                echo '<td>'.($this->sorszam + 1) . '</td>';
                echo '<td>'.$s['azon'].'</td>';
                echo '<td>'.date("Y.m.d",strtotime($s['letrehozas_datuma'])).'</td>';
                echo '<td>'.$s['allapot'].'</td>';
                echo '<td>'.$s['admin_azon'].'</td>';
                echo '<form method="post">';
                echo '<input type="hidden" name="sab_azon" value="'.$s['azon'].'">';
                echo '<td> <input type="submit" name="GetFields" value="Mezők lekérdezese"></td>';
                echo '</form>';
                echo '<form method="post">';
                echo '<input type="hidden" name="szerkAzon" value="'.$s['azon'].'">';
                echo '<td> <input type="submit" name="editButton" value="Szerkesztés"></td>';
                echo '</form>';
                ?><td> <form action="" method="post">
                    <input type="submit" name="deleteButton" value="Törlés" onclick="return confirm('Biztosan törli a kiválasztott sablont?')" >
                    <input type="hidden" name="deleteAzon" value="<? echo $s['azon'] ?>">
                </form></td>
                <?
                echo '<form method="post">';
                echo '<input type="hidden" name="sablon_azon" value="'.$s['azon'].'">';
                echo '<td> <input type="submit" name="addFieldButton" value="Új Mező"></td>';
                echo '</form>';
                echo '</tr>';
                $this->sorszam++;
            }
            $this->showPagination(count($osszes));
        }
    }


    private function pagination(){
        $this->limit=(isset($_POST['limit']) && !empty($_POST['limit'])) ? $_POST['limit'] : 50;
        $this->offset=(isset($_POST['offset']) && !empty($_POST['offset'])) ? $_POST['offset'] : 0;
        $this->paginationNumber=(isset($_POST['pagination']) && !empty($_POST['pagination'])) ? $_POST['pagination'] : 1;
        /* echo $_POST['selected'].' '.$_POST['next'].' '.$_POST['previous'].'<br>';
         echo "Limit:".$this->limit.' '."Offset:".$this->offset;*/
        if(isset($_POST['selected']) && !isset($_POST['previous']) && !isset($_POST['next']) && empty($_POST['previous']) && empty($_POST['next'])){
            //echo "belép";
            $this->limit = $_POST['selected'];
            $this->offset = 0;
            $this->paginationNumber=1;
        }
        if(isset($_POST['selected']) && isset($_POST['previous'])){
            if($_POST['selected']==50 && $this->paginationNumber>0){
                if(!$this->offset==0){
                    $this->offset-=50;
                    $this->paginationNumber--;
                    $this->limit=50;
                }else{
                    $this->limit=50;
                    $this->offset=0;
                    $this->paginationNumber=1;
                }
            }else if($_POST['selected']==100 && $this->paginationNumber>0){
                if(!$this->offset==0){
                    $this->offset-=100;
                    $this->paginationNumber--;
                    $this->limit=100;
                }else{
                    $this->limit=100;
                    $this->offset=0;
                    $this->paginationNumber=1;
                }
            }
            else if($_POST['selected']==500 && $this->paginationNumber>0){
                if(!$this->offset==0){
                    $this->offset-=500;
                    $this->paginationNumber--;
                    $this->limit=500;
                }else{
                    $this->limit=500;
                    $this->offset=0;
                    $this->paginationNumber=1;
                }
            }
        }
        if(isset($_POST['selected']) && isset($_POST['next'])){
            if($_POST['selected']==50){
                $this->offset+=50;
                $this->paginationNumber++;
                $this->limit=50;
            }else if($_POST['selected']==100){
                $this->offset+=100;
                $this->paginationNumber++;
                $this->limit=100;
            }
            else if($_POST['selected']==500){
                $this->offset+=500;
                $this->limit=500;
                $this->paginationNumber++;
            }
        }
    }
    private function showPagination($ugyfelek){
        ?>
        <div class="pagination">
            <p>Találatok száma: <? echo $ugyfelek;?></p>
            <form action="" method="post">
                <select name="selected" onchange="this.form.submit()">
                    <option value="50" <?if(empty($_POST['selected']) || $_POST['selected']==50) echo 'selected' ?> >50</option>
                    <option value="100" <?if($_POST['selected']==100) echo 'selected' ?>>100</option>
                    <option value="500" <?if($_POST['selected']==500) echo 'selected' ?>>500</option>
                </select> <input type="submit" name="previous" value="Előző">
                             <span class="pagination_page_number">
                                    <span class="pagination_active_page_number"><?echo $this->paginationNumber;?></span>
                            </span>
                <input type="hidden" value="<?echo $this->offset?>" name="offset">
                <input type="hidden" value="<?echo $this->limit?>" name="limit">
                <input type="hidden" value="<?echo $this->paginationNumber?>" name="pagination">
                <input type="submit" name="next" value="Következő">
            </form>
        </div>
    <?
    }
}?>
