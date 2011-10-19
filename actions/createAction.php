<?php
/**
 * CreateAction class file.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 * @copyright Copyright &copy; 2011 Andrius Marcinkevicius
 * @license Licensed under MIT license. http://ifdattic.com/MIT-license.txt
 * @version 2.0
 */

/**
 * CreateAction represents an action that creates a new model using
 * normal view or EUpdateDialog extension.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 */
class CreateAction extends CAction
{
  /**
   * @var mixed the redirect URL processed by the class method.
   */
  private $_redirectTo = null;
  
  /**
   * @var string the name of the AJAX view.
   */
  public $ajaxView = '_form';
  
  /**
   * @var string a callback method in controller for additional processing.
   */
  public $callback = null;
  
  /**
   * @var string scripts which should be disabled on AJAX call.
   */
  public $disableScripts = array();
  
  /**
   * @var string flash messages prefix.
   */
  public $flashTypePrefix = '';
  
  /**
   * @var boolean is this an AJAX request.
   */
  protected $isAjaxRequest;
  
  /**
   * @var array user set messages for the action.
   */
  public $messages = array();
  
  /**
   * @var mixed the redirect URL set by the user.
   */
  public $redirectTo = null;
  
  /**
   * @var mixed the redirect URL used for cancel button.
   */
  public $redirectToOnCancel = array( 'admin' );
  
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
      'error' => Yii::t( $this->tCategory, 
        'There was an error while saving. Please try again.' ),
      'postRequest' => Yii::t( $this->tCategory,
        'Only post requests are allowed' ),
      'success' => Yii::t( $this->tCategory, 'Successfully created' ),
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
    
    // Get the Model class
    $modelClass = ucfirst( $controller->getId() );
    
    // Create the model
    $model = new $modelClass();
    
    // Process submitted form
    if( isset( $_POST[$modelClass] ) )
    {
      // Get input data
      $model->attributes = $_POST[$modelClass];
      
      // Save only if model validates
      if( $model->validate() )
      {
        // Save the model
        if( $model->save() )
        {
          // If callback is set run additional processing
          if( $this->callback !== null &&
              method_exists( $controller, $this->callback ) )
            $controller->{$this->callback}( $model );
          
          // Accessing through AJAX, return success content
          if( $this->isAjaxRequest )
          {
            // Output JSON encoded content
            echo CJSON::encode( array(
              'status' => 'success',
              'content' => $this->messages['success'],
            ));
            
            // Stop script execution
            Yii::app()->end();
          }
          // Accessing without AJAX, redirect
          else
          {
            $controller->setFlash( 'flashMessage', array(
              'type' => $this->flashTypePrefix . 'success',
              'content' => $this->messages['success'] ) );
            $controller->redirect( $this->getRedirectUrl( $model->id ) );
          }
        }
        // Save was unsuccessful, set flash message
        else
          $controller->setFlash( 'flashMessage', array(
            'type' => $this->flashTypePrefix . 'error',
            'content' => $this->messages['error'] ) );
      }
    }
    
    // Render create page using AJAX
    if( $this->isAjaxRequest )
    {
      // Output JSON encoded content
      echo CJSON::encode( array(
        'status' => 'render',
        'content' => $controller->renderPartial( $this->ajaxView, array(
          'model' => $model,
          'cancelUrl' => $this->redirectToOnCancel ), true, true ),
      ));
      
      // Stop script execution
      Yii::app()->end();
    }
    // Render create page without using AJAX
    else
      $controller->render( $this->view, array(
        'model' => $model,
        'cancelUrl' => $this->redirectToOnCancel ) );
  }
  
  /**
   * Returns whether this is an AJAX request.
   * @return boolean true if this is an AJAX request.
   */
  public function getIsAjaxRequest()
  {
    return $this->isAjaxRequest;
  }
  
  /**
   * Returns an URL for redirect.
   * @param int $id the id of the model to redirect to.
   * @return mixed processed redirect URL.
   */
  protected function getRedirectUrl( $id )
  {
    // Process redirect URL
    if( $this->_redirectTo === null )
    {
      // Use default redirect URL
      if( $this->redirectTo === null )
        $this->_redirectTo = array( 'view', 'id' => $id );
      // User set redirect URL is an array, check if id is needed
      else if( is_array( $this->redirectTo ) )
      {
        // ID is set
        if( isset( $this->redirectTo['id'] ) )
          // ID needed, set it to the model id
          if( $this->redirectTo['id'] )
            $this->redirectTo['id'] = $id;
          // ID is not needed, remove it from redirect URL
          else
            unset( $this->redirectTo['id'] );
        
        // Set processed redirect URL
        $this->_redirectTo = $this->redirectTo;
      }
      // User set redirect URL is a string
      else
        $this->_redirectTo = $this->redirectTo;
    }
    
    // Return processed redirect URL
    return $this->_redirectTo;
  }
}
?>