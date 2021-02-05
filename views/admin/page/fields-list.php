<?php echo head(array('title' => __('EmanIndex : liste des champs à afficher publiquement')));
echo flash();
?>

<div id='ei-menu'>
  <a class='add button small green' href='<?php echo WEB_ROOT; ?>/admin/emanindex/fieldslist'><?php echo __('Fields'); ?></a>
  <a class='add button small green' href='<?php echo WEB_ROOT; ?>/admin/emanindex/preferences'><?php echo __('Settings'); ?></a>
</div>

<script type="text/javascript" src="<?php echo WEB_ROOT; ?>/plugins/EmanIndex/javascripts/emanindex.js"></script>
<br /><br />
<p>Choisissez les champs qui seront disponibles pour les utilisateurs anonymes.</p>

<?php echo $form; ?>

<?php echo foot();