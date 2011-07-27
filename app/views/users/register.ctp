<h2><?php __( 'Register' ) ?></h2>

<?php echo $this->Form->create( array( 'action' => 'register' ) ) ?>
  <?php echo $this->Form->inputs(
    array(
      'legend' => false,
      'User.first_name',
      'User.last_name',
      'User.email',
      'User.password',
      'User.confirm_password' => array(
        'type' => 'password',
      ),
    )
  ) ?>
<?php echo $this->Form->end( 'Register Now' ) ?>
