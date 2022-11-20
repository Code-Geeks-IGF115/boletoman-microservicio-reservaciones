<?php

namespace App\Repository;

use App\Entity\Disponibilidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Disponibilidad>
 *
 * @method Disponibilidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disponibilidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disponibilidad[]    findAll()
 * @method Disponibilidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisponibilidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disponibilidad::class);
    }

    public function save(Disponibilidad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Disponibilidad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Disponibilidad[] Returns an array of Disponibilidad objects
    * Estado es Disponible, No disponible o Bloqueado
    */
   public function findByDisponibilidad($idEvento, $estado): array
   {
       return $this->createQueryBuilder('d')
           ->andWhere('d.idEvento = :id_evento')
           ->setParameter('id_evento', $idEvento)
           ->andWhere('d.disponible = :disponible')
           ->setParameter('disponible', $estado)
           ->orderBy('d.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

   /**
    * @return Disponibilidad[] Returns an array of Disponibilidad objects
    * Estado es Disponible, No disponible o Bloqueado
    * Función que busca disponibilidad por estado, id del Butacas e id del Evento, para bloquear,
    * desbloquear y comprar butacas
    */
   public function findByDisp($idEvento, $estado): array
   {
       return $this->createQueryBuilder('d')
           ->andWhere('d.idEvento = :id_evento')
           ->setParameter('id_evento', $idEvento)
           ->andWhere('d.disponible = :disponible')
           ->setParameter('disponible', $estado)
           ->orderBy('d.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }
   public function findByEstado($idEvento, $estado, array $idButacas): array
   {
       return $this->createQueryBuilder('d')
           ->andWhere('d.idEvento = :id_evento')
           ->setParameter('id_evento', $idEvento)
           ->andWhere('d.disponible = :disponible')
           ->setParameter('disponible', $estado)
           ->andWhere('d.butaca IN (:idButacas)')
           ->setParameter('idButacas', $idButacas, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
           ->orderBy('d.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

   public function calcularIngresosPorCategoriaButaca($idEvento, $estado): array
   {
    $entityManager = $this->getEntityManager();
    $query = $entityManager->createQuery(
        'SELECT cb.codigo, cb.precioUnitario, SUM(cb.precioUnitario), COUNT(cb.codigo)
        FROM App\Entity\Disponibilidad u 
        JOIN u.butaca a 
        JOIN a.celda c 
        JOIN c.categoriaButaca cb
        where u.disponible = :disponible and u.idEvento = :idEvento
        GROUP BY cb.codigo, cb.precioUnitario
        '
    )->setParameter('disponible', $estado)
    ->setParameter('idEvento',$idEvento);

    return $query->getResult();
   }
//    /**
//     * @return Disponibilidad[] Returns an array of Disponibilidad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Disponibilidad
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
