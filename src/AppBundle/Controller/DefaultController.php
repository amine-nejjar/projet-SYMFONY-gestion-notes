<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function indexAction(Request $request){
        $session=$request->getSession();
        if($session->has('statut')){
            $statut=$session->get('statut');
            if($statut=="admin"){
                return $this->redirectToRoute('etudiants_list');
            }else{
                return $this->redirectToRoute('home_etudiant');
            }
        }else{
            $form = $this->createFormBuilder() 
            ->add('email',TextType::class, array('attr'=> array('class' => 'form-control','style' => 'margin-bottom:10px','placeholder'=>'Email')))
            ->add('password',TextType::class, array('attr'=> array('class' => 'form-control','style' => 'margin-bottom:30px','placeholder' => 'Password','type'=>'password')))
            ->add('save',SubmitType::class, array('label'=>'Submit', 'attr'=> array('class' => 'btn btn-primary','style' => 'margin-bottom:20px')))
            ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){

                $username=$form['email']->getData();
                $password=$form['password']->getData();
                if($username=="admin@gmail.com" && $password=="AdminNotes"){
                    $session->set('statut','admin');
                    return $this->redirectToRoute('etudiants_list');
                }else{
                    $etudiant = $this->getDoctrine()->getRepository('AppBundle:etudiants')->findOneByEmail($username);
                    if($etudiant==null){
                        $this->addFlash('errorLogin', 'Email/password incorrects');
                        return $this->render('notes/login.html.twig',array('form'=> $form->createView()));

                    }else if($etudiant->getPassword()==$password){
                        $session->set('statut','etudiant');
                        $session->set('idEtudiant',$etudiant->getId());
                        return $this->redirectToRoute('home_etudiant');
                    }else{
                        $this->addFlash('errorLogin', 'Email/password incorrects');
                        return $this->render('notes/login.html.twig',array('form'=> $form->createView()));
                    }
                }

            }
            return $this->render('notes/login.html.twig',array('form'=> $form->createView()));
        }
    }
}
