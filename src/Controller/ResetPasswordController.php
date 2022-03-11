<?php

namespace App\Controller;

use DateTime;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */

    public function index(Request $request, UserRepository $repo, EntityManagerInterface $manager): Response
    {

        //   dump($request->getContent());
        //   dd($request->get('email'));

        if ($request->get('email')) {
            $user = $repo->findOneByEmail($request->get('email'));

            //    dd($user);

            if ($user) {
                $reset_password = new ResetPassword();

                $reset_password->setUser($user)
                    ->setToken(uniqid())
                    ->setCreatedAt(new DateTime());

                $manager->persist($reset_password);
                $manager->flush();

                //    dd($reset_password);

                $content_mail = 'Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant.
          <br>
          <a href="http://' . $_SERVER['HTTP_HOST'] . '/modifier-mot-de-passe/' . $reset_password->getToken() . '"> http://' . $_SERVER['HTTP_HOST'] . '/modifier-mot-de-passe/' . $reset_password->getToken() . '</a>';

                // $mail->sendMail($content_mail, $user->getEmail(), $user->getFullName(), 'Inscription sur le site MyBoutique');
                mail($user->getEmail(), 'Réinitialisation mot de passe', $content_mail);

                $this->addFlash(
                    'success',
                    'Un lien de réinitialisation vous a été envoyé par mail'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Le mail est inconnu'
                );
            }
        }

        return $this->render('reset_password/index.html.twig', []);
    }


    /**
     * @Route("/modifier-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request,  ResetPasswordRepository $repo, EntityManagerInterface $manager, $token, UserPasswordHasherInterface $passwordHasher)
    {

        $reset_password = $repo->findOneByToken($token);

        if (!$reset_password) {

            $this->addFlash(
                'danger',
                'Lien de réinitialisation incorrect'
            );

            return $this->redirectToRoute('app_login');
        }

        // récupération de la date de création du lien
        $date_create = $reset_password->getCreatedAt();

        $now = new DateTime();

        if ($now > $date_create->modify('+ 3 hour')) {

            $this->addFlash(
                'danger',
                'La demande de modification du mot de passe a expiré'
            );

            return $this->redirectToRoute('reset_password');
        }


        $user = $reset_password->getUser();
        $form = $this->createForm(ResetPasswordType::class, $user);



        $form->handleRequest($request); // On récupère la requete

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $user->getNewPassword()

            ));

            $manager->flush();

            $this->addFlash(
                'success',
                'Le mot de passe a bien été réinitialisé'
            );
        }

        return $this->render('reset_password/update.html.twig', [

            'form' => $form->createView()

        ]);
    }
}
