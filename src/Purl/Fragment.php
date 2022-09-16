<?php

declare(strict_types=1);

namespace Purl;

use function array_merge;
use function is_array;
use function parse_url;
use function sprintf;

/**
 * Fragment represents the part of a Url after the hashmark (#).
 *
 * @property Path|string $path
 * @property Query|string $query
 */
class Fragment extends AbstractPart
{
    /** @var string|null The original fragment string. */
    private ?string $fragment;

    /** @var array<string, mixed> */
    protected array $data = [
        'path'  => null,
        'query' => null,
    ];

    /** @var string[] */
    protected array $partClassMap = [
        'path' => 'Purl\Path',
        'query' => 'Purl\Query',
    ];


    public function __construct(string|Path|null $fragment = null, ?Query $query = null)
    {
        if ($fragment instanceof Path) {
            $this->initialized  = true;
            $this->data['path'] = $fragment;
        } else {
            $this->fragment = $fragment;
        }

        $this->data['query'] = $query;
    }

    public function set(string $key, mixed $value): AbstractPart
    {
        $this->initialize();
        $this->data[$key] = $this->preparePartValue($key, $value);

        return $this;
    }

    public function getFragment(): string
    {
        $this->initialize();

        return sprintf(
            '%s%s',
            (string) $this->path,
            (string) $this->query !== '' ? '?' . (string) $this->query : ''
        );
    }

    public function setFragment(string $fragment): AbstractPart
    {
        $this->initialized = false;
        $this->data        = [];
        $this->fragment    = $fragment;

        return $this;
    }

    public function setPath(Path $path): AbstractPart
    {
        $this->data['path'] = $path;

        return $this;
    }

    public function getPath(): Path
    {
        $this->initialize();

        return $this->data['path'];
    }

    public function setQuery(Query $query): AbstractPart
    {
        $this->data['query'] = $query;

        return $this;
    }

    public function getQuery(): Query
    {
        $this->initialize();

        return $this->data['query'];
    }

    public function __toString(): string
    {
        return $this->getFragment();
    }

    protected function doInitialize(): void
    {
        if (isset($this->fragment)) {
            $parsed = parse_url($this->fragment);

            if (is_array($parsed)) {
                $this->data = array_merge($this->data, $parsed);
            }
        }

        foreach ($this->data as $key => $value) {
            $this->data[$key] = $this->preparePartValue($key, $value);
        }
    }
}
