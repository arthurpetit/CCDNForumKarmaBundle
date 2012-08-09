<?php

namespace CCDNForum\KarmaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * KarmaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class KarmaRepository extends EntityRepository
{

    /**
     *
     * @access public
     * @param Int $userId
     */
    public function findKarmaForUserById($userId)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT k FROM CCDNForumKarmaBundle:Karma k
                LEFT JOIN k.ratingByUser kc
                LEFT JOIN k.ratingForUser kf
                WHERE k.ratingForUser = :id
                ORDER BY k.postedDate DESC')
            ->setParameter('id', $userId);

        try {
            return new Pagerfanta(new DoctrineORMAdapter($query));
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * @access public
 	 * @param Int $userId
     */
    public function getKarmaCountForUserById($userId)
    {

        $kpQuery = $this->getEntityManager()
            ->createQuery('
                SELECT COUNT(kp.id) AS karmaPositiveCount
                FROM CCDNForumKarmaBundle:Karma kp
                WHERE kp.ratingForUser = :id AND kp.isPositive = TRUE')
            ->setParameter('id', $userId);

        $knQuery = $this->getEntityManager()
            ->createQuery('
                SELECT COUNT(kn.id) AS karmaNegativeCount
                FROM CCDNForumKarmaBundle:Karma kn
                WHERE kn.ratingForUser = :id AND kn.isPositive = FALSE')
            ->setParameter('id', $userId);

        try {
            $kp = $kpQuery->getsingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $kp = null;
        }

        try {
            $kn = $knQuery->getsingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $kn = null;
        }

        return array_merge($kp, $kn);

    }

}
