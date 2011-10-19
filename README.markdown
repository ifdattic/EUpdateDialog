EUpdateDialog
=============

**EUpdateDialog** is an extension for Yii framework. This extension allows to run controller actions using jQuery UI dialog. 

It is used for extending your application to allow easy CRUD (create-read-update-delete) actions or any other action which returns a proper JSON response. Click event can be added to any link element using a jQuery selector, allowing you to extend your application with additional functionality, without sacrificing design.

Extension degrades gracefully, so your application won't lose any functionality if JavaScript is turned off.

**Note:** I made this extension for the project I'm working on, so like always you are can change it to better suit your needs. If you find this extension useful, please consider a [donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6VDDH9LN5TDNY "Donate"). Also please let me know about any bugs or enhancements you find, so that I could have more ideas then working on the next version (new version is good, but I'm still not satisfied with it) :)

Requirements
------------

* Yii 1.1 or above (tested on 1.1.7, 1.1.8)
* jQuery (included in Yii)
* jQuery UI dialog widget (included in Yii)
* jQuery cookie plugin (included in Yii) [optional]

Installation
------------

* **actions** folder contains controller actions, place it in your application folder (usually `protected`, or in the folder of your choice, just don't forget to update `actions()` method in your controller).
* **EUpdateDialog** folder contains the extension, place it in your applications extensions folder (usually `protected/extensions`).
* **README.markdown** is a readme file in markdown syntax containing this documentation. You can delete this file.

Basic extension usage
---------------------

Basic extension usage is pretty simple. First make sure your controller has a `loadModule()` method and it's something like:

```php
<?php
public function loadModel()
{
  if( $this->_model === null )
  {
    if( isset( $_GET['id'] ) )
      $this->_model = ModelName::model()->findByPk( (int) $_GET['id'] );
    if( $this->_model === null )
      throw new CHttpException( 404, 'The requested page does not exist.' );
  }
  return $this->_model;
}
?>
```

Actions automatically sets flash message for additional feedback. If you don't want to use them you will have to remove `$controller->setFlash( ... )` calls from you action files. To use flash messages you will need to add following method to your controller:

```php
<?php
public function setFlash( $key, $value, $defaultValue = null )
{
  Yii::app()->user->setFlash( $key, $value, $defaultValue );
}
?>
```

Next change your controllers `actions()` method (change the alias if you put them somewhere else):

```php
<?php
public function actions()
{
  return array(
    'view' => 'application.actions.CreateAction',
  );
}
?>
```

Now make changes to the view file which will use an EUpdateDialog. Add a `update-dialog-open-link` class to all the links which need to open in EUpdateDialog. Finally add an extension widget to your view file:

```php
<?php
$this->widget( 'ext.EUpdateDialog.EUpdateDialog' );
?>
```

That's it, you should be able to use EUpdateDialog extension in your application. However this is the most basic usage of the extension, and you will probably want to customize it to your needs. So keep reading to find out more about customization and other tricks.

Widget options
--------------

Here are the available options which you can send to the widget:

**dialogOptions** is an array containing options for `JuiDialog`. You can find all available options at [jQuery UI dialog demo](http://jqueryui.com/demos/dialog/ "jQuery UI dialog demo") page. By default extension only sets `autoOpen` and `modal` options.

**options** is an array with options for extension. They should be provided in the `option => value` fashion, more on this later.

**preload** is an array with scripts to preload. This array should contain scripts which are not in the page containing EUpdateDialog extension but is loaded with a page which will be opened in EUpdateDialog. It is used with actions `disableScripts` property for extra performance. More on this later.

**target** is a jQuery selector which will add a click event to the elements for opening EUpdateDialog.

**tCategory** is a category name for a file which has translations for `Yii::t()` method.

Extension options
-----------------

Extension uses `updateDialogOpen()` JavaScript function which initialize the `updateDialog` JavaScript object and loads the contents in the dialog.

`updateDialog` object properties and methods can be overwritten or extended. Here are the available properties and methods:

**csrfToken** is a property containing a CSRF token data which is used if your application uses CSRF validation.

**csrfTokenName** is a property containing the name of CSRF cookie. It's automatically set by the extension.

**defaultTitle** is a property containing the default title for the dialog. It's used then dialog title is not provided in the link. Defaults to: 'Dialog'. More on adding custom dialog title later.

**timeout** is a property containing the time (in milliseconds) to wait before running callback. Defaults to: 1000 (one second).

**addContent** is a method which removes a loader and adds returned contents to the EUpdateDialog.

**addLoader** is a method which adds the loader. By default it replaces EUpdateDialog contents with 'Loading...'.

**callback** is a method which runs a callback depending on the status. Currently recognized statuses: 'render', 'success', 'deleted', 'canceled', 'imagedeleted'. *Note:* if someone knows how to implement something like PHPs `$method = 'callback' . $status; if( method_exists( $this, $method ) $this->{$method}();` I would really appreciate if you shared that knowledge. Sharing is caring ;)

**canceledCallback** is a method which runs if 'canceled' status was received. By default it runs `close` method.

**cleanContents** is a method which removes all EUpdateDialog contents.

**close** is a method which runs `cleanContents` method and closes EUpdateDialog.

**defaultCallback** is a method which runs when no callback status was recognized. By default it runs `close` method.

**deletedCallback** is a method which runs if 'deleted' status was received. By default it redirects to a `redirectToAfterDelete` property value if it's set.

**getCsrfToken** is a method which sets csrfToken if jQuery cookie plugin and `csrfTokenName` property are available.

**imageDeletedCallback** is a method which runs if 'imagedeleted' status was received. By default it runs `close` method. You can ignore this as I added it only because I needed it in my project.

**init** is a method which initialize the `updateDialog` object on the first run. By default it sets the `dialog`, `defaultWidth`, `dialogContent`, `csrfToken` properties. It also attaches `click` event to all submit buttons (for submitting the form) and all elements with `update-dialog-cancel-button` class (for closing dialog) in EUpdateDialog content.

**open** is an element which opens and load contents for EUpdateDialog. By default it runs `cleanContents` method, changes the `title` to `defaultTitle` if it's not set (`title` is set automatically when opening EUpdateDialog), changes the width of the dialog if it's bigger than the window width, opens the dialog, runs the `addLoader` and `addContent` method.

**removeLoader** is a method which removes the loader. By default it removes all EUpdateDialog contents.

**renderCallback** is a method which runs if 'render' status was received. By default it does nothing.

**submit** is a method which submits the form from EUpdateDialog. By default it gathers all needed form data, runs the `addLoader` method, sends the data to a controller action and on success runs the `removeLoader` method, updates the EUpdateDialog contents and runs a `callback` method with a returned status.

**successCallback** is a method which runs if 'success' status was received. By default it updates all gridviews with `grid-view` class, then updates all listviews with `list-view` class and runs `close` method.

Create action options
---------------------

Here are the available options for `CreateAction`:

**ajaxView** has a name of the view to use then action is accessed through AJAX. Defaults to: '_form'.

**callback** is a callback method in controller for additional processing. It is run after the model is saved and sends the model to a callback method. Defaults to: null. No method is run if callback is null or doesn't exist in the controller.

**disableScripts** is an array containing the scripts for `scriptMap` which should be disabled. By default `jquery.js`, `jquery.min.js` and `jquery-ui.min.js` scripts are disabled. Because `$.ajax` doesn't cache scripts, they will always be downloaded. For performance I will suggest to combine all (or most) of JavaScript files into one file which will be included in the page containing EUpdateDialog extension and then in the action disable those scripts. This can be used together with extensions `preload` property. (for example WYMeditor don't want to live in the save file as all other scripts, so I preload combined file of WYMeditor script files and disable then disable them in the action).

