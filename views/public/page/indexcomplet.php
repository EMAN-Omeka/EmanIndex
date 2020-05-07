  <?php echo head(array('title' => __('Index'))); ?>
  
<h3>Index des valeurs</h3> <br />

En sélectionnant un champ, vous avez accès à la liste complète des valeurs
utilisées pour ce champ. La fonction ne marche que pour les notices et non pour
les collections ou les images. Les résultats sont présentées par ordre
alphabétique. La liste déroulante présente les champs Dublin Core et
Métadonnées personnalisées ensemble.
<br /><br />
  <?php
  echo $dropdown;
  
  echo "<br /><a href='" . WEB_ROOT . "/emanindexpage?q=" . $id . "&order=alpha&vide=" . $vide . "'>Trier par ordre alphabétique</a><br />";
  echo "<a href='" . WEB_ROOT . "/emanindexpage?q=" . $id . "&order=alphainverse&vide=" . $vide . "'>Trier par ordre alphabétique inverse</a><br />";
  echo "<br />$texte<br /><br />" ;    
  echo $content;
  ?>
  <script>
  $ = jQuery;
  $(document).ready(function() {
    $('#vides').change(function() {
//       event.preventDefault();       
      $('#fieldName').change();
    });
    $('#fieldName').change(function () {
      var vide = 0;
//       alert($('#vides').attr('checked'));
      if ($('#vides').attr('checked') == 'checked') {
        vide = 1;
      }
      window.location = '<?php echo WEB_ROOT ?>/emanindexpage?q=' + $(this).val() + "&vide=" + vide;
    });
  });

  </script>
  
  <?php echo foot();