<?php

namespace App\Http\Controllers;

use App\Http\Requests\Testimonial\StoreTestimonialRequest;
use App\Http\Requests\Testimonial\TestimonialIndexRequest;
use App\Http\Requests\Testimonial\UpdateTestimonialRequest;
use App\Http\Resources\Testimonial\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    public function index(TestimonialIndexRequest $request): Response
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);

        $testimonials = Testimonial::query()
            ->search($filters['search'] ?? null)
            ->filter($filters)
            ->orderByDesc('is_published')
            ->orderBy('sort_order')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('testimonials/Index', [
            'testimonials' => TestimonialResource::collection($testimonials->getCollection())->resolve(),
            'pagination' => [
                'current_page' => $testimonials->currentPage(),
                'last_page' => $testimonials->lastPage(),
                'per_page' => $testimonials->perPage(),
                'total' => $testimonials->total(),
                'from' => $testimonials->firstItem(),
                'to' => $testimonials->lastItem(),
                'links' => $testimonials->linkCollection()->toArray(),
            ],
            'filters' => [
                'search' => $filters['search'] ?? '',
                'status' => $filters['status'] ?? null,
                'rating' => isset($filters['rating']) ? (int) $filters['rating'] : null,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('testimonials/Create');
    }

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['published_at'] = ! empty($data['is_published']) ? now() : null;

        Testimonial::query()->create($data);

        return redirect()->route('testimonials.index')->with('success', 'Testimoni berhasil dibuat.');
    }

    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render('testimonials/Edit', [
            'testimonial' => TestimonialResource::make($testimonial)->resolve(),
        ]);
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $request->validated();

        $publishingNow = ! $testimonial->is_published && ! empty($data['is_published']);
        $unpublishedNow = $testimonial->is_published && empty($data['is_published']);

        if ($publishingNow) {
            $data['published_at'] = now();
        }

        if ($unpublishedNow) {
            $data['published_at'] = null;
        }

        $testimonial->update($data);

        return redirect()->route('testimonials.index')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();

        return redirect()->route('testimonials.index')->with('success', 'Testimoni berhasil dihapus.');
    }

    public function publish(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return redirect()->route('testimonials.index')->with('success', 'Testimoni berhasil dipublikasikan.');
    }

    public function unpublish(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        return redirect()->route('testimonials.index')->with('success', 'Testimoni berhasil disembunyikan.');
    }
}
