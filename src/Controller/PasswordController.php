<?php
// Utilise le contrôleur pour changer le mot de passe de l'utilisateur

namespace App\Controller; // Espace de noms pour le contrôleur

use App\Form\ChangePasswordType; // Utilise le type de formulaire personnalisé pour le changement de mot de passe
use Doctrine\ORM\EntityManagerInterface; // Utilise l'interface du gestionnaire d'entités pour interagir avec la base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base pour tous les contrôleurs dans Symfony
use Symfony\Component\HttpFoundation\Request; // Représente une requête HTTP entrante
use Symfony\Component\HttpFoundation\Response; // Représente une réponse HTTP à renvoyer
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Service pour hacher les mots de passe
use Symfony\Component\Routing\Annotation\Route; // Annotation pour définir les routes

class PasswordController extends AbstractController // Déclaration du contrôleur
{
    /**
     * @Route("/compte/motdepasse", name="app_password") // Route pour la page de changement de mot de passe
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        dd(phpinfo());
        $notification = null; // Variable pour stocker les notifications à afficher à l'utilisateur
        $user = $this->getUser(); // Récupère l'utilisateur actuellement connecté
        $form = $this->createForm(ChangePasswordType::class, $user); // Crée le formulaire de changement de mot de passe

        $form->handleRequest($request); // Gère la requête entrante du formulaire

        if ($form->isSubmitted() && $form->isValid()) { // Vérifie si le formulaire a été soumis et est valide

            $old_pwd = $form->get('old_password')->getData(); // Récupère l'ancien mot de passe du formulaire

            if ($passwordHasher->isPasswordValid($user, $old_pwd)) { // Vérifie si l'ancien mot de passe est correct

                $new_pwd = $form->get('new_password')->getData(); // Récupère le nouveau mot de passe du formulaire
                $password = $passwordHasher->hashPassword($user, $new_pwd); // Hache le nouveau mot de passe
                $user->setPassword($password); // Met à jour le mot de passe de l'utilisateur
                $entityManager->flush(); // Enregistre les modifications dans la base de données
                $notification = "Votre mot de passe à bien été mis à jour."; // Message de succès
            } else {
                $notification = "Votre mot de passe actuel n'est pas le bon."; // Message d'erreur
            }
        }

        // Rendu de la page avec le formulaire et la notification
        return $this->render('compte/password.html.twig', [
            'form' => $form->createView(), 'notification' => $notification
        ]);
    }
}