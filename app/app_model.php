<?php

class AppModel extends Model {
  public $actsAs = array( 'Nullable', 'Containable' );
  
  /**
   * OVERRIDES
   */
  
	/**
	 * Override Model::deconstruct() in order to use an integrated date
	 * value, but multipart time value. The parent method expects both
	 * date and time to be segmented, but, if a date picker is used to
	 * select the date, then that component is unified.
	 *
	 * In order to use what's already in place, we'll deconstruct the date
	 * portion here and then pass everything to the parent method for
	 * reassimilation.
	 *
	 * @param		string	$field 	The name of the field to be deconstructed
	 * @param		mixed 	$data 	An array or object to be deconstructed into a field
	 * @return	mixed						The resulting data that should be assigned to a field
	 * @access  protected
	 */
	public function deconstruct( $field, $data ) {
		$type = $this->getColumnType( $field );
		
		if( in_array( $type, array( 'datetime', 'timestamp' ) ) ) {
			if( isset( $data['date'] ) && !empty( $data['date'] ) ) {
				$date = date( 'U', strtotime( $data['date'] ) );
				
				if( $date ) {
					$data['month'] = date( 'm', $date );
					$data['day']   = date( 'd', $date );
					$data['year']  = date( 'Y', $date );
				}
			}
		}
		
		return parent::deconstruct( $field, $data );
	}
  
  /**
   * VALIDATORS
   */
  
	/**
	 * Validates a datetime value by acting as a decorator for native
	 * Validation::date() and Validation::time() methods.
	 *
	 * @param		$check		array		field_name => value
	 * @param		$options	array		Options for this rule
	 * @return	boolean
	 * @access  public
	 */
	public function datetime( $check, $options ) {
		$check    = array_shift( array_values( $check ) );
		$datetime = strtotime( $check );
		
		if( $datetime !== false ) {
			return Validation::date( date( 'Y-m-d', $datetime ), 'ymd' ) && Validation::time( date( 'H:i', $datetime ) );
		}
		
		return false;
	}
  
  /**
   * Custom validation method specific to integers.
   *
   * @param   $check
   * @access  public
   */
  public function integer( $check = array() ) {
    $value = array_shift( array_values( $check ) );
    
    return preg_match( '/^\d+$/', $value );
  }
  
  /**
   * PUBLIC METHODS
   */
  
	/**
	 * Returns the current user object/array. Useful in the context of
	 * the Auditable behavior.
	 *
	 * @return		mixed   null if empty,
	 *                    an array if no property is specified,
	 *                    a scalar if a property is specified
	 * @access    public
	 * @todo      Detect Auth user model automatically?
	 */
	public function current_user( $property = null ) {
		$user = isset( $this->data['User'] )
      ? $this->data['User']
      : null;
    
    # Pull just the property, if specified
    if( !empty( $property ) ) {
      $user = $user[$property];
    }
    
    return $user;
	}
  
  /**
   * Returns the generated SQL executed during the request.
   * Useful when debugging.
   *
   * @return array
   * @access public
   */
  public function sql() {
    return $this->getDataSource()->getLog( false, false );
  }
}
