<?php

namespace App\Controller;

use App\Entity\BankTransaction;
use App\Http\JsonApi\Traits\ValidatesInputTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use PhpParser\Node\Expr\Empty_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransactionController extends AbstractController
{

    use ValidatesInputTrait;


    /**
     * @var EntityManagerInterface
     */
    public $em;

    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator
    )
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * todo: move validation to a logical step "above" or "before" the controller
     * @Route("/transaction", name="transaction", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        try {
            $transaction = BankTransaction::createFromRequest($request);
        } catch (\RuntimeException $invalidJson) {
            return JsonResponse::create([ 'errors' => $invalidJson->getMessage()], 400);
        }
        if ($formatErrors = $this->validateInput($transaction)) {
            return JsonResponse::create([ 'errors' => $formatErrors], 400);
        }

        $this->em->persist($transaction);
        $this->em->flush(); // DB-exceptions should be caught on a global level (don't want to deal with it here)
        $this->em->refresh($transaction); // HACK: the uuid is generated in a pre-persist life-cycle-callback
        return JsonResponse::create([ 'data' => $transaction ], Response::HTTP_CREATED);
    }

    /**
     *
     *
     * @Route("/transaction/{transactionUuid}", name="transaction", methods={"GET"})
     * @param string $transactionUuid
     * @param Request $request
     *
     * @return Response
     */
    public function show(string $transactionUuid, Request $request): Response
    {

        $transaction = $this->queryOneByUuid($transactionUuid);
        if ($transaction) {
            return JsonResponse::create([ 'data' => $transaction ]);
        }
        return JsonResponse::create('No such transaction', 400);

    }

    /**
     * todo: move all the query-stuff to a repository.
     *
     * @param string $transactionUuid
     */
    private function queryOneByUuid(string $transactionUuid): ?BankTransaction
    {

        //$repo = $this->em->getRepository(BankTransaction::class);
        //$transaction = $repo->findOneBy([ 'uuid' => $transactionUuid ]);

        $trq = $this->em->createQueryBuilder()
            ->select('btr')
            ->from(BankTransaction::class, 'btr')
            ->where('btr.uuid = :uuid')->setParameter('uuid', $transactionUuid);
        $notFound = null;

        $transaction = null;
        try {

            $transaction  = $trq->getQuery()->getSingleResult();
            //} catch (NoResultException | NonUniqueResultException $e)  { /// um.. php 7.3 ?
        } catch (NoResultException $e) {
            $notFound = 1;
        } catch (NonUniqueResultException $e) {
            $notFound = 1;
        }

        return $transaction;
    }

}
