<?php

namespace App\Controller;

use App\Entity\{SalaDeEventos,Celda};
use App\Form\SalaDeEventosType;
use App\Repository\CeldaRepository;
use App\Repository\SalaDeEventosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\ResponseHelper;
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
        return $this->responseHelper->responseDatos(['salas'=>$salaDeEvento],['ver_evento']);
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
                        $celdaRepository->save($celda,true);
                    }
                }

                return $this->responseHelper->responseMessage("Sala de Eventos Guardada.");
        }       
        else{
            return $this->responseHelper->responseDatosNoValidos("Sala de Eventos Guardada.");
            
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
    #[Route('/{id}', name: 'app_sala_de_eventos_show', methods: ['GET'])]
    public function show(SalaDeEventos $salaDeEvento): JsonResponse
    {
        try{
            return $this->responseHelper->responseDatos(['salaDeEvento'=>$salaDeEvento],['ver_evento']);
        }catch(Exception $e){
            return $this->responseHelper->responseDatosNoValidos("No se encontraron datos.");
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
            $form = $this->createForm(SalaDeEventosType::class, $salaDeEvento);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $salaDeEventosRepository->save($salaDeEvento, true);
                return $this->responseHelper->responseMessage("Sala de Eventos se modificó con exito.");
            }
            else{
                return $this->responseHelper->responseMessage("Sala de Eventos se modificó con exito.");
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