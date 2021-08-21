<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use Doctrine\DBAL\Driver\PDO\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use function PHPUnit\Framework\throwException;

/**
 * @IsGranted("ROLE_USER")
 */

class GenreController extends AbstractController
{
   /**
     * @Route ("/genre", name="genre_index")
     */
    public function indexGenre() {
        $genres = $this->getDoctrine()
                       ->getRepository(Genre::class)
                       ->findAll();
        return $this->render(
            "genre/index.html.twig",
            [
               "genres" => $genres 
            ]
        );
    }
    
    /**
     * @Route("/genre/detail/{id}", name="genre_detail")
     */
    public function detailBook($id) {
        $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);

        return $this->render(
            'genre/detail.html.twig',
            [
                'genre' => $genre
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/genre/create", name="genre_create")
     */
    public function createnewGenre(Request $request) {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager -> persist($manager);
            $manager -> flush();
            $this-> addFlash("error", "add successfully");
            return $this->redirectToRoute('genre_index');
        }
        return $this-> render(
            'genre/create.html.twig',
            [
                'form' => $form->createView()
            ]
            );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
    * @Route ("/genre/update/{id}", name="genre_update")
    */
    public function updateGenre(Request $request, $id) {
        $genre = $this->getDoctrine()
                      ->getRepository(Genre::class)
                      ->find($id);      
        $form = $this->createForm(GenreType::class,$genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($genre);
            $manager->flush();
            $this->addFlash("Info","Update successfully !");
            return $this->redirectToRoute("genre_index");
        }  

        return $this->render(
            "genre/update.html.twig",
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/genre/delete/{id}", name="genre_delete")
     */
    public function deleteGenre($id) {
        try{
            $genre = $this->getDoctrine()->getRepository(Genre::class)->find($id);
            $chk = $genre->getMangas();
            if(count($chk)>0){  
                $this->addFlash("Error","delete failed");
                return $this->redirectToRoute("genre_index");
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($genre);
            $manager->flush();
            $this->addFlash("Info", "Delete succeed !");
            return $this->redirectToRoute("genre_index");
        }catch(Exception $e){
            throwException($e);
        }
    }   
}
