<?php

namespace App\Paginator;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaginatorService
{
    private int $currentPage;

    private string $search;

    private int $limit;

    /**
     * @var array<object>
     */
    private array $data;

    private int $countItemsTotal;

    private int $lastPage;

    private string $currentRoute;

    private function getDefaultPage(ServiceEntityRepositoryInterface $repository): mixed
    {
        $defaultPerPage = '3';
        if ($repository instanceof TaskRepository) {
            $defaultPerPage = $this->parameterBag->get('default_task_per_page');
        }
        if ($repository instanceof UserRepository) {
            $defaultPerPage = $this->parameterBag->get('default_user_per_page');
        }

        return $defaultPerPage;
    }

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    /**
     * @return array<string, int|string>|null
     */
    public function create(
        ServiceEntityRepositoryInterface $repository,
        Request $request,
        string $currentRoute
    ): array|null {
        $this->currentPage = intval($request->get('page', 1));

        $this->limit = intval($request->get('limit', $this->getDefaultPage($repository)));
        $this->currentRoute = $currentRoute;
        $this->search = $request->get('q', '');
        if ($this->currentPage < 1) {
            return [
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $this->translator->trans('app.exceptions.bad_request_http_exception_page'),
            ];
        }
        if ($this->limit < 1) {
            return [
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $this->translator->trans('app.exceptions.bad_request_http_exception_limit'),
            ];
        }
        if (
            (!$repository instanceof TaskRepository && !$repository instanceof UserRepository)
            || !method_exists($repository, 'searchAndPaginate')
        ) {
            return [
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $this->translator->trans('app.exceptions.bad_method_call_exception_searchAndPaginate'),
            ];
        }
        $this->data = $repository->searchAndPaginate(
            $this->limit,
            ($this->currentPage - 1) * $this->limit,
            $this->currentRoute,
            $this->search
        );
        $this->countItemsTotal = count($repository->searchAndPaginate(null, null, $this->currentRoute, $this->search));
        $this->lastPage = intval(ceil($this->countItemsTotal / $this->limit));

        return null;
    }

    public function getUrlFirstPage(): string
    {
        return $this->urlGenerator->generate(
            $this->currentRoute,
            ['limit' => $this->limit, 'page' => 1]
        );
    }

    public function getUrlLastPage(): string
    {
        return $this->urlGenerator->generate(
            $this->currentRoute,
            ['limit' => $this->limit, 'page' => $this->lastPage]
        );
    }

    public function getUrlNextPage(): ?string
    {
        return $this->currentPage < $this->lastPage ? $this->urlGenerator->generate(
            $this->currentRoute,
            ['limit' => $this->limit, 'page' => $this->currentPage + 1]
        ) : null;
    }

    public function getUrlPreviousPage(): ?string
    {
        return $this->currentPage === 1 ? null : $this->urlGenerator->generate(
            $this->currentRoute,
            ['limit' => $this->limit, 'page' => $this->currentPage - 1]
        );
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return array<object>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getCountItemsTotal(): int
    {
        return $this->countItemsTotal;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getCurrentRoute(): string
    {
        return $this->currentRoute;
    }
}
