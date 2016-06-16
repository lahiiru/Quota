<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 03/01/2016
 * Time: 9:54 PM
 */
namespace AppBundle\Controller;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Util;
use AppBundle\DTO;
use AppBundle\DQL;
use AppBundle\Entity;
class PaymentController extends  Controller
{
    public function addAction(Request $request){
        $data_package = new Entity\slave_payment();


        $form = $this->createFormBuilder($data_package)
            ->add('fee', IntegerType::class, array(
                'required' => true,
                'attr' => array(
                    'style' => 'width: 200px',
                    'maxlength' => '10',
                ),
                'label' => 'LKR.'
            ))
            ->add('slave_user', EntityType::class, array(
                // query choices from this entity
                'class' => 'AppBundle:slave_user',
                'query_builder' => function(EntityRepository $er) {
                    $user=$this->get('security.token_storage')->getToken()->getUser();
                    return $er->createQueryBuilder('s')->where('s.auth_user='.$user->getId())->andWhere('s.mac!=\'FFFFFFFFFFFF\'')->orderBy('s.name', 'asc');
                },
                // use the User.username property as the visible option string
                'choice_label' => 'name')
            )
            ->add('add', SubmitType::class, array('label' => 'Add Payment'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getEntityManager();
            $em->persist($data_package);
            $em->flush($data_package);
            return $this->redirect($request->getUri());
        }

        return $this->render('dashboard/payments/add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    public function dueAction(Request $request){}
    public function summaryAction(Request $request){
        $user=$this->get('security.token_storage')->getToken()->getUser();
        $paymentData = $this->getDoctrine()->getRepository('AppBundle:slave_payment')
            ->getPayments($user->getZone());

        return $this->render('dashboard/payments/summary.html.twig', array(
            'payments' => $paymentData
        ));
    }
    public function settingsAction(Request $request){}
}