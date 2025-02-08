<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Entity\Inquiry;
use League\Csv\Writer;

/**
 * @Route("/admin/inquiry")
 */
class AdminInquiryListController extends Controller
{
    /**
     * @Route("/search.{_format}",
     *      defaults={"_format": "html"},
     *      requirements={
     *          "_format": "html|csv",
     *      }
     * )
     */
    public function indexAction(Request $request, $_format)
    {
        $form = $this->createSearchForm();
        $form->handleRequest($request);
        $keyword = null;
        if ($form->isValid()) {
            $keyword = $form->get('search')->getData();
        }

        $em = $this->getDoctrine()->getManager();
        $inquiryRepository = $em->getRepository('AppBundle:Inquiry');

        $inquiryList = $inquiryRepository->findAllByKeyword($keyword);

        if ($_format == 'csv') {
            $response = new Response($this->createCsv($inquiryList));
            $d = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'inquiry_list.csv'
            );
            $response->headers->set('Content-Disposition', $d);

            return $response;
        }

        return $this->render('Admin/Inquiry/index.html.twig',
            [
                'form' => $form->createView(),    
                'inquiryList' => $inquiryList,
                'format' => $_format
            ]
        );
    }

    private function createSearchForm()
    {
        return $this->createFormBuilder()
            ->add('search', 'search')
            ->add('submit', 'button', [
                'label' => '検索',
            ])
            ->getForm();
    }

    private function createCsv($inquiryList) 
    {
        /** @var Writer $writer */
        $writer = Writer::createFromString('','');
        $writer->setNewline("\r\n");

        foreach ($inquiryList as $inquiry)
        {
            /** @var Inquiry $inquiry */
            $writer->insertOne([
                $inquiry->getId(),
                $inquiry->getName(),
                $inquiry->getEmail(),
            ]);
        }

        return mb_convert_encoding((string)$writer, 'Shift_JIS', 'UTF-8');
    }
}
