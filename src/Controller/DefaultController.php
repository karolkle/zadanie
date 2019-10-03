<?php


namespace App\Controller;

use App\Entity\User;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/user", name="user", methods={"GET"})
     */
    public function index(Request $request){
        $repository = $this->getDoctrine()->getRepository(User::class);
        $items = $repository->findAll();
        return $this->json([
            'data' => array_map(function (User $item){
                return $this->generateUrl('user_by_id', ['id' => $item->getId()]);
            }, $items)
        ]
        );
    }


    /**
    * @Route("/user/{id}", name="user_by_id", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function user(User $user){
        return $this->json($user);
    }


    /**
     * @Route("/add", name="user_add", methods={"POST"})
     */
    public function add(Request $request){
        $serializer = $this->get('serializer');
        $users = $serializer->deserialize($request->getContent(), User::class, 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($users);
        $em->flush();

        return $this->json($users);
    }




    /**
     * @Route("/user/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(User $user){
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }





}