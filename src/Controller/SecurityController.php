<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {

    /**
     * @Route(
     *     path="/",
     *     methods={"GET", "POST"},
     *     name="login"
     * )
     */
    public function login(AuthenticationUtils $authenticationUtils): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('security/login.html.twig');
    }

    /**
     * @Route(
     *     path="/ajax/login",
     *     methods={"GET", "POST"},
     *     name="ajax.login",
     * )
     */
    public function loginJson(AuthenticationUtils $authenticationUtils): Response {
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            return $this->json([
                'error' => $error->getMessageKey()
            ]);
//        } else if (!$this->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
//            return $this->json([
//                'error' => 'Invalid request type'
//            ]);
        }

        $user = $this->getUser();

        return $this->json([
            'roles' => $user->getRoles(),
            'redirect' => $this->generateUrl('dashboard')
        ]);
    }

    /**
     * @Route(
     *     path="/logout",
     *     methods={"GET"},
     *     name="logout",
     * )
     */
    public function logout(): void {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
