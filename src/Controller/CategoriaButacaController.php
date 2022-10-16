<?php

namespace App\Controller;

use App\Entity\CategoriaButaca;
use App\Form\CategoriaButacaType;
use App\Repository\CategoriaButacaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\ResponseHelper;

#[Route('/categoria/butaca')]
class CategoriaButacaController extends AbstractController
{
    private ResponseHelper $responseHelper;
    private TranslatorInterface $translator;

    public function __construct(ResponseHelper $responseHelper, TranslatorInterface $translator)
    {
        $this->responseHelper=$responseHelper;
        $this->translator = $translator;
    }


    #[Route('/', name: 'app_categoria_butaca_index', methods: ['GET'])]
    public function index(CategoriaButacaRepository $categoriaButacaRepository): JsonResponse
    {
        
        $categoriaButaca=$categoriaButacaRepository->findAll();
        return $this->responseHelper->responseDatos(['categoria'=>$categoriaButaca]);
        /**return $this->render('categoria_butaca/index.html.twig', [
            'categoria_butacas' => $categoriaButacaRepository->findAll(),
        ]);*/
    }

    #[Route('/new', name: 'app_categoria_butaca_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoriaButacaRepository $categoriaButacaRepository): Response
    {
        $categoriaButaca = new CategoriaButaca();
        $form = $this->createForm(CategoriaButacaType::class, $categoriaButaca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriaButacaRepository->save($categoriaButaca, true);

            return $this->redirectToRoute('app_categoria_butaca_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categoria_butaca/new.html.twig', [
            'categoria_butaca' => $categoriaButaca,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categoria_butaca_show', methods: ['GET'])]
    public function show(CategoriaButaca $categoriaButaca): Response
    {
        return $this->render('categoria_butaca/show.html.twig', [
            'categoria_butaca' => $categoriaButaca,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categoria_butaca_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoriaButaca $categoriaButaca, CategoriaButacaRepository $categoriaButacaRepository): Response
    {
        $form = $this->createForm(CategoriaButacaType::class, $categoriaButaca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoriaButacaRepository->save($categoriaButaca, true);

            return $this->redirectToRoute('app_categoria_butaca_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categoria_butaca/edit.html.twig', [
            'categoria_butaca' => $categoriaButaca,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categoria_butaca_delete', methods: ['POST'])]
    public function delete(Request $request, CategoriaButaca $categoriaButaca, CategoriaButacaRepository $categoriaButacaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categoriaButaca->getId(), $request->request->get('_token'))) {
            $categoriaButacaRepository->remove($categoriaButaca, true);
        }

        return $this->redirectToRoute('app_categoria_butaca_index', [], Response::HTTP_SEE_OTHER);
    }
}
