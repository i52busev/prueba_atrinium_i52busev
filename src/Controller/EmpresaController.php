<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Form\EmpresaType;
use App\Form\EliminarEmpresaType;
use Knp\Component\Pager\PaginatorInterface;
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

    #[Route('/empresa/empresaBuscar', name: 'empresaBuscar')]
    public function empresaBuscar(Request $request): Response
    {
        $empresas = "";

        return $this->render('empresa/buscarEmpresa.html.twig', [
            'controller_name' => 'BuscarEmpresa',
            'empresas' => $empresas
        ]);
    }

    //Búsqueda de una empresa
    #[Route('/empresa/buscar', name: 'buscarEmpresa')]
    public function buscarEmpresa(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $empresas = "";
        $empresasTemp = "";

        //Parámetros de búsqueda
        $nombre = $request->request->get('nombre');
        $sector = $request->request->get('sector');

        if(!empty($nombre) && !empty($sector)) {
            //Buscar empresa por nombre y sector
            /*$empresasTemp = $em->getRepository(Empresa::class)->createQueryBuilder('e')
                ->where('e.nombre LIKE :nombre')
                ->andWhere('e.sector LIKE :sector')
                ->orderBy('e.nombre', 'ASC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('sector',$sector)
                ->getQuery()
                ->getResult();*/
            $query = $em->getRepository(Empresa::class)->createQueryBuilder('e')
                ->where('e.nombre LIKE :nombre')
                ->andWhere('e.sector LIKE :sector')
                ->orderBy('e.nombre', 'ASC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->setParameter('sector',$sector)
                ->getQuery();
        }

        elseif(!empty($nombre) && empty($sector)) {
            //Buscar empresa por nombre
            /*$empresasTemp = $em->getRepository(Empresa::class)->createQueryBuilder('e')
                ->where('e.nombre LIKE :nombre')
                ->orderBy('e.nombre', 'ASC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->getQuery()
                ->getResult();*/
            $query = $em->getRepository(Empresa::class)->createQueryBuilder('e')
                ->where('e.nombre LIKE :nombre')
                ->orderBy('e.nombre', 'ASC')
                ->setParameter('nombre','%'.$nombre.'%')
                ->getQuery();
        }

        elseif(empty($nombre) && !empty($sector)) {
            //Buscar empresa por sector
            /*$empresasTemp = $em->getRepository(Empresa::class)->createQueryBuilder('e')
                ->where('e.sector LIKE :sector')
                ->orderBy('e.nombre', 'ASC')
                ->setParameter('sector',$sector)
                ->getQuery()
                ->getResult();*/
            $query = $em->getRepository(Empresa::class)->createQueryBuilder('e')
                ->where('e.sector LIKE :sector')
                ->orderBy('e.nombre', 'ASC')
                ->setParameter('sector',$sector)
                ->getQuery();
        }

        else {
            //Buscar todas las empresas
            $dql = "SELECT empresas FROM App:Empresa empresas";
            $query = $em->createQuery($dql);
        }

        $empresasTemp = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        $empresas = $empresasTemp;

        return $this->render('empresa/listadoEmpresas.html.twig', [
            'controller_name' => 'BuscarEmpresa',
            'empresas' => $empresas
        ]);
    }

    //Mostrar el listado con la información de las empresas
    #[Route('/empresa/listado', name: 'listadoEmpresas')]
    public function listadoEmpresas(PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT empresas FROM App:Empresa empresas";
        $query = $em->createQuery($dql);

        $empresas = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

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
