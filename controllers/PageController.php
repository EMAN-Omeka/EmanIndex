<?php
class EmanIndex_PageController extends Omeka_Controller_AbstractActionController
{
    public function fetchfieldsAction()
  	{
  		$title = strtoupper($this->getParam('q'));
  		$fieldid = $this->getParam('fieldid');
  		$db = get_db();
  		$items = $db->query("SELECT DISTINCT(text) FROM omeka_element_texts WHERE element_id = $fieldid AND record_type = 'Item' AND UPPER(text) LIKE '%$title%' ORDER BY text ASC")->fetchAll();
  		$this->_helper->json($items);
  	}

    public function indexcompletAction()
  	{
  		$db = get_db();
      $content = "";
  		$id = $this->getParam('q');
  		$id == null ? $id = 0 : null;
  		$fieldName = $db->query("SELECT name FROM `$db->Elements` WHERE id = $id")->fetchObject();
  		$this->view->id = $id;
  		$vide = $this->getParam('vide');
  		$this->view->vide = $vide;
  		$this->view->texte = '';
      $this->fieldsDropdown();
  		if ($vide == 1 && $id <> 0) {
    		$check = 'checked';
        $query = "SELECT DISTINCT(text), record_id id FROM `$db->ElementTexts` WHERE record_type = 'Item' AND element_id = 50 AND record_id NOT IN (SELECT item.id FROM `$db->ElementTexts` e LEFT JOIN `$db->Items` item ON e.record_id = item.id WHERE record_type = 'Item' AND element_id = $id)";
      } else {
    		$private = "";
        if (! current_user()) {
          $private = "AND item.public = 1";
        }
        $check = "";
        $query = "SELECT DISTINCT(text), record_id id FROM `$db->ElementTexts` text LEFT JOIN `$db->Items` item ON item.id = text.record_id WHERE record_type = 'Item' AND element_id = $id $private";
      }
      if (current_user()) {
        $this->view->dropdown .= '<span style="padding-left:20px;"><input ' . $check . ' type="checkbox" name="vides" id="vides" /><label for="vides">Afficher les champs vides</label></span>';
      }
      $resultats = [];
   		$items = $db->query($query)->fetchAll();
      $valeurs = [];
      foreach($items as $i => $item) {
        $resultats[$i]['id'] = $item['id'];
        $resultats[$i]['text'] = $item['text'];
        $litem = get_record_by_id('Item', $item['id']);
        $title = strip_tags(metadata($litem, array('Dublin Core', 'Title')));
        if (isset($valeurs[$item['text']])) {
          $valeurs[$item['text']][$item['id']] = $title;
          $valeurs[$item['text']]['count']++;
        } else {
          $valeurs[$item['text']][$item['id']] = $title;
          $valeurs[$item['text']]['count'] = 1;
        }
      }
      $nbNotices = count(array_count_values(array_column($resultats, 'id')));
      $count = count($resultats);
      $count > 1 ? $e = 's' : $e = '';
      $count > 1 ? $v = 'ont' : $v = 'a';
      $nbNotices > 1 ? $n = 's' : $n = '';
      $text = "";
      if (empty($items) && $vide == 0 && $id <> 0) {
        $text = "Aucune notice n'a de valeur pour ce champ.";
      } elseif (empty($items) && $vide == 1 && $id <> 0) {
        $text = "Toutes les notices ont une valeur pour ce champ.";
      } elseif ($vide == 1 && $id <> 0) {
        $text = $count . " notice$e n'$v pas de valeur pour ce champ.";
      } elseif ($vide == 0 && $id <> 0) {
        $text = $count . " valeur$e pour ce champ dans $nbNotices notice$n.";
      }
      $this->view->texte = $text;

      setlocale(LC_COLLATE, 'fr_FR.utf8');
  		usort($resultats, function($a, $b) {
    		return strcoll(strip_tags($a['text']), strip_tags($b['text']));
  		});

      if (isset($_GET['order']) && $_GET['order'] == 'alphainverse') {
    		$resultats = array_reverse($resultats, true);
      }
      foreach ($resultats as $i => $valeur) {
        if (isset($valeurs[$valeur['text']])) {
          $count = $valeurs[$valeur['text']]['count'];
          $content .= "<a id='item-$i'></a><div id='$i' class='wrap'><span class='html-value' style='display:none;'>" . $valeur['text'] . "</span><span class='field-value' style='font-weight:bold;'>" . strip_tags($valeur['text']) . '</span> (' . $count . ') <span class="montrer"> + </span><br />';
          unset($valeurs[$valeur['text']]['count']);
          $res = $valeurs[$valeur['text']];
      		uasort($res, function($a, $b) {
        		return strcoll($a, $b);
      		});
      		if ($count == 1) {
        		$e = '';
        		$v = 'a';
      		} else {
        		$e = 's';
        		$v = 'ont';
      		}
      		$content .= "<ol class='notices' style='display:none;'>" . $count . " notice$e $v cette  valeur pour le champ <em>" . __($fieldName->name) . '</em> : ';
          foreach ($res as $itemId => $title) {
            $modif = '';
            if (current_user()) {
              $modif = " - <span class='edit-value'>Modifier la valeur du champ pour cette notice</span>";
            }
            $content .= "<li class='valeur' id='" . $itemId . "'><a href='" . WEB_ROOT . "/items/show/" . $itemId ."' target='_blank'>" . strip_tags($title) . "</a>$modif</li>";
          }
          $content .= "</ol></div>";
          unset($valeurs[$valeur['text']]);
        }
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
      // TODO : Limiter aux champs sélectionné par l'admin quand auser anonyme
      $fieldsAvailable = unserialize(get_option('emanindex_fields'));
  		$query = "SELECT DISTINCT (name), id FROM omeka_elements ORDER BY name";
  		$elements = $db->query($query)->fetchAll();
  		foreach ($elements as $i => $element) {
     		$elements[$i]['name'] = __($elements[$i]['name']);
      }
      setlocale(LC_COLLATE, 'fr_FR.utf8');
      usort($elements, function($a, $b) {return strcoll($a['name'], $b['name']); } );
  		$content = '<select id="fieldName"><option value="0" $selected>Sélectionner un champ</option>';
  		foreach ($elements as $i => $element) {
    		if ($fieldsAvailable['field_' . $element['id']] == 1 || current_user()) {
      		$id == $element['id'] ? $selected = 'selected' : $selected = '';
      		$content .= "<option $selected value='" . $element['id'] . "'>" . $element['name'] . "</option>";
        }
  		}
  		$content .= '</select>';
  		$this->view->dropdown = $content;
  	}

  	public function fieldsListAction() {
    	$form = $this->getFieldsForm();
  		if ($this->_request->isPost()) {
  			$formData = $this->_request->getPost();
  			if ($form->isValid($formData)) {
  				set_option('emanindex_fields', serialize($form->getValues()));
  		  }
  		}
    	$this->view->form = $form;
  	}

    public function getFieldsForm() {
      $values = unserialize(get_option('emanindex_fields'));
  		$db = get_db();
  		$query = "SELECT DISTINCT (name) name, id FROM `$db->Elements` ORDER BY name";
  		$elements = $db->query($query)->fetchAll();
  		$fields = [];
  		foreach ($elements as $i => $element) {
    		$fields[$element['id']] = $element['name'];
      }

  		$form = new Zend_Form();
  		$form->setName('FieldList');

    	$checkall = new Zend_Form_Element_Checkbox('checkall');
    	$checkall->setLabel('Tout cocher');
    	$checkall->setAttrib('title', 'Tout cocher');
    	$checkall->setValue('checkall');
    	$form->addElement($checkall);

  		foreach ($fields as $id => $field) {
   			$lefield = new Zend_Form_Element_Checkbox('field_' . $id);
   			$lefield->setLabel($field);
   			$lefield->setValue($values['field_' . $id] == 1);
      	$lefield->setAttrib('class', 'check-available');
   			$form->addElement($lefield);
  		}

      $submit = new Zend_Form_Element_Submit('submit');
      $submit->setLabel('Save Template');
      $form->addElement($submit);

  		$form = $this->prettifyForm($form);
      return $form;
    }

  	private function prettifyForm($form) {
  		// Prettify form
  		$form->setDecorators(array(
  				'FormElements',
  				array('HtmlTag', array('tag' => 'table')),
  				'Form'
  		));
  		$form->setElementDecorators(array(
  				'ViewHelper',
  				'Errors',
  				array(array('data' => 'HtmlTag'), array('tag' => 'td')),
  				array('Label', array('tag' => 'td', 'style' => 'text-align:right;float:right;')),
  				array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
  		));
  		return $form;
  	}
}

