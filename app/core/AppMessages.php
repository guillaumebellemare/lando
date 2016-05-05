<?php

if($app_errors)
{
	echo '<div class="app-msg app-is-failure">';
		foreach ($app_errors as $app_error) {
			echo '<p><span class="msg-icon"></span>'.$app_error.'</p>';
		}
	echo '</div>';
}

if($app_messages)
{
	echo '<div class="app-msg app-is-success">';
		foreach ($app_messages as $app_message) {
			echo '<span class="msg-icon"></span>'.$app_message;
		}
	echo '</div>';
}

if($app_floating_messages)
{
	echo '<div class="app-floating-msg">';
		foreach ($app_floating_messages as $app_floating_message) {
			echo $app_floating_message;
		}
	echo '</div>';
}