<?php
class EmanIndex_PageController extends Omeka_Controller_AbstractActionController
{
    public function fetchfieldsAction($type = 'Item')
  	{
  		$title = strtoupper($this->getParam('q'));
  		$fieldid = $this->getParam('fieldid');
  		$db = get_db();
  		$items = $db->query("SELECT DISTINCT(text) FROM `$db->ElementTexts' WHERE element_id = $fieldid AND record_type = '$type' AND UPPER(text) LIKE '%$title%' ORDER BY text ASC")->fetchAll();
  		$this->_helper->json($items);
  	}

    public function indexcompletAction()
  	{
  		$db = get_db();
      $content = $fichiers = $items = $collections = "";
  		$id = $this->getParam('q');
  		$id == null ? $id = 0 : null;
  		$type = $this->getParam('type');
  		$type == null || ! in_array($type, ['Collection', 'Item', 'File']) ? $type = 'Item' : null;
  		$mainType = $db->{$type . 's'};
  		$frenchTypes = ['Item' => 'item', 'Collection' => 'collection', 'File' => 'fichier'];
  		$fieldName = $db->query("SELECT name FROM `$db->Elements` WHERE id = $id")->fetchObject();
  		$this->view->id = $id;
  		$vide = $this->getParam('vide');
  		$this->view->vide = $vide;
  		$this->view->type = $type;
   		$this->view->dropdown = '';
  		$options = unserialize(get_option('emanindex_preferences'));
  		if (! current_user() && $options['collection'] == 1 || current_user()) {
        $type == 'Collection' ? $checked = 'checked' :  $checked = '';
        $collections = '<span style="margin-right:1em;"><input type="radio" id="Collection" class="type" name="type" value="Collection" ' . $checked . '><label for="Collection">Collections</label></span>';
      }
      if ($options['fichier'] == 1 || $options['collection'] == 1 || current_user()) {
     		$this->view->dropdown = 'Types de données indexées : ';
        $type == 'Item' ? $checked = 'checked' :  $checked = '';
        $items = '<span style="margin-right:1em;"><input type="radio" id="Item" class="type" name="type" value="Item" ' . $checked . '><label for="Item">Items</label></span>';
      }
  		if (! current_user() && $options['fichier'] == 1 || current_user()) {
        $type == 'File' ? $checked = 'checked' :  $checked = '';
        $fichiers .= '<span style="margin-right:1em;"><input type="radio" id="File" class="type" name="type" value="File" ' . $checked . '><label for="File">Fichiers</label></span>';
      }
      $this->view->dropdown .= $collections . $items . $fichiers . '<br />' . $this->fieldsDropdown();
  		if ($vide == 1 && $id <> 0) {
    		$check = 'checked';
        $query = "SELECT DISTINCT(text), record_id id FROM `$db->ElementTexts` WHERE record_type = '$type' AND element_id = 50 AND record_id NOT IN (SELECT main.id FROM `$db->ElementTexts` e LEFT JOIN $mainType main ON e.record_id = main.id WHERE record_type = '$type' AND element_id = $id)";
      } else {
    		$private = "";
        if (! current_user() && $type <> 'File') {
          $private = "AND main.public = 1";
        }
        $check = "";
        $query = "SELECT DISTINCT(text), record_id id FROM `$db->ElementTexts` text LEFT JOIN $mainType main ON main.id = text.record_id WHERE record_type = '$type' AND element_id = $id $private";
      }
      if (current_user()) {
        $this->view->dropdown .= '<span style="padding-left:20px;"><input ' . $check . ' type="checkbox" name="vides" id="vides" /><label style="padding-left:5px;" for="vides">Afficher les champs vides</label></span>';
      }
      $resultats = [];
   		$records = $db->query($query)->fetchAll();
      $valeurs = [];
      foreach($records as $i => $record) {
        $resultats[$i]['id'] = $record['id'];
        $resultats[$i]['text'] = $record['text'];
        $theRecord = get_record_by_id($type, $record['id']);
        if ($theRecord) {
          $title = strip_tags(metadata($theRecord, array('Dublin Core', 'Title')));
        }
        if (isset($valeurs[$record['text']])) {
          $valeurs[$record['text']][$record['id']] = $title;
          $valeurs[$record['text']]['count']++;
        } else {
          $valeurs[$record['text']][$record['id']] = $title;
          $valeurs[$record['text']]['count'] = 1;
        }
      }
      $nbNotices = count(array_count_values(array_column($resultats, 'id')));
      $count = count($resultats);
      $count > 1 ? $e = 's' : $e = '';
      $count > 1 ? $v = 'ont' : $v = 'a';
      $nbNotices > 1 ? $n = 's' : $n = '';
      $currentType = $frenchTypes[$type];
      $text = "";
      if (empty($records) && $vide == 0 && $id <> 0) {
        $type == 'File' ? $a = 'Aucun' : $a = 'Aucune';
        $text = "$a $currentType n'a de valeur pour ce champ.";
      } elseif (empty($records) && $vide == 1 && $id <> 0) {
        $type == 'File' ? $t = 'Tous' : $t = 'Toutes';
        $text = "$t les $currentType" . "s ont une valeur pour ce champ.";
      } elseif ($vide == 1 && $id <> 0) {
        $text = $count . " $currentType$e n'$v pas de valeur pour ce champ.";
      } elseif ($vide == 0 && $id <> 0) {
        $text = $count . " valeur$e pour ce champ dans $nbNotices $currentType" . "$e.";
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
          $content .= "<a id='$type-$i'></a><div id='$i' class='wrap' type='$type'><span class='html-value' style='display:none;'>" . $valeur['text'] . "</span><span class='field-value' style='font-weight:bold;'>" . strip_tags($valeur['text']) . '</span> (' . $count . ') <span class="montrer"> + </span><br />';
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
      		$content .= "<ol class='records' style='display:none;'>" . $count . " " . strtolower($currentType) . "$e $v cette  valeur pour le champ <em>" . __($fieldName->name) . '</em> : ';
          foreach ($res as $recordId => $title) {
            $modif = '';
            if (current_user()) {
              $modif = " - <span class='edit-value'>Modifier la valeur du champ pour cette $type</span>";
            }
            $content .= "<li class='valeur' id='" . $recordId . "'><a href='" . WEB_ROOT . "/". strtolower($type) . "s/show/" . $recordId ."' target='_blank'>" . strip_tags($title) . "</a>$modif</li>";
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
      $fieldsAvailable = unserialize(get_option('emanindex_fields'));
  		$query = "SELECT DISTINCT (name), id FROM omeka_elements ORDER BY name";
  		$elements = $db->query($query)->fetchAll();
  		foreach ($elements as $i => $element) {
     		$elements[$i]['name'] = __($elements[$i]['name']);
      }
      setlocale(LC_COLLATE, 'fr_FR.utf8');
      usort($elements, function($a, $b) {return strcoll($a['name'], $b['name']); } );
      $message  = "<div>Le(s) champ(s) suivants ne figurent pas dans la liste : <br /><ul>";
  		$content = '<select id="fieldName"><option value="0" $selected>Sélectionner un champ</option>';
  		foreach ($elements as $i => $element) {
    		if (isset($fieldsAvailable['field_' . $element['id']])) {
      		if ($fieldsAvailable['field_' . $element['id']] == 1 || current_user()) {
        		$id == $element['id'] ? $selected = 'selected' : $selected = '';
        		$content .= "<option $selected value='" . $element['id'] . "'>" . $element['name'] . "</option>";
          }
    		} else {
      		$message .= "<li><em>" . $element['name'] . "</em></li>";
    		}
  		}
  		$content .= '</select>';
  		if (current_user() && strstr($message, "<li>")) {
    		$content = $message . "</ul>Pour prendre en compte ces champs, rendez-vous sur <a target='_blank' href='" . WEB_ROOT . "/admin/emanindex/fieldslist'> cette page</a>.</div>" . $content;
  		}
  		return $content;
  	}

  	public function fieldsListAction() {
    	$form = $this->getFieldsForm();
  		if ($this->_request->isPost()) {
  			$formData = $this->_request->getPost();
  			if ($form->isValid($formData)) {
  				set_option('emanindex_fields', serialize($form->getValues()));
          $this->_helper->flashMessenger('Eman Index available fields saved.');
  		  }
  		}
    	$this->view->form = $form;
  	}

  	public function preferencesAction() {
    	$form = $this->getPreferencesForm();
  		if ($this->_request->isPost()) {
  			$formData = $this->_request->getPost();
  			if ($form->isValid($formData)) {
  				set_option('emanindex_preferences', serialize($form->getValues()));
          $this->_helper->flashMessenger('Eman Index parameters saved.');
  		  }
  		}
    	$this->view->form = $form;
  	}

    public function getPreferencesForm() {
      $values = unserialize(get_option('emanindex_preferences'));
  		$form = new Zend_Form();
  		$form->setName('preferences');

    	$file = new Zend_Form_Element_Checkbox('fichier');
    	$file->setLabel('Monter les informations concernant les fichiers :');
    	$file->setAttrib('title', 'File');
    	isset($values['fichier']) ? $file->setValue($values['fichier']) : $file->setValue(0);
    	$form->addElement($file);

    	$collection = new Zend_Form_Element_Checkbox('collection');
    	$collection->setLabel('Monter les informations concernant les collections :');
    	$collection->setAttrib('title', 'Collection');
    	isset($values['collection']) ? $collection->setValue($values['collection']) : $collection->setValue(0);
    	$form->addElement($collection);

      $submit = new Zend_Form_Element_Submit('submit');
      $submit->setLabel('Save Template');
      $form->addElement($submit);

  		$form = $this->prettifyForm($form);
      return $form;
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

