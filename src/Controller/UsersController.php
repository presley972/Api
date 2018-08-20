<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups;

class UsersController extends FOSRestController
{
    private $userRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em =$em;
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @return \FOS\RestBundle\View\View
     */
    public function getUsersAction()
    {
        $users = $this->userRepository->findAll();

        return $this->view($users);
        // "get_users"
    }


    /**
     * @Rest\View(serializerGroups={"article"})
     * @return \FOS\RestBundle\View\View
     */
    public function getUserAction(User $user)
    {
        //return new JsonResponse('Not the same user');
        return $this->view($user);
        // "get_user"
    }

    /**
     * @Rest\Post("/users")
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function postUsersAction(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
        return $this->view($user);
        // "post_users"
    }

    public function putUserAction(int $id, UserRepository $userRepository, Request $request
                                   )
    {
        $fn = $request->get('firstname');
        $fl = $request->get('lastname');
        $fe = $request->get('email');
      $user = $this->userRepository->find($id);
        if ( $fn ){
        $user->setFirstname($fn);
        }
        if ( $fl ){
            $user->setLastname($fl);
        }
        if ( $fe ){
            $user->setEmail($fe);
        }

      $this->em->persist($user);
      $this->em->flush();
      return $this->view($user);


      // "put_user"
    }

    public function deleteUserAction(User $user)
    {

        $this->em->remove($user);
        $this->em->flush();
        // "delete_user"
    }
}
