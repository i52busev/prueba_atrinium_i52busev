<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Entity\Cliente;
use App\Entity\Empresa;
use App\Entity\ClienteSector;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClienteController extends AbstractController
{
    #[Route('/cliente', name: 'cliente')]
    public function index(): Response
    {
        return $this->render('cliente/index.html.twig', [
            'controller_name' => 'ClienteController',
        ]);
    }

    //Mostrar el listado con la información de los sectores
    #[Route('/cliente/listadoSectores', name: 'listadoSectoresCli')]
    public function listadoSectores(PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        //Vínculos del usuario actual
        $cliente = $em->getRepository(Cliente::class)->findOneBy(array('id_usuario' => $this->getUser()->getId()));
        $vinculos = $em->getRepository(ClienteSector::class)->findBy(array('id_cliente' => $cliente->getId()));

        //Sectores vinculados al usuario actual
        $sectores = array();
        for($i = 0; $i < sizeof($vinculos); $i++) {
           $aux = $em->getRepository(Sector::class)->findBy(array('id' => $vinculos[$i]->getIdSector()));
           if($aux !== null) {
               for ($j = 0; $j < sizeof($aux); $j++) {
                   array_push($sectores,$aux[$j]);
               }
           }
        }

        return $this->render('cliente/listadoSectores.html.twig', [
            'controller_name' => 'ListadoSectores',
            'sectores' => $sectores
        ]);
    }

    //Mostrar el listado con la información de las empresas
    #[Route('/cliente/listadoEmpresas', name: 'listadoEmpresasCli')]
    public function listadoEmpresas(PaginatorInterface $paginator, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        //Vínculos del usuario actual
        $cliente = $em->getRepository(Cliente::class)->findOneBy(array('id_usuario' => $this->getUser()->getId()));
        $vinculos = $em->getRepository(ClienteSector::class)->findBy(array('id_cliente' => $cliente->getId()));

        //Sectores vinculados al usuario actual
        $sectores = array();
        for($i = 0; $i < sizeof($vinculos); $i++) {
           $aux = $em->getRepository(Sector::class)->findBy(array('id' => $vinculos[$i]->getIdSector()));
           if($aux !== null) {
               for ($j = 0; $j < sizeof($aux); $j++) {
                   array_push($sectores,$aux[$j]);
               }
           }
        }

        //Empresas de los sectores vinculados al usuario actual
        $empresas = array();
        for($i = 0; $i < sizeof($sectores); $i++) {
            $auxEmpresas = $em->getRepository(Empresa::class)->findBy(array('sector' => $sectores[$i]->getId()));
            if($aux !== null) {
                for ($j = 0; $j < sizeof($auxEmpresas); $j++) {
                    array_push($empresas,$auxEmpresas[$j]);
                }
            }
        }

        return $this->render('cliente/listadoEmpresas.html.twig', [
            'controller_name' => 'ListadoEmpresas',
            'empresas' => $empresas
        ]);
    }
}
