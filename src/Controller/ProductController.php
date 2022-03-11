<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Entity\SearchFilters;
use App\Form\SearchFiltersType;
use App\Repository\ProductRepository;

use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\b;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/nos-produits/{page}", name="products")
     */
    public function index(ProductRepository $repo, Request $request, PaginatorInterface $paginator, $page = 1): Response
    {

        // $repo = $this->getDoctrine()->getRepository(Product::class);

        // $products = $repo->find(21);
        // findbyX (X nom d'un champ)
        // $products = $repo->findByName('consectetur id quam');

        // dd($repo->myFind(56));

        // dd($repo->myFindProductPrice(50*100,120*100));



        $search = new SearchFilters();

        $form = $this->createForm(SearchFiltersType::class, $search);

        //dd($user);

        $form->handleRequest($request); // On récupère la requete

        if ($form->isSubmitted() && $form->isValid()) {




            // $products = $repo->findby(['category' => $idCat]);
            $donnees = $repo->myFindSearch($search);

            $products = $paginator->paginate(
                $donnees, // Données
                $page, // Page sur laquelle on se trouve
                6 // Nombre d'éléments par page
            );


            if (count($donnees) < 1) {
                $error = "Aucun produit ne correspond à votre recherche";
            } else $error = null;
        } else {

            $donnees = $repo->myFindAll();

            $products = $paginator->paginate(
                $donnees, // Données
                $page, // Page sur laquelle on se trouve
                6 // Nombre d'éléments par page
            );

            $error = null;
        }



        // $products = $repo->findBy(['category' => 115], ['price' => 'desc'], 2, 0);

        // dump($products);


        return $this->render('product/index.html.twig', [
            'products' => $products,
            'error' => $error,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/produit/{slug}", name="show_product")
     */
    public function show(Product $product): Response
    {

        // $product = $repo->findOneBySlug($slug);


        return $this->render('product/show.html.twig', [
            'product' => $product

        ]);
    }




    /**
     * @Route("/compte/mes-commandes/{slug}/comment", name="comment_product")
     */
    public function comment(Product $product, Request $request, EntityManagerInterface $manager): Response
    {

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        //dd($user);

        $form->handleRequest($request); // On récupère la requete

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setCreatedAt(new \DateTime());
            $comment->setUser($this->getUser());
            $comment->setProduct($product);


            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le commentaire pour le produit ' . $product->getName() . ' a bien été enregistré'
            );

            return $this->redirectToRoute('show_product', ['slug' => $product->getSlug()]);
        }


        return $this->render('product/comment.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            //   date('d/m/Y')
        ]);
    }
}
