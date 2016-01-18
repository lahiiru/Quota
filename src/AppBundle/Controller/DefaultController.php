<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Util;
use AppBundle\DTO;
use AppBundle\DQL;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
    private function downloadFileAction($os)
    {
        /**
         * $basePath can be either exposed (typically inside web/)
         * or "internal".
         */
        $basePath = $this->container->getParameter('kernel.root_dir').'/Resources/my_custom_folder';
        $dir = $this->get('kernel')->getRootDir() . "/../../../Dropbox/quota/updates";
        $handle = fopen("$dir/updates.txt", "r");
        if ($handle) {
            $version="0.0.0";
            $link="";
            while (($line = fgets($handle)) !== false) {
                $s=explode('#',$line);
                if(version_compare($s[0],$version)==1){
                    $link=$s[1];
                    $version=$s[0];
                }
            }

            fclose($handle);
        } else {
            // error opening the file.
        }

        $a=explode("/",$link);
        $filename = trim(end($a));
        $filePath="$dir/$filename";
        // check if file exists
        $fs = new FileSystem();
        if (!$fs->exists($filePath)) {
            throw $this->createNotFoundException();
        }
        $filename="Quota_Setup_$os.exe";
        // prepare BinaryFileResponse
        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename,
            iconv('UTF-8', 'ASCII//TRANSLIT', $filename)
        );

        return $response;
    }

    public function downloadAction(Request $request)
    {

        if($request->query->has('win8')){
            return $this->downloadFileAction("win8");
        }
        if($request->query->has('win7')){
            return $this->downloadFileAction("win7");
        }
        $html=$this->render('dashboard/download.html.twig', array(
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
            $user->setPkey(Util\SSIDEncryptor::encode($name));
            $user->setSkey(Util\PassGenerator::generateStrongPassword());
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
