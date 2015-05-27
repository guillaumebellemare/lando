<?php

if($_SESSION['errors'])
{
	foreach($_SESSION['errors'] as $session_error)
	{
		$errors[] = $session_error;
	}
}

if($_SESSION['messages'])
{
	foreach($_SESSION['messages'] as $session_messages)
	{
		$messages[] = $session_messages;
	}
}


if($errors)
{
	echo '<div class="msg is-failure">';
		foreach ($errors as $error) {
			echo '<p>'.$error.'</p>';
		}
	echo '</div>';
}

if($messages)
{
	echo '<div class="msg is-success">';
		foreach ($messages as $message) {
			echo '<p>'.$message.'</p>';
		}
	echo '</div>';
}

if($_SESSION['errors']) { unset($_SESSION['errors']); }
if($_SESSION['messages']) { unset($_SESSION['messages']); }
