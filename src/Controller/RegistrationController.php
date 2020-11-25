<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Particular;
use App\Entity\User;
use App\Event\UserCreatedEvent;
use App\Form\RegistrationFormType;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/register", name="app_register")
 */
final class RegistrationController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("", name="_choices", methods={"GET"})
     */
    public function choices(): Response
    {
        return $this->render('registration/choices.html.twig');
    }

    /**
     * @Route("/{role}", name="")
     *
     * @return Response|RedirectResponse
     */
    public function register
    (
        string $role,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator,
        EventDispatcherInterface $dispatcher
    )
    {
        if (!in_array($role, User::$roles, true)) {
            $this->addFlash('danger', "Le role sélectionné n'éxiste pas");
            return $this->redirectToRoute('app_register_choices');
        }

        $user = Company::ROLE === $role ? new Company() : new Particular();

        $form = $this->createForm(RegistrationFormType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setConfirmationToken($tokenGenerator->generate());

            $this->manager->persist($user);
            $this->manager->flush();

            $dispatcher->dispatch(new UserCreatedEvent($user));

            $this->addFlash(
                'success',
                'Un message avec un lien de confirmation vous a été envoyé par mail. Veuillez suivre ce lien pour activer votre compte.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/check/{id<\d+>}", name="_check")
     */
    public function check(User $user, Request $request): RedirectResponse
    {
        $token = $request->get('token');
        if (empty($token) || $token !== $user->getConfirmationToken()) {
            $this->addFlash(
                'danger',
                'Votre token n\'est pas valide'
            );
            return $this->redirectToRoute('app_login');
        }

        if ($user->getCreatedAt() < new \DateTime(User::TOKEN_VALIDITY)) {
            $this->addFlash(
                'danger',
                'Votre token est expiré'
            );
            return $this->redirectToRoute('app_login');
        }

        $user->setConfirmationToken(null)
            ->setIsVerified(true);

        $this->manager->flush();

        $this->addFlash(
            'success',
            'Votre compte a été validé'
        );

        return $this->redirectToRoute('app_login');
    }
}
