<?php
class EmanIndex_PageController extends Omeka_Controller_AbstractActionController
{
    public function fetchfieldsAction()
  	{
  		$title = strtoupper($this->getParam('q'));
  		$fieldid = $this->getParam('fieldid');
  		$db = get_db();
  		$items = $db->query("SELECT DISTINCT(text) FROM `$db->ElementTexts` WHERE element_id = $fieldid AND record_type = 'Item' AND UPPER(text) LIKE '%$title%' ORDER BY text ASC")->fetchAll();
  		$this->_helper->json($items);
  	}

    public function indexcompletAction()
  	{
      $content = "";    	
  		$id = $this->getParam('q');
  		$id == null ? $id = 0 : null;  
  		$this->view->id = $id;		
  		$vide = $this->getParam('vide');
  		$this->view->vide = $vide;	
  		$this->view->texte = '';
  		$db = get_db();
      $this->fieldsDropdown();      
  		if ($vide == 1 && $id <> 0) {
    		$check = 'checked';
        $query = "SELECT DISTINCT(text), record_id id FROM `$db->ElementTexts` WHERE record_type = 'Item' AND element_id = 50 AND record_id NOT IN (SELECT item.id FROM `$db->ElementTexts` e LEFT JOIN `$db->Items` item ON e.record_id = item.id WHERE record_type = 'Item' AND element_id = $id)";
      } else {
        $check = "";
        $query = "SELECT DISTINCT(text), record_id id FROM `$db->ElementTexts` WHERE record_type = 'Item' AND element_id = $id";
      }
      $this->view->dropdown .= '<span style="padding-left:20px;"><input ' . $check . ' type="checkbox" name="vides" id="vides" /><label for="vides">Afficher les champs vides</label></span>';      
      $res = $ids = [];
   		$items = $db->query($query)->fetchAll();
      $nb = 0;
      foreach($items as $i => $item) {
        $res[$i]['id'] = $item['id'];
        $res[$i]['text'] = $item['text'];
        $nb++;
      }
      $count = count($res);
      $count > 1 ? $e = 's' : $e = '';
      $count > 1 ? $v = 'ont' : $v = 'a';
      $text = "";
      if (empty($items) && $vide == 0 && $id <> 0) {
        $text = "Aucune notice n'a de valeur pour ce champ."; 
      } elseif (empty($items) && $vide == 1 && $id <> 0) {
        $text = "Toutes les notices ont une valeur pour ce champ.";         
      } elseif ($vide == 1 && $id <> 0) {
        $text = $count . " notice$e n'$v pas de valeur pour ce champ.";
      } elseif ($vide == 0 && $id <> 0) {
        $text = $count . " notice$e $v une valeur pour ce champ.";
      }
      $this->view->texte = $text;
      
            
  		usort($res, function($a, $b) {
    		return strnatcasecmp($a['text'], $b['text']);
  		});
		      
      if (isset($_GET['order'])) {
    		if ($_GET['order'] == 'alphainverse') {
      		$res = array_reverse($res, true);
    		}  		        
      }      

      foreach ($res as $i => $valeur) {
        $content .= "<a href='" . WEB_ROOT . "/items/show/" . $valeur['id'] ."' target='_blank'>" . $valeur['text'] . "</a><br />";
      }
      $this->view->content = $content;
  	}
  	
  	public function fieldsDropdown() {
  		$id = $this->getParam('q');
  		if ($id == null) {
    		$selected = ""; 
      } else {
        $selected = "selected";    		
      }
  		$db = get_db();
  		$query = "SELECT DISTINCT (name), id FROM omeka_elements ORDER BY name";
  		$elements = $db->query($query)->fetchAll();
  		foreach ($elements as $i => $element) {
    		$elements[$i]['name'] = __($elements[$i]['name']);
      }
      setlocale(LC_COLLATE, 'fr_FR.utf8');
      usort($elements, function($a, $b) {return strcoll($a['name'], $b['name']); } );      
  		$content = '<select id="fieldName"><option value="0" $selected>Sélectionner un champ</option>';
  		foreach ($elements as $i => $element) {
    		$id == $element['id'] ? $selected = 'selected' : $selected = ''; 
    		$content .= "<option $selected value='" . $element['id'] . "'>" . $element['name'] . "</option>";
  		}
  		$content .= '</select>';
  		$this->view->dropdown = $content;
  	}
  	public function checkBox() {
    	$content = "";
  		$this->view->dropdown = $content;    	
    }  	
}

