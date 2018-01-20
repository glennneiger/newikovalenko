<ul class="tr-users admins-list">
<?php
	$trusers = get_users('orderby=nicename&role=administrator');
	foreach ($trusers as $user) { ?>
<li>
<?php if ($user->first_name !== "") {
	echo "<a href='mailto:" . $user->user_email . "'>" . get_avatar( $user->ID , '100') . "<span class='admin-icon' title='" . __( "Administrator", "taskrocket" ) . "'></span><strong>" . $user->first_name . " " . $user->last_name . "</strong></a>";
} else {
	_e( "Nobody", "taskrocket" );
}
?></li>
<?php
	}
?>
</ul>