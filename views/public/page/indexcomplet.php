<?php echo head(array('title' => __('Index'))); ?>

<script type="text/javascript" src="<?php echo WEB_ROOT; ?>/plugins/EmanIndex/javascripts/emanindex.js"></script>
<style>
#wrap > #content > ol.notices {
  display:none;
  background-color:transparent;
  border:none;
  -webkit-box-shadow: rgba(0, 0, 0, 0) 0 0 10px;
  -moz-box-shadow: rgba(0, 0, 0, 0) 0 0 10px;
  box-shadow: rgba(0, 0, 0, 0) 0 0 10px;
  -webkit-box-sizing: border-box;
  margin:0;
  padding:0 0 0 2em;
}
span.montrer {
  cursor:pointer;
  color:#777;
}
.valeur {
  margin:0;
}
.tout {
  cursor:pointer;
  font-weight: bold;
  font-size: 1.2em;
  float:right;
}
.edit-value {
  cursor:pointer;
}
</style>

<h3>Index des valeurs</h3> <br />

En sélectionnant un champ, vous avez accès à la liste complète des valeurs
utilisées pour ce champ. La fonction ne fonctionne que pour les notices et non pour
les collections ou les images. Les résultats sont présentés par ordre
alphabétique. La liste déroulante présente les champs Dublin Core et
Métadonnées personnalisées ensemble.
<br /><br />
  <?php
  echo $dropdown;
if ($id) {
  echo "<br /><a href='" . WEB_ROOT . "/emanindexpage?q=" . $id . "&order=alpha&vide=" . $vide . "'>Trier par ordre alphabétique</a><br />";
  echo "<a href='" . WEB_ROOT . "/emanindexpage?q=" . $id . "&order=alphainverse&vide=" . $vide . "'>Trier par ordre alphabétique inverse</a><br />";
  echo "<br />$texte<br /><br />" ;
?>
<span class="tout">Tout d&eacute;plier</span><br /><br />
<?php
}
  echo $content;
  ?>
  <script>
  $ = jQuery;
  $(document).ready(function() {
    $('#vides').change(function() {
      $('#fieldName').change();
    });
    $('#fieldName').change(function () {
      var vide = 0;
      if ($('#vides').attr('checked') == 'checked') {
        vide = 1;
      }
      window.location = '<?php echo WEB_ROOT ?>/emanindexpage?q=' + $(this).val() + "&vide=" + vide;
    });
  });

  </script>

  <?php echo foot();