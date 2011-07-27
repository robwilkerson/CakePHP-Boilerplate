<h2><?php __( 'Login' ) ?></h2>

<?php echo $this->Form->create( array( 'action' => 'login' ) ) ?>
  <?php echo $this->Form->inputs(
    array(
      'legend' => false,
      'User.email',
      'User.password',
    )
  ) ?>
<?php echo $this->Form->end( 'Login' ) ?>