**flashTypePrefix** is a prefix for flash message type. Defaults to: ''.

**messages** is an array containing the messages for the action. By default `error`, `postRequest` and `success` messages are set. Default messages use `Yii::t()` for translations.

**redirectTo** is a redirect URL set by the user. Defaults to: null. If `null` user will be redirected to a `view` action with a newly created model `id`. This property can also be set as an array or a string.

**redirectToOnCancel** is used to create an url for a link. Useful if you want to use a cancel button/link in your forms.

**tCategory** is a category name for a file which has translations for `Yii::t()` method.

**view** contains a name of the view to use then action is accessed without AJAX. Defaults to: null. If `null` current action id will be used (for example if action is `create` the `create` view file will be used for rendering).

*Note:* action allows only `POST` requests.

Delete action options
---------------------

Here are the available options for `DeleteAction`:

**ajaxView** has a name of the view to use then action is accessed through AJAX. Defaults to: '_delete'.

**disableScripts** is an array containing the scripts for `scriptMap` which should be disabled. By default `jquery.js`, `jquery.min.js` and `jquery-ui.min.js` scripts are disabled. Because `$.ajax` doesn't cache scripts, they will always be downloaded. For performance I will suggest to combine all (or most) of JavaScript files into one file which will be included in the page containing EUpdateDialog extension and then in the action disable those scripts. This can be used together with extensions `preload` property. (for example WYMeditor don't want to live in the save file as all other scripts, so I preload combined file of WYMeditor script files and disable then disable them in the action).

