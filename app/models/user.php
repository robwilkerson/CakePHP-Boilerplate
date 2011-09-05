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
    'NamedScope.NamedScope' => array(
      'active' => array(
        'conditions' => array( 'User.active' => 1 ),
      ),
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
    parent::beforeValidate();
    
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
          # When editing, just remove the data so no change is attempted.
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
   * CakePHP's beforeFind callback.
   *
   * @param   $query
   * @return  array
   * @access  public
   */
  public function beforeFind( $query ) {
    # Don't return the password field unless it's explicitly specified.
    $query['fields'] = empty( $query['fields'] )
      ? array_diff( array_keys( $this->schema() ), array( 'password' ) )
      : $query['fields'];
  
    return $query;
  }
  
  /**
   * PUBLIC METHODS
   */
   
  /**
   * Constructor.
   */
  public function __construct( $id = false, $table = null, $ds = null ) {
    parent::__construct( $id, $table, $ds );
    
    # Define virtual fields here so that aliases are respected.
    # @see http://book.cakephp.org/view/1632/Virtual-fields-and-model-aliases
    $this->virtualFields['full_name'] = sprintf( 'CONCAT(%s.first_name, " ", %s.last_name)', $this->alias, $this->alias );
    
    # The listed fields cannot be modified in a batch update.
    $this->whitelist = array_diff( array_keys( $this->schema() ), array( 'id', 'last_login', 'active', 'created', 'modified' ) );
  }
  
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
  
  /**
   * Retrieves a list of users who can be impersonated by the current
   * user. By default, a list of all active users, but this is the
   * place to include any filter logic.
   *
   * @return	array
   * @access	public
   */
  public function impersonatable( $user_id = null ) {
    $user_id = empty( $user_id ) ? User::get( 'id' ) : $user_id;
    
    return $this->active(
      'list',
      array(
        'contain'    => false,
        'conditions' => array(
          'User.id <> ' => $user_id,
        ),
        'order'      => array(
          'User.last_name',
          'User.first_name',
        )
      )
    ); # see NamedScope config for active()
  }
  
  /**
   * Returns the user who is impersonating another, if any.
   *
   * @return	array
   * @access	public
   */
  public function impersonating() {
    $user = User::get( 'id' );
    $impersonator = Configure::read( 'Impersonator.id' );
    
    return !empty( $user ) && !empty( $impersonator ) && $user !== $impersonator
      ? $impersonator
      : false;
  }
}
