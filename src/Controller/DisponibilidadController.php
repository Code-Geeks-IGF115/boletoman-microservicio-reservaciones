<?php

namespace App\Controller;
use App\Entity\Disponibilidad;
use Symfony\Component\HttpFoundation\{Response,JsonResponse};
use App\Repository\DisponibilidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ResponseHelper;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/disponibilidad')]
class DisponibilidadController extends AbstractController
{
    private ResponseHelper $responseHelper;
    private HttpClientInterface $client;

    public function __construct(ResponseHelper $responseHelper,HttpClientInterface $client)
    {
        $this->responseHelper=$responseHelper;
        $this->client = $client;
    }

     #[Route('/', name: 'app_disponibilidad_index', methods: ['GET'])]
      public function index(DisponibilidadRepository $disponibilidadRepository): Response
      {
          return $this->render('disponibilidad/index.html.twig', [
              'disponibilidads' => $disponibilidadRepository->findAll(),
          ]);
      }

//      #[Route('/new', name: 'app_disponibilidad_new', methods: ['GET', 'POST'])]
//      public function new(Request $request, DisponibilidadRepository $disponibilidadRepository): Response
//      {
//          $disponibilidad = new Disponibilidad();
//          $form = $this->createForm(DisponibilidadType::class, $disponibilidad);
//          $form->handleRequest($request);

//          if ($form->isSubmitted() && $form->isValid()) {
//              $disponibilidadRepository->save($disponibilidad, true);

//              return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
//          }

//          return $this->renderForm('disponibilidad/new.html.twig', [
//              'disponibilidad' => $disponibilidad,
//              'form' => $form,
//          ]);
//      }

//      #[Route('/{id}', name: 'app_disponibilidad_show', methods: ['GET'])]
//      public function show(Disponibilidad $disponibilidad): Response
//      {
//          return $this->render('disponibilidad/show.html.twig', [
//              'disponibilidad' => $disponibilidad,
//          ]);
//      }

//      #[Route('/{id}/edit', name: 'app_disponibilidad_edit', methods: ['GET', 'POST'])]
//      public function edit(Request $request, Disponibilidad $disponibilidad, DisponibilidadRepository $disponibilidadRepository): Response
//      {
//          $form = $this->createForm(DisponibilidadType::class, $disponibilidad);
//          $form->handleRequest($request);

//          if ($form->isSubmitted() && $form->isValid()) {
//              $disponibilidadRepository->save($disponibilidad, true);

//              return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
//          }

//          return $this->renderForm('disponibilidad/edit.html.twig', [
//              'disponibilidad' => $disponibilidad,
//              'form' => $form,
//          ]);
//      }

//      #[Route('/{id}', name: 'app_disponibilidad_delete', methods: ['POST'])]
//      public function delete(Request $request, Disponibilidad $disponibilidad, DisponibilidadRepository $disponibilidadRepository): Response
//      {
//          if ($this->isCsrfTokenValid('delete'.$disponibilidad->getId(), $request->request->get('_token'))) {
//              $disponibilidadRepository->remove($disponibilidad, true);
//          }

//          return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
//      }

    #[Route('/bloquearbutacas', name: 'app_disponibilidad_bloquear_butacas',  methods: ['POST'])]
    public function bloquearbutacas(Request $request, DisponibilidadRepository $disponibilidadRepository): JsonResponse
    {
        $estado="Disponible";
        $parametros = $request->toArray();
        $butacasIDs=$parametros["butacas"];
        // trae todas las disponibilidades donde el id del evento, estado disponible e id butaca corresponden
        $disponibilidadesButaca=$disponibilidadRepository->findByEstado($parametros["idEvento"],$estado, $butacasIDs);

        foreach ($disponibilidadesButaca as $key => $disponibilidadButaca){
                $disponibilidadButaca->setDisponible('Bloqueado');
                $disponibilidadRepository->save($disponibilidadButaca, true);
                // agregar aqui id butaca al array
        }
        //calcular métricas
        $cantidadButacasBloqueadas=count($disponibilidadesButaca);
        $cantidadButacasBuscadas=count($butacasIDs);
        $cantidadButacasModificadas=$cantidadButacasBuscadas-$cantidadButacasBloqueadas;
        $mensaje=null;
        if($cantidadButacasBuscadas==$cantidadButacasBloqueadas){
            $mensaje='Butacas modificadas con éxito';
        }else{
            $mensaje='Algunas butacas no han sido bloqueadas';
        }
        //agregar array ids de butacas modificadas a la data
        $data=[
            'buscadas'=>$cantidadButacasBuscadas,
            'modificadas'=>$cantidadButacasBloqueadas,
            'no-validas'=>$cantidadButacasModificadas,
            'mensaje'=>$mensaje
        ];
        return $this->responseHelper->responseDatos($data);
    }
    //Recibe en la ruta el parametro idUsuario y consulta
    //al microservicio compras el idDetallesCompras del idUsuario

