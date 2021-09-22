<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Form\EmpresaType;
use App\Form\EliminarEmpresaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmpresaController extends AbstractController
{
    #[Route('/empresa', name: 'empresa')]
    public function index(): Response
    {
        return $this->render('empresa/index.html.twig', [
            'controller_name' => 'EmpresaController',
        ]);
    }

    //Mostrar el listado con la información de las empresas
    #[Route('/empresa/listado', name: 'listadoEmpresas')]
    public function listadoEmpresas(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $empresas = "";

        $empresas = $em->getRepository(Empresa::class)->findBy(array(), array('nombre' => 'ASC'));

        return $this->render('empresa/listadoEmpresas.html.twig', [
            'controller_name' => 'ListadoEmpresas',
            'empresas' => $empresas
        ]);
    }

    //Registrar una empresa
    #[Route('/empresa/registrar', name: 'registrarEmpresa')]
    public function registrarEmpresa(Request $request): Response
    {
        $empresa = new Empresa();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(EmpresaType::class, $empresa);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            //Se guarda la empresa en la base de datos
            $em->persist($empresa);
            $em->flush();

            return $this->redirectToRoute(route: 'listadoEmpresas');
        }

        return $this->render('empresa/registrarEmpresa.html.twig', [
            'controller_name' => 'RegistrarEmpresa',
            'formulario' => $form->createView(),
        ]);
    }

    //Modificar una empresa
    #[Route('/empresa/modificar/{id}', name: 'modificarEmpresa')]
    public function modificarEmpresa(Request $request, $id): Response
    {
        $empresa = new Empresa();
        $em = $this->getDoctrine()->getManager();

        //Buscar la empresa a modificar
        $empresa = $em->getRepository(Empresa::class)->find($id);
        $form = $this->createForm(EmpresaType::class, $empresa);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //Se actualiza la empresa en la base de datos
            $em->persist($empresa);
            $em->flush();

            return $this->redirectToRoute(route: 'listadoEmpresas');
        }

        return $this->render('empresa/modificarEmpresa.html.twig', [
            'controller_name' => 'ModificarEmpresa',
            'formulario' => $form->createView(),
        ]);
    }

    //Eliminar una empresa
    #[Route('/empresa/eliminar/{id}', name: 'eliminarEmpresa')]
    public function eliminarEmpresa(Request $request): Response
    {
        $empresa = new Empresa();
        $em = $this->getDoctrine()->getManager();

        //Buscar la empresa a eliminar
        $empresa = $em->getRepository(Empresa::class)->find($id);
        $form = $this->createForm(EliminarEmpresaType::class, $empresa);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //Se elimina la empresa de la base de datos
            $em->remove($empresa);
            $em->flush();

            return $this->redirectToRoute(route: 'listadoEmpresas');
        }

        return $this->render('empresa/eliminarEmpresa.html.twig', [
            'controller_name' => 'EliminarEmpresa',
            'formulario' => $form->createView(),
        ]);
    }
}
