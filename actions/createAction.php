<?php
/**
 * CreateAction class file.
 * 
 * @author Andrew M. <andrew.web@ifdattic.com>
 * @copyright Copyright &copy; 2011 Andrew M.
 * @license Licensed under MIT license. http://ifdattic.com/MIT-license.txt
 * @version 1.0
 */

/**
 * CreateAction represents an action that creates new model using normal
 * view or EUpdateDialog extension.
 * 
 * @author Andrew M. <andrew.web@ifdattic.com>
 */
class CreateAction extends CAction
{
  /**
   * @var string message to display on success.
   */
  public $successMessage = 'Successfully created';
  
  /**
   * Run the action.
   */
  public function run()
  {
    $controller = $this->getController();
    
    // Get the Model Name
    $modelClass = ucfirst( $controller->getId() );
    
    $model = new $modelClass();
    if( isset( $_POST[$modelClass] ) )
    {
      $model->attributes = $_POST[$modelClass];
      if( $model->save() )
      {
        if( Yii::app()->request->isAjaxRequest )
        {
          // Stop jQuery from re-initialization
          Yii::app()->clientScript->scriptMap['jquery.js'] = false;
          
          echo CJSON::encode( array(
            'status' => 'success',
            'content' => $this->successMessage,
          ));
          exit;
        }
        else
          $controller->redirect( array( 'view', 'id' => $model->id ) );
      }
    }
    
    if( Yii::app()->request->isAjaxRequest )
    {
      // Stop jQuery from re-initialization
      Yii::app()->clientScript->scriptMap['jquery.js'] = false;
      
      echo CJSON::encode( array(
        'status' => 'failure',
        'content' => $controller->renderPartial( '_form', array(
          'model' => $model ), true, true ),
      ));
      exit;
    }
    else
      $controller->render( 'create', array( 'model' => $model ) );
  }
}
?>