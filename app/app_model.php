<?php

class AppModel extends Model {
  public $actsAs = array( 'Nullable.Nullable', 'Containable' );

  /**
   * OVERRIDES
   */

  /**
   * Constructor.
   *
   * Handy way of creating a whitelist that will be reasonably suitable for most.
   */
  public function __construct( $id = false, $table = null, $ds = null ) {
    parent::__construct( $id, $table, $ds );

    # Generate a whitelist that doesn't require me to make an update every time
    # I add a property...unless I don't want that property to be batch updated.
    # Don't make any assumptions if the model has a non-standard primary key.
    # In that case, the key will have to be part of the save data.
    if( empty( $this->whitelist ) && $this->has_standard_primary_key() ) {
      $this->whitelist = array_diff( array_keys( $this->schema() ), array( 'id', 'created', 'modified' ) );
    }
  }

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
   * @param   string  $field  The name of the field to be deconstructed
   * @param   mixed   $data   An array or object to be deconstructed into a field
   * @return  mixed           The resulting data that should be assigned to a field
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
   * CALLBACKS
   */

  /**
   * CakePHP's afterFind callback.
   *
   * @param 	$results
   * @param   $primary
   * @return	mixed
   * @access	public
   */
  public function afterFind( $results, $primary = false ) {
    # Massage aggregated result values so they're less awkward
    if( !empty( $results ) ) {
      foreach( $results as $i => $result ) {
        # Sometimes a find call just returns a string which can be
        # accessed as an array. Ignore such results. In an array of
        # results, aggregated values will be stored in a "0" index.
        # This is what we want to extract and restore as a property
        # of the parent object.
        if( !is_string( $result ) && !empty( $result[0] ) ) {
          foreach( $result[0] as $field => $value ) { # aggregated field alias => aggregate value
            if( !empty( $result[$this->alias][$field] ) ) {
              $field = 'aggregated_' . $field;
            }

            $results[$i][$this->alias][$field] = $value;
          }

          unset( $results[$i][0] ); # Unset the awkward array element
        }
      }
    }

    return parent::afterFind( $results, $primary );
  }

  /**
   * VALIDATORS
   */

  /**
   * Validates a datetime value by acting as a decorator for native
   * Validation::date() and Validation::time() methods.
   *
   * @param   $check    array   field_name => value
   * @param   $options  array   Options for this rule
   * @return  boolean
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
   * Custom validation method to ensure that two field values are the
   * same before validating the model. Useful (and ubiquitous) for
   * authentication credentials.
   *
   * @param   $field
   * @param   $confirm_field
   * @access  public
   * @see     http://bakery.cakephp.org/articles/aranworld/2008/01/14/using-equalto-validation-to-compare-two-form-fields
   */
  public function identical( $check = array(), $confirm_field = null ) {
    $value   = array_shift( array_values( $check ) );
    $compare = $this->data[$this->alias][$confirm_field];

    return $value === $compare;
  }

  /**
   * PUBLIC METHODS
   */

  /**
   * Returns the current user object/array. Useful in the context of
   * the Auditable behavior.
   *
   * @return    mixed   null if empty,
   *                    an array if no property is specified,
   *                    a scalar if a property is specified
   * @access    public
   * @todo      Detect Auth user model automatically?
   */
  public function current_user( $property = null ) {
    return User::get( $property );
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

  /** 
   * PRIVATE METHODS
   */
  
  /**
   * Determines whether the model has a "conventional" primary key. Meaning
   * that the key is either an integer or a UUID.
   *
   * @return  boolean
   * @access  public
   */
  public function has_standard_primary_key() {
    $is_uuid = $this->_schema[$this->primaryKey]['length'] === 36 && (
      $this->_schema[$this->primaryKey]['type'] === 'string' || $this->_schema[$this->primaryKey]['type'] === 'binary'
    );

    return $this->_schema[$this->primaryKey]['type'] === 'integer' || $is_uuid;
  }
}
