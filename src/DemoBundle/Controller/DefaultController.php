<?php

namespace DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use DemoBundle\Entity\post;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DemoBundle:Default:index.html.twig');
    }


    public function viewPostAction(){
      $posts = $this->getDoctrine()->getRepository('DemoBundle:post')->findAll();
      return $this->render('DemoBundle:Default:services.html.twig',['posts' => $posts ]);
    }

    public function createPostAction(Request $request){
      $post = new post;
      $form = $this->createFormBuilder($post)
      ->add('title',TextType::Class, array('attr'=> array('class' => 'form-control')))
      ->add('description',TextareaType::Class, array('attr'=> array('class' => 'form-control')))
      ->add('category',TextType::Class, array('attr'=> array('class' => 'form-control')))
      ->add('Save',SubmitType::Class, array('label'=>'Create Post','attr'=> array('class' => 'btn btn-primary')))
      ->getForm();
      $form->handleRequest($request);
      if($form ->isSubmitted() && $form->isValid()){
          $title = $form['title'] ->getData();
          $description = $form['description'] ->getData();
          $category = $form['category'] ->getData();

          $post->setTitle($title);
          $post->setDescription($description);
          $post->setCategory($category);

          $em = $this->getDoctrine()->getManager();
          $em->persist($post);
          $em->flush();
          $this->addFlash('message','Post saved successfully');
          return $this->redirectToRoute('view_services_route');
      }
      return $this->render('DemoBundle:Default:create.html.twig',['form' => $form->createView()]);
    }



    public function updatePostAction(Request $request,$id){
      $post = $this->getDoctrine()->getRepository('DemoBundle:post')->find($id);
      $post->setTitle($post->getTitle());
      $post->setDescription($post->getDescription());
      $post->setCategory($post->getCategory());

      $form = $this->createFormBuilder($post)
      ->add('title',TextType::Class, array('attr'=> array('class' => 'form-control')))
      ->add('description',TextareaType::Class, array('attr'=> array('class' => 'form-control')))
      ->add('category',TextType::Class, array('attr'=> array('class' => 'form-control')))
      ->add('Save',SubmitType::Class, array('label'=>'Update Post','attr'=> array('class' => 'btn btn-primary')))
      ->getForm();
      $form->handleRequest($request);
      if($form ->isSubmitted() && $form->isValid()){
          $title = $form['title'] ->getData();
          $description = $form['description'] ->getData();
          $category = $form['category'] ->getData();

          $em = $this->getDoctrine()->getManager();
          $post = $em->getRepository('DemoBundle:post')->find($id);

          $post->setTitle($title);
          $post->setDescription($description);
          $post->setCategory($category);

          $em->flush();
          $this->addFlash('message','Post updated successfully');
          return $this->redirectToRoute('view_services_route');

      }
      return $this->render('DemoBundle:Default:update.html.twig',['form' => $form->createView()]);


    }



    public function deletePostAction($id){
      $em =$this->getDoctrine()->getManager();
      $post = $em->getRepository('DemoBundle:post')->find($id);
      $em->remove($post);
      $em->flush();
      $this->addFlash('message','Post deleted successfully');
      return $this->redirectToRoute('view_services_route');
      return $this->render('DemoBundle:Default:delete.html.twig');
    }

    public function showPostAction($id){
      $post = $this->getDoctrine()->getRepository('DemoBundle:post')->find($id);
      return $this->render('DemoBundle:Default:show.html.twig',['post'=> $post]);
    }
}
