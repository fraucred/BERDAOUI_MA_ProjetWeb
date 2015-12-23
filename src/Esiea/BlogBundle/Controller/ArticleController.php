<?php

namespace Esiea\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Esiea\BlogBundle\Entity\Article;
use Esiea\BlogBundle\Entity\Task;

class ArticleController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
      $repository = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('EsieaBlogBundle:Article')
      ;
      $listAdverts = $repository->findBy(
        array(), 
        array('articleNumber' => 'desc'),        
        100,                        
        0                            
        );
      return $this->render('EsieaBlogBundle:Article:index.html.twig', array(
        'listAdverts' => $listAdverts
        ));
    }
    public function addAction(Request $request)
    {
      $article = new Article();

      $article->setTitle("Votre titre")
      ->setContent("Le contenu de votre article")
      ->setArticleNumber("99")
      ->setAuthor("BERDAOUI")
      ->setWriteDate(new \Datetime());


      $form = $this->get('form.factory')->createBuilder('form', $article)

      ->add('writeDate',      'datetime')
      ->add('title',     'text')
      ->add('content',   'textarea')
      ->add('articleNumber', 'integer')
      ->add('author',    'text')
      ->add('save',      'submit')

      ->getForm();

      $form->handleRequest($request);

      if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistré.');

        return $this->redirect($this->generateUrl('esiea_blog_view', array('id' => $article->getId())));
      }

      return $this->render('EsieaBlogBundle:Article:add.html.twig', array(
        'form' => $form->createView(),
        ));
      // $article = new Article();

      //   $form = $this->createFormBuilder($article)
      //       ->getForm();
      //    $form->handleRequest($request);

      //    if ($form->isValid()) {
      //     return $this->redirectToRoute('task_success'); 
      //    }
      //   return $this->render('EsieaBlogBundle:Article:new.html.twig', array(
      //       'form' => $form->createView(),
      //   ));
      //return $this->render('EsieaBlogBundle:Article:add.html.twig');
    }
    public function menuAction($limit)
    {
      $repository = $this
      ->getDoctrine()
      ->getManager()
      ->getRepository('EsieaBlogBundle:Article')
      ;
      $listAdverts = $repository->findBy(
        array(), 
        array('articleNumber' => 'desc'),        
        $limit,                        
        0                           
        );

      return $this->render('EsieaBlogBundle:Article:menu.html.twig', array(
        'listAdverts' => $listAdverts
        ));
    }
    public function viewAction($id)
    {
      $article = $this->getDoctrine()
      ->getManager()
      ->find('EsieaBlogBundle:Article', $id)
      ;
      if (null === $article) {
        throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas.");
      }
      return $this->render('EsieaBlogBundle:Article:view.html.twig', array(
        'article' => $article
        ));
    }
    public function editAction($id, Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $article = $em->getRepository('EsieaBlogBundle:Article')->find($id);
      if (null === $article) {
        throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas.");
      }
      $form = $this->get('form.factory')->createBuilder('form', $article);
      $form
      ->add('writeDate',      'datetime', array("data" => $article->getWriteDate() ))
      ->add('title',     'text', array("data" => $article->getTitle() ))
      ->add('content',   'textarea', array("data" => $article->getContent() ))
      ->add('articleNumber', 'integer', array("data" => $article->getArticleNumber() ))
      ->add('author',    'text', array("data" => $article->getAuthor() ))
      ->add('save',      'submit')
      ;
      $form = $form->getForm();
      $form->handleRequest($request);

      if ($form->isValid()) {
        $em->persist($article);
        $em->flush();
        $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistré.');
        return $this->redirect($this->generateUrl('esiea_blog_view', array('id' => $article->getId())));
      }

      return $this->render('EsieaBlogBundle:Article:edit.html.twig', array(
        'form' => $form->createView(),'article' => $article
        ));
    }
    public function deleteAction($id, Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $article = $em->getRepository('EsieaBlogBundle:Article')->find($id);
      if ($article == null) {
        throw $this->createNotFoundException("L'article d'id ".$id." n'existe pas.");
      }
      if ($request->isMethod('POST')) {

        $request->getSession()->getFlashBag()->add('info', 'Article bien supprimé.');
        return $this->redirect($this->generateUrl('blog_homepage'));
      }
      $em->remove($article);
      $em->flush();

      return $this->render('EsieaBlogBundle:Article:delete.html.twig', array(
        'article' => $article
        ));
    }
    public function aboutAction() {
      return $this->render('EsieaBlogBundle:Article:about.html.twig');
    }
    public function contactAction() {
      return $this->render('EsieaBlogBundle:Article:contact.html.twig');
    }
  }
