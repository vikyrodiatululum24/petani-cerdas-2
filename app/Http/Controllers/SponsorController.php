<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sponsor\SponsorIndexRequest;
use App\Http\Requests\Sponsor\StoreSponsorRequest;
use App\Http\Requests\Sponsor\UpdateSponsorRequest;
use App\Http\Resources\Sponsor\SponsorResource;
use App\Models\Sponsor;
use App\Services\ImageCompressionService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SponsorController extends Controller
{
    public function __construct(
        private ImageCompressionService $imageCompressionService,
        private SlugService $slugService
    )
    {
    }

    public function index(SponsorIndexRequest $request): Response
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);

        $sponsors = Sponsor::query()
            ->search($filters['search'] ?? null)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('sponsors/Index', [
            'sponsors' => SponsorResource::collection($sponsors->getCollection())->resolve(),
            'pagination' => [
                'current_page' => $sponsors->currentPage(),
                'last_page' => $sponsors->lastPage(),
                'per_page' => $sponsors->perPage(),
                'total' => $sponsors->total(),
                'from' => $sponsors->firstItem(),
                'to' => $sponsors->lastItem(),
                'links' => $sponsors->linkCollection()->toArray(),
            ],
            'filters' => [
                'search' => $filters['search'] ?? '',
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('sponsors/Create');
    }

    public function store(StoreSponsorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->slugService->generateUnique(Sponsor::class, $data['name']);

        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = $this->imageCompressionService->compressAndStore(
                $request->file('logo_path'),
                'sponsors'
            );
        }

        Sponsor::create($data);

        return redirect()->route('sponsors.index')->with('success', 'Sponsor berhasil dibuat.');
    }

    public function show(Sponsor $sponsor): Response
    {
        return Inertia::render('sponsors/Show', [
            'sponsor' => SponsorResource::make($sponsor)->resolve(),
        ]);
    }

    public function edit(Sponsor $sponsor): Response
    {
        return Inertia::render('sponsors/Edit', [
            'sponsor' => SponsorResource::make($sponsor)->resolve(),
        ]);
    }

    public function update(UpdateSponsorRequest $request, Sponsor $sponsor): RedirectResponse
    {
        $data = $request->validated();
        $removeLogo = (bool) ($data['remove_logo'] ?? false);
        unset($data['remove_logo']);

        $data['slug'] = $this->slugService->generateUnique(
            Sponsor::class,
            $data['name'],
            'slug',
            $sponsor->id
        );

        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = $this->imageCompressionService->compressAndStore(
                $request->file('logo_path'),
                'sponsors',
                $sponsor->logo_path
            );
        } elseif ($removeLogo) {
            $this->imageCompressionService->deleteIfExists($sponsor->logo_path);
            $data['logo_path'] = null;
        }

        $sponsor->update($data);

        return redirect()->route('sponsors.index')->with('success', 'Sponsor berhasil diperbarui.');
    }

    public function destroy(Sponsor $sponsor): RedirectResponse
    {
        $this->imageCompressionService->deleteIfExists($sponsor->logo_path);
        $sponsor->delete();

        return redirect()->route('sponsors.index')->with('success', 'Sponsor berhasil dihapus.');
    }
}
