<?php 
namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;

class ConcertController extends FOSRestController{
    public function getConcertsAction()
    {
        // エンティティマネージャを取得
        $em = $this->get('doctrine')->getManager();
        // エンティティマネージャから、エンティティリポジトリを取得
        $repository = $em->getRepository('AppBundle:Concert');
        // エンティティリポジトリのファインダメソッドを実行して、情報を取得
        $concertList = $repository->findAll();

        return $concertList;
    }
}
