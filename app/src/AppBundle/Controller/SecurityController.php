<?php

namespace AppBundle\Controller;

use AppBundle\Model\ApiData;
use AppBundle\Model\UserToken;
use AppBUndle\Service\GoogleClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SecurityController extends BaseController
{
    /**
     * @param Request $request
     * @param UserToken $userToken
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, UserToken $userToken): JsonResponse
    {
        $user = $userToken->getUser();
        $manager = $this->get('fos_user.user_manager');

        if ($user === null) {
            $token = $userToken->getToken();
            // Verify oauth with Google to prevent usurpation of token
            $payload = $this->get(GoogleClient::class)->getClient()->verifyIdToken($token);

            if ($payload === false) {
                throw new \Exception('The verification of token has been failed');
            }

            // Retrieve user by email
            $user = $manager->findUserByEmail($payload['email']);

            // If any user exists we create them
            if ($user === null) {
                $data = $request->request->all();
                $user = ($manager->createUser())
                    ->setUsername($data['email'])
                    ->setEmail($data['email'])
                    ->setGoogleId($payload['sub'])
                    ->setPlainPassword(md5(uniqid())) // Generate random password
                ;
            }
        }

        // Re-generate apiKey on each login (more secure)
        $user->setApiKey(md5(uniqid()));
        $manager->updateUser($user);

        $data = (new ApiData())->setData(['token' => $user->getApiKey()]);

        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param UserToken $userToken
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request, UserToken $userToken): JsonResponse
    {
        $data = new ApiData();
        $user = $userToken->getUser();

        if ($user === null) {
            $data->addError('Any user with the apiKey "'.$userToken->getToken().'" has been found');
        } else {
            $user->clearApiKey();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
        }

        return $this->response($data);
    }
}