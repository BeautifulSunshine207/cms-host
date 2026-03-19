<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $teamMember = $this->resolveTeamMember($request);

        $documents = $teamMember
            ? $teamMember->documents()->latest()->get()
            : collect();

        $documentCards = $this->buildDocumentCards($documents);
        $username = $user->email ? Str::before($user->email, '@') : ($user->name ?? 'employee');

        return view('employee.profile.show', [
            'user' => $user,
            'teamMember' => $teamMember,
            'username' => $username,
            'documentCards' => $documentCards,
            'documentsCount' => $documents->count(),
        ]);
    }

    private function resolveTeamMember(Request $request): ?TeamMember
    {
        $user = $request->user();
        $email = strtolower(trim((string) ($user->email ?? '')));

        if ($email !== '') {
            $byEmail = TeamMember::whereRaw('LOWER(email) = ?', [$email])->first();
            if ($byEmail) {
                return $byEmail;
            }
        }

        $name = trim((string) ($user->name ?? ''));
        if ($name === '') {
            return null;
        }

        $matches = TeamMember::where('name', $name)->limit(2)->get();
        return $matches->count() === 1 ? $matches->first() : null;
    }

    private function buildDocumentCards(Collection $documents): Collection
    {
        return $documents
            ->groupBy(function ($doc) {
                return trim((string) ($doc->type ?? 'Document')) ?: 'Document';
            })
            ->map(function (Collection $docs, string $type) {
                $previewDoc = $docs->first(function ($doc) {
                    return $this->isImageFile((string) ($doc->path ?? ''), (string) ($doc->name ?? ''));
                }) ?? $docs->first();

                $previewUrl = $previewDoc ? $this->resolvePublicUrl((string) ($previewDoc->path ?? '')) : null;

                return [
                    'type' => $type,
                    'label' => Str::title(str_replace(['_', '-'], ' ', $type)),
                    'count' => $docs->count(),
                    'preview_url' => $previewUrl,
                    'preview_is_image' => $previewDoc
                        ? $this->isImageFile((string) ($previewDoc->path ?? ''), (string) ($previewDoc->name ?? ''))
                        : false,
                ];
            })
            ->values();
    }

    private function resolvePublicUrl(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    private function isImageFile(string $path, string $name = ''): bool
    {
        $value = strtolower($path . ' ' . $name);
        foreach (['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp', '.svg'] as $ext) {
            if (Str::contains($value, $ext)) {
                return true;
            }
        }

        return false;
    }
}
