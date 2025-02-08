<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inquiry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/inquiry")
 */
class InquiryController extends Controller
{
    /**
     * @Route("/")
     * @Method("get")
     */
    public function indexAction()
    {       
        return $this->render('Inquiry/index.html.twig',
            ['form' => $this->createInquiryForm()->createView()]
        );
    }

    /**
     * @Route("/")
     * @Method("post")
     */
    public function indexPostAction(Request $request)
    {
        $form = $this->createInquiryForm();
        $form->handleReQuest($request);
        if($form->isValid())
        {
            $inquiry = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($inquiry);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Webサイトからのお問い合わせ')
                ->setFrom('test021700@gmail.com')
                ->setTo('carp041800@gmail.com')
                ->setBody(
                    $this->renderView(
                        'mail/inquiry.txt.twig',
                        ['data' => $inquiry]
                    )
                );
            
            $this->get('mailer')->send($message);

            return $this->redirect(
                $this->generateUrl('app_inquiry_complete'));
        }

        return $this->render('Inquiry/index.html.twig',
        ['form' => $form->createView()]
        );
    }

    private function createInquiryForm()
    {
        return $this->createFormBuilder(new Inquiry)
            ->add('name', 'text')
            ->add('email', 'text')
            ->add('tel', 'text', [
                'required' => false,
            ])
            ->add('type', 'choice', [
                'choices' => [
                    '公演について',
                    'その他',
                ],
                'expanded' => true,
            ])
            ->add('content', 'textarea')
            ->add('submit', 'submit', [
                'label' => '送信',
            ])
            ->getForm();
    }

    /**
     * @Route("/complete")
     */
    public function completeAction()
    {
        return $this->render('Inquiry/complete.html.twig');
    }
}
