<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function latestListAction()
    {
        // エンティティマネージャを取得
        $em = $this->getDoctrine()->getManager();
        // エンティティマネージャから、エンティティリポジトリを取得
        $blogArticleRepository = $em->getRepository('AppBundle:BlogArticle');
        // エンティティリポジトリのファインダメソッドを実行して、情報を取得
        $blogList = $blogArticleRepository->findBy([], ['targetDate' => 'DESC']); 

        return $this->render('Blog/latestList.html.twig',
            ['blogList' => $blogList]
        );
    }
}
