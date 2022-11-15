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

        //recuperar todas las celdas de esta sala de eventos
        /*$celdas = $celdaRepository->findBy(['categoriaButaca' => $categoriaButaca]);
        //var_dump($celdas);
        //almacenando json request en array para comparar*/
        $celdas = [];
        if ($request->getContent()) {
            $celdas = json_decode($request->getContent(), true);
        }
        
            $opcion = ["Disponible", "Bloqueado"];//estados de disponibilidad
            $contadorCeldasModificadas =0;
            
            //$result = "celdas no creadas";
            //$consultaDisponibilidad = $disponibilidadRepository->findBy(['idEvento' => $idEvento]);

            $consultaButacas = $butacaRepository->findBy(['disponible' => $opcion[0]], );
            $contadorButacas = 0;
            foreach ($consultaButacas as $key => $value) {
                $consultaDisponibilidad = $disponibilidadRepository->findOneBy(['butaca' => $consultaButacas[$key]]);
                if ($consultaDisponibilidad != null){//verifica si la consulta existe
                    if ($consultaDisponibilidad->getIdEvento() == null ) {
                        //sino tiene ningun evento(null), se elimina la disponibilidad y la butaca                     
                        $disponibilidadRepository->remove($consultaDisponibilidad, true);
                        $butacaRepository->remove($consultaButacas[$key], true);
                    }
                    elseif ($consultaDisponibilidad->getIdEvento() == $idEvento) {
                        //si existen butacas a este evento, se resta de la cantidad que se va crear
                        $contadorButacas ++;
                    }
                }
            }

            foreach ($celdas["celdas"] as $key => $celda) {
                $consultaCelda = $celdaRepository->findOneBy(['salaDeEventos' => $salaDeEvento, 
                'fila' => $celda["fila"], 'columna' => $celda["columna"]]);

                    $butacasACrear = $celda["cantidadButacas"] - $contadorButacas;
                    
                    $consultaCelda->setCantidadButacas($celda["cantidadButacas"]);
                    $consultaCelda->setCategoriaButaca($categoriaButaca);

                    
                    //crear disponibilidades y butacas
                    for ($i=0+3; $i < $butacasACrear+3; $i++) { 
                        $newButaca = new Butaca();
                        $newButaca->setCodigoButaca(strval($i.":".$categoriaButaca->getCodigo()));
                        $newButaca->setDisponible($opcion[0]);
                        $newButaca->setCelda($consultaCelda);

                        $newDisponibilidad = new Disponibilidad();
                        $newDisponibilidad->setButaca($newButaca);
                        $newDisponibilidad->setDisponible($newButaca->getDisponible());
                        $newDisponibilidad->setIdEvento($idEvento);

                        $butacaRepository->save($newButaca, true);
                        $disponibilidadRepository->save($newDisponibilidad, true);
                        
                    }
                $celdaRepository->save($consultaCelda, true);
                $contadorCeldasModificadas++;
                  
                
            }
        
        return $this->responseHelper->responsedatos(['message' =>"celdas modificadas: " . strval($contadorCeldasModificadas),
    'celdasModificadas' => $contadorCeldasModificadas]);
    //return $this->responseHelper->responsedatos(['cantidad' => $contadorButacas]);
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