    #[Route('/mis/eventos', name: 'app_mis_eventos',  methods: ['GET'])]
    public function misEventos(Request $request, DisponibilidadRepository $disponibilidadRepository): JsonResponse
    {

        $comprasResultado=$request->toArray();// recuperando ids detalle compras que envia microservicio compras
        try{
            //buscanto idEventos dado idDetalleCompra
            
            $eventos=$disponibilidadRepository->findEventosByidDetalleCompra($comprasResultado["idsDetallesCompra"]);
            $idEventos=[];
            foreach ($eventos as $key => $evento) {
                $idEventos[]=$evento["idEvento"];
            }
            // dd($idEventos);
            //Consulta a microservicio eventos
            $eventosResultado = $this->client->request(
                'GET', 
                'https://boletoman-eventos.herokuapp.com/evento/mis/eventos',[
                    'json'=>['idEventos' =>$idEventos],
                    'timeout' => 20
                ]
            );
            $resultado=$eventosResultado->toArray()["eventos"];
            return $this->responseHelper->responseDatos(['eventos' =>$resultado]); 
        }catch(Exception $e){
            return $this->responseHelper->responseDatosNoValidos($e->getMessage());  
        }
    }





     #[Route('/desbloquearbutacas', name: 'app_disponibilidad_desbloquear_butacas', methods: ['POST'])]
     public function desbloquearbutacas(Request $request, DisponibilidadRepository $disponibilidadRepository): JsonResponse
     {
        $estado="Bloqueado";
        $parametros = $request->toArray();
        $butacasIDs=$parametros["butacas"];
        // trae todas las disponibilidades donde el id del evento, estado disponible e id butaca corresponden
        $disponibilidadesButaca=$disponibilidadRepository->findByEstado($parametros["idEvento"],$estado, $butacasIDs);
        foreach ($disponibilidadesButaca as $key => $disponibilidadButaca){
                $disponibilidadButaca->setDisponible('Disponible');
                $disponibilidadRepository->save($disponibilidadButaca, true);
                // agregar aqui id butaca al array 
        }
        //calcular métricas
        $cantidadButacasDesbloqueadas=count($disponibilidadesButaca);
        $cantidadButacasBuscadas=count($butacasIDs);
        $cantidadButacasModificadas=$cantidadButacasBuscadas-$cantidadButacasDesbloqueadas;
        $mensaje=null;
        //id de butacas modificadas
        
        if($cantidadButacasBuscadas==$cantidadButacasDesbloqueadas){
            $mensaje='Butacas modificadas con éxito';
        }else{
            $mensaje='Algunas butacas no fueron desbloqueadas';
        }
        //agregar array ids de butacas modificadas a la data
        $data=[
            'buscadas'=>$cantidadButacasBuscadas,
            'modificadas'=>$cantidadButacasDesbloqueadas,
            'no-validas'=>$cantidadButacasModificadas,
            'mensaje'=>$mensaje
        ];
        return $this->responseHelper->responseDatos($data);
     }

