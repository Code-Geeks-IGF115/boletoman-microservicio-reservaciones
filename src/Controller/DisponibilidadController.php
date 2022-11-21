<?php

namespace App\Controller;

use App\Entity\CategoriaButaca;
use App\Entity\Disponibilidad;
use App\Repository\CategoriaButacaRepository;
use Symfony\Component\HttpFoundation\{Response,JsonResponse};
use App\Repository\DisponibilidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ResponseHelper;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\Serializer\Encoder\JsonEncode;




 #[Route('/disponibilidad')]
 class DisponibilidadController extends AbstractController
 {
    private ResponseHelper $responseHelper;

    
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
       
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
     public function comprarbutacas(Request $request, DisponibilidadRepository $disponibilidadRepository): Response
     {
        $responsegud = new Response(
            'Fallo',
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );
        $responsebad = new Response(
            'bien',
            Response::HTTP_PRECONDITION_FAILED,
            array('content-type' => 'text/html')
        );
       
        //Definir el estado habilitado para comprar
        $estado="Bloqueado";

        //convertir el request en array
        $parametros = $request->toArray();

        //Obtener los arreglos de disponibilidades
        $disp=$parametros["disponibilidades"];

        //Obtener el numero de arreglos de butacas
        foreach($disp as $diponibilidad)
        {
            $butacas=$diponibilidad["butacas"];

            //Obtener el butaca_id de todos los arreglos butacas del request
            foreach($butacas as $butaca)
            {
                $butacasIDs[]=$butaca;
            }

        }
               
        // trae todas las disponibilidades donde el id del evento, estado disponible e id butaca corresponden
        $disponibilidadesButaca=$disponibilidadRepository->findByEstado($parametros["idEvento"],$estado, $butacasIDs);


        //calcular métricas 
        $cantidadButacasCompradas=count($disponibilidadesButaca);
        $cantidadButacasBuscadas=count($butacasIDs);

        //id de butacas modificadas
        
        if($cantidadButacasBuscadas==$cantidadButacasCompradas){
            //Modifica la disponible a todas las disponibilidades que corresponden
            foreach ($disponibilidadesButaca as $key => $disponibilidadButaca)
            {
                $disponibilidadButaca->setDisponible('No disponible');
                $disponibilidadRepository->save($disponibilidadButaca, true);
                // agregar aqui id butaca al array 
                return $responsegud;
            }
        return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_OK);
        }else{
            return $responsebad;
        }
     }

     //Ver Butacas Vendiads
     #[Route('/butacasVendidas/{idEvento}', name: 'app_butacas_vendidas', methods: ['GET'])]
     public function butacasVendidas($idEvento, CategoriaButacaRepository $categoriaButacaRepository, DisponibilidadRepository $disponibilidadRepository ): JsonResponse{
       
      //Declaracion de variabales
        $estado = 'No Disponible';
        $cantidadButacasCompradas = 0;
        $cantidadTotalButacas=0;
        $precioTotal = 0;

        //Llamada a base de datos de todo lo necesario
        $categorias=$categoriaButacaRepository->findBysalaDeEventos($idEvento);      
        $disponibilidadesButaca=$disponibilidadRepository->findByidEvento($idEvento);
        
        //obtenemos los precios de cada categoria
        foreach($categorias as $categoria){
            $precio=$categoria->getPrecioUnitario();
            $tipoButacas= $categoria->getCodigo();
        } 
      
        //obtenemos cuantas 
        foreach($disponibilidadesButaca as $disp){
            if($disp->getdisponible()==$estado){
                $cantidadButacasCompradas++;
            }   
            $cantidadTotalButacas++;
        }

        $precioTotal = $cantidadButacasCompradas*intval($precio);

        $data = [
            'idEvento'=>$idEvento,
            'cantidadButacasTotal'=>$cantidadTotalButacas,
            'tipoButaca'=>$tipoButacas,
            'cantidadButacasCompradas'=>$cantidadButacasCompradas,
            'precioTotal' => $precioTotal,
            'precioUnitario'=>intval($precio)
        ];

        $resultado=$disponibilidadRepository->calcularIngresosPorCategoriaButaca($idEvento, $estado);

        // return $this->responseHelper->responseDatos($resultado);

        /*$estado = 'No Disponible';
        $categoriaButacas=$disponibilidadRepository->calcularIngresosPorCategoriaButaca($idEvento,$estado);
*/
        return $this->responseHelper->responseDatos($resultado);
                
     }

     #[Route('/mis/boletos', name: 'mis_boletos', methods: ['POST'])]
    public function buscarCompras(Request $request): JsonResponse
    {
        $mensaje="Hola Mundo!";
        $parametrosDetalleCompra = $request->toArray();
        //var_dump($parametrosDetalleCompra);
        
        /*try{
            // recibiendo parametros
            //SOY SERVIDOR
            //$parametros=$request->toArray(); 
            //$miNombre=$parametros["nombreCompleto"];
            // contruyendo cliente - AGREGACIÓN - TAMBIÉN SOY CLIENTE
            $response = $this->client->request(
                'POST', 
                'https://boletoman-reservaciones.herokuapp.com/', [
                // defining data using an array of parameters
                'json' => ['miNombre' => $idCompra],
            ]);
            $resultadosDeConsulta=$response->toArray();
            $mensaje=$resultadosDeConsulta["message"];
        }catch(Exception $e){
            return $this->responseHelper->responseDatosNoValidos($mensaje);  
        }*/

        return $this->responseHelper->responseDatos($parametrosDetalleCompra, ['ver_boletos']);     
    }
     
}
