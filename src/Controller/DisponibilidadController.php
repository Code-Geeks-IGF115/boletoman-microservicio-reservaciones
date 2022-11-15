<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\DisponibilidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ResponseHelper;

#[Route('/disponibilidad')]
class DisponibilidadController extends AbstractController
{
    private ResponseHelper $responseHelper;


    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }
    // #[Route('/', name: 'app_disponibilidad_index', methods: ['GET'])]
    // public function index(DisponibilidadRepository $disponibilidadRepository): Response
    // {
    //     return $this->render('disponibilidad/index.html.twig', [
    //         'disponibilidads' => $disponibilidadRepository->findAll(),
    //     ]);
    // }

    // #[Route('/new', name: 'app_disponibilidad_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, DisponibilidadRepository $disponibilidadRepository): Response
    // {
    //     $disponibilidad = new Disponibilidad();
    //     $form = $this->createForm(DisponibilidadType::class, $disponibilidad);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $disponibilidadRepository->save($disponibilidad, true);

    //         return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('disponibilidad/new.html.twig', [
    //         'disponibilidad' => $disponibilidad,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_disponibilidad_show', methods: ['GET'])]
    // public function show(Disponibilidad $disponibilidad): Response
    // {
    //     return $this->render('disponibilidad/show.html.twig', [
    //         'disponibilidad' => $disponibilidad,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_disponibilidad_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Disponibilidad $disponibilidad, DisponibilidadRepository $disponibilidadRepository): Response
    // {
    //     $form = $this->createForm(DisponibilidadType::class, $disponibilidad);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $disponibilidadRepository->save($disponibilidad, true);

    //         return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('disponibilidad/edit.html.twig', [
    //         'disponibilidad' => $disponibilidad,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_disponibilidad_delete', methods: ['POST'])]
    // public function delete(Request $request, Disponibilidad $disponibilidad, DisponibilidadRepository $disponibilidadRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$disponibilidad->getId(), $request->request->get('_token'))) {
    //         $disponibilidadRepository->remove($disponibilidad, true);
    //     }

    //     return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
    // }

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
            $mensaje='Algunas butacas ya han sido compradas';
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


    // #[Route('/desbloquearbutacas', name: 'app_disponibilidad_edit', methods: ['GET', 'POST'])]
    // public function desbloquearbutaca(Request $request, DisponibilidadRepository $disponibilidadRepository): Response
    //     {
    //     $disp=$disponibilidadRepository;
    //     $parametro = Array($request);
    //     $longitud=count($parametro);
    //     for($contador=0;$contador<$longitud;$contador++){
    //      if($disp->findOneBy(['id'=>$parametro[$contador]])->getDisponible()!='Bloqueado'){
    //             return new Response('F', Response::HTTP_PRECONDITION_FAILED);
    //         }
    //         if($disp->findOneBy(['id'=>$parametro[$contador]])->getDisponible()=='Bloqueado'){
    //             $disp->setDisponible('Disponible');
    //             return new Response('GG', Response::HTTP_OK);
    //         }
    //     }
    // }
}
