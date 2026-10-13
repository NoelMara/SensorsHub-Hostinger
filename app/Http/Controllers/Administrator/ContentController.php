<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Project;
use App\Models\Sensor;
use App\Models\Video;
use Cloudinary\Cloudinary;
use App\Helpers\ActivityLogHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function sensors(Request $request)
    {
        $search = $request->query('search');
        $items = Sensor::when($search, function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();
        $stats = [
            'total' => Sensor::count(),
            'active' => Sensor::where('is_active', true)->count(),
            'inactive' => Sensor::where('is_active', false)->count(),
        ];

        return view('administrator.content.index', [
            'items' => $items,
            'stats' => $stats,
            'type' => 'sensors',
            'title' => 'Sensors',
            'description' => 'Monitor the sensor catalog available across SensorHub.',
        ]);
    }

    public function projects(Request $request)
    {
        $search = $request->query('search');
        $items = Project::with('sensor')
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%");
            })
            ->latest()->paginate(10)->withQueryString();
        $stats = [
            'total' => Project::count(),
            'active' => Project::where('is_active', true)->count(),
            'inactive' => Project::where('is_active', false)->count(),
            'featured' => Project::where('is_featured', true)->count(),
        ];

        return view('administrator.content.index', [
            'items' => $items,
            'stats' => $stats,
            'type' => 'projects',
            'title' => 'Projects',
            'description' => 'Review project tutorials, visibility, and featured content.',
        ]);
    }

    public function products(Request $request)
    {
        $search = $request->query('search');
        $items = Product::when($search, function ($q) use ($search) {
            $q->where('name', 'ilike', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
        ];

        return view('administrator.content.index', [
            'items' => $items,
            'stats' => $stats,
            'type' => 'products',
            'title' => 'Products',
            'description' => 'Review shop products, categories, and publishing status.',
        ]);
    }

    public function videos(Request $request)
    {
        $search = $request->query('search');
        $items = Video::with('sensor')
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%");
            })
            ->latest()->paginate(10)->withQueryString();
        $stats = [
            'total' => Video::count(),
            'active' => Video::where('is_active', true)->count(),
            'inactive' => Video::where('is_active', false)->count(),
            'featured' => Video::where('is_featured', true)->count(),
        ];

        return view('administrator.content.index', [
            'items' => $items,
            'stats' => $stats,
            'type' => 'videos',
            'title' => 'Videos',
            'description' => 'Review tutorial videos and their linked sensor coverage.',
        ]);
    }

    public function create(string $type)
    {
        $this->ensureValidType($type);

        $view = match ($type) {
            'products' => 'administrator.content.products.create',
            'sensors' => 'administrator.content.sensors.create',
            'projects' => 'administrator.content.projects.create',
            'videos' => 'administrator.content.videos.create',
        };

        return view($view, [
            'prefix' => 'administrator',
            'sensors' => $this->needsSensors($type) ? Sensor::where('is_active', true)->orderBy('name')->get() : collect(),
        ]);
    }

    public function store(Request $request, string $type)
    {
        $this->ensureValidType($type);

        $data = $this->validatedData($request, $type);
        $item = $this->modelFor($type)::create($data);
        $name = $item->title ?? $item->name;
        ActivityLogHelper::log('created', $type, "created a new {$type} '{$name}'");

        return redirect()->route('administrator.' . $type . '.index')
            ->with('success', $this->singularLabelFor($type) . ' created successfully.');
    }

    public function edit(string $type, int $id)
    {
        $this->ensureValidType($type);

        $item = $this->findItem($type, $id);
        $variableName = Str::singular($type); // sensors -> sensor, products -> product

        $view = match ($type) {
            'products' => 'administrator.content.products.edit',
            'sensors' => 'administrator.content.sensors.edit',
            'projects' => 'administrator.content.projects.edit',
            'videos' => 'administrator.content.videos.edit',
        };

        return view($view, [
            'prefix' => 'administrator',
            $variableName => $item,
            'sensors' => $this->needsSensors($type) ? Sensor::where('is_active', true)->orderBy('name')->get() : collect(),
        ]);
    }

    public function update(Request $request, string $type, int $id)
    {
        $this->ensureValidType($type);

        $item = $this->findItem($type, $id);
        $item->update($this->validatedData($request, $type, $item));

        $name = $item->title ?? $item->name;
        ActivityLogHelper::log('updated', $type, "updated {$type} '{$name}'");

        return redirect()->route('administrator.' . $type . '.index')
            ->with('success', $this->singularLabelFor($type) . ' updated successfully.');
    }

   public function destroy(string $type, int $id)
    {
        $this->ensureValidType($type);
        $item = $this->findItem($type, $id);
        $name = $item->title ?? $item->name;
        $item->delete();
        ActivityLogHelper::log('deleted', $type, "deleted {$type} '{$name}'");

        return redirect()->route('administrator.' . $type . '.index')
            ->with('success', $this->singularLabelFor($type) . ' deleted successfully.');
    }

    private function ensureValidType(string $type): void
    {
        abort_unless(in_array($type, ['sensors', 'projects', 'products', 'videos'], true), 404);
    }

    /**
     * @return class-string<Model>
     */
    private function modelFor(string $type): string
    {
        return match ($type) {
            'sensors' => Sensor::class,
            'projects' => Project::class,
            'products' => Product::class,
            'videos' => Video::class,
        };
    }

    private function findItem(string $type, int $id): Model
    {
        return $this->modelFor($type)::findOrFail($id);
    }

    private function singularLabelFor(string $type): string
    {
        return match ($type) {
            'sensors' => 'Sensor',
            'projects' => 'Project',
            'products' => 'Product',
            'videos' => 'Video',
        };
    }

    private function needsSensors(string $type): bool
    {
        return in_array($type, ['projects', 'videos'], true);
    }

    private function validatedData(Request $request, string $type, ?Model $item = null): array
    {
        $data = match ($type) {
            'sensors' => $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'how_it_works' => 'required|string',
                'use_cases' => 'required|string',
                'components_needed' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]),
            'projects' => $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
                'sensor_id' => 'required|exists:sensors,id',
                'components_needed' => 'required|string',
                'instructions' => 'required|string',
                'code' => 'nullable|string',
            ]),
            'products' => $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'link' => 'required|url',
                'category' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]),
            'videos' => $request->validate([
                'title' => 'required|string|max:255',
                'youtube_link' => 'required|url',
                'category' => 'required|string|max:255',
                'description' => 'nullable|string',
                'sensor_id' => 'nullable|exists:sensors,id',
            ]),
        };

        // Only set slug on create — keep it stable on update
        if ($item === null) {
            if (in_array($type, ['sensors', 'products'], true)) {
                $data['slug'] = Str::slug($data['name']);
            }

            if (in_array($type, ['projects', 'videos'], true)) {
                $data['slug'] = Str::slug($data['title']);
            }
        }

        if ($type === 'videos') {
            $data['youtube_id'] = $this->extractYouTubeId($data['youtube_link']);
        }

        if (in_array($type, ['sensors', 'products'])) {
            if ($request->hasFile('image')) {
                try {
                    $cloudinary = new \Cloudinary\Cloudinary([
                        'cloud' => [
                            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                            'api_key'    => env('CLOUDINARY_API_KEY'),
                            'api_secret' => env('CLOUDINARY_API_SECRET'),
                        ],
                        'url' => ['secure' => true],
                    ]);
                    $result = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
                    $data['image'] = $result['secure_url'];
                } catch (\Exception $e) {
                    \Log::error('Cloudinary upload failed', ['error' => $e->getMessage()]);
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'image' => 'Image upload failed. Please try again.',
                    ]);
                }
            } elseif ($item !== null) {
                $data['image'] = $item->image;
            }
        }

        $data['is_active'] = $request->has('is_active');

        if (in_array($type, ['projects', 'videos'], true)) {
            $data['is_featured'] = $request->has('is_featured');
        }

        return $data;
    }

    private function extractYouTubeId(string $url): ?string
    {
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\/?]+)/', $url, $matches);

        return $matches[1] ?? null;
    }
}