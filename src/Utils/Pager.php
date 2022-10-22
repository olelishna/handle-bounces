<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Pager
{
    public int $default_limit;

    public int $counted_data;
    public int $offset;
    public int $limit;

    private int $count;
    public int $prev;
    public int $next;
    public int $page;

    public string $route_name;
    public array $path_parameters;

    public array $firstq = [];
    public array $prevq = [];
    public array $nextq = [];
    public array $lastq = [];

    public function __construct(private UrlGeneratorInterface $router, int $pager_default_limit)
    {
        $this->default_limit = $pager_default_limit;
    }

    public function load(int $offset, int $limit, int $counted_data, string $route_name, array $path_parameters): void
    {
        $this->router->generate($route_name, $path_parameters);

        $this->offset = $offset;
        $this->limit = $limit;
        $this->counted_data = $counted_data;
        $this->route_name = $route_name;
        $this->path_parameters = $path_parameters;

        $this->count = ($this->limit === 0) ? 0 : intdiv($this->counted_data, $this->limit) * $this->limit;
        $this->prev = $this->offset - $this->limit;
        $this->next = $this->offset + $this->limit;
        $this->page = ($this->limit === 0) ? 0 : intdiv($this->offset, $this->limit) + 1;

        $this->firstq = $this->prevq = $this->nextq = $this->lastq = $path_parameters;

        $this->firstq['offset'] = 0;
        $this->prevq['offset'] = $this->prev;
        $this->nextq['offset'] = $this->next;
        $this->lastq['offset'] = $this->count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function processPagerParams(Request $request, &$offset, &$limit): void
    {
        $offset = filter_var(
            $request->get('offset', 0),
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 0, "default" => 0]]
        );

        $limit = filter_var(
            $request->get('limit', $this->default_limit),
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1, "default" => $this->default_limit]]
        );
    }
}

