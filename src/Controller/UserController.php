<?php

namespace App\Controller;

use App\Form\UserAccountFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account", name="account_")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/edit", name="edit")
     *
     * @return RedirectResponse|Response
     */
    public function edit(Request $request)
    {
        $form = $this->createForm(UserAccountFormType::class, $this->getUser())->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Vos informations ont bien été mise à jour");

            return $this->redirectToRoute('account_index');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

}