<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\etudiants;
use AppBundle\Entity\notes;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;



class notesController extends Controller
{
    /**
     * @Route("/", name="etudiants_list")
     */
    public function EtudiantsAction(Request $request)
    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "admin"){
                $etudiants = $this->getDoctrine()->getRepository('AppBundle:etudiants')->findAll();        
                // replace this example code with whatever you need
                return $this->render('notes/index.html.twig',array('etudiants'=>$etudiants));
            }}
        return $this->redirectToRoute("login");    
    }

    /**
     * @Route("/ajouter", name="etudiants_add")
     */
    public function AddEtudiantAction(Request $request)
    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "admin"){
                $etudiant = new etudiants ;
                $form = $this->createFormBuilder($etudiant)
                ->add('nom',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                ->add('prenom',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                ->add('email',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                ->add('annee',ChoiceType::class,array('choices'=>array('1ere annee'=>'1ere annee','2eme annee'=>'2eme annee','3eme annee'=>'3eme annee'),'attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                ->add('addresse',TextareaType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                ->add('ajouter',SubmitType::class,array('attr'=>array('class'=>'btn btn-success')))
                ->getForm();
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid()){
                    $nom=$form["nom"]->getData();
                    $prenom=$form["prenom"]->getData();
                    $email=$form["email"]->getData();
                    $addresse=$form["addresse"]->getData();
                    $annee=$form["annee"]->getData();

                    $etudiant->setNom($nom);
                    $etudiant->setPrenom($prenom);
                    $etudiant->setEmail($email);
                    $etudiant->setAddresse($addresse);
                    $etudiant->setAnnee($annee);
                    $etudiant->setPassword($email);

                    try {
                        $em = $this->getDoctrine()->getManager();
                        $em -> persist($etudiant);
                        $em -> flush();
                        $this->addFlash('successAddEtud', 'Etudiant ajouté');
                    } catch ( \Exception $e ) {
                        $this->addFlash('errorAddEtud', 'un probleme est survenu');
                    }


                    //return $this->redirectToRoute('etudiants_list');

                    return $this->render('notes/addEtudiant.html.twig',array('form'=>$form->createView()));


                }
                // replace this example code with whatever you need
                return $this->render('notes/addEtudiant.html.twig',array('form'=>$form->createView()));
            }}
            return $this->redirectToRoute("login");
    }


    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function DetailEtudiantAction(Request $request,$id)

    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "admin"){
                // replace this example code with whatever you need
                $etudiant = $this->getDoctrine()->getRepository('AppBundle:etudiants')->findOneById($id); 
                return $this->render('notes/etudiants.html.twig',array('etudiant'=>$etudiant));
            }}
            return $this->redirectToRoute("login");
    }


    /**
     * @Route("/delete/{id}", name="etudiants_delete")
     */
    public function DeleteEtudiantAction(Request $request,$id)
    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "admin"){
                // replace this example code with whatever you need
                $etudiant = $this->getDoctrine()->getRepository('AppBundle:etudiants')->findOneById($id); 
                try{
                    $em = $this->getDoctrine()->getManager();
                    $notes= $this->getDoctrine()->getRepository('AppBundle:notes')->findByEtudiant($id);
                    foreach($notes as $note){
                        $em->remove($note);
                    }    
                    $em -> remove($etudiant);
                    $em -> flush();
                    $this->addFlash('successDelete', 'Etudiant retiré');
                } catch ( \Exception $e ) {
                    $this->addFlash('errorDelete', 'Un probleme est servenu');
                }   
                return $this->redirectToRoute('etudiants_list');
            }}
            return $this->redirectToRoute("login");
    }

    /**
     * @Route("/deleteNote/{id}", name="delete_note")
     */
    public function DeleteNoteAction(Request $request,$id)
    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "admin"){
                // replace this example code with whatever you need
                $note = $this->getDoctrine()->getRepository('AppBundle:notes')->findOneById($id); 
                $em = $this->getDoctrine()->getManager();
                $em->remove($note);
                $em -> flush();
                $Etud=$note->getEtudiant();
                $idd=$Etud->getId();
                return $this->redirectToRoute("notes_list",array("id"=> $idd ));
            }}
            return $this->redirectToRoute("login");
    }

    /**
     * @Route("/ajouterNote/{id}", name="note_add")
     */
    public function AddNoteAction(Request $request, $id)
    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "admin"){
                $note = new notes ;
                $form = $this->createFormBuilder($note)
                ->add('valeur',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                ->add('matiere',ChoiceType::class,array('choices'=>array('XML'=>'XML','Developpement web'=>'Developpement web','Data Mining'=>'Data Mining','Gestion de projets'=>'Gestion de projets','Réseau WAN'=>'Réseau WAN'),'attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                ->add('ajouter la note',SubmitType::class,array('attr'=>array('class'=>'btn btn-success')))
                ->getForm();
                $form->handleRequest($request);

                $em = $this->getDoctrine()->getManager();
                $etudiant = $em->getRepository('AppBundle:etudiants')->findOneById($id);

                if($form->isSubmitted() && $form->isValid()){
                    $valeur=$form["valeur"]->getData();
                    $matiere=$form["matiere"]->getData();


                    $note->setValeur($valeur);
                    $note->setMatiere($matiere);
                    $note->setEtudiant($etudiant);

                    try {
                        $em = $this->getDoctrine()->getManager();
                        $em -> persist($note);
                        $em -> flush();
                        $this->addFlash('successNote', 'note attribuée');
                    } catch ( \Exception $e ) {
                        $this->addFlash('errorNote', 'un probleme est servenu');
                    }
                    return $this->render('notes/addNote.html.twig',array('form'=>$form->createView()));
                }
                // replace this example code with whatever you need
                return $this->render('notes/addNote.html.twig',array('form'=>$form->createView()));
            }}
            return $this->redirectToRoute("login");
    }

    /**
* @Route("/updateEtudiant/{id}")
*/  
public function updateEtudiantAction(Request $request, $id) {
    $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "admin"){

                $em = $this->getDoctrine()->getManager();
                $etudiant = $em->getRepository('AppBundle:etudiants')->findOneById($id);
              
              
                $form = $this->createFormBuilder($etudiant)
                    ->add('nom',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                    ->add('prenom',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                    ->add('email',TextType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                    ->add('annee',ChoiceType::class,array('choices'=>array('1ere annee'=>'1ere annee','2eme annee'=>'2eme annee','3eme annee'=>'3eme annee'),'attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                    ->add('addresse',TextareaType::class,array('attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                    ->add('Modifier',SubmitType::class,array('attr'=>array('class'=>'btn btn-success')))
                    ->getForm();
              
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                  try{
                      $etudiant = $form->getData();
                      $em->flush();
                      $this->addFlash('successUpdate', 'Données modifiées');
                    }catch ( \Exception $e ) {
                        $this->addFlash('errorUpdate', 'un probleme est servenu');
                    }
                  //return $this->render('notes/etudiants.html.twig',array('etudiant' => $etudiant));
                    return $this->render('notes/updateEtudiant.html.twig',array('form' => $form->createView()));
                }
              
                return $this->render(
                  'notes/updateEtudiant.html.twig',
                  array('form' => $form->createView())
                  );
            }}
        return $this->redirectToRoute("login");
  }
  /**
     * @Route("/notes/{id}", name="notes_list")
     */
    public function NotessAction(Request $request,$id)
    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "admin"){
                $notes = $this->getDoctrine()->getRepository('AppBundle:notes')->findByEtudiant($id);        
                // replace this example code with whatever you need
                return $this->render('notes/EtudiantNotes.html.twig',array('notes'=>$notes));
            }}
        return $this->redirectToRoute("login");
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function LogoutAction(Request $request)
    {
        $session=$request->getSession();
        $session->clear();
        // replace this example code with whatever you need
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/home", name="home_etudiant")
     */
    public function HomeEtudiantAction(Request $request)
    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "etudiant"){
                $id = $session->get("idEtudiant");
                // replace this example code with whatever you need
                $notes = $this->getDoctrine()->getRepository('AppBundle:notes')->findByEtudiant($id);
                return $this->render('notes/hometudiant.html.twig',array('notes'=>$notes));
            }
        }
        return $this->redirectToRoute("login");
        
    }

     /**
     * @Route("/updatemdp", name="updatemdp")
     */
    public function UpdateAction(Request $request)
    {
        $session = $request->getSession();
        if($session->has("statut")){
            $status = $session->get("statut");
            if($status == "etudiant"){
                $id = $session->get("idEtudiant");
                // replace this example code with whatever you need
                $etudiant = new etudiants;
                $form = $this->createFormBuilder($etudiant)
                ->add('password',TextType::class,array("mapped" => false,'attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                
                ->add('newpassword',PasswordType::class,array("mapped" => false,'attr'=>array('class'=>'form-control','style'=>'margin-Bottom:15px')))
                ->add('Modifier',SubmitType::class,array('attr'=>array('class'=>'btn btn-success')))
                ->getForm();
  
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid() ) {
                    $password = $form['password']->getData();
                    $newpassword = $form['newpassword']->getData();
                    try{
                        $em = $this->getDoctrine()->getManager();
                        $etudiant = $em->getRepository('AppBundle:etudiants')->findOneById($id);
                        $apassword = $etudiant->getPassword();
                        if($password == $apassword){
                            $etudiant->setPassword($newpassword);
                            $em->flush();
                            $this->addFlash('successMDP', 'Mot de passe modifié');
                        }else{
                            $this->addFlash('errorMDP', 'Mot de passe incorrect');
                        }
                    }catch( \Exception $e ) {
                        $this->addFlash('errorMDP', 'un probleme est servenu');
                    }



                    }
                }
                return $this->render(
                    'notes/updatemdp.html.twig',array('form'=>$form->createView()));
                }                

    }
        
    
        
  
  
    

}   
