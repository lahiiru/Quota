<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 03/01/2016
 * Time: 9:54 PM
 */
namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\DTO;
use AppBundle\DQL;
use AppBundle\Entity;
class SettingsController extends  Controller
{
    public function packagesAction(Request $request){
        $user=$this->get('security.token_storage')->getToken()->getUser();
        $data_package = new Entity\data_package($user);

        $form = $this->createFormBuilder($data_package)
            ->add('kbytes', IntegerType::class, array(
                'required' => true,
                'attr' => array(
                    'style' => 'width: 100px',
                    'maxlength' => '10',
                    'min' => '100000',
                    'step' => '100000'
                ),
                'label' => 'Balance in KB'
            ))
            ->add('start', DateType::class)
            ->add('end', DateType::class)
            ->add('Active', SubmitType::class, array('label' => 'Activate'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ... perform some action, such as saving the task to the database
            $inserter = new DQL\InsertData($this);
            $inserter->activatePackage($data_package);
            return $this->redirect($request->getUri());
        }

        $fetcher = new DQL\FetchData($this);
        return $this->render('dashboard/settings/packages.html.twig', array(
            'form' => $form->createView(),
            'runningPackage' => $fetcher->getRunningDataPackage()
        ));
    }

    public function myZoneAction(Request $request){
        $user=$this->get('security.token_storage')->getToken()->getUser();
        $data_package = new Entity\data_package($user);

        $form = $this->createFormBuilder($data_package)
            ->add('kbytes', IntegerType::class, array(
                'required' => true,
                'attr' => array(
                    'style' => 'width: 100px',
                    'maxlength' => '10',
                    'min' => '100000',
                    'step' => '100000'
                ),
                'label' => 'Balance in KB'
            ))
            ->add('start', DateType::class)
            ->add('end', DateType::class)
            ->add('Active', SubmitType::class, array('label' => 'Activate'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ... perform some action, such as saving the task to the database
            $inserter = new DQL\InsertData($this);
            $inserter->activatePackage($data_package);
            return $this->redirect($request->getUri());
        }

        $fetcher = new DQL\FetchData($this);
        return $this->render('dashboard/settings/myzone.html.twig', array(
            'form' => $form->createView(),
            'runningPackage' => $fetcher->getRunningDataPackage()
        ));
    }
}