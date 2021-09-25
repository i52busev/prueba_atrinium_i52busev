<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Sector;
use App\Form\UserType;
use App\Entity\Cliente;
use App\Form\AdminType;
use App\Form\ClienteType;
use App\Entity\ClienteSector;
use App\Form\EliminarUserType;
use App\Form\ClienteSectorType;
use App\Form\ModificarUserType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/listadoUsuarios', name: 'listadoUsuarios')]
    public function listadoUsuarios(PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT usuarios FROM App:User usuarios";
        $query = $em->createQuery($dql);

        $usuarios = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('admin/listadoUsuarios.html.twig', [
            'controller_name' => 'ListadoUsuarios',
            'usuarios' => $usuarios
        ]);
    }

    #[Route('admin/registroUsuario', name: 'registroUsuario')]
    public function registroUsuario(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $cliente = new Cliente();
        $admin = new Admin();
        $form = $this->createForm(UserType::class, $user);
        $formC = $this->createForm(ClienteType::class, $cliente);
        $formA = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            //Se codifica la contraseña
            $user->setPassword($passwordEncoder->encodePassword($user,$form['password']->getData()));

            //Se guarda el user en la base de datos
            $em->persist($user);
            $em->flush();

            //Se crea la tupla del tipo de user según el tipo elegido en el formulario
            if ($user->esCliente()) {
                $formC->handleRequest($request);
                $em = $this->getDoctrine()->getManager();
                $cliente->setIdusuario($user);
                $em->persist($cliente);
                $em->flush();
            }

            if ($user->esAdmin()) {
                $formA->handleRequest($request);
                $em = $this->getDoctrine()->getManager();
                $admin->setIdusuario($user);
                $em->persist($admin);
                $em->flush();
            }

            return $this->redirectToRoute(route: 'listadoUsuarios');
        }

        return $this->render('admin/registroUsuario.html.twig', [
            'controller_name' => 'RegistroUsuario',
            'formulario' => $form->createView(),
            'formC' => $formC->createView(),
            'formA' => $formA->createView()
        ]);
    }

    #[Route('/admin/usuario/modificar/{id}', name: 'modificarUsuario')]
    public function modificarUsuario(Request $request, $id): Response
    {
        $usuario = new User();
        $em = $this->getDoctrine()->getManager();

        //Buscar el usuario a modificar
        $usuario = $em->getRepository(User::class)->find($id);
        $form = $this->createForm(ModificarUserType::class, $usuario);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //Se codifica la contraseña
            $user->setPassword($passwordEncoder->encodePassword($user,$form['password']->getData()));

            //Se actualiza el usuario en la base de datos
            $em->persist($usuario);
            $em->flush();

            return $this->redirectToRoute(route: 'listadoUsuarios');
        }

        return $this->render('admin/modificarUsuario.html.twig', [
            'controller_name' => 'ModificarUsuario',
            'formulario' => $form->createView()
        ]);
    }

    #[Route('/admin/usuario/eliminar/{id}', name: 'eliminarUsuario')]
    public function eliminarUsuario(Request $request, $id): Response
    {
        $usuario = new User();
        $em = $this->getDoctrine()->getManager();

        //Buscar el usuario a eliminar
        $usuario = $em->getRepository(User::class)->find($id);
        $form = $this->createForm(EliminarUserType::class, $usuario);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //Se elimina la tupla del tipo de usuario
            if ($usuario->esCliente()) {
                $cliente = new Cliente();
                $cliente = $em->getRepository(Cliente::class)->findOneBy(array('id_usuario' => $id));

                //Eliminar la tupla del cliente
                $em->remove($cliente);
                $em->flush();
            }

            if ($usuario->esAdmin()) {
                $administrador = new Admin();
                $administrador = $em->getRepository(Admin::class)->findOneBy(array('id_usuario' => $id));

                //Eliminar la tupla del administrador
                $em->remove($administrador);
                $em->flush();
            }

            //Se elimina el usuario de la base de datos
            $em->remove($usuario);
            $em->flush();

            return $this->redirectToRoute(route: 'listadoUsuarios');
        }

        return $this->render('admin/eliminarUsuario.html.twig', [
            'controller_name' => 'EliminarUsuario',
            'formulario' => $form->createView()
        ]);
    }

    #[Route('/admin/usuario/vincular/{id}', name: 'vincularUsuario')]
    public function vincularUsuario(Request $request, $id): Response
    {
        $vinculo = new ClienteSector();
        $em = $this->getDoctrine()->getManager();

        //Datos del cliente
        $cliente = $em->getRepository(Cliente::class)->findOneBy(array('id_usuario' => $id));
        $vinculo->setIdCliente($cliente);

        $form = $this->createForm(ClienteSectorType::class, $vinculo);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //Si no existe el vínculo entre el cliente y el sector, se guarda en la base de datos
            $hayVinculo = $em->getRepository(ClienteSector::class)->findOneBy(array('id_cliente' => $cliente->getId(), 'id_sector' => $vinculo->getIdSector()));
            if ($hayVinculo === null) {
                $em->persist($vinculo);
                $em->flush();
            }
            return $this->redirectToRoute(route: 'listadoUsuarios');
        }

        return $this->render('admin/vincularUsuario.html.twig', [
            'controller_name' => 'VincularUsuario',
            'formulario' => $form->createView()
        ]);
    }
}
