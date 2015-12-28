<?php
namespace AppBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        $previousUser = $this->userManager->findUserBy(array($property => $username));
        var_dump(
            $property,
            $response->getProfilePicture(),
            $response->getRealName(),
            $response->getNickname(),
            $response->getUsername(),
            $previousUser
        );

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username,'auth_type'=>$service))) {
            $previousUser->setAccessToken($response->getAccessToken());
            $this->userManager->updateUser($previousUser);
        }


    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $service = $response->getResourceOwner()->getName();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username,'auth_type'=>$service));
        //when the user is registrating
        if (null === $user) {

            $user = $this->userManager->createUser();
            $user->setUid($username);
            $user->setAccessToken($response->getAccessToken());
            $user->setAuthType($service);
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($response->getRealName());
            $user->setEmail($response->getEmail());
            if($service=="facebook"){
                $user->setPicture("https://graph.facebook.com/$username/picture");
            }else{
                $user->setPicture($response->getProfilePicture());
            }

            $user->setPassword($username);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
            return $user;
        }

        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        //update access token
        $user->setAccessToken($response->getAccessToken());

        return $user;
    }

}