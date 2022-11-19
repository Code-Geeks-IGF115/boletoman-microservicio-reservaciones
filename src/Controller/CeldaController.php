<?php

namespace App\Controller;

use App\Entity\Butaca;
use App\Entity\CategoriaButaca;
use App\Entity\Celda;
use App\Entity\Disponibilidad;
use App\Form\CeldaType;
use App\Repository\ButacaRepository;
use App\Repository\CeldaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use App\Repository\CategoriaButacaRepository;
use App\Repository\DisponibilidadRepository;
use App\Service\ResponseHelper;
use Exception;
use Nelmio\CorsBundle;

#[Route('/celda')]
class CeldaController extends AbstractController
{
    private ResponseHelper $responseHelper;


    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    #[Route('/', name: 'app_celda_index', methods: ['GET'])]
    public function index(CeldaRepository $celdaRepository): Response
    {
        return $this->render('celda/index.html.twig', [
            'celdas' => $celdaRepository->findAll(),
        ]);
    }

    /*#[Route('/new', name: 'app_celda_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CeldaRepository $celdaRepository): Response
    {
        $celda = new Celda();
        $form = $this->createForm(CeldaType::class, $celda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $celdaRepository->save($celda, true);

            return $this->redirectToRoute('app_celda_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('celda/new.html.twig', [
            'celda' => $celda,
            'form' => $form,
        ]);
    }*/

    #[Route('/{id}', name: 'app_celda_show', methods: ['GET'])]
    public function show(Celda $celda): Response
    {
        return $this->render('celda/show.html.twig', [
            'celda' => $celda,
        ]);
    }

    #[Route('/{idCategoria}/{idSalaDeEventos}/{idEvento}/new', name: 'asignar_categoria_a_celda', methods: ['POST'])]
    public function asignarCategoriaACeldas(
        Request $request, CategoriaButacaRepository $categoriaButacaRepository,
        CeldaRepository $celdaRepository, ButacaRepository $butacaRepository, 
        $idCategoria, $idSalaDeEventos, $idEvento, 
        DisponibilidadRepository $disponibilidadRepository): JsonResponse {

        //recuperando la categoria butaca
        $categoriaButaca = $categoriaButacaRepository->find($idCategoria);
        if ($categoriaButaca == null) { //verifica si el id ingresado existe 
            return $this->responseHelper->responseMessage("Categoría no existe");
        }
        $salaDeEvento = $categoriaButaca->getSalaDeEventos();

        //almacenando json request en array p
        $celdas = [];
        if ($request->getContent()) {
            $celdas = json_decode($request->getContent(), true);
        }
        $estado = ["Disponible", "Bloqueado"];//estados de disponibilidad
        $contadorCeldasModificadas =0;
            
        //consulta que butacas estan disponibles y sin evento para eliminarlas
        $consultaButacasVacias = $disponibilidadRepository->findBy(['disponible' => $estado[0], 'idEvento' => null]);
        $contadorConsultaButacasVacias = count($consultaButacasVacias);
            
        //si hay butacas disponibles sin evento, se eliminan
        if ($contadorConsultaButacasVacias > 0) {
            foreach ($consultaButacasVacias as $key) {
                    $disponibilidadRepository->remove($consultaButacasVacias[$key], true);
                    $butacaRepository->remove($consultaButacasVacias[$key]->getButaca(), true);
            }
        }

        foreach ($celdas["celdas"] as $key => $celda) {
            $consultaCelda = $celdaRepository->findOneBy(['salaDeEventos' => $salaDeEvento, 
            'fila' => $celda["fila"], 'columna' => $celda["columna"]]);

            if ($consultaCelda != null) {
                $butacasACrear = 0;
                $consultaCelda->setCantidadButacas($celda["cantidadButacas"]);
                $consultaCelda->setCategoriaButaca($categoriaButaca);
                    
                $variable=array(null);
                //se cuenta cuantas butacas hay de la celda 
                $butacasCelda = $butacaRepository->findBy(['celda' =>$consultaCelda->getId()]);
                foreach ($butacasCelda as $key => $value) {
                        
                    $variable[] = $butacasCelda[$key]->getId();
                }
                //sabiendo las butacas de la celda, se filtran por el evento seleccionado y disponibles
                $butacaDisponibilidad = $disponibilidadRepository->findByEstado($idEvento, $estado[0], $variable);
                $cantidadButacas = count($butacaDisponibilidad);

                //se hace una resta a las butacas que pide el json request para saber cuantas butacas necesita crear o quitar
                $butacasACrear = $celda["cantidadButacas"] - $cantidadButacas;
            
                //si hay mas butacas creadas de las que pide el json request, se eliminan las sobrantes
                if ($butacasACrear < 0) {
                       
                    for ($i=0; $i < ($butacasACrear*-1); $i++) {
                                        
                        $disponibilidadRepository->remove($butacaDisponibilidad[$cantidadButacas-($i+1)], true);
                        $butacaRepository->remove($butacaDisponibilidad[$cantidadButacas-($i+1)]->getButaca(), true);
                    }           
                }
                //si hay menos butacas creadas de las que pide el json request, se crean las que faltan
                elseif ($butacasACrear > 0) {
                    //crear disponibilidades y butacas
                    for ($i=0; $i < $butacasACrear; $i++) { 

                        $newButaca = new Butaca();
                        $newButaca->setCodigoButaca(strval(($i+1+$cantidadButacas).":".$categoriaButaca->getCodigo()));
                        $newButaca->setCelda($consultaCelda);
        
                        $newDisponibilidad = new Disponibilidad();
                        $newDisponibilidad->setButaca($newButaca);
                        $newDisponibilidad->setDisponible($estado[0]);
                        $newDisponibilidad->setIdEvento($idEvento);
        
                        $butacaRepository->save($newButaca, true);
                        $disponibilidadRepository->save($newDisponibilidad, true);         
                    }
                }       
                $celdaRepository->save($consultaCelda, true);
                $contadorCeldasModificadas++;
            }          
        }  
        return $this->responseHelper->responsedatos(['message' =>"celdas modificadas: " . strval($contadorCeldasModificadas),
                'celdasModificadas' => $contadorCeldasModificadas]);
    }

    #[Route('/{id}/edit', name: 'app_celda_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Celda $celda, CeldaRepository $celdaRepository): Response
    {
        $form = $this->createForm(CeldaType::class, $celda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $celdaRepository->save($celda, true);

            return $this->redirectToRoute('app_celda_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('celda/edit.html.twig', [
            'celda' => $celda,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_celda_delete', methods: ['POST'])]
    public function delete(Request $request, Celda $celda, CeldaRepository $celdaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $celda->getId(), $request->request->get('_token'))) {
            $celdaRepository->remove($celda, true);
        }
        return $this->redirectToRoute('app_celda_index', [], Response::HTTP_SEE_OTHER);
    }
}
