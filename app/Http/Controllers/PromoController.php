<?php

namespace App\Http\Controllers;

use App\Http\Resources\PromoResource;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PromoController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        // Read location from JSON body; default to 'online'
        $location = $request->input('location', 'online');
        if (! in_array($location, ['online', 'offline'], true)) {
            $location = 'online';
        }

        $perPage = (int) ($request->input('per_page') ?? 20);
        $perPage = max(1, min(100, $perPage));

        $promos = Promo::query()
            ->where('active', true)
            ->where('location', $location)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return PromoResource::collection($promos);
    }

    public function detail(string $slug): PromoResource
    {
        $promo = Promo::query()
            ->where('active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return new PromoResource($promo);
    }

    // For detail, slug is unique across promos, so location isn't needed.
}
