<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Form\MangaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use function PHPUnit\Framework\throwException;

/**
 * @IsGranted("ROLE_USER")
 */
class MangaController extends AbstractController
{
    /**
     * @Route("/manga", name="manga_index")
     */
    public function indexManga() 
    {
        $mangas = $this->getDoctrine()->getRepository(Manga::class)->findAll();

        return $this->render(
            'manga/index.html.twig',
            [
                'mangas' => $mangas
            ]
        );
    }

    /**
     * @Route("/manga/detail/{id}", name="manga_detail")
     */
    public function detailManga($id) {
        $manga = $this->getDoctrine()->getRepository(Manga::class)->find($id);

        return $this->render(
            'manga/detail.html.twig',
            [
                'manga' => $manga
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/manga/delete/{id}", name="manga_delete")
     */
    public function deleteManga($id) {
        $manga = $this->getDoctrine()->getRepository(Manga::class)->find($id);

        if ($manga == null) {
            $this->addFlash("Error", "Delete manga failed !");
            return $this->redirectToRoute("manga_index");
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($manga);
        $manager->flush();

        $this->addFlash("Info","Delete manga succeed !");
        return $this->redirectToRoute("manga_index");
    }

     /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("manga/create", name="manga_create")
     */
    public function createManga(Request $request) {
        $manga = new Manga();
        $form = $this->createForm(MangaType::class,$manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //get Image from uploaded file
            $image = $manga ->getImage();
            //create an unique image name
            $fileName = md5(uniqid());
            //get image extension
            $fileExtension = $image->guessExtension();
            //merge image name & image extension => get a complete image name
            $imageName = $fileName . '.' . $fileExtension;
            //move upload file to a predefined location
            try {
                $image->move(
                    $this->getParameter('manga_image'), $imageName
                );
            } catch (FileException $e) {
                throwException($e);
            }
            //set imageName to database
            $manga->setImage($imageName);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($manga);
            $manager->flush();

            $this->addFlash("Info", "Create manga succeed !");
            return $this->redirectToRoute("manga_index"); 
        }

        return $this->render(
            'manga/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("manga/update/{id}", name="manga_update")
     */
    public function updateManga(Request $request, $id) {
        $manga = $this->getDoctrine()->getRepository(Manga::class)->find($id);
        $form = $this->createForm(MangaType::class,$manga);
        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $form['Image']->getData();
            if ($uploadFile != null) {
                //get Image from uploaded file
                $image = $manga->getImage();

                //create an unique image name
                $fileName = md5(uniqid());
                //get image extension
                $fileExtension = $image->guessExtension();
                //merge image name & image extension => get a complete image name
                $imageName = $fileName . '.' . $fileExtension;

                //move upload file to a predefined location
                try {
                    $image->move(
                        $this->getParameter('manga_image'), $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }

                //set imageName to database
                $manga->setImage($imageName);
            } 

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($manga);
            $manager->flush();

            $this->addFlash("Info", "Update manga succeed !");
            return $this->redirectToRoute("manga_index"); 
        }

        return $this->render(
            'manga/update.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}