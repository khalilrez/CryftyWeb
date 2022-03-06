<?php

namespace App\Controller;


use App\Entity\Payment\Cart;
use App\Entity\Users\Client;
use App\Entity\Users\User;
use App\Repository\CartRepository;
use App\Repository\ClientRepository;
use App\Repository\NftRepository;
use App\Repository\UserRepository;
use App\Services\Cart\CartService;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\NFT\Nft;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class CartController extends AbstractController
{

    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(Request $request,SessionInterface $session,NftRepository $repository,CartRepository $cartRepository,ClientRepository $client): Response
    {
        $panier = $session->get("panier", []);
        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;
        $nft=null;
        foreach($panier as $id => $quantite){
            $nft = $repository->find($id);
            $dataPanier[] = [
                "produit" => $nft,
                "quantite"=>$quantite
            ];
        }
        $user= $this->getUser();
        $thisClient = $client->find($user);
        $cart = $cartRepository->find($thisClient->getCartId());
        if($nft != null)
        {
            $nft->setCartProd([$cart]);
        }
        foreach ($dataPanier as $item){
            $total=$total+$item["produit"]->getPrice();
            //$total=0;
        }
        $tab=[];
        for($i=0;$i<count($dataPanier);$i++)
        {
            $tab[$i]=$item["produit"];
        }
        $cart->setNftProd($tab);
        $cart->setTotal($total);
        $em=$this->getDoctrine()->getManager();
        $em->flush();
        return $this->render('cart/index.html.twig',['dataPanier'=>$dataPanier,'cart'=>$cart]);
        }

    /**
     * @Route("/delete/{id}", name="delete_panier")
     */
    public function SupprimerDuPanier(Nft $nft,ClientRepository $client, $id,SessionInterface $session,NftRepository $nftRepository,CartRepository $cartRepository)
    {

        $panier = $session->get("panier", []);
        $user = $this->getUser();
        $nftc = $nftRepository->SuppNft($user);
        $user= $this->getUser();
        $thisClient = $client->find($user);
        $cart = $cartRepository->find($thisClient->getCartId());

        for($i=0;$i<count($nftc);$i++)
        {
            if($nftc[$i]->getId()==$id)
            {
                if(!empty($panier[$id])) {
                    unset($panier[$id]);
                    $session->set("panier", $panier);
                    $nftc[$i]->setCartProd(null);
                    $em=$this->getDoctrine()->getManager();
                    $em->flush();
                    return $this->redirectToRoute('cart_index');
                }
                else {
                    return $this->redirectToRoute('nft');

                }


            }
        }
        return $this->render('cart/index.html.twig',['dataPanier'=>$panier,'cart'=>$cart]);


    }


    /**
     * @Route("/cart/ajout/{id}",name="ajouter_panier_test")
     */
    public function AjouterPanier(SessionInterface $session,$id)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }
        // On sauvegarde dans la session
        $session->set("panier", $panier);
        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/stripe", name="stripe")
     */
    public function stripe(): Response
    {
        \Stripe\Stripe::setApiKey('sk_test_51IyEiPKXO6zoy45XnSBJfUeShcjGESS1F0uIZoZH3XQjKcJrBVsctduUrUqjabgjdHZVALOU1OFe4lefVdlriKJg00dp6rwSy2');

        return $this->render('cart/Stripe.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }


    /**
     * @Route("/addPanier/{id}",name="ajoutAuPanier")
     */
    function AjoutAuPanier($id,CartRepository $cartRep, NftRepository $nftRepo,ClientRepository $clientRepo){
        $client = $clientRepo->find($this->getUser());
        $cart = $cartRep->find($client->getCartId());
        $nft = $nftRepo->find($id);
        $cart->setNftProd([$nft]);
        $nft->setCartProd([$cart]);
        $em =$this->getDoctrine()->getManager();
        $em->flush();
        return $this->render('nft/affiche.html.twig',['id'=>$cart->getNftProd()]);
    }

    /**
     * @Route("/test/{id}" ,name="test")
     */
    function ajouterUnProduit(Request $request, $id){
        $session = $request->getSession();
        $panier = $session->get('panier',[]);
        $panier[$id]=1;
        $session->set('panier',$panier);
        return $this->render('/nft/affiche.html.twig',['id'=>$session]);
    }

}
