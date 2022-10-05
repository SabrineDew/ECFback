<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ShopController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $produits = $doctrine->getRepository(Produit::class)->findBy(
            ['actif' => true],
        );
        dump($produits);
        return $this->render("shop/index.html.twig", ["produits" => $produits]);
    }


    #[Route('/p/{id}/{slug}', name: 'app_produit', requirements: ['id' => "\d+", 'slug' => '.{1,}'])]

    #[ParamConverter('Produit', class: Produit::class)]
    public function produit(Produit $produit, Request $request, SessionInterface $session): Response
    {

        if ($request->request->get('ajout')) {
            dump($request->request->get('quantité'));
            dump($request->request->get('produit'));

            echo "Vous avez ajouté un ou plusieurs produits à votre panier";

            //je n'arrive pas à afficher le panier
            $panier = $session->get('panier', []);
            $panierWithData = [];
            foreach ($panier as $id => $quantity) {
                $panierWithData[] = [
                    'produit' => $produit($id),
                    'quantité' => $quantity
                ];
            }
            return $this->render('shop/panier.html.twig', [
                'items' => $panierWithData
            ]);
        }
        return $this->render('shop/lecture.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/panier', name: 'panier')]


    //Je n'arrive pas à afficher le panier magré que les request.
    //


    // public function shopping(SessionInterface $session, ProduitRepository $produitRepository): Response
    // {
    //     $panier = $session->get('panier', []);
    //     $panierWithData = [];
    //     foreach ($panier as $id => $quantity) {
    //         $panierWithData[] = [
    //             'produit' => $produitRepository->find($id),
    //             'quantité' => $quantity
    //         ];
    //     }
    //     return $this->render('shop/panier.html.twig', [
    //         'items' => $panierWithData
    //     ]);
    // }


    public function menu()
    {
        $listMenu = [
            ['title' => "Mon Magasin", "text" => 'Boutique', "url" => $this->generateUrl('homepage')],
            ['title' => "Mon Panier", "text" => 'Panier', "url" => $this->generateUrl('panier')],

        ];

        return $this->render("parts/menu.html.twig", ["listMenu" => $listMenu]);
    }
}
