<?php

namespace App\Controller;

use App\Entity\CategoriaButaca;
use App\Form\CategoriaButacaType;
use App\Repository\CategoriaButacaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ResponseHelper;
use Exception;

#[Route('/categoria/butaca')]
class CategoriaButacaController extends AbstractController
{
    private ResponseHelper $responseHelper;

    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper=$responseHelper;
    }

    #[Route('/{id}', name: 'app_categoria_butaca_show', methods: ['GET'])]
    public function show(CategoriaButaca $categoriaButaca): JsonResponse
    {
        return $this->responseHelper->responseDatos( [
            'categoriasButaca' => $categoriaButaca
        ],['ver_categoria']);
    }

    #[Route('/new', name: 'app_categoria_butaca_new', methods: ['POST'])]
    public function new(Request $request, CategoriaButacaRepository $categoriaButacaRepository): JsonResponse
    {
        // recuperando frecuencias   
        try{
            $parametros=$request->toArray();
            $request->request->replace(["categoria_butaca"=>$parametros]);
            $categoriaButaca = new CategoriaButaca();
            $form = $this->createForm(CategoriaButacaType::class, $categoriaButaca);

            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $categoriaButacaRepository->save($categoriaButaca, true);
    
                return $this->responseHelper->responseDatos(["message"=>"Categoría de butacas guardada.", "id"=>$categoriaButaca->getId()]);
            }else{

                return $this->responseHelper->responseDatosNoValidos();
            }
            
        }catch(Exception $e){
            return $this->responseHelper->responseMessage($e->getMessage());
        }
    }
    
    #[Route('/{id}/edit', name: 'app_categoria_butaca_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoriaButaca $categoriaButaca, CategoriaButacaRepository $categoriaButacaRepository): JsonResponse
    {
        // recuperando frecuencias   
        $parametros=$request->toArray();
        $request->request->replace(["categoria_butaca"=>$parametros]);
        try{
            $form = $this->createForm(CategoriaButacaType::class, $categoriaButaca);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $categoriaButacaRepository->save($categoriaButaca, true);
                
                return $this->responseHelper->responseDatos(["message"=>"Categoría de butacas guardada.", "id"=>$categoriaButaca->getId()]);
            }else{
                return $this->responseHelper->responseDatosNoValidos();
            }
        }catch(Exception $e){
            return $this->responseHelper->responseMessage($e->getMessage());
        }
        
    }
    
    #[Route('/{id}', name: 'app_categoria_butaca_delete', methods: ['POST'])]
    public function delete(Request $request, CategoriaButaca $categoriaButaca=null, CategoriaButacaRepository $categoriaButacaRepository): JsonResponse
    {
        if ($categoriaButaca) {
            $categoriaButacaRepository->remove($categoriaButaca, true);
        }else{
            return $this->responseHelper->responseDatosNoValidos("Categoría de butacas no existe.");
        }

        return $this->responseHelper->responseMessage("Categoría de butacas ".$categoriaButaca->getNombre()." eliminada.");
    }
}
