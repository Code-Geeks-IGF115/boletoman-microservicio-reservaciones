<?php

namespace App\Controller;

use App\Entity\Disponibilidad;
use App\Form\DisponibilidadType;
use App\Repository\DisponibilidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/disponibilidad')]
class DisponibilidadController extends AbstractController
{
    #[Route('/', name: 'app_disponibilidad_index', methods: ['GET'])]
    public function index(DisponibilidadRepository $disponibilidadRepository): Response
    {
        return $this->render('disponibilidad/index.html.twig', [
            'disponibilidads' => $disponibilidadRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_disponibilidad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DisponibilidadRepository $disponibilidadRepository): Response
    {
        $disponibilidad = new Disponibilidad();
        $form = $this->createForm(DisponibilidadType::class, $disponibilidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $disponibilidadRepository->save($disponibilidad, true);

            return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('disponibilidad/new.html.twig', [
            'disponibilidad' => $disponibilidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_disponibilidad_show', methods: ['GET'])]
    public function show(Disponibilidad $disponibilidad): Response
    {
        return $this->render('disponibilidad/show.html.twig', [
            'disponibilidad' => $disponibilidad,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_disponibilidad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Disponibilidad $disponibilidad, DisponibilidadRepository $disponibilidadRepository): Response
    {
        $form = $this->createForm(DisponibilidadType::class, $disponibilidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $disponibilidadRepository->save($disponibilidad, true);

            return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('disponibilidad/edit.html.twig', [
            'disponibilidad' => $disponibilidad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_disponibilidad_delete', methods: ['POST'])]
    public function delete(Request $request, Disponibilidad $disponibilidad, DisponibilidadRepository $disponibilidadRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$disponibilidad->getId(), $request->request->get('_token'))) {
            $disponibilidadRepository->remove($disponibilidad, true);
        }

        return $this->redirectToRoute('app_disponibilidad_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/bloquearbutacas', name: 'app_disponibilidad_edit', methods: ['GET', 'POST'])]
    public function bloquearbutaca(Request $request, DisponibilidadRepository $disponibilidadRepository): Response
    {
        $disp=$disponibilidadRepository;
        $parametro = Array($request);
        $longitud=count($parametro);
        for($contador=0;$contador<$longitud;$contador++){
            if($disp->findOneBy(['id'=>$parametro[$contador]])->getDisponible()!='Desbloqueado'){
                return new Response('Disponible', Response::HTTP_PRECONDITION_FAILED);
            }
            if($disp->findOneBy(['id'=>$parametro[$contador]])->getDisponible()=='Bloqueado'){
                $disp->setDisponible('Bloqueado');
                return new Response('Bloqueado', Response::HTTP_OK);
            }
        }
    }
}