     #[Route('/comprarbutacas', name: 'app_disponibilidad_comprar_butacas', methods: ['POST'])]
     public function comprarbutacas(Request $request, DisponibilidadRepository $disponibilidadRepository): JsonResponse
     {
       
        //Definir el estado habilitado para comprar
        $estado="Bloqueado";
        $parametros = $request->toArray();
        $butacasIDs=$parametros["butacas"];
        
        
        // trae todas las disponibilidades donde el id del evento, estado disponible e id butaca corresponden
        $disponibilidadesButaca=$disponibilidadRepository->findByEstado($parametros["idEvento"],$estado, $butacasIDs);
        foreach ($disponibilidadesButaca as $key => $disponibilidadButaca){
                $disponibilidadButaca->setDisponible('No disponible');
                $disponibilidadRepository->save($disponibilidadButaca, true);
                // agregar aqui id butaca al array 
        }
        //calcular métricas
        
        $cantidadButacasCompradas=count($disponibilidadesButaca);
        $cantidadButacasBuscadas=count($butacasIDs);
        $cantidadButacasModificadas=$cantidadButacasBuscadas-$cantidadButacasCompradas;
        $mensaje=null;
        //id de butacas modificadas
        $butacasCompradas=[];
        if($cantidadButacasBuscadas==$cantidadButacasCompradas){
            //Modifica la disponible a todas las disponibilidades que corresponden
            foreach ($disponibilidadesButaca as $key => $disponibilidadButaca)
            {
                $disponibilidadButaca->setDisponible('No disponible');
                $butacasCompradas[]=$disponibilidadButaca;
                $disponibilidadRepository->save($disponibilidadButaca, true);
                // agregar aqui id butaca al array 
            }

            
            return $this->responseHelper->responseDatos(
                [
                'message'=>'Se realizo la compra de butacas.',
                'butacasCompradas'=>$butacasCompradas
                ],
                ['comprar_butacas'],
                Response::HTTP_OK
            );
            }else{
            return $this->responseHelper->responseDatos(
                [
                    'message'=>'No se pudo realizar la compra de todas las butacas butacas.',
                    'butacasCompradas'=>$butacasCompradas
                ],
                ['comprar_butacas'],
                Response::HTTP_PRECONDITION_FAILED,
            );
        }
        //agregar array ids de butacas modificadas a la data
        $data=[
            'buscadas'=>$cantidadButacasBuscadas,
            'compradas'=>$cantidadButacasCompradas,
            'no-validas'=>$cantidadButacasModificadas,
            'mensaje'=>$mensaje
        ];
        return $this->responseHelper->responseDatos($data);
     }

     #[Route('/mis/boletos', name: 'mis_boletos', methods: ['POST'])]
    public function buscarCompras(Request $request, DisponibilidadRepository $disponibilidadRepository): JsonResponse
    {
        $mensaje="Hola Mundo!";
        $variable=[];
        $parametrosDetalleCompra = $request->toArray();
        foreach ($parametrosDetalleCompra as $key) {
            $disponibilidadCompra = $disponibilidadRepository->findOneBy(['idDetalleCompra' => $key]);
            if ($disponibilidadCompra != null) {
                $variable[] = [
                    'id'=>$disponibilidadCompra->getId(),
                    'disponible'=>$disponibilidadCompra->getDisponible(),
                    'idEvento'=>$disponibilidadCompra->getIdEvento(),
                    'idDetalleCompra'=>$disponibilidadCompra->getIdDetalleCompra(),
                    'idButaca' => $disponibilidadCompra->getButaca()->getId(),
                    'codigoButaca'=> $disponibilidadCompra->getButaca()->getCodigoButaca(),
                    'idCelda' => $disponibilidadCompra->getButaca()->getCelda()->getId(),
                    'idCategoriaButaca' => $disponibilidadCompra->getButaca()->getCategoriaButaca()->getId(),
                    'nombreCategoria' => $disponibilidadCompra->getButaca()->getCategoriaButaca()->getNombre()
                ];
            }     
        }
        //codigo para crear pdf
        //$html = this->renderView;        

        return $this->responseHelper->responseDatos($variable);     
    }
     
     #[Route('/butacasVendidas', name: 'app_disponibilidad_comprar_butacas', methods: ['POST'])]
     public function butacasVendidas(Request $request, DisponibilidadRepository $disponibilidadRepository): Response{
        $responsebad = new Response(
            'Fallo',
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );
        return $responsebad;;
     }
//Quiero tener los nombres de categoria butaca, su id, y su detalle compra, basicamente por el idEvento al que pertenecen
     #[Route('/butacas/de/evento/{idEvento}/pdf', name: 'app_disponibilidad_butacas_por_evento', methods: ['GET'])]
     public function butacasPorEvento(Request $request, DisponibilidadRepository $disponibilidadRepository, 
     $idEvento): JsonResponse{      
        $estado="Bloqueado";
        $disponibilidadBuscar=$disponibilidadRepository->findBy(['idEvento'=>$idEvento]); 
        if ($disponibilidadBuscar == null) { //verifica si el id ingresado existe 
            return $this->responseHelper->responseMessage("Evento no existe");
        }
        $disponibilidadDeButaca=$disponibilidadRepository->findByDisponibilidade($idEvento,$estado);
        return $this->responseHelper->responseDatos(['disponibilidad'=>$disponibilidadDeButaca]);
     }
}
