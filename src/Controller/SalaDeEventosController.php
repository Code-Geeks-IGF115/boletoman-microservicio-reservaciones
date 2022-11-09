<?php

namespace App\Controller;

use App\Entity\{CategoriaButaca, SalaDeEventos, Celda, Disponibilidad, Butaca};
use App\Form\SalaDeEventosType;
use App\Repository\ButacaRepository;
use App\Repository\CategoriaButacaRepository;
use App\Repository\CeldaRepository;
use App\Repository\DisponibilidadRepository;
use App\Repository\SalaDeEventosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\ResponseHelper;
use ContainerE64h0Px\getCeldaService;
use Exception;

#[Route('/sala/de/eventos')]
class SalaDeEventosController extends AbstractController
{
    private ResponseHelper $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper=$responseHelper;
    }
    /** Tarea: Función verSalasDeEventos
    * Nombre: Roman Mauricio Hernández Beltrán
    * Carnet: HB21009
    * Fecha de Revisión: 10/10/2022
    * Fecha de Aprobación: 10/10/2022
    * Revisión: Andrea Melissa Monterrosa Morales
    */
    #[Route('/', name: 'app_sala_de_eventos_index', methods: ['GET'])]
    public function index(SalaDeEventosRepository $salaDeEventosRepository): JsonResponse
    {
        $salaDeEvento=$salaDeEventosRepository->findAll();
        return $this->responseHelper->responseDatos(['salas'=>$salaDeEvento],['ver_salas_de_eventos']);
    }

      /** Tarea: Función crearSalaDeEventos
     * Nombre: Carlos Josué Argueta Alvarado
     * Carnet: AA20099
     * Estado: Aprobado
     * Fecha de Revisión: 10/10/2022
     * Fecha de Aprobación: 10/10/2022
     * Revisión: Andrea Melissa Monterrosa Morales
     */
    #[Route('/new', name: 'app_sala_de_eventos_new', methods: ['POST'])]
    public function new(Request $request, 
    SalaDeEventosRepository $salaDeEventosRepository,
    CeldaRepository $celdaRepository): JsonResponse
    {  
        $parametros=$request->toArray(); 
        $request->request->replace(["sala_de_eventos"=>$parametros]);
        $salaDeEvento = new SalaDeEventos();
        $form = $this->createForm(SalaDeEventosType::class, $salaDeEvento);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                //para guardar en base de datos
                $salaDeEventosRepository->save($salaDeEvento, true);
                
                //crear celdas
                for ($fila=1; $fila <= $salaDeEvento->getFilas() ; $fila++) { 
                    for ($columna=1; $columna <= $salaDeEvento->getColumnas() ; $columna++) { 
                        $celda = new Celda();
                        $celda->setFila($fila);
                        $celda->setColumna($columna);
                        $celda->setSalaDeEventos($salaDeEvento);
                        //$salaDeEvento->addCelda($celda);
                        $celdaRepository->save($celda,true);
                        
                    }
                }

                return $this->responseHelper->responseDatos(["message"=>"Sala de Eventos Guardada.", "id"=>$salaDeEvento->getId()]);
        }       
        else{
            return $this->responseHelper->responseDatosNoValidos();
            
        } 

    }  
    
    

    /** Tarea: Función verSalaDeEventos
     * Nombre: Carlos Josué Argueta Alvarado
     * Carnet: AA20099
     * Nombre: Roman Mauricio Hernández Beltrán
     * Carnet: HB21009
     * Estado: Aprobado
     * Fecha de Revisión: 10/10/2022
     * Fecha de Aprobación: 10/10/2022
     * Revisión: Andrea Melissa Monterrosa Morales
     */
    #[Route('/{idSalaDeEventos}/{idEvento}/{idDetalleCompra}', name: 'app_sala_de_eventos_show', methods: ['GET'])]
    public function show(SalaDeEventos $salaDeEvento = null, ButacaRepository $butacaRepository,
    SalaDeEventosRepository $salaDeEventosRepository, $idSalaDeEventos, $idEvento, $idDetalleCompra,
    DisponibilidadRepository $disponibilidadRepository, CeldaRepository $celdaRepository): JsonResponse
    {
        $opcion = ["Desbloqueado", "Bloqueado"];

        
        $salaDeEvento = $salaDeEventosRepository->find($idSalaDeEventos);
        if(!$salaDeEvento){
            return $this->responseHelper->responseMessage("Sala de eventos no existe.");
        }else{
            $celdas = $celdaRepository->findBy(['categoriaButaca' => $salaDeEvento->getCategoriaButacas()[0]->getId()]);
            $butaca = $butacaRepository->findAll();
            foreach ($salaDeEvento->getCategoriaButacas() as $key => $value) {
                $disponibilidad = new Disponibilidad();
                $disponibilidad->setButaca($butaca[$key]);
                $disponibilidad->setDisponible($opcion[0]);
                $disponibilidad->setIdEvento($idEvento);
                $disponibilidad->setIdDetalleCompra($idDetalleCompra);
                
                $disponibilidadRepository->save($disponibilidad, true);

            }
            foreach ($celdas as $key => $value) {
                $salaDeEvento->addCelda($celdas[$key]);
            }
            

            return $this->responseHelper->responseDatos([['salaDeEvento'=>
            $salaDeEvento], ['Disponibilidad' => "Disponibilidad Creadas"]], ['ver_evento']);
        }        
    }
    
    
    /** Tarea: Función editarSalaDeEventos
     * Nombre: Carlos Josué Argueta Alvarado
     * Carnet: AA20099
     * Estado: Aprobado
     * Fecha de Aprobación: 11/10/2022
     * fecha de ultima modificacion : 11/10/2022
     * Fecha de Revisión: 10/10/2022
     * Revisión: Andrea Melissa Monterrosa Morales
     */
    #[Route('/{id}/edit', name: 'app_sala_de_eventos_edit', methods: ['POST'])]
    public function edit(Request $request, SalaDeEventos $salaDeEvento = null, 
    SalaDeEventosRepository $salaDeEventosRepository): JsonResponse
    {
        if(empty($salaDeEvento)){
            return $this->responseHelper->responseDatosNoValidos(); 
        }
        else{
 
            $parametros=$request->toArray(); 
            $request->request->replace(["sala_de_eventos"=>$parametros]);
            $form = $this->createForm(SalaDeEventosType::class, $salaDeEvento);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $salaDeEventosRepository->save($salaDeEvento, true);
                return $this->responseHelper->responseMessage("Sala de Eventos se modificó con exito.");
            }
            else{
                return $this->responseHelper->responseDatosNoValidos();
            }
        } 
    }


    #[Route('/{id}', name: 'app_sala_de_eventos_delete', methods: ['POST'])]
    public function delete(Request $request, SalaDeEventos $salaDeEvento = null, SalaDeEventosRepository $salaDeEventosRepository): Response
    {
        try{
            if(!$salaDeEvento){
                throw new Exception('Sala de eventos no existe.');
            }
            $salaDeEventosRepository->remove($salaDeEvento, true);
            return $this->responseHelper->responseMessage("Sala de Eventos Eliminada.");
        }catch(Exception $e){
            return $this->responseHelper->responseDatosNoValidos($e->getMessage());
        }
    }
}
