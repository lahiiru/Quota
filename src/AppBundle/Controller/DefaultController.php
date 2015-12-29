<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('homepage/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    public function dashboardAction(Request $request)
    {
        $zone= '';
        $user=$this->get('security.token_storage')->getToken()->getUser();
        if($user->getZone()==""){
            return $this->redirect($this->generateUrl('setZone', array(), false));
        }
        $html=$this->render('dashboard/index.html.twig', array(
            'x' => $user->getUid(),
            'logout_url' => $this->generateUrl('logout', array(), false)
        ));
        return $html;
    }

    public function setZoneAction(Request $request){
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

    public function inquiryHandlerAction(Request $request){
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
