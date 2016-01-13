<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 13/01/2016
 * Time: 9:20 AM
 */

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BannerController extends Controller
{
    public function indexAction(Request $request){
        $html = $this->render('Banner\base.html.twig', array(
        ));
        return $html;
    }
}