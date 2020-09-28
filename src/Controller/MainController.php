<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {

        $cards = $this->getDoctrine()->getRepository(Card::class)->findAll();
        $cardsByVideo = array_chunk($cards, 5);
        $colors = ["primary",
            "info",
            "success",
            "warning",
            "danger",
            "primary",
            "success",
            "warning",
            "success",
            "danger"];

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'cardsByVideo' => $cardsByVideo,
            'colors' => $colors,
        ]);
    }

    /**
     * @Route ("/takeCard/{card}", name="takeCard")
     * @param Card $card
     * Method ({"GET", "POST"})
     * @return RedirectResponse|Response
     */

    public function takeCard(Request $request, Card $card)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)->add('prenom', TextType::class, array('attr' => array('label' => 'Nom', 'class' => 'ml-4')))
            ->add('pays', TextType::class, array('attr' => array('label' => 'Pays', 'class' => 'ml-3')))
            ->add('ville', TextType::class, array('attr' => array('label' => 'Ville', 'class' => 'ml-5')))
            ->add('save', SubmitType::class, array(
                'label' => 'Valider',
                'attr' => array('class' => 'my-3 btn btn-success')
            ))->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $card->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->persist($card);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('main/takeCard.html.twig', array('card' => $card, 'form' => $form->createView()));
    }

    /**
     * @Route("/adminKOA", name="admin")
     */
    public function admin(){
        $cards = $this->getDoctrine()->getRepository(Card::class)->findAll();
        return $this->render('main/admin.html.twig', array
        ('cards' => $cards));
    }
    /**
     * @Route ("/add", name="addCard")
     * Method ({"GET", "POST"})
     * @param $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {

        $article = new Card();
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('content', TextareaType::class, array(
                'attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-primary')
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $card = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('main/addCard.html.twig', array(
            'form' => $form->createView()
        ));


    }

    /**
     * @Route ("/update/{card}", name="update_article")
     * @param Card $card
     * Method ({"GET", "POST"})
     * @return RedirectResponse|Response
     */

    public function update(Request $request, Card $card)
    {

        $form = $this->createFormBuilder($card)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('content', TextareaType::class, array(
                'attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-primary')
            ))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($card);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin');
        }

        return $this->render('main/update.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route ("/removeUser/{card}", name="removeUser")
     * @param Card $card
     * @return RedirectResponse
     */
    public function removeUser(Card $card){

        $card->setUser(null);
        $this->getDoctrine()->getManager()->persist($card);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route ("/delete/{card}",name="deleteCard")
     * @param Card $card
     * @return RedirectResponse
     */
    public function delete(Card $card){

        $this->getDoctrine()->getManager()->remove($card);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('admin');
    }
}
