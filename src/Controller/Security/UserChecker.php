<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Exception\AccountDeletedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;

class UserChecker implements UserCheckerInterface
{
	public function checkPreAuth(UserInterface $user)
	{
		if (!$user->getActif()) {
			throw new DisabledException('...');
		}
	}

	public function checkPostAuth(UserInterface $user)
	{
	}
}