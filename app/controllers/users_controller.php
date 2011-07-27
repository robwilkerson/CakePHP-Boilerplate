<?php

class UsersController extends AppController {
	public $name = 'Users';
	public $scaffold;

  /**
   * CALLBACKS
   */
  
  /**
   * CakePHP beforeFilter callback.
   *
   * @return	void
   * @access	public
   */
  public function beforeFilter() {
    parent::beforeFilter();
    
    $this->Auth->allow( 'register' ); # UsersController::login() is allowed by default
  }
  
  /**
   * PUBLIC METHODS
   */
  
  public function register() {
    if( !empty( $this->data ) ) {
      # The password value is hashed automagically. We need to hash the
      # confirmation value manually for validation.
      # @see AppModel::identical()
      $this->data[$this->Auth->getModel()->alias]['confirm_password'] = $this->Auth->password( $this->data[$this->Auth->getModel()->alias]['confirm_password'] );
      
      $this->User->create();
      
      if( $this->User->save( $this->data ) ) {
        $this->Session->setFlash( 'Welcome. Thanks for registering.', null, null, 'success' );
        $this->redirect( array( 'action' => 'login' ) );
      }
      else {
        # Ensure that these inputs aren't pre-populated when the form
        # is re-displayed.
        unset( $this->data[$this->Auth->getModel()->alias]['password'] );
        unset( $this->data[$this->Auth->getModel()->alias]['confirm_password'] );
        $this->Session->setFlash( 'Please correct the errors shown below.', null, null, 'validation' );
      }
    }
  }

  /**
   * Log a user into the system.
   *
   * @access	public
   */
  public function login() {
    # User was just authenticated
    if ( !empty( $this->data ) && $this->Auth->user() ) {
      $this->User->id = $this->Auth->user( 'id' );
			$this->User->saveField( 'last_login', date( 'Y-m-d H:i:s' ) );
      
      $this->redirect( $this->Auth->redirect(), null, true );
		}
  }
  
  /**
   * Logs a user out of the system
   *
   * @access	public
   */
  public function logout() {
    $this->redirect( $this->Auth->logout(), null, true );
  }
}
