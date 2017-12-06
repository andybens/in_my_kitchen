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

    public function contactAction(Request $request)
    {

        $firstname = $lastname = $email = $object = $message = NULL;

        $form = $this->createFormBuilder()
            ->add('firstname', TextType::class, array('constraints' => array(new NotBlank(array(//'message' => 'contact.error.firstname'
            ))
            , new Length(array('min' => 3,
                    'max' => 10,
                    //'minMessage' => 'contact.error.firstnamemin,
                    //'maxMessage' => 'contact.error.firstnamemax'
                )))))
            ->add('lastname', TextType::class, array('constraints' => array(new NotBlank(array(//'message' => 'contact.error.lastname'
            ))
            , new Length(array('min' => 3,
                    'max' => 10,
                    //'minMessage' => 'contact.error.lastnamemin',
                    //'maxMessage' => 'contact.error.lastnamemax'
                )))))
            ->add('email', TextType::class, array('constraints' => array(new Assert\Email(array('checkMX' => true)),
                new NotBlank(),)))
//        ->add('email', EmailType::class, array('constraints' => array(new Assert\Email(array(//'message' => 'contact.error.email',
            //          											'checkMX' => true)))))
            /*
                ->add('object', ChoiceType::class, array('choices' => array('' => '','Autre' => "Autre",'Bug' => "Bug", 'Demande' => "Demande"))
                                 , array('constraints' => array(new NotBlank(array(//'message' => 'contact.error.lastname'
            )))))
            */
            ->add('object', ChoiceType::class, array('choices' => array('' => '', 'Autre' => "Autre", 'Bug' => "Bug", 'Demande' => "Demande")),
                array('constraints' => array(new Length(array('min' => 3)))))
            ->add('message', TextareaType::class, array('constraints' => array(new NotBlank(array(//'message' => 'contact.error.messagenotblank'
            ))
            , new Length(array('min' => 8,
                    'max' => 10,
                    // 'minMessage' => 'contact.error.messagemin',
                    // 'maxMessage' => 'contact.error.messagemax'
                )))))
            ->add('send', SubmitType::class, array('label' => 'Envoyer'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {


            //$request = Request::createFromGlobals();
            $firstname = $form["firstname"]->getData();
            $lastname = $form["lastname"]->getData();
            $email = $form["email"]->getData();
            $object = $form["object"]->getData();
            $message = $form["message"]->getData();

            if ($request->isMethod('POST')) {
                $this->addFlash('notice', 'Ok');
                $this->addFlash('sent', 'Ok');
            } else {
                $this->addFlash('error', 'Cant be reached like this');
            }
        }


        return $this->render('DemoBundle:Default:contact.html.twig', array('form' => $form->createView(),
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'object' => $object,
            'message' => $message));
    }


    public function testAction(Request $request)
    {
        $name = $email = $objet = NULL;

        $form = $this ->createFormBuilder()
            ->add('name')
            ->add('email')
            ->add('objet', ChoiceType::class, array('choices' => array(''=> null,'Yes' => "Yes", 'No' => "No",
                'Why Not' => "Why Not")))
            ->add('send', SubmitType::class, array('label' => 'Ok'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $name = $form["name"]->getData();
            $email = $form["email"]->getData();
            $objet = $form["objet"]->getData();
        }

        return $this->render('DemoBundle:Default:test.html.twig', array('form' => $form -> createView(),
            'name'=> $name,
            'email'=> $email,
            'objet'=> $objet));

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
