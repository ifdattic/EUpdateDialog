<?php
/**
 * EUpdateDialog class file.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 * @copyright Copyright &copy; 2011 Andrius Marcinkevicius
 * @license Licensed under MIT license. http://ifdattic.com/MIT-license.txt
 * @version 1.2
 */

/**
 * EUpdateDialog allows to create/update/delete model entry from JUI Dialog.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 */
class EUpdateDialog extends CWidget
{
  /**
   * @var int the height of the dialog.
   */
  public $height = 'auto';
  
  /**
   * @var bool if set to true, the dialog will be resizable.
   */
  public $resizable = false;
  
  /**
   * @var string the title of the dialog.
   */
  public $title = 'Dialog';
  
  /**
   * @var int the width of the dialog.
   */
  public $width = 500;
  
  /**
   * Add the update dialog to current page.
   */
  public function run()
  {
    $this->beginWidget( 'zii.widgets.jui.CJuiDialog', array(
      'id' => 'update-dialog',
      'options' => array(
        'autoOpen' => false,
        'height' => $this->height,
        'modal' => true,
        'resizable' => $this->resizable,
        'title' => $this->title,
        'width' => $this->width,
        
      ),
    )); ?>
    <div class="update-dialog-content"></div>
    <?php $this->endWidget();
    
    // Publish extension assets
    $assets = Yii::app()->getAssetManager()->publish( Yii::getPathOfAlias(
      'ext.EUpdateDialog' ) . '/assets' );
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile( $assets . '/EUpdateDialog.js' );
    
    // Add cookie script and csrf cookie if using CSRF validation.
    if( Yii::app()->request->enableCsrfValidation )
    {
      $cs->registerScriptFile( $assets . '/jquery.cookie.js' );
      Yii::app()->request->getCsrfToken();
    }
  }
}
?>