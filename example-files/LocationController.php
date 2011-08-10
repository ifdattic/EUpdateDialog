<?php
class LocationController extends MyController
{
	/**
	 * @var string the default layout for the views. Defaults to 
	 * '//layouts/column2', meaning using two-column layout. 
	 * See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '/layouts/column2';
	
	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;
  
  /**
   * @var string the name of the default action. Defaults to 'index'.
   */
  public $defaultAction = 'admin';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
      array( 'allow',
        'actions' => array( 'admin', 'create', 'view', 'update', 'delete' ),
        'users' => array( '@' ),
      ),
      array( 'deny',
        'users' => array( '*' ),
      ),
    );
	}

  /**
   * Returns a list of external action classes.
   * @return array list of external action classes
   */
  public function actions()
  {
    return array(
      'admin' => 'application.actions.adminAction',
      'create' => array(
        'class' => 'application.actions.createAction',
        'successMessage' => 'Location successfully created',
      ),
      'delete' => 'application.actions.deleteAction',
      'update' => array(
        'class' => 'application.actions.updateAction',
        'successMessage' => 'Location successfully updated',
      ),
      'view' => 'application.actions.viewAction',
    );
  }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if( $this->_model === null )
		{
		  if( isset( $_GET['id'] ) )
		    $this->_model = Location::model()->findByPk( (int)$_GET['id'] );
		  if( $this->_model === null )
		    throw new CHttpException( 404, 'The requested page does not exist.' );
		}
		return $this->_model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation( $model )
	{
		if( isset( $_POST['ajax'] ) && $_POST['ajax'] === 'location-form' )
		{
			echo CActiveForm::validate( $model );
			Yii::app()->end();
		}
	}
}
