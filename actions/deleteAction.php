<?php
/**
 * DeleteAction class file.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 * @copyright Copyright &copy; 2011 Andrius Marcinkevicius
 * @license Licensed under MIT license. http://ifdattic.com/MIT-license.txt
 * @version 1.1
 */

/**
 * DeleteAction represents an action that deletes a model using normal
 * view or EUpdateDialog extension.
 * 
 * @author Andrius Marcinkevicius <andrew.web@ifdattic.com>
 */
class DeleteAction extends CAction
{
  /**
   * @var string message to display on success.
   */
  public $successMessage = 'Successfully deleted';
  
  /**
   * @var string message to display on cancelation.
   */
  public $cancelMessage = 'Deletion canceled';
  
  /**
   * Run the action.
   */
  public function run()
  {
    $controller = $this->getController();
    
    $model = $controller->loadModel();
    if( Yii::app()->request->isAjaxRequest )
    {
      // Stop jQuery from re-initialization
      Yii::app()->clientScript->scriptMap['jquery.js'] = false;
      
      if( isset( $_POST['action'] ) && $_POST['action'] == 'confirmDelete' )
      {
        $model->delete();
        echo CJSON::encode( array(
          'status' => 'success',
          'content' => $this->successMessage,
        ));
        exit;
      }
      else if( isset( $_POST['action'] ) )
      {
        echo CJSON::encode( array(
          'status' => 'canceled',
          'content' => $this->cancelMessage,
        ));
        exit;
      }
      else
      {
        echo CJSON::encode( array(
          'status' => 'failure',
          'content' => $controller->renderPartial( 'delete', array(
            'model' => $model ), true, true ),
        ));
        exit;
      }
    }
    else
    {
      if( isset( $_POST['confirmDelete'] ) )
      {
        $model->delete();
        $controller->redirect( array( 'admin' ) );
      }
      else if( isset( $_POST['denyDelete'] ) )
        $controller->redirect( array( 'view', 'id' => $model->id ) );
      else
        $controller->render( 'delete', array( 'model' => $model ) );
    }
  }
}
?>