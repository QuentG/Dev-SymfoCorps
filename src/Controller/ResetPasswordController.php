<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reset-password", name="app_reset_password")
 */
class ResetPasswordController extends AbstractController
{
    public function reset()
    {
        // Pré-requis : nouveau champ dans la table User resetPasswordToken STRING OU NULL

        // Vérification de l'email envoyé
        // Si OK
            // Création d'un nouveau token, set dans la table User
            // FLUSH
            // On envoie un mail => Si possible dans un event + affichage d'un flash

        // Si NOK alors on redirige vers la même page + affichage d'un flash

        // Render le nouveau template de reset-password
    }

    public function check()
    {
        // Pré-requis : création d'un nouveau formulaire pour les deux champs password

        // Récupération du token + vérification userId => token
        // Vérification des deux nouveaux mots de passe
        // On hash et on insère dans la base
        // Message Flash + redirection
    }
}