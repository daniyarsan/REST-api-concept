<?php


namespace App\Business\Page;


use Symfony\Component\HttpFoundation\Request;

class PageCreator
{

    public function create(Request $request)
    {
        dd($request);
    }
}