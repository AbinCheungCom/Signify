<?php

namespace App\Http\Controllers;

use App\Models\Entrepreneur;
use Illuminate\Http\Request;

class EntrepreneurController extends Controller
{
    /**
     * 首页 - 展示推荐企业家
     */
    public function home()
    {
        $featuredEntrepreneurs = Entrepreneur::approved()
            ->featured()
            ->latest()
            ->take(6)
            ->get();

        return inertia('Home', [
            'featuredEntrepreneurs' => $featuredEntrepreneurs,
        ]);
    }

    /**
     * 企业家库列表
     */
    public function index(Request $request)
    {
        $entrepreneurs = Entrepreneur::approved()
            ->search($request->get('search'))
            ->when($request->get('industry'), function ($query, $industry) {
                $query->where('industry', $industry);
            })
            ->when($request->get('city'), function ($query, $city) {
                $query->where('city', $city);
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $industries = Entrepreneur::approved()->pluck('industry')->filter()->unique()->sort()->values();
        $cities = Entrepreneur::approved()->pluck('city')->filter()->unique()->sort()->values();

        return inertia('Entrepreneurs/Index', [
            'entrepreneurs' => $entrepreneurs,
            'industries' => $industries,
            'cities' => $cities,
            'filters' => $request->only(['search', 'industry', 'city']),
        ]);
    }

    /**
     * 企业家详情
     */
    public function show(Entrepreneur $entrepreneur)
    {
        if ($entrepreneur->status !== Entrepreneur::STATUS_APPROVED) {
            abort(403, '该企业家档案尚未认证');
        }

        return inertia('Entrepreneurs/Show', [
            'entrepreneur' => $entrepreneur->load('user'),
        ]);
    }
}
