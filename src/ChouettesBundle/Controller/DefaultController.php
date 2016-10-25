<?php

namespace ChouettesBundle\Controller;

use Doctrine\DBAL\Types\TextType;
use ChouettesBundle\ChouettesBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    public function indexAction()
    {
        // Connexion à la BdD
        $em = $this->getDoctrine()->getManager();
        // Recupération des données CITATIONS
        $citations=$em->getRepository('ChouettesBundle:Citation')->findAll();
        // Recupération des données MODELE pour les images affichables sur la pages d'accueil
        $modeles=$em->getRepository('ChouettesBundle:Modele')->findBy(array('add_block' => true));

        // -----------------------------------------------------------------------------------------------------
        // Mise en place random pour afficher aléatoirement les CITATIONS sur la page
        // d'accueil. Si aucune citation existe dans la base de données on renvoi comme contenu un chaine vide
        // Dans Default/index.html.twig
        // -----------------------------------------------------------------------------------------------------
        if(!empty($citations)) {
            $randomcitation = $citations[array_rand($citations)]->getText();
        }
        else {
            $randomcitation = '';
        }

        // -----------------------------------------------------------------------------------------------------
        // Mise en place random pour afficher aléatoirement les PHOTOS par CATEGORIES sur la page
        // d'accueil.
        // -----------------------------------------------------------------------------------------------------
        // 1 - Bijoux
        // 2 - Doudous
        // 3 - Accessoires
        // Récuperation des champs contenus dans la variable $modeles
        $nbElement = count($modeles);
        $categorie =array();
        foreach ($modeles as $modele){
            // récupération des éléménts MODELE qui seront transmis à la vue
            $categorie[] = $modele;
            $titre = $modele->getTitre();
        }

        // retourne citation et image dans Default/index.html.twig
        return $this->render('@Chouettes/Default/index.html.twig', array(
            'modeles' => $modeles,
            'categorie' => $categorie,
            'citation'=> $randomcitation,
//            'titre' => $titre,
            'nbElement' => $nbElement
        ));
    }


    public function doudousAction()
    {
        $em = $this->getDoctrine()->getManager();
        $modeles=$em->getRepository("ChouettesBundle:Modele")->findBy(array('categorie' => 2));
        return $this->render('@Chouettes/user/doudous.html.twig', array(
            'modeles'=>$modeles
        ));
    }

    public function bijouxAction()
    {
        $em = $this->getDoctrine()->getManager();
        $modeles=$em->getRepository("ChouettesBundle:Modele")->findBy(array('categorie' => 1));
        return $this->render('@Chouettes/user/bijoux.html.twig', array(
            'modeles'=>$modeles
        ));
    }

    public function accessoiresAction()
    {
        $em = $this->getDoctrine()->getManager();
        $modeles=$em->getRepository("ChouettesBundle:Modele")->findBy(array('categorie' => 3));
        return $this->render('@Chouettes/user/accessoires.html.twig', array(
            'modeles'=>$modeles
        ));
    }

    public function aboutAction()
    {
        return $this->render('@Chouettes/user/about.html.twig');
    }

    public function contactAction()
    {
        return $this->render('@Chouettes/user/contact.html.twig');
    }
    
    public function sendAction(Request $request)
    {
        $name = $request->request->get('nom');
        $firstname = $request->request->get('prenom');
        $mail = $request->request->get('mail');
        $sujet = $request->request->get('sujet');
        $msg = $request->request->get('msg');
        $message = \Swift_Message::newInstance()
            ->setSubject('Contact Chouettes')
            ->setFrom($mail)
            ->setTo('bibouye77@gmail.com')
            ->setBody(
                $this->renderView(
                    '@Chouettes/user/mail.html.twig',
                    array(
                        'nom' => $name,
                        'prenom' => $firstname,
                        'mail' => $mail,
                        'sujet' => $sujet,
                        'message' => $msg
                    )
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);
        return $this->render('@Chouettes/user/contact.html.twig');
    }



    public function messageAction()
    {
        require_once '/lib/swift_required.php';

        // Create the Transport
        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465);

        // Create the Mailer using your created Transport
        $mailer = Swift_Mailer::newInstance($transport);

        // Create a message
        $message = Swift_Message::newInstance('Wonderful Subject')
        ->setFrom(array('bibouye77@gmail.com' => 'Bibouye'))
        ->setTo(array('bibouye77@gmail.com'))
        ->setFirstName("Here is the sender's first name")
        ->setLastName("Here is the sender's last name")
        ->setEmail('Here is user email')
        ->setBody('Here is the message itself')
        ;
//
//        // Send the message
        $result = $mailer->send($message);
        $status = $mailer->send($message);
        if($status)
        {
            echo "Success!";
        }

        else
        {
            echo "Failure";
        }
//    }
}

    public function adminAction()
    {
        return $this->render('@Chouettes/Admin/index.html.twig');
    }


    public function sendMailAction(Request $request)
    {
//        php natif
        $donnée_via_post = $_POST;

//        methode sous symfony
        $poiu = $request;

        return $this->render('@Chouettes/user/contact.html.twig');
    }

}