**flashTypePrefix** is a prefix for flash message type. Defaults to: ''.

**redirectTo** is a redirect URL set by the user. Defaults to: null. If `null` user will be redirected to a `view` action with an updated model `id`. This property can also be set as an array or a string.

**redirectToAfterDelete** is used to as a redirect URL after successful deletion. Defaults to: `array( 'admin' )`.

**tCategory** is a category name for a file which has translations for `Yii::t()` method.

**view** has a name of the view to use then action is accessed without AJAX. Defaults to: null. If `null` current action id will be used (for example if action is `delete` the `delete` view file will be used for rendering).

*Note:* action allows only `POST` requests.

Update action options
---------------------

Here are the available options for `UpdateAction`:

**ajaxView** has a name of the view to use then action is accessed through AJAX. Defaults to: '_form'.

**callback** is a callback method in controller for additional processing. It is run after the model is saved and sends the model to a callback method. Defaults to: null. No method is run if callback is null or doesn't exist in the controller.

**disableScripts** is an array containing the scripts for `scriptMap` which should be disabled. By default `jquery.js`, `jquery.min.js` and `jquery-ui.min.js` scripts are disabled. Because `$.ajax` doesn't cache scripts, they will always be downloaded. For performance I will suggest to combine all (or most) of JavaScript files into one file which will be included in the page containing EUpdateDialog extension and then in the action disable those scripts. This can be used together with extensions `preload` property. (for example WYMeditor don't want to live in the save file as all other scripts, so I preload combined file of WYMeditor script files and disable then disable them in the action).

**flashTypePrefix** is a prefix for flash message type. Defaults to: ''.

**messages** is an array containing the messages for the action. By default `error`, `postRequest` and `success` messages are set. Default messages use `Yii::t()` for translations.

**redirectTo** is a redirect URL set by the user. Defaults to: null. If `null` user will be redirected to a `view` action with an updated model `id`. This property can also be set as an array or a string.

**tCategory** is a category name for a file which has translations for `Yii::t()` method.

**view** has a name of the view to use then action is accessed without AJAX. Defaults to: null. If `null` current action id will be used (for example if action is `update` the `update` view file will be used for rendering).

*Note:* action allows only `POST` requests.

View action options
-------------------

**ajaxView** has a name of the view to use then action is accessed through AJAX. Defaults to: '_view'.

**disableScripts** is an array containing the scripts for `scriptMap` which should be disabled. By default `jquery.js`, `jquery.min.js` and `jquery-ui.min.js` scripts are disabled. Because `$.ajax` doesn't cache scripts, they will always be downloaded. For performance I will suggest to combine all (or most) of JavaScript files into one file which will be included in the page containing EUpdateDialog extension and then in the action disable those scripts. This can be used together with extensions `preload` property. (for example WYMeditor don't want to live in the save file as all other scripts, so I preload combined file of WYMeditor script files and disable then disable them in the action).

**messages** is an array containing the messages for the action. By default `postRequest` messages are set. Default messages use `Yii::t()` for translations.

**tCategory** is a category name for a file which has translations for `Yii::t()` method.

**view** has a name of the view to use then action is accessed without AJAX. Defaults to: null. If `null` current action id will be used (for example if action is `view` the `view` view file will be used for rendering).

*Note:* action allows `GET` requests which have `ajax` parameter. This request will run `ajaxUpdate` method which will load the model and render non AJAX view without processing JavaScript (I use it in view pages to reload contents, but watch out for extensions which use jQuery `bind`).

