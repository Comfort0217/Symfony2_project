<?php 
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cs:test')
            ->setDescription('test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // 公演情報を取得
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $repository = $em->getRepository('AppBundle:Concert');
        $concertList = $repository->findAll();
        
        // シリアライザを取得
        $serializer = $container->get('jms_serializer');
        // シリアライザを使ってシリアライズ
        $json = $serializer->serialize($concertList, 'json');

        dump($json);
    }
}
?>