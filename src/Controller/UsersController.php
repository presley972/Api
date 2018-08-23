<?php

namespace App\Controller;

use App\Entity\User;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        if ($this->isGranted('ROLE_ADMIN')) {
            $users = $this->userRepository->findAll();

            return $this->view($users);
            // "get_users"
        }
        return $this->view('not admin', 401);
    }


    /**
     * @Rest\View(serializerGroups={"article"})
     * @return \FOS\RestBundle\View\View
     */
    public function getUserAction(User $user)
    {
        if ($user == $this->getUser() or $this->isGranted('ROLE_ADMIN')) {

            //return new JsonResponse('Not the same user');
            return $this->view($user);
            // "get_user"
        }
        return $this->view('not admin', 401);
    }

    /**
     * @Rest\Post("/users")
     * @Rest\View(serializerGroups={"article"})
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @param User $user
     * @param ValidatorInterface $validator
     * @return \FOS\RestBundle\View\View|JsonResponse
     */
    public function postUsersAction(User $user, ValidatorInterface $validator)
    {
        /** @var ConstraintViolationList $validationErrors */
        $validationErrors = $validator->validate($user);
        if ($validationErrors->count() > 0){
            $error = array();
            foreach ($validationErrors as $constraintViolation) {


                $message = $constraintViolation->getMessage();
                // Returns the violation message. (Ex. This value should not be blank.)
                $propertyPath = $constraintViolation->getPropertyPath();
                array_push($error, $propertyPath . '  ' . $message);
            }
            return new JsonResponse($error);

        }
        $this->em->persist($user);
        $this->em->flush();
        return $this->view($user, 201);
        // "post_users"
    }

    /**
     * @Rest\View(serializerGroups={"article"})
     *
     */
    public function putUserAction(int $id, Request $request, ValidatorInterface $validator)
    {

        /** @var ConstraintViolationList $validationErrors */
        $user = $this->userRepository->find($id);
        $validationErrors = $validator->validate($user);
        if ($validationErrors->count() > 0) {
            $error = array();
            foreach ($validationErrors as $constraintViolation) {


                $message = $constraintViolation->getMessage();
                // Returns the violation message. (Ex. This value should not be blank.)
                $propertyPath = $constraintViolation->getPropertyPath();
                array_push($error, $propertyPath . '  ' . $message);
            }
            return new JsonResponse($error);
        }
        $fn = $request->get('firstname');
        $fl = $request->get('lastname');
        $fe = $request->get('email');
        if ($user == $this->getUser() or $this->isGranted('ROLE_ADMIN')) {
            if ($fn) {
                $user->setFirstname($fn);
            }
            if ($fl) {
                $user->setLastname($fl);
            }
            if ($fe) {
                $user->setEmail($fe);
            }
        }

        $this->em->persist($user);
        $this->em->flush();
        return $this->view($user);


        // "put_user"
    }

    public function deleteUserAction(User $user)
    {
        if ( $this->isGranted('ROLE_ADMIN')) {
            $articles = $user->getArticles();
            foreach ($articles as $article) {
                $article->setUser(null);
            }

            $this->em->remove($user);
            $this->em->flush();
        }
        // "delete_user"
    }
}
