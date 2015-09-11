<h1>Bestätigung der E-Mail Adresse</h1>
<?PHP
	$success = false;
	if (!empty($_GET['key']))
	{
		$user = User::findFirstByVerificationKey($_GET['key']);
		if ($user != null)
		{
			$user->setVerified(true);
			success_msg("Deine E-Mailadresse wurde erfolgreich bestätigt!");
			$success = true;
		}
		else
		{
			error_msg("Der Verifikationscode ist ungültig!");
		}
	}
	else
	{
		error_msg("Kein Verifikationscode angegeben!");
	}
?>
<p class="center">
	<?php if ($success) : ?>
		<?=button("Zum Login", getLoginUrl())?>
	<?php else : ?>
		Wenn du Hilfe benötigst, kannst du <a href="?index=contact">hier</a> einen Game-Admin kontaktieren.
	<?php endif; ?>
</p>
