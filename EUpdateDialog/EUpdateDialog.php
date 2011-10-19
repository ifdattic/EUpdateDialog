<?php
/**
 * EUpdateDialog class file.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 * @copyright Copyright &copy; 2011 Andrius Marcinkevicius
 * @license Licensed under MIT license. http://ifdattic.com/MIT-license.txt
 * @version 2.0
 */

/**
 * EUpdateDialog allows to make CRUD actions using JUI Dialog.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 */
class EUpdateDialog extends CWidget
{
  /**
   * @var array an array with options for JUI Dialog.
   */
  public $dialogOptions = array();
  
  /**
   * @var array an array with options for extension.
   */
  public $options = array();
  
  /**
   * @var array an array with scripts to preload for performance.
   */
  public $preload = array();
  
  /**
   * @var string open update dialog after clicking these elements.
   */
  public $target = '.update-dialog-open-link';
  
  /**
   * @var string message category used for Yii::t method.
   */
  public $tCategory = 'app';
  
  /**
   * Initializes the widget.
   */
  public function init()
  {
    // Create default options array
    $defaultOptions = array(
      'autoOpen' => false,
      'modal' => true,
    );
    
    // Merge with user set options if array is provided
    if( is_array( $this->dialogOptions ) )
    {
      $this->dialogOptions = CMap::mergeArray(
        $defaultOptions, $this->dialogOptions );
    }
    else
      throw new CException( Yii::t( $this->tCategory,
        'Widget options need to be an array' ) );
  }
  
  /**
   * Add the update dialog to current page.
   */
  public function run()
  {
    // Create jQuery UI dialog
    $this->beginWidget( 'zii.widgets.jui.CJuiDialog', array(
      'id' => 'update-dialog',
      'options' => $this->dialogOptions,
    )); ?>
    <div class="update-dialog-content"></div>
    <?php $this->endWidget();
    
    // Publish extension assets
    $assets = Yii::app()->getAssetManager()->publish( Yii::getPathOfAlias(
      'ext.EUpdateDialog' ) . '/assets' );
    
    // Register extension assets
    $cs = Yii::app()->getClientScript();
    $cs->registerScriptFile( $assets . '/EUpdateDialog.js',
      CClientScript::POS_END );
    
    // Add cookie script and CSRF cookie if using CSRF validation
    if( Yii::app()->request->enableCsrfValidation )
    {
      // Include jQuery cookie plugin
      $cs->registerCoreScript( 'cookie' );
      
      // Add CSRF cookie
      Yii::app()->request->getCsrfToken();
      
      // Set CSRF token name for extension
      $this->options['csrfTokenName'] = Yii::app()->request->csrfTokenName;
    }
    
    // Open update dialog the clicking target elements
    $cs->registerScript( 'eupdatedialog', "
      jQuery( '{$this->target}' ).live( 'click', updateDialogOpen );",
      CClientScript::POS_END );
    
    // Register additional options for an extension
    if( $this->options !== array() )
    {
      // Initialize script variable
      $js = '';
      
      // Go through all options
      foreach( $this->options as $option => $value )
      {
        // Encode option value
        $value = CJavaScript::encode( $value );
        
        // Add option to script variable
        $js .= "updateDialog.{$option} = {$value};";
      }
      
      // Register options for an extension
      $cs->registerScript( 'eupdatedialogoptions', $js, CClientScript::POS_END );
    }
    
    // Preload scripts for performance
    foreach( $this->preload as $script )
      $cs->registerScriptFile( $script, CClientScript::POS_END );
  }
}
?>