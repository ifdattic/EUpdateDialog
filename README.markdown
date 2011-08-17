EUpdateDialog
=============

**EUpdateDialog** is an extension for Yii framework. This extension allows to create/update/delete model using dialog window (CJuiDialog). It works with CGridView buttons and normal links, and it degrades gracefully with JavaScript turned off.

Requirements
------------

* Yii 1.1 or above (tested on 1.1.7, 1.1.8)
* jQuery (included in Yii)
* jQuery UI dialog widget (included in Yii)

Installation
------------

**EUpdateDialog** folder contains these folders:

* **actions** folder contains controller actions, place it in your applications folder (`protected`, or in the folder of your choice, just don't forget to update `actions()` method in your controller).
* **EUpdateDialog** folder contains the extension, place it in your applications extensions folder (default: `protected/extensions`).
* **example-files** folder contains files to be used as reference if you need help implementing extension. *You don't need to add this folder to your application!*

Using extension
---------------

Just place the following code inside your view file:

```php
<?php $this->widget( 'ext.EUpdateDialog.EUpdateDialog' ); ?>
```
    
You can also change some default settings:

```php
<?php $this->widget( 'ext.EUpdateDialog.EUpdateDialog', array(
  'height' => 200,
  'resizable' => true,
  'width' => 300,
)); ?>
```

Links with class `update-dialog-create` (this can be changed, but you will need to update extensions JavaScript with the new selector) will automatically be opened inside dialog:

```php
<?php echo CHtml::link( 'Create a new model', array( 'create' ), array(
  'class' => 'update-dialog-create' ) ); ?>
```

To add update and delete functionality, make the following changes to grid view:

```php
<?php $this->widget( 'zii.widgets.grid.CGridView', array(
  ...
  'columns' => array(
    ...
    array(
      'class' => 'CButtonColumn',
      'deleteButtonUrl' => 'Yii::app()->createUrl( 
        "/path/to/delete/action", 
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
```

Change your controllers `actions()` method (change the alias if you put them somewhere else):

```php
public function actions()
{
  return array(
    'create' => 'application.actions.createAction',
    'delete' => 'application.actions.deleteAction',
    'update' => 'application.actions.updateAction',
  );
}
```

You can also overwrite default messages:

```php
public function actions()
{
  return array(
    'create' => array(
      'class' => 'application.actions.createAction',
      'successMessage' => 'This is success message',
    ),
    'delete' => array(
      'class' => 'application.actions.deleteAction',
      'successMessage' => 'This is success message',
      'cancelMessage' => 'This is cancel message',
    ),
    'update' => array(
      'class' => 'application.actions.updateAction',
      'successMessage' => 'This is success message',
    ),
  );
}
```

### Delete view ###

Your models delete view has to contain a form with two submit buttons. One of buttons `name` attribute has to be `confirmDelete` (which deletes the model), and the other `denyDelete` (which cancels the delete action):

```php
// You need to have a form in your delete view file!
<?php $form = $this->beginWidget( 'CActiveForm', array(
  'id' => 'id-of-the-form',
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
```

Gii code generator
------------------

This won't work with default code generated through Gii. You will have to add `$id` parameter to controllers methods or modify `loadModule()` method to look similarly to this:

```php
public function loadModel()
{
  if( $this->_model === null )
  {
    if( isset( $_GET['id'] ) )
      $this->_model = ModelName::model()->findByPk( (int)$_GET['id'] );
    if( $this->_model === null )
      throw new CHttpException( 404, 'The requested page does not exist.' );
  }
  return $this->_model;
}
```

CSRF validation
---------------

EUpdateDialog works with or without CSRF validation. It will need jQuery cookie plugin (which is included in extension) or you can download it from [jQuery plugins page](http://plugins.jquery.com/project/Cookie "jQuery plugins page") (with a few changes it should work with any method which can read cookie values). If you are using other than default name for token, you will need to update `EUpdateDialog.js` file, line 23 with your CSRF token name. If you are not using CSRF validation, extension degrades gracefully without adding cookie or CSRF token cookie.

Flash message
-------------

Actions automatically sets flash message for additional feedback. If you don't want to use them you will have to remove `$controller->setFlash( ...` from your action files. To use flash messages you will need to add following method to your controller:

```php
public function setFlash( $key, $value, $defaultValue = null )
{
  Yii::app()->user->setFlash( $key, $value, $defaultValue );
}
```