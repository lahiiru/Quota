<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\DTO;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $param=array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        );

        if($request->query->has('ad')){
            $param=array_merge($param,['access_denied'=>'']);
        }

        return $this->render('homepage/index.html.twig',$param);
    }

    public function overviewAction(Request $request)
    {
        $cUser=$this->get('security.token_storage')->getToken()->getUser();
        $cId=$cUser->getId();
        $em = $this->getDoctrine()->getManager(); //SELECT NEW AppBundle\DTO\ClientStatusDTO(1,1,1,p.kbytes) FROM AppBundle\Entity\slave_user s JOIN s.auth_user a JOIN AppBundle\Entity\slave_usage WHERE a.id=$cId

        $query = $em->createQuery("SELECT p FROM AppBundle\Entity\data_package p WHERE p.start < CURRENT_TIMESTAMP() AND CURRENT_TIMESTAMP() < p.end AND p.auth_user=$cId");

        $cPackage = $query->getResult()[0]; // array of CustomerDTO
        $pstart=$cPackage->getStart();
        $pend=$cPackage->getEnd();

        $query = $em->createQuery("SELECT NEW AppBundle\DTO\ClientStatusDTO(su.name,MAX(u.date),SUM(u.kbytes),su.state) FROM AppBundle\Entity\slave_usage u JOIN u.slave_user as su JOIN su.auth_user as au WHERE au=$cId AND  :st < u.date AND u.date < :end GROUP BY su.sid");
        $query->setParameter('st', $pstart)
              ->setParameter('end', $pend);
        $clientStatusDTO = $query->getResult();
        var_dump($clientStatusDTO);
        $query = $em->createQuery("SELECT au.username as authUser ,MAX(u.date) as lastUpdate,su.name,SUM(u.kbytes) as total FROM AppBundle\Entity\slave_usage u JOIN u.slave_user as su JOIN su.auth_user as au WHERE au=$cId AND  :st < u.date AND u.date < :end GROUP BY au.id");
        $query->setParameter('st', $pstart)
            ->setParameter('end', $pend);

        $totalUsageObj = $query->getResult()[0];
        var_dump($totalUsageObj);

        $now = new \DateTime(); // or your date as well

        $totDays = $pend->diff($pstart)->format("%a");
        $today = $now->diff($pstart)->format("%a");
        var_dump($now,$pstart,$pend,$totDays,$today);

        $html=$this->render('dashboard/overview.html.twig', array(
            'clientStatusDTO' => $clientStatusDTO,
            'totalUsageObj' => $totalUsageObj,
            'totalPackage' => $cPackage->getKbytes(),
            'totalDays' => $totDays,
            'today' => $today,
            'expireDate'=>$pend->format('Y-m-d')
        ));
        return $html;
    }

    public function dashboardAction(Request $request)
    {
        $user=$this->get('security.token_storage')->getToken()->getUser();
        $this->checkForFirstLogin($user);

        $defaultData = array('message' => 'Type your message here');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('message', TextareaType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = ['name'=>'','email'=>'','message'=>''];
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            var_dump($data);
        }
        $zone= '';



        $html=$this->render('dashboard/index.html.twig', array(
            'form' => $form->createView(),
            'x' => $user->getUid(),
            'logout_url' => $this->generateUrl('logout', array(), false)
        ));
        return $html;
    }

    private function checkForFirstLogin($user){
        if($user->getZone()==""){
            return $this->redirect($this->generateUrl('setZone', array(), false));
        }
    }

    public function setZoneAction(Request $request)
    {
        $name=$request->request->get('zonename');
        if($name){
            $user=$this->get('security.token_storage')->getToken()->getUser();
            $user->setZone($name);
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('dashboard', array(), false));
        }else{
            return $this->render('setzone/base.html.twig', array(
                'zoneName' => '',
            ));
        }
    }

    public  function  failAction(Request $request)
    {
        return $this->redirect($this->get('router')->generate('homepage', array('ad' => '')));
    }

    public function inquiryHandlerAction(Request $request)
    {
        $name=$request->request->get('checkname');
        preg_match('/^[A-Za-z0-9\.\-\&_\@]*$/', $name, $matches);

        if (empty($matches)) {
            return $this->render('divs/zone/zoneNotVal.html.twig', array(
                'zone' => $name,
            ));
        }

        $exist = $this->getDoctrine()
            ->getRepository('AppBundle:auth_user')
            ->findOneBy(['zone'=>$name]);
        if(strlen($name)<4 || $exist!=null || strlen($name)>15){
            return $this->render('divs/zone/zoneNotAva.html.twig', array(
                'zone' => $name,
            ));
        }

        return $this->render('divs/zone/zoneAva.html.twig', array(
            'zone' => $name,
        ));
    }

    public function logoutAction(Request $request)
    {
        //$this->container->get('security.context')->setToken(null);
    }

}
