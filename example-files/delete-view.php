<?php
$this->breadcrumbs = array(
  'Locations' => array( 'admin' ),
  $model->name => array( 'view', 'id' => $model->id ),
  'Delete',
);

$this->menu = array(
  array( 'label' => 'Manage locations', 'url' => array( 'admin' ) ),
  array( 'label' => 'Create location', 'url' => array( 'create' ) ),
  array( 'label' => 'Update location', 'url' => array(
    'update', 'id' => $model->id ) ),
  array( 'label' => 'View location', 'url' => array(
    'view', 'id' => $model->id ) ),
);

$this->pageTitle = title( 'Delete ' . $model->name );
?>

<h2>
  Are you sure you want to delete location:
  <?php echo CHtml::encode( $model->name ); ?>?
</h2>

<?php $form = $this->beginWidget( 'CActiveForm', array(
  'id' => 'location-delete-form',
  'enableAjaxValidation' => false,
  'focus' => '#confirmDelete',
)); ?>

<div class="buttons">
  <?php 
  echo CHtml::submitButton( 'Yes', array( 'name' => 'confirmDelete', 
    'id' => 'confirmDelete' ) );
  echo CHtml::submitButton( 'No', array( 'name' => 'denyDelete' ) ); 
  ?>

  <?php
  /* !!! Or you can use jQuery UI buttons, makes no difference !!!
  $this->widget( 'zii.widgets.jui.CJuiButton', array(
    'name' => 'confirmDelete',
    'caption' => 'Yes',
  ));
  $this->widget( 'zii.widgets.jui.CJuiButton', array(
    'name' => 'denyDelete',
    'caption' => 'No',
  ));*/
  ?>
</div>

<?php $this->endWidget(); ?>