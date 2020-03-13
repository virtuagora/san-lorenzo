<?php

namespace App\Util;

class Paginator
{
    protected $items;
    protected $total;
    protected $size;
    protected $page;
    protected $last;
    protected $params;
    protected $uri;

    public function __construct($query, $params)
    {
        $this->params = $params;
        $this->total = $query->toBase()->getCountForPagination();
        $this->size = $params['size'];
        $this->last = max((int) ceil($this->total / $params['size']), 1);
        $this->page = min($params['page'], $this->last);
        $this->items = $query->forPage($params['page'], $params['size'])->get();
    }

    public function makeVisible($attr)
    {
        $this->items->makeVisible($attr);
    }

    public function setUri($uri)
    {
        $this->uri = $uri; //->withQuery(http_build_query($uri));
    }

    public function items()
    {
        return $this->items;
    }

    public function addItem($new)
    {
        return $this->items->push($new);
    }

    public function total()
    {
        return $this->total;
    }

    public function currentPage()
    {
        return $this->page;
    }

    public function lastPage()
    {
        return $this->last;
    }

    public function hasMorePages()
    {
        return $this->page < $this->last;
    }

    public function url($page)
    {
        $params = $this->params;
        $params['page'] = $page;
        if (isset($this->uri)) {
            return ''.$this->uri->withQuery(http_build_query($params));
        } else {
            return http_build_query($params);
        }
    }

    public function nextPageUrl()
    {
        return ($this->page < $this->last)? $this->url($this->page+1): null;
    }

    public function previousPageUrl()
    {
        return ($this->page > 1)? $this->url($this->page-1): null;
    }

    public function toArray()
    {
        return $this->items->toArray();
    }

    public function metadata()
    {
        return [
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
        ];
    }
}
