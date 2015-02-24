<?php

namespace Newscoop\ExternalLoginPluginBundle\Security\User;

use Newscoop\Entity\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class SimpleUserProvider implements UserProviderInterface
{
    protected $users;

    protected $em;

    protected $config;

    public function __construct($em, array $config)
    {
        $this->em = $em;
        $this->config = $config;
        if (file_exists($this->config['users_file'])) {
            $this->users = json_decode(file_get_contents($this->config['users_file']), true);
        } else {
            $this->users = array();
        }
    }

    public function loadUserByUsername($username)
    {
        error_log('FROM PLUGIN CHECK');

        // TODO: check if this is a request for external login controller
        $user = $this->em->getRepository('Newscoop\Entity\User')->findOneByUsername($username);

        if (!empty($user)) {
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return 'Newscoop\Entity\User' === $class;
    }
}
