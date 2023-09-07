<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Asset;
use App\Form\AssetUpdateType;
use App\Form\AssetCreateType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(UserInterface $user, Connection $conn): Response
    {
        $user_id = $user->getId();

        if(isset($user)) {
            $queryBuilder = $conn->prepare('select a.asset_id, a.label, a.value, c.symbol, c.price, a.value*c.price cost_usd from asset a, currency c where a.currency_id = c.currency_id and a.user_id = ' . $user_id);
            $data = $queryBuilder->executeQuery()->fetchAllAssociative();

            $total_price = 0;
            foreach ($data as $value)
                $total_price += $value['cost_usd'];
        }
        
        return $this->render('index.html.twig', [ 'total_price' => $total_price, 'data' => $data ]);
    }

    #[Route('/update/{id}', name: 'update_asset')]
    public function updateAsset(Request $request, Asset $asset, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(AssetUpdateType::class, $asset);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->merge($asset);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'create_asset')]
    public function createAsset(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $asset = new Asset();
        $form = $this->createForm(AssetCreateType::class, $asset);
        $asset->setUserId($user->getId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($asset);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