Tips & Tricks
-------------

Here are a few tips & tricks (they are mostly taken from the project I'm working on so update to your own needs).

### EUpdateDialog links ###

To open EUpdateDialog when clicking on the link; add custom title:

```php
<?php
echo CHtml::link( 'Create', array( 'create' ),
  array(
    'class' => 'update-dialog-open-link',
    'data-update-dialog-title' => Yii::t( 'app', 'Create a new mix' ),
));
?>
```

### Add customized EUpdateDialog widget ###

This code will add an EUpdateDialog with custom successCallback, deletedCallback methods (write them as `"js:function(){ // your code }"`, don't use `;` at the end!). This will also preload a `wymeditor.js` file:

```php
<?php
$widgetUpdate = "js:function(){}";
$this->widget( 'ext.EUpdateDialog.EUpdateDialog', array(
  'options' => array(
    'successCallback' => $widgetUpdate,
    'deletedCallback' => $widgetUpdate,
  ),
  'preload' => array( Yii::app()->request->baseUrl . '/js/wymeditor.js' ),
));
?>
```

### GridView widget ###

To make gridview widget default buttons use EUpdateDialog change your `columns` in a widget to look something like this:

```php
<?php
$this->widget( 'zii.widgets.grid.CGridView', array(
  ...
  'columns' => array(
    ...
    array(
      'class' => 'CButtonColumn',
      'buttons' => array(
        // Delete button
        'delete' => array(
          'click' => 'updateDialogOpen',
          'url' => 'Yii::app()->createUrl(
            "/admin/mix/delete",
            array( "id" => $data->primaryKey ) )',
          'options' => array(
            'data-update-dialog-title' => Yii::t( 'app', 'Delete confirmation' ),
          ),
        ),
        // Update button
        'update' => array(
          'click' => 'updateDialogOpen',
          'options' => array(
            'data-update-dialog-title' => Yii::t( 'app', 'Update mix' ),
          ),
        ),
        // View button
        'view' => array(
          'click' => 'updateDialogOpen',
          'options' => array(
            'data-update-dialog-title' => Yii::t( 'app', 'Preview mix' ),
          ),
        ),
      ),
    ),
  ),
));
?>
```

### Add cancel link in your form ###

To add cancel link (returns to a model view or some other page normally, closes EUpdateDialog then using extension) in forms use this code:

```php
<?php
echo CHtml::link( 'Cancel', $cancelUrl, array(
  'class' => 'update-dialog-cancel-button' ) );
?>
```

### Advanced controller action configuration ###

This is the code for configured action. This will run `addFeedItem` method in controller after successfully saving the model. It will also disable the scripts in the form (guilty as charged, all extensions are made by me :D ) and replace `success` message:

```php
<?php
public function actions()
{
  return array(
    'create' => array(
      'class' => 'application.actions.CreateAction',
      'callback' => 'addFeedItem',
      'disableScripts' => array(
        'jquery.wymeditor.js',
        'jquery.wymeditor.fullscreen.js',
        'jquery-ui-timepicker-addon.js',
        'chosen.jquery.min.js',
      ),
      'messages' => array(
        'success' => 'Mix successfully created',
      ),
    ),
  );
}
?>
```

### Delete view ###

**This is very important**: you must have a delete view which has to containing a form with two submit buttons named `deleteConfirmed` (to confirm deletion) and `deleteCanceled` (to cancel deletion).

```php
<?php
echo CHtml::submitButton( 'Yes', array( 'name' => 'deleteConfirmed' ) );
echo CHtml::submitButton( 'No', array( 'name' => 'deleteCanceled' ) );
?>
```

Resources
---------

* [Wiki article](http://www.yiiframework.com/wiki/216/update-delete-model-with-cjuidialog-works-in-cgridview/ "Wiki article")
* [Forum topic - (questions, bugs, etc.)](http://www.yiiframework.com/forum/index.php?/topic/22421-extension-eupdatedialog/ "Forum topic")
* [GitHub repository](https://github.com/ifdattic/EUpdateDialog "GitHub repository")
* If you find this extension useful, please consider a [donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6VDDH9LN5TDNY "Donate")

License
-------

The extension is licensed under [MIT](http://ifdattic.com/MIT-license.txt "MIT") license.