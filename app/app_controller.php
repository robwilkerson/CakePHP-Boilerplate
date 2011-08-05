<?php

class AppController extends Controller {
  public $helpers    = array( 'Html', 'Number', 'Session', 'Text', 'Time' );
  public $components = array(
    'Auth' => array(
      'authorize'    => 'controller',
      'fields'       => array( 'username' => 'email', 'password' => 'password' ), 
      'userScope'    => array( 'User.active' => 1 ),
      # 'loginRedirect' => array(), # Sends to the homepage (/) by default
      'autoRedirect' => false,
      # 'logoutRedirect' => array(), # Redirects to the login page by default
    ),
    'RequestHandler',
    'Session'
  );
  
  /**
   * OVERRIDES
   */
  
  /**
   * Override this method to ensure that some components get loaded
   * conditionally.
   *
   * @access	public
   */
  public function constructClasses() {
    if( Configure::read( 'debug' ) > 0 ) {
      $this->components[] = 'DebugKit.Toolbar';
    }
    
    parent::constructClasses();
  }
  
  /**
   * CALLBACKS
   */
  
  /**
   * CakePHP's beforeFilter callback.
   *
   * @return	void
   * @access	public
   */
  public function beforeFilter() {
    $this->Auth->loginError = __( 'Invalid authentication credentials. Please try again.', true );
    $this->Auth->authError  = __( 'Authentication required. Please login.', true );
    
    $user = $this->Auth->user();
    if( !empty( $user ) ) {
      Configure::write( 'User', $user[$this->Auth->getModel()->alias] );
    }
  }
  
  /**
   * PUBLIC METHODS
   */
  
  /**
   * Has the final call over whether a user gets authenticated. Called
   * by the Auth component.
   *
   * @return	boolean
   * @access	public
   */
  public function isAuthorized() {
    return true;
  }
  
  /**
   * PROTECTED METHODS
   */
  
  /**
   * Returns the current user object. Used to support the Auditable
   * behavior for delete actions which send no data, but perform a
   * soft delete by updating the active value.
   *
   * @return  array
   * @access  protected
   */
  protected function current_user( $property = null ) {
    $user = $this->Auth->user();
    
    if( !empty( $user ) ) {
      if( empty( $property ) ) {
        $user = $user[$this->Auth->userModel]; # Return the complete user array
      }
      else {
        $user = $this->Auth->user( $property ); # Return a specific property
      }
    }
    
    return $user;
  }
  
  /**
   * Refreshes the authenticated user session partially or en masse.
   *
   * @param   $field
   * @param   $value
   * @return  boolean
   * @see     http://milesj.me/blog/read/31/Refreshing-The-Auths-Session
   */
  protected function refresh_auth( $field = null, $value = null ) {
    if( $this->Auth->user() ) {
      if( !empty( $field ) && !empty( $value ) ) { # Refresh a single key
        $this->Session->write( $this->Auth->sessionKey . '.' . $field, $value );
      }
      else { # Refresh the entire session
        $user = ClassRegistry::init( $this->Auth->userModel )->find(
          'first',
          array(
            'contain'    => false,
            'conditions' => array( 'User.id' => $this->Auth->User( 'id' ) ),
          )
        );
        
        $this->Auth->login( $user );
      }
    }
  }
  
  /**
   * PRIVATE METHODS
   */
  
  /**
   * Force traffic to a given action through SSL.
   */
  private function forceSSL() {
    if( !$this->RequestHandler->isSSL() ) {
      $this->redirect( 'https://' . $_SERVER['HTTP_HOST'] . $this->here, null, true );
    }
  }
  
  /**
   * Force traffic to a given action away from SSL.
   */
  private function unforceSSL() {
    if( $this->RequestHandler->isSSL() ) {
      $this->redirect( 'http://' . $_SERVER['HTTP_HOST'] . $this->here, null, true );
    }
  }
}
