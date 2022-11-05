<?php

namespace App\Controller;

use App\Entity\CategoriaButaca;
use App\Entity\Celda;
use App\Form\CeldaType;
use App\Repository\CeldaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use App\Repository\CategoriaButacaRepository;
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
    }

    #[Route('/{id}', name: 'app_celda_show', methods: ['GET'])]
    public function show(Celda $celda): Response
    {
        return $this->render('celda/show.html.twig', [
            'celda' => $celda,
        ]);
    }*/

    #[Route('/{idCategoria}/new', name: 'app_celda_new', methods: ['POST'])]
    public function new(
        Request $request,
        CategoriaButacaRepository $categoriaButacaRepository,
        CeldaRepository $celdaRepository,
        $idCategoria
    ): JsonResponse {
        //recuperando la categoria butaca
        $categoriaButaca = $categoriaButacaRepository->find($idCategoria);
        if ($categoriaButaca == null) { //verifica si el id ingresado existe 
            return $this->responseHelper->responseMessage("Categoría no existe");
        }
        $salaDeEvento = $categoriaButaca->getSalaDeEventos();

        //recuperar todas las celdas de esta sala de eventos
        $celdas = $celdaRepository->findBy(['categoriaButaca' => $categoriaButaca]);
        //var_dump($celdas);
        //almacenando json request en array para comparar
        $parametrosarray = [];
        if ($request->getContent()) {
            $parametrosarray = json_decode($request->getContent(), true);
        }
        $result = "no se logro";
        //crear celdas
        if($celdas == 0){
            return $this->responseHelper->responseDatos("No existe las celdas que busca");
        }
        else{
            for ($fila = 1; $fila <= $salaDeEvento->getFilas(); $fila++) {
                for ($columna = 1; $columna <= $salaDeEvento->getColumnas(); $columna++) {
                    //recorrer las celdas del request y las celdas de la base de datos en
                    //simultaneo y comparar los atributos de fila y columna
                    // y si son iguales entonces actualizar(update) 
                    //la celda (cantidad butacas y asignar categoriaButaca)y 
                    // guardarla en la base de datos
                    
                    for ($i = 0; $i < count($parametrosarray["celdas"]); $i++) {
                        if (($parametrosarray["celdas"][$i]["fila"] == $celdas[$fila]->getFila()) &&
                            ($parametrosarray["celdas"][$i]["columna"] == $celdas[$columna]->getColumna())
                        ) {
                            $celda = new Celda();
                            $celda->setSalaDeEventos($salaDeEvento);
                            $celda->setCantidadButacas($fila);
                            $celda->setCategoriaButaca($categoriaButaca);
                            $celdaRepository->save($celda, true);
                            $result = "si se logro";
                        }
                    }
                }
                
            }
        }
        
        return $this->responseHelper->responseDatos(count($celdas));
        
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
