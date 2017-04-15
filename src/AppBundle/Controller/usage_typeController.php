<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\usage_type;
use AppBundle\Form\usage_typeType;
use AppBundle\DQL;

/**
 * usage_type controller.
 *
 */
class usage_typeController extends Controller
{
    /**
     * Lists all usage_type entities.
     *
     */


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user=$this->get('security.token_storage')->getToken()->getUser();

        $usage_types = $em->getRepository('AppBundle:usage_type')->getUsageTypes($user);

        return $this->render('dashboard/settings/usage_type/index.html.twig', array(
            'usage_types' => $usage_types,
        ));
    }

    /**
     * Creates a new usage_type entity.
     *
     */
    public function newAction(Request $request)
    {
        $usage_type = new usage_type();
        $user=$this->get('security.token_storage')->getToken()->getUser();
        $usage_type->setAuthUser($user);
        $form = $this->createForm('AppBundle\Form\usage_typeType', $usage_type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($usage_type);
            $em->flush();

            return $this->redirectToRoute('usage_type_index');
        }

        return $this->render('dashboard/settings/usage_type/new.html.twig', array(
            'usage_type' => $usage_type,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a usage_type entity.
     *
     */
    public function showAction(usage_type $usage_type)
    {
        if(!$this->isValidAction($usage_type)){
            return $this->redirectToRoute('usage_type_index');
        }
        $deleteForm = $this->createDeleteForm($usage_type);

        return $this->render('dashboard/settings/usage_type/show.html.twig', array(
            'usage_type' => $usage_type,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing usage_type entity.
     *
     */
    public function editAction(Request $request, usage_type $usage_type)
    {
        if(!$this->isValidAction($usage_type)){
            return $this->redirectToRoute('usage_type_index');
        }
        $deleteForm = $this->createDeleteForm($usage_type);
        $editForm = $this->createForm('AppBundle\Form\usage_typeType', $usage_type);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($usage_type);
            $em->flush();

            return $this->redirectToRoute('usage_type_edit', array('id' => $usage_type->getId()));
        }

        return $this->render('dashboard/settings/usage_type/edit.html.twig', array(
            'usage_type' => $usage_type,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a usage_type entity.
     *
     */
    public function deleteAction(Request $request, usage_type $usage_type)
    {
        if(!$this->isValidAction($usage_type)){
            return $this->redirectToRoute('usage_type_index');
        }
        $form = $this->createDeleteForm($usage_type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($usage_type);
            $em->flush();
        }

        return $this->redirectToRoute('usage_type_index');
    }

    /**
     * Creates a form to delete a usage_type entity.
     *
     * @param usage_type $usage_type The usage_type entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(usage_type $usage_type)
    {
        if(!$this->isValidAction($usage_type)){
            return $this->redirectToRoute('usage_type_index');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usage_type_delete', array('id' => $usage_type->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function isValidAction(usage_type $usage_type){
        $fetcher = new DQL\FetchData($this);
        $auid=$fetcher->getAuByUtid($usage_type->getId());
        if($auid==null) return false;
        $user=$this->get('security.token_storage')->getToken()->getUser();

        if($user->getId()==$auid['id']){
            return true;
        }
        return false;
    }
}
