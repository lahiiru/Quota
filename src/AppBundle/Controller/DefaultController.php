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
use AppBundle\DQL;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $param=array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        );

        return $this->render('homepage/index.html.twig',$param);
    }

    public function homeErrorAction(Request $request,$error_name){
        $errorData=
        [
            'access_denied'=>
                [
                    'model_err_title'=>'Access is denied',
                    'model_err_body'=>'<p><b>You don\'t have privileges to access requested content.</b></p>
                                               <p>Please sign in using your facebook profile or google plus profile. If you are already signed in and seeing this message, you can ask site administrator for privileges.</p>
                                               <p>adm.trine@gmail.com</p>'
                ],
            'invalid_zone'=>
                [
                'model_err_title'=>'Zone name is invalid',
                    'model_err_body'=>'<p><b>It seems you are trying to obtain restricted zone name.</b></p>
                                               <p>Your zone name should be longer than 4 english letters which haven\'t obtained by others. If you think you are seeing this message by system fault, please contact us.</p>
                                               <p>adm.trine@gmail.com</p>'
                ]
        ];
        return $this->render('homepage/index.html.twig',$errorData[$error_name]);
    }

    public function overviewAction(Request $request)
    {
        $fetcher = new DQL\FetchData($this);

        $cPackage = $cPackage = $fetcher->getRunningDataPackage();
        $pstart=$cPackage->getStart();
        $pend=$cPackage->getEnd();

        $clientStatusDTO = $fetcher->getClientStatusDTO($cPackage);
        //var_dump($clientStatusDTO);

        $totalUsageObj = $fetcher->getTotalUsageObj($cPackage);
        //var_dump($totalUsageObj);

        $now = new \DateTime(); // or your date as well

        $totDays = $pend->diff($pstart)->format("%a");
        $today = $now->diff($pstart)->format("%a");
        //var_dump($now,$pstart,$pend,$totDays,$today);

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
        $result=$this->checkForFirstLogin($user);
        if($result!=null){
            return $result;
        }

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
        if($user->getZone()=="" || $user->getZone()==null){
            return $this->redirect($this->generateUrl('setZone', array(), false));
        }else{
            return null;
        }
    }

    public function setZoneAction(Request $request)
    {
        $name=$request->request->get('zonename');
        if($name){
            if($this->zoneValidator($name)!=0) {
                var_dump($this->zoneValidator($name));
                return $this->redirectToRoute('home_error', array('error_name' => 'invalid_zone'));
            }

            $user=$this->get('security.token_storage')->getToken()->getUser();
            $user->setZone($name);
            $user->setPkey($this->generateStrongPassword());
            $user->setSkey($this->generateStrongPassword());
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();
            // Creating default `Unregistered` user
            $unregisteredUser=new Entity\slave_user("FFFFFFFFFFFF",$user,"UNREGISTERED",0,1);
            $em->persist($unregisteredUser);
            $em->flush();
            return $this->redirect($this->generateUrl('dashboard', array(), false));
        }else{
            return $this->render('setzone/base.html.twig', array(
                'zoneName' => '',
            ));
        }
    }

    private function generateStrongPassword($length = 10, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(!$add_dashes)
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }

    public  function  failAction(Request $request)
    {
        return $this->redirectToRoute('home_error', array('error_name'=>'access_denied'));
    }

    private function zoneValidator($name){
        preg_match('/^[A-Za-z0-9\.\-\&_\@]*$/', $name, $matches);

        if (empty($matches)) {
            return 1;
        }

        $exist = $this->getDoctrine()
            ->getRepository('AppBundle:auth_user')
            ->findOneBy(['zone'=>$name]);

        if(strlen($name)<4 || $exist!=null || strlen($name)>15){
            return 2;
        }

        return 0;
    }

    public function inquiryHandlerAction(Request $request)
    {
        $name=$request->request->get('checkname');
        $html=$this->render('divs/zone/zoneNotVal.html.twig', array(
            'zone' => $name,
        ));
        switch($this->zoneValidator($name)){
            case 0:
                $html=$this->render('divs/zone/zoneAva.html.twig', array(
                    'zone' => $name,
                ));break;
            case 2:
                $html=$this->render('divs/zone/zoneNotAva.html.twig', array(
                    'zone' => $name,
                ));break;
        }
        return $html;
    }

    public function logoutAction(Request $request)
    {
        //$this->container->get('security.context')->setToken(null);
    }

}
