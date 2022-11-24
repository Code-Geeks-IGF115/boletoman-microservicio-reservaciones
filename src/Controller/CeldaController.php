<?php

namespace App\Controller;

use App\Entity\Butaca;
use App\Entity\Celda;
use App\Form\CeldaType;
use App\Repository\ButacaRepository;
use App\Repository\CeldaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriaButacaRepository;
use App\Service\ResponseHelper;

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
  
    #[Route('/categoria/{idCategoria}', name: 'asignar_categoria_a_celda', methods: ['POST'])]
    public function asignarCategoriaACeldas(
        Request $request, CategoriaButacaRepository $categoriaButacaRepository,
        CeldaRepository $celdaRepository, ButacaRepository $butacaRepository, 
        $idCategoria): JsonResponse {

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

        foreach ($celdas["celdas"] as $key => $celda) {
            $consultaCelda = $celdaRepository->findOneBy(['salaDeEventos' => $salaDeEvento, 
            'fila' => $celda["fila"], 'columna' => $celda["columna"]]);

            if ($consultaCelda != null) {
                $consultaCelda->setCantidadButacas($celda["cantidadButacas"]);
                $consultaCelda->setCategoriaButaca($categoriaButaca);
                    
                //crear  butacas
                for ($i=0; $i < $celda["cantidadButacas"]; $i++) { 

                    $newButaca = new Butaca();
                    $newButaca->setCodigoButaca(strval(($i+1)."-".$categoriaButaca->getCodigo()));
                    $newButaca->setCategoriaButaca($categoriaButaca);
                    $newButaca->setCelda($consultaCelda);
                    $butacaRepository->save($newButaca, true);        
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
