<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\ResetPasswordEvent;
use App\Form\ResetPasswordFormType;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/reset-password", name="app_reset_password")
 */
final class ResetPasswordController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("", name="")
     *
     * @return RedirectResponse|Response
     */
    public function reset(Request $request, TokenGenerator $tokenGenerator, EventDispatcherInterface $dispatcher)
    {
        if (!$email = $request->get('email')) {
            return $this->render('security/reset_password_request.html.twig');
        }

        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        if (null !== $user) {
            $user->setResetPasswordToken($tokenGenerator->generate());
            $this->manager->flush();

            $dispatcher->dispatch(new ResetPasswordEvent($user));

            $this->addFlash(
                'success',
                "Un email a été envoyé à l'adresse" . $user->getEmail()
            );
        } else {
            $this->addFlash(
                'danger',
                'Aucun compte n\'est lié à cet email'
            );

            return $this->redirectToRoute('app_reset_password');
        }

        return $this->render('security/reset_password_request.html.twig');
    }

    /**
     * @Route("/check/{id<\d+>}", name="_check")
     *
     * @return RedirectResponse|Response
     */
    public function check(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $token = $request->get('token');
        if (empty($token) || $token !== $user->getResetPasswordToken()) {
            $this->addFlash(
                'danger',
                'Votre token n\'est pas valide'
            );
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPasswordFormType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setResetPasswordToken(null);
            $this->manager->flush();

            $this->addFlash(
                'success',
                'Votre mot de passe à bien été réinitialisé'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}