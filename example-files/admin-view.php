<?php
$this->breadcrumbs = array(
  'Locations' => array( 'admin' ),
  'Manage',
);

$this->menu = array(
  array( 'label' => 'Manage locations', 'url' => array( 'admin' ) ),
  array( 'label' => 'Create location', 'url' => array( 'create' ) ),
);

$this->pageTitle = title( 'Manage locations' );
?>

<h2>Manage locations</h2>

<p>
  You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>,
  <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>)
  at the beginning of each of your search values to specify how the
  comparison should be done.
</p>

<p>
  <?php echo CHtml::link( 'Create a new location', array( 'create' ), array( 
    'class' => 'update-dialog-create' ) ); ?>
</p>

<?php $this->widget( 'zii.widgets.grid.CGridView', array(
  'id' => 'location-grid',
  'dataProvider' => $model->search(),
  'filter' => $model,
  'columns' => array(
    array(
      'name' => 'name',
      'type' => 'raw',
      'value' => 'CHtml::link(
        CHtml::encode( $data->name ),
        array( "view", "id" => $data->id ) )',
    ),
    array(
      'name' => 'url',
      'type' => 'raw',
      'value' => 'CHtml::link(
        CHtml::encode( $data->url ), $data->url, array( 
          "target" => "_blank" ) )',
    ),
    'address',
    'city',
    array(
      'class' => 'CButtonColumn',
      'template' => '{update}{delete}',
      'header' => 'Action',
      'deleteButtonUrl' => 'Yii::app()->createUrl( 
        "/admin/location/delete", 
        array( "id" => $data->primaryKey ) )',
      'buttons' => array(
        'delete' => array(
          'click' => 'updateDialogDelete',
        ),
        'update' => array(
          'click' => 'updateDialogUpdate',
        ),
      ),
    ),
  ),
)); ?>

<?php $this->widget( 'ext.EUpdateDialog.EUpdateDialog' ); ?>