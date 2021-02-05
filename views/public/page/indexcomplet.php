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
  font-family: Helvetica;
  cursor:pointer;
  color:white;
  float:right;
  background: #A4C637;
  background: -webkit-linear-gradient(top, #A4C637, #75940A);
  background: -moz-linear-gradient(top, #A4C637, #75940A);
  background: -o-linear-gradient(top, #A4C637, #75940A);
  background: linear-gradient(to bottom, #A4C637, #75940A);
  -webkit-box-shadow: 0px 1px 0px 0px #bdd662 inset, 0px -1px 0px 0px #6a821a inset, 0px 2px 2px 0px #d4d4d4;
  -moz-box-shadow: 0px 1px 0px 0px #bdd662 inset, 0px -1px 0px 0px #6a821a inset, 0px 2px 2px 0px #d4d4d4;
  box-shadow: 0px 1px 0px 0px #bdd662 inset, 0px -1px 0px 0px #6a821a inset, 0px 2px 2px 0px #d4d4d4;
  border: 1px solid #749308;
  border-radius: 5px;
  padding:5px;
}
.edit-value {
  cursor:pointer;
}
h3 {
  margin:0 0 20px 0;
}
p {
  margin: 0 0 10px 0;
}
input.type, label {
  cursor:pointer;
}
</style>

<h3>Index des valeurs</h3>
<p>En sélectionnant un champ, vous avez accès à la liste complète des valeurs
utilisées pour ce champ. Les résultats sont présentés par ordre
alphabétique. La liste déroulante présente les champs Dublin Core et
Métadonnées personnalisées ensemble.</p>
<?php if (current_user()) : ?>
<p>Vous pouvez modifier directement une valeur. Il peut arriver que la modification ne soit pas prise en compte ; il faut alors aller modifier directement la valeur sur le formulaire de la notice.</p>
<?php endif; ?>
<hr />
  <?php
  echo $dropdown;
if ($id) {
  echo "<br /><a href='" . WEB_ROOT . "/emanindexpage?q=" . $id . "&order=alpha&type=" . $type . "&vide=" . $vide . "'>Trier par ordre alphabétique</a><br />";
  echo "<a href='" . WEB_ROOT . "/emanindexpage?q=" . $id . "&order=alphainverse&type=" . $type . "&vide=" . $vide . "'>Trier par ordre alphabétique inverse</a><br />";
  echo "<h4>$texte</h4>" ;
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
    $('.type').change(function() {
      $('#fieldName').change();
    });
    $('#fieldName').change(function () {
      var vide = 0;
      if ($('#vides').attr('checked') == 'checked') {
        vide = 1;
      }
      type = $('.type:checked').attr('value');
      window.location = '<?php echo WEB_ROOT ?>/emanindexpage?q=' + $(this).val() + "&vide=" + vide + "&type=" + type;
    });
  });

  </script>

  <?php echo foot();