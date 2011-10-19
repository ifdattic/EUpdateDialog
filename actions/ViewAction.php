<?php
/**
 * ViewAction class file.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 * @copyright Copyright &copy; 2011 Andrius Marcinkevicius
 * @license Licensed under MIT license. http://ifdattic.com/MIT-license.txt
 * @version 2.0
 */

/**
 * ViewAction represents an action that displays a view of the model using
 * normal view or EUpdateDialog extension.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 */
class ViewAction extends CAction
{
  /**
   * @var string the name of the AJAX view.
   */
  public $ajaxView = '_view';
  
  /**
   * @var boolean is this an AJAX request.
   */
  protected $isAjaxRequest;
  
  /**
   * @var string scripts which should be disabled on AJAX call.
   */
  public $disableScripts = array();
  
  /**
   * @var array user set messages for the action.
   */
  public $messages = array();
  
  /**
   * @var string message category used for Yii::t method.
   */
  public $tCategory = 'app';
  
  /**
   * @var string the name of the view.
   */
  public $view = null;
  
  /**
   * Initialize the action.
   */
  protected function init()
  {
    // Create default messages array
    $defaultMessages = array(
      'postRequest' => Yii::t( $this->tCategory,
        'Only post requests are allowed' ),
    );
    
    // Merge with user set messages if array is provided
    if( is_array( $this->messages ) )
    {
      $this->messages = CMap::mergeArray(
        $defaultMessages, $this->messages );
    }
    else
      throw new CException( Yii::t( $this->tCategory,
        'Action messages need to be an array' ) );
    
    // If view is not set, use action id for view
    if( $this->view === null )
      $this->view = $this->id;
    
    // Check if this is an AJAX request
    if( $this->isAjaxRequest = Yii::app()->request->isAjaxRequest )
    {
      // Create default array for scripts which should be disabled
      $defaultDisableScripts = array(
        'jquery.js',
        'jquery.min.js',
        'jquery-ui.min.js'
      );
      
      // Merge with user set scripts which should be disabled
      if( is_array( $this->disableScripts ) )
      {
        $this->disableScripts = CMap::mergeArray(
          $defaultDisableScripts, $this->disableScripts );
      }
      else
        throw new CException( Yii::t( $this->tCategory,
          'Disable scripts need to be an array.' ) );
      
      // Disable scripts
      foreach( $this->disableScripts as $script )
        Yii::app()->clientScript->scriptMap[$script] = false;
      
      // Allow only post requests
      if( !Yii::app()->request->isPostRequest )
      {
        // Just render full contents for update
        if( isset( $_GET['ajax'] ) )
          $this->ajaxUpdate();
          
        // Output JSON encoded content
        echo CJSON::encode( array(
          'status' => 'failure',
          'content' => $this->messages['postRequest'],
        ));
        
        // Stop script execution
        Yii::app()->end();
      }
    }
  }
  
  /**
   * Run the action.
   */
  public function run()
  {
    // Initialize the action
    $this->init();
    
    // Get the controller
    $controller = $this->getController();
    
    // Get the model
    $model = $controller->loadModel();
    
    // Render view page using AJAX
    if( $this->isAjaxRequest )
    {      
      // Output JSON encoded content
      echo CJSON::encode( array(
        'status' => 'render',
        'content' => $controller->renderPartial( $this->ajaxView, array(
          'model' => $model ), true, true ),
      ));
      
      // Stop script execution
      Yii::app()->end();
    }
    // Render view page without using AJAX
    else
      $controller->render( $this->view, array(
        'model' => $model ) );
  }
  
  /**
   * Output full view contents for update.
   */
  protected function ajaxUpdate()
  {
    // Get the controller
    $controller = $this->getController();
    
    // Get the model
    $model = $controller->loadModel();
    
    // Render view page using AJAX
    echo CJSON::encode(
      $controller->renderPartial( $this->view, array(
        'model' => $model ), true, false )
    );
    
    // Stop script execution
    Yii::app()->end();
  }
}
?>