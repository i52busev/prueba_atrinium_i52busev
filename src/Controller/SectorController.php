<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Entity\Empresa;
use App\Form\SectorType;
use App\Form\EliminarSectorType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SectorController extends AbstractController
{
    #[Route('/sector', name: 'sector')]
    public function index(): Response
    {
        return $this->render('sector/index.html.twig', [
            'controller_name' => 'SectorController',
        ]);
    }

    //Mostrar el listado con la información de los sectores
    #[Route('/sector/listado', name: 'listadoSectores')]
    public function listadoSectores(PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT sectores FROM App:Sector sectores";
        $query = $em->createQuery($dql);

        $sectores = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('sector/listadoSectores.html.twig', [
            'controller_name' => 'ListadoSectores',
            'sectores' => $sectores
        ]);
    }

    //Registrar un sector
    #[Route('/sector/registrar', name: 'registrarSector')]
    public function registrarSector(Request $request): Response
    {
        $sector = new Sector();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SectorType::class, $sector);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            //Se comprueba que no existe un sector con el mismo nombre
            $nombreSector = $sector->getNombre();
            $existeSector = $em->getRepository(Sector::class)->findOneBy(array('nombre' => $nombreSector));
            if(!$existeSector) {

                //Se guarda el sector en la base de datos
                $em->persist($sector);
                $em->flush();

                return $this->redirectToRoute(route: 'listadoSectores');
            }
            else {
                return $this->render('sector/errorMismoNombre.html.twig', [
                    'controller_name' => 'RegistrarSector',
                ]);
            }
        }

        return $this->render('sector/registrarSector.html.twig', [
            'controller_name' => 'RegistrarSector',
            'formulario' => $form->createView(),
        ]);
    }

    //Modificar un sector
    #[Route('/sector/modificar/{id}', name: 'modificarSector')]
    public function modificarSector(Request $request, $id): Response
    {
        $sector = new Sector();
        $em = $this->getDoctrine()->getManager();

        //Buscar el sector a modificar
        $sector = $em->getRepository(Sector::class)->find($id);
        $form = $this->createForm(SectorType::class, $sector);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //Se comprueba que no existe un sector con el mismo nombre
            $nombreSector = $sector->getNombre();
            $existeSector = $em->getRepository(Sector::class)->findOneBy(array('nombre' => $nombreSector));
            if(!$existeSector) {

                //Se actualiza el sector en la base de datos
                $em->persist($sector);
                $em->flush();

                return $this->redirectToRoute(route: 'listadoSectores');
            }
            else {
                return $this->render('sector/errorMismoNombre.html.twig', [
                    'controller_name' => 'RegistrarSector',
                ]);
            }
        }

        return $this->render('sector/modificarSector.html.twig', [
            'controller_name' => 'ModificarSector',
            'formulario' => $form->createView(),
        ]);
    }

    //Eliminar un sector
    #[Route('/sector/eliminar/{id}', name: 'eliminarSector')]
    public function eliminarSector(Request $request, $id): Response
    {
        $sector = new Sector();
        $em = $this->getDoctrine()->getManager();

        //Buscar el sector a eliminar
        $sector = $em->getRepository(Sector::class)->find($id);
        $form = $this->createForm(EliminarSectorType::class, $sector);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            //Comprobar que el sector no está siendo utilizado por ninguna empresa
            $empresa = $em->getRepository(Empresa::class)->findOneBy(array('sector' => $sector));
            if (!$empresa) {

                //Se elimina el sector de la base de datos
                $em->remove($sector);
                $em->flush();

                return $this->redirectToRoute(route: 'listadoSectores');
            }
            else {
                return $this->render('sector/errorSectorUso.html.twig', [
                    'controller_name' => 'EliminarSector',
                ]);
            }
        }

        return $this->render('sector/eliminarSector.html.twig', [
            'controller_name' => 'EliminarSector',
            'formulario' => $form->createView(),
        ]);
    }
}
