<?php echo head(array('title' => __('EmanIndex : Préféfences')));
echo flash();
 ?>

<div id='ei-menu'>
  <a class='add button small green' href='<?php echo WEB_ROOT; ?>/admin/emanindex/fieldslist'><?php echo __('Fields'); ?></a>
  <a class='add button small green' href='<?php echo WEB_ROOT; ?>/admin/emanindex/preferences'><?php echo __('Settings'); ?></a>
</div>

<script type="text/javascript" src="<?php echo WEB_ROOT; ?>/plugins/EmanIndex/javascripts/emanindex.js"></script>
<br /><br />
<p>Choisissez si les informations concernant les fichiers seront proposées aux utilisateurs non connectés.</p>
<?php echo $form; ?>

<?php echo foot();