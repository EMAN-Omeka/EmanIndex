<?php
/**
 * Eman Index
 */

class EmanIndex_AjaxController extends Omeka_Controller_AbstractActionController
{
    public function updateAction()
  	{
      $data = $this->_request->getPost();
  		$db = get_db();

    	$itemId = $data['itemId'];
    	$elementId = $data['elementId'];
    	$valeur = $db->quote($data['valeur']);
    	$orig = $db->quote($data['orig']);

      // $orig = mysqli_real_escape_string($db->getAdapter()->getConnection(), $data['orig']);
      // $orig = addcslashes($data['orig'], "\000\n\r\\'\"\032");

      // TODO : fonctionne avec % mais pas _
      $orig = str_replace("\\n", "%", $orig);

      $query = "UPDATE `$db->ElementTexts` SET text = $valeur WHERE record_type = 'Item' AND record_id = $itemId AND element_id = $elementId AND text LIKE $orig";
      $db->query($query);
  		$this->_helper->json($query);
  	}
}
