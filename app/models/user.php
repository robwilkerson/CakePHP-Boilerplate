<?php

class User extends AppModel {
	public $name = 'User';
	public $displayField = 'first_name';
  
	public $validate = array(
		'first_name' => array(
			'notempty' => array(
				'rule'       => 'notEmpty',
				'message'    => 'A first name is required.',
				'allowEmpty' => false,
				'required'   => true,
			),
		),
		'last_name' => array(
			'notempty' => array(
				'rule'       => 'notEmpty',
				'message'    => 'A last name is required.',
				'allowEmpty' => false,
				'required'   => true,
			),
		),
		'email' => array(
			'notempty' => array(
				'rule'       => 'notEmpty',
				'message'    => 'An email address is required.',
				'allowEmpty' => false,
				'required'   => true,
        'last'       => true,
			),
			'email' => array(
				'rule'       => array( 'email' ),
				'message'    => 'This email address does not appear to be valid.',
				'allowEmpty' => false,
				'required'   => true,
        'last'       => true,
			),
			'unique' => array(
				'rule'       => 'isUnique',
				'message'    => 'This email address is already in use.',
				'allowEmpty' => false,
				'required'   => true,
        'last'       => true,
			),
		),
		'password' => array(
			'notempty' => array(
				'rule'       => 'notEmpty',
				'message'    => 'Password cannot be empty.',
				'allowEmpty' => false,
				'required'   => false,
        'last'       => true,
			),
      'identical' => array(
        'rule'    => array( 'identical', 'confirm_password' ), 
        'message' => 'Your password values don\'t match.' 
      ),
    ),
	);
  
  public $actsAs = array(
    'AuditLog.Auditable' => array(
      'ignore' => array( 'last_login' ),
    ),
  );
  
  /**
   * CALLBACKS
   */
  
  /**
   * CakePHP's beforeValidate callback.
   *
   * @return	boolean
   * @access	public
   */
  public function beforeValidate() {
    if( !empty( $this->data ) ) {
      /**
       * An empty password value is never empty. The Auth module hashes
       * the empty value which makes it non-empty and fools the notEmpty
       * validation rule. This is bad.
       *
       * We want to recognize an empty password when we see one and
       * throw it out, so we have to make that adjustment manually.
       */
      $empty_password = Security::hash( '', null, true );
      
      if( isset( $this->data[$this->alias]['password'] ) && $this->data[$this->alias]['password'] === $empty_password ) {
        if( !empty( $this->id ) ) {
          # When editing, just remove the data so no change is made.
          unset( $this->data[$this->alias]['password'] );
          unset( $this->data[$this->alias]['confirm_password'] );
        }
        else {
          # When creating, empty the value so it will be caught by validation.
          $this->data[$this->alias]['password'] = '';
          $this->data[$this->alias]['confirm_password'] = '';
        }
      }
    }
    
    return true;
  }
  
  /**
   * PUBLIC METHODS
   */
  
  /**
   * Retrieves the authenticated user data or, optionally, a specific
   * property of the user data.
   *
   * @param 	$property
   * @return	mixed
   * @access	public
   */
  static public function get( $property = null ) {
    $user = Configure::read( 'User' );
    if( empty( $user ) || ( !empty( $property ) && !array_key_exists( $property, $user ) ) ) {
      return false;
    }
    
    return empty( $property ) ? $user : $user[$property];
  }
}
