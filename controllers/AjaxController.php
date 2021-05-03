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

    	$recordType = $data['recordType'];
    	$recordId = $data['recordId'];
    	$elementId = $data['elementId'];
    	$valeur = $db->quote($data['valeur']);
    	$orig = $db->quote($data['orig']);

      if ($data['orig']) {
        $query = "UPDATE `$db->ElementTexts` SET text = $valeur WHERE record_type = '$recordType' AND record_id = $recordId AND element_id = $elementId AND text = $orig";
      } else {
        $query = "INSERT INTO `$db->ElementTexts` VALUES (null, $recordId, '$recordType', $elementId, 0, $valeur)";
      }

      $db->query($query);
      $this->_helper->json($query);
  	}
}
