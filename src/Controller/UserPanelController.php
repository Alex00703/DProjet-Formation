<?php
namespace App\Controller;

use App\Entity\Users;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPanelController extends AbstractController
{

    public function userPanel(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $doctrine): Response
    {
        
        if(!$this->getUser()){
            return $this->redirectToRoute('index');
        }

        $user = $this->getUsers();
        
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        $error = null;
        $success = null;

        $im = imagecreatefrompng( $this->getParameter('assets') . '/img/skin.png');
        $im2 = imagecrop($im, ['x' => 8, 'y' => 8, 'width' => 8, 'height' => 8]);
        imagepng($im2, $this->getParameter('assets') . '/img/avatars/default_avatar.png');
        $img =  $this->resize_imagepng($this->getParameter('assets') . '/img/avatars/default_avatar.png', 70, 70);
        imagepng($img, $this->getParameter('assets') . '/img/avatars/default_avatar.png');
        
        if($form->isSubmitted() && $form->isValid()){
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmNewPassword = $form->get('confirmNewPassword')->getData();
            $checkPass = $passwordEncoder->isPasswordValid($user, $oldPassword);
            if($checkPass == false){
                $error = "Mot de passe incorrect.";

            }
            if($newPassword !== $confirmNewPassword){
                $error = "Les mots de passe doivent être identiques.";
            }    
            if(!$error){
                $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));
                $doctrine->persist($user);
                $doctrine->flush();
                $success = "Votre mot de passe a été modifié avec succès !";
            }
        }
    
        return $this->render('panels/account.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'success' => $success,
        ]); 
    }

    function resize_imagepng($file, $w, $h) {
        list($width, $height) = getimagesize($file);
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
        return $dst;
     }

}